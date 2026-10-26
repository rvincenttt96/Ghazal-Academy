<?php

require_once __DIR__ . '/includes/layout.php';
require_auth();

require_once __DIR__ . '/services/EitaaUniomService.php';

$pdo = db();


/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * بررسی وجود جداول مورد نیاز بات
 *
 * عمداً از SHOW TABLES LIKE ? استفاده نمی‌کنیم
 * چون روی بعضی نسخه‌های MySQL/MariaDB با Native Prepare مشکل دارد.
 */
function eitaa_tables_ready(PDO $pdo): bool
{
    $required = [
        'eitaa_conversations',
        'eitaa_updates',
    ];

    try {
        $stmt = $pdo->query('SHOW TABLES');
        $existing = [];

        while (($table = $stmt->fetchColumn()) !== false) {
            $existing[] = (string)$table;
        }

        foreach ($required as $table) {
            if (!in_array($table, $existing, true)) {
                return false;
            }
        }

        return true;

    } catch (Throwable $e) {
        error_log(
            '[EITAA] Table check error: ' .
            $e->getMessage()
        );

        return false;
    }
}


/**
 * خواندن تنظیم
 *
 * این تابع مستقل از get_setting پروژه نوشته شده
 * تا این صفحه به Cache یا نسخه قبلی تنظیمات وابسته نباشد.
 */
function eitaa_get_setting(
    PDO $pdo,
    string $key,
    string $default = ''
): string {

    try {

        $stmt = $pdo->prepare(
            'SELECT setting_value
             FROM settings
             WHERE setting_key = ?
             LIMIT 1'
        );

        $stmt->execute([$key]);

        $value = $stmt->fetchColumn();

        if ($value === false || $value === null) {
            return $default;
        }

        return (string)$value;

    } catch (Throwable $e) {

        error_log(
            '[EITAA] Read setting error (' .
            $key .
            '): ' .
            $e->getMessage()
        );

        return $default;
    }
}


/**
 * ذخیره تنظیم
 *
 * عمداً updated_at در Query نیامده.
 * بنابراین هم با نسخه قدیمی جدول settings
 * و هم نسخه جدید کار می‌کند.
 */
function eitaa_set_setting(
    PDO $pdo,
    string $key,
    ?string $value
): void {

    $stmt = $pdo->prepare(
        'INSERT INTO settings
            (setting_key, setting_value)
         VALUES
            (?, ?)
         ON DUPLICATE KEY UPDATE
            setting_value = VALUES(setting_value)'
    );

    $stmt->execute([
        $key,
        $value,
    ]);
}


/**
 * استخراج متن خطای سرویس API
 */
function eitaa_error_message(array $response): string
{
    foreach (
        [
            'description',
            'error',
            'message',
        ]
        as $key
    ) {

        if (
            isset($response[$key]) &&
            is_scalar($response[$key]) &&
            trim((string)$response[$key]) !== ''
        ) {
            return (string)$response[$key];
        }
    }

    if (!empty($response['http_status'])) {
        return 'HTTP ' . (string)$response['http_status'];
    }

    return 'پاسخ مشخصی از سرویس دریافت نشد.';
}


/*
|--------------------------------------------------------------------------
| Database readiness
|--------------------------------------------------------------------------
*/

if (!eitaa_tables_ready($pdo)) {
    redirect('upgrade-v03.php');
}


/*
|--------------------------------------------------------------------------
| Load settings
|--------------------------------------------------------------------------
*/

$baseUrl = eitaa_get_setting(
    $pdo,
    'eitaa_api_base_url',
    'https://api.uniom.ir/bot'
);

$apiKey = eitaa_get_setting(
    $pdo,
    'eitaa_api_key',
    ''
);

$academyName = eitaa_get_setting(
    $pdo,
    'eitaa_academy_name',
    'آموزشگاه زبان غزل'
);

$enabled =
    eitaa_get_setting(
        $pdo,
        'eitaa_enabled',
        '0'
    ) === '1';

$webhookSecret = eitaa_get_setting(
    $pdo,
    'eitaa_webhook_secret',
    ''
);

$selfId = eitaa_get_setting(
    $pdo,
    'eitaa_self_id',
    ''
);

$webhookActive =
    eitaa_get_setting(
        $pdo,
        'eitaa_webhook_active',
        '0'
    ) === '1';


/*
|--------------------------------------------------------------------------
| Create webhook secret
|--------------------------------------------------------------------------
*/

if ($webhookSecret === '') {

    try {

        $webhookSecret = bin2hex(
            random_bytes(24)
        );

        eitaa_set_setting(
            $pdo,
            'eitaa_webhook_secret',
            $webhookSecret
        );

    } catch (Throwable $e) {

        error_log(
            '[EITAA] Webhook secret error: ' .
            $e->getMessage()
        );

        $webhookSecret =
            hash(
                'sha256',
                session_id() .
                microtime(true) .
                __FILE__
            );
    }
}


/*
|--------------------------------------------------------------------------
| Build Webhook URL
|--------------------------------------------------------------------------
*/

$isHttps =
    !empty($_SERVER['HTTPS']) &&
    strtolower((string)$_SERVER['HTTPS']) !== 'off';

$scheme = $isHttps
    ? 'https'
    : 'http';

$host =
    $_SERVER['HTTP_HOST']
    ?? 'books.ghazalify.com';

$scriptName =
    $_SERVER['SCRIPT_NAME']
    ?? '/eitaa-settings.php';

$basePath =
    rtrim(
        str_replace(
            '\\',
            '/',
            dirname($scriptName)
        ),
        '/'
    );

if ($basePath === '.') {
    $basePath = '';
}

$webhookUrl =
    $scheme .
    '://' .
    $host .
    $basePath .
    '/eitaa-webhook.php?key=' .
    rawurlencode($webhookSecret);


/*
|--------------------------------------------------------------------------
| POST actions
|--------------------------------------------------------------------------
*/

if (
    ($_SERVER['REQUEST_METHOD'] ?? 'GET')
    === 'POST'
) {

    verify_csrf();

    $action =
        trim(
            (string)(
                $_POST['action']
                ?? 'save'
            )
        );


    /*
    |--------------------------------------------------------------------------
    | Save settings
    |--------------------------------------------------------------------------
    */

    if ($action === 'save') {

        try {

            $newBase =
                trim(
                    (string)(
                        $_POST['base_url']
                        ?? ''
                    )
                );

            $newApiKey =
                trim(
                    (string)(
                        $_POST['api_key']
                        ?? ''
                    )
                );

            $newAcademy =
                trim(
                    (string)(
                        $_POST['academy_name']
                        ?? ''
                    )
                );

            $newEnabled =
                !empty($_POST['enabled'])
                ? '1'
                : '0';


            if ($newBase === '') {
                $newBase =
                    'https://api.uniom.ir/bot';
            }

            if ($newAcademy === '') {
                $newAcademy =
                    'آموزشگاه زبان غزل';
            }


            eitaa_set_setting(
                $pdo,
                'eitaa_api_base_url',
                $newBase
            );


            /*
             * اگر API Key خالی باشد
             * کلید قبلی حفظ می‌شود.
             */
            if ($newApiKey !== '') {

                eitaa_set_setting(
                    $pdo,
                    'eitaa_api_key',
                    $newApiKey
                );
            }


            eitaa_set_setting(
                $pdo,
                'eitaa_academy_name',
                $newAcademy
            );


            eitaa_set_setting(
                $pdo,
                'eitaa_enabled',
                $newEnabled
            );


            flash(
                'success',
                'تنظیمات ایتا با موفقیت ذخیره شد.'
            );


        } catch (Throwable $e) {

            error_log(
                '[EITAA] Save settings error: ' .
                $e->getMessage()
            );

            flash(
                'danger',
                'ذخیره تنظیمات انجام نشد. خطای دیتابیس رخ داد.'
            );
        }


        redirect(
            'eitaa-settings.php'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Reload API information
    |--------------------------------------------------------------------------
    */

    $apiKey =
        eitaa_get_setting(
            $pdo,
            'eitaa_api_key',
            ''
        );

    $baseUrl =
        eitaa_get_setting(
            $pdo,
            'eitaa_api_base_url',
            'https://api.uniom.ir/bot'
        );


    /*
    |--------------------------------------------------------------------------
    | Create service
    |--------------------------------------------------------------------------
    */

    try {

        $service =
            new EitaaUniomService(
                $apiKey,
                $baseUrl
            );


        /*
        |--------------------------------------------------------------------------
        | Test connection
        |--------------------------------------------------------------------------
        */

        if ($action === 'test') {

            if ($apiKey === '') {

                flash(
                    'danger',
                    'ابتدا API Key ایتا را وارد و ذخیره کنید.'
                );

                redirect(
                    'eitaa-settings.php'
                );
            }


            $response =
                $service->getMe();


            if (
                !empty($response['ok']) &&
                !empty($response['result']) &&
                is_array($response['result'])
            ) {

                $result =
                    $response['result'];


                $id =
                    isset($result['id'])
                    ? (string)$result['id']
                    : '';


                $firstName =
                    isset($result['first_name'])
                    ? trim(
                        (string)$result['first_name']
                    )
                    : '';


                $lastName =
                    isset($result['last_name'])
                    ? trim(
                        (string)$result['last_name']
                    )
                    : '';


                $name =
                    trim(
                        $firstName .
                        ' ' .
                        $lastName
                    );


                if ($id !== '') {

                    eitaa_set_setting(
                        $pdo,
                        'eitaa_self_id',
                        $id
                    );
                }


                $message =
                    'اتصال ایتا با موفقیت برقرار شد.';


                if ($name !== '') {

                    $message .=
                        ' حساب متصل: ' .
                        $name .
                        '.';
                }


                if ($id !== '') {

                    $message .=
                        ' شناسه حساب: ' .
                        $id .
                        '.';
                }


                flash(
                    'success',
                    $message
                );

            } else {

                flash(
                    'danger',
                    'تست اتصال ناموفق بود: ' .
                    eitaa_error_message(
                        $response
                    )
                );
            }


            redirect(
                'eitaa-settings.php'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Set webhook
        |--------------------------------------------------------------------------
        */

        if ($action === 'set_webhook') {

            if ($apiKey === '') {

                flash(
                    'danger',
                    'ابتدا API Key را وارد و ذخیره کنید.'
                );

                redirect(
                    'eitaa-settings.php'
                );
            }


            $response =
                $service->setWebhook(
                    $webhookUrl
                );


            if (!empty($response['ok'])) {

                eitaa_set_setting(
                    $pdo,
                    'eitaa_webhook_active',
                    '1'
                );


                flash(
                    'success',
                    'وب‌هوک با موفقیت ثبت شد.'
                );

            } else {

                flash(
                    'danger',
                    'ثبت وب‌هوک ناموفق بود: ' .
                    eitaa_error_message(
                        $response
                    )
                );
            }


            redirect(
                'eitaa-settings.php'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Delete webhook
        |--------------------------------------------------------------------------
        */

        if ($action === 'delete_webhook') {

            if ($apiKey === '') {

                flash(
                    'danger',
                    'API Key ثبت نشده است.'
                );

                redirect(
                    'eitaa-settings.php'
                );
            }


            $response =
                $service->deleteWebhook();


            if (!empty($response['ok'])) {

                eitaa_set_setting(
                    $pdo,
                    'eitaa_webhook_active',
                    '0'
                );


                flash(
                    'success',
                    'وب‌هوک با موفقیت حذف شد.'
                );

            } else {

                flash(
                    'danger',
                    'حذف وب‌هوک ناموفق بود: ' .
                    eitaa_error_message(
                        $response
                    )
                );
            }


            redirect(
                'eitaa-settings.php'
            );
        }


    } catch (Throwable $e) {

        error_log(
            '[EITAA] API action error: ' .
            $e->getMessage()
        );


        flash(
            'danger',
            'هنگام ارتباط با سرویس ایتا خطایی رخ داد.'
        );


        redirect(
            'eitaa-settings.php'
        );
    }
}


/*
|--------------------------------------------------------------------------
| Reload data for page
|--------------------------------------------------------------------------
*/

$baseUrl =
    eitaa_get_setting(
        $pdo,
        'eitaa_api_base_url',
        'https://api.uniom.ir/bot'
    );

$apiKey =
    eitaa_get_setting(
        $pdo,
        'eitaa_api_key',
        ''
    );

$academyName =
    eitaa_get_setting(
        $pdo,
        'eitaa_academy_name',
        'آموزشگاه زبان غزل'
    );

$enabled =
    eitaa_get_setting(
        $pdo,
        'eitaa_enabled',
        '0'
    ) === '1';

$selfId =
    eitaa_get_setting(
        $pdo,
        'eitaa_self_id',
        ''
    );

$webhookActive =
    eitaa_get_setting(
        $pdo,
        'eitaa_webhook_active',
        '0'
    ) === '1';


/*
|--------------------------------------------------------------------------
| API Key mask
|--------------------------------------------------------------------------
|
| فقط ASCII است، پس اصلاً به mbstring احتیاج نداریم.
|
*/

if ($apiKey === '') {

    $maskedKey =
        'ثبت نشده';

} elseif (strlen($apiKey) <= 10) {

    $maskedKey =
        '••••••••';

} else {

    $maskedKey =
        substr(
            $apiKey,
            0,
            6
        )
        .
        '••••••'
        .
        substr(
            $apiKey,
            -4
        );
}


/*
|--------------------------------------------------------------------------
| Page
|--------------------------------------------------------------------------
*/

render_header(
    'تنظیمات ایتا',
    'eitaa'
);

?>

<div class="grid eitaa-grid">


    <!-- اتصال حساب -->
    <div class="card glass">

        <div class="card-head">

            <div>

                <h2>
                    اتصال حساب ایتا
                </h2>

                <p>
                    تنظیمات ارتباط سامانه سفارش کتاب با حساب ایتا.
                </p>

            </div>

            <span
                class="badge <?= $enabled ? 'success' : 'muted' ?>"
            >
                <?= $enabled ? 'فعال' : 'غیرفعال' ?>
            </span>

        </div>


        <form
            method="post"
            action="eitaa-settings.php"
        >

            <?= csrf_field() ?>

            <input
                type="hidden"
                name="action"
                value="save"
            >


            <div class="form-grid">


                <div class="field full">

                    <label>
                        نام آموزشگاه در پیام خوش‌آمد
                    </label>

                    <input
                        class="input"
                        type="text"
                        name="academy_name"
                        value="<?= e($academyName) ?>"
                        autocomplete="off"
                    >

                </div>


                <div class="field full">

                    <label>
                        API Key
                    </label>

                    <input
                        class="input mono"
                        type="password"
                        name="api_key"
                        dir="ltr"
                        autocomplete="new-password"
                        placeholder="برای حفظ کلید فعلی، این قسمت را خالی بگذارید"
                    >

                    <div class="help">

                        کلید فعلی:

                        <span class="mono">
                            <?= e($maskedKey) ?>
                        </span>

                    </div>

                </div>


                <div class="field full">

                    <label>
                        Base URL
                    </label>

                    <input
                        class="input mono"
                        type="text"
                        name="base_url"
                        dir="ltr"
                        value="<?= e($baseUrl) ?>"
                        autocomplete="off"
                    >

                </div>


                <div class="field full">

                    <label class="checkrow">

                        <input
                            type="checkbox"
                            name="enabled"
                            value="1"
                            <?= $enabled ? 'checked' : '' ?>
                        >

                        پردازش پیام‌های ورودی ایتا فعال باشد

                    </label>

                </div>


            </div>


            <div
                class="actions"
                style="margin-top:16px"
            >

                <button
                    class="btn primary"
                    type="submit"
                >
                    ذخیره تنظیمات
                </button>

            </div>

        </form>

    </div>



    <!-- Test + Webhook -->
    <div class="card glass">

        <div class="card-head">

            <div>

                <h2>
                    تست و وب‌هوک
                </h2>

                <p>
                    ابتدا API را تست کنید و بعد وب‌هوک را فعال کنید.
                </p>

            </div>

        </div>


        <div class="status-list">


            <div>

                <span>
                    API Key
                </span>

                <strong>
                    <?= $apiKey !== '' ? 'ثبت شده' : 'ثبت نشده' ?>
                </strong>

            </div>


            <div>

                <span>
                    شناسه حساب متصل
                </span>

                <strong
                    class="mono"
                    dir="ltr"
                >
                    <?= e($selfId !== '' ? $selfId : '—') ?>
                </strong>

            </div>


            <div>

                <span>
                    وضعیت وب‌هوک
                </span>

                <strong>
                    <?= $webhookActive
                        ? 'ثبت شده'
                        : 'ثبت نشده / نامشخص'
                    ?>
                </strong>

            </div>


        </div>


        <div
            class="field full"
            style="margin-top:14px"
        >

            <label>
                Webhook URL
            </label>

            <input
                class="input mono"
                type="text"
                dir="ltr"
                readonly
                value="<?= e($webhookUrl) ?>"
            >

            <div class="help">

                این آدرس دارای کلید امنیتی تصادفی است.

            </div>

        </div>



        <div
            class="actions"
            style="margin-top:16px"
        >


            <form
                method="post"
                action="eitaa-settings.php"
            >

                <?= csrf_field() ?>

                <input
                    type="hidden"
                    name="action"
                    value="test"
                >

                <button
                    class="btn"
                    type="submit"
                    <?= $apiKey === '' ? 'disabled' : '' ?>
                >
                    تست اتصال
                </button>

            </form>



            <form
                method="post"
                action="eitaa-settings.php"
            >

                <?= csrf_field() ?>

                <input
                    type="hidden"
                    name="action"
                    value="set_webhook"
                >

                <button
                    class="btn success"
                    type="submit"
                    <?= $apiKey === '' ? 'disabled' : '' ?>
                >
                    ثبت وب‌هوک
                </button>

            </form>



            <form
                method="post"
                action="eitaa-settings.php"
            >

                <?= csrf_field() ?>

                <input
                    type="hidden"
                    name="action"
                    value="delete_webhook"
                >

                <button
                    class="btn danger"
                    type="submit"
                    <?= $apiKey === '' ? 'disabled' : '' ?>
                >
                    حذف وب‌هوک
                </button>

            </form>


        </div>

    </div>

</div>



<!-- Flow -->
<div class="card glass">

    <div class="card-head">

        <div>

            <h2>
                جریان ثبت پیش‌سفارش
            </h2>

            <p>
                روندی که زبان‌آموز در ایتا طی می‌کند.
            </p>

        </div>

    </div>


    <div class="flow-grid">


        <div>
            <b>۱</b>
            <span>شروع</span>
        </div>


        <div>
            <b>۲</b>
            <span>نام</span>
        </div>


        <div>
            <b>۳</b>
            <span>نام خانوادگی</span>
        </div>


        <div>
            <b>۴</b>
            <span>شماره همراه</span>
        </div>


        <div>
            <b>۵</b>
            <span>انتخاب ترم</span>
        </div>


        <div>
            <b>۶</b>
            <span>نمایش کتاب‌ها</span>
        </div>


        <div>
            <b>۷</b>
            <span>تأیید سفارش</span>
        </div>


        <div>
            <b>۸</b>
            <span>کد رهگیری</span>
        </div>


    </div>


    <div
        class="help"
        style="margin-top:14px"
    >
        فرمان‌های عمومی:
        «شروع»،
        «پیگیری»
        و
        «لغو».
    </div>


</div>


<?php

render_footer();