<?php
/**
 * Plugin Name: Ghazal SEO
 * Description: Technical SEO for ghazalify.com only — meta, H1, redirects, local landing, schema, weekly re-apply and health checks.
 * Version: 1.6.0
 *
 * Copy to wp-content/mu-plugins/ghazal-seo.php (must-use, always on).
 */
defined('ABSPATH') || exit;

define('GHAZAL_SEO_VERSION', '1.6.0');
define('GHAZAL_SEO_LAT', 35.599064);
define('GHAZAL_SEO_LNG', 51.436079);

function ghazal_seo_h1_map() {
    return [
        9977 => 'دوره TTC آموزشگاه زبان غزال',
        9729 => 'دوره حضوری زبان در شهرری',
        10103 => 'دوره آنلاین زبان غزال',
        7713 => 'مدرسه زبان غزال (Ghazal School)',
        7 => 'فروشگاه کتاب آموزشگاه زبان غزال',
    ];
}

function ghazal_seo_meta_map() {
    return [
        9977 => [
            'title' => 'دوره TTC آموزشگاه زبان غزال | تربیت مدرس در شهرری',
            'description' => 'تربیت مدرس زبان (TTC) در شهرری با رویکرد عملی؛ مناسب کسی که می‌خواهد تدریس انگلیسی را در آموزشگاه غزال یاد بگیرد.',
            'focus' => 'دوره TTC,تربیت مدرس زبان,آموزشگاه زبان غزال',
        ],
        9729 => [
            'title' => 'کلاس حضوری زبان انگلیسی و آلمانی | آموزشگاه غزال شهرری',
            'description' => 'کلاس حضوری زبان انگلیسی و آلمانی آموزشگاه غزال در شهرری، جنوب تهران؛ تعیین سطح، پشتیبان آموزشی و برنامه متناسب با وقت شما.',
            'focus' => 'کلاس حضوری زبان,آموزشگاه زبان شهرری',
        ],
        10103 => [
            'title' => 'کلاس آنلاین زبان غزال | انگلیسی و آلمانی',
            'description' => 'کلاس آنلاین انگلیسی و آلمانی با پشتیبانی زنده؛ تعیین سطح و برنامه منعطف برای زبان‌آموز جنوب تهران، بدون رفت‌وآمد به مرکز شهر.',
            'focus' => 'کلاس آنلاین زبان,آموزشگاه زبان غزال',
        ],
        7713 => [
            'title' => 'فونیکس کودکان شهرری | مدرسه زبان غزال',
            'description' => 'فونیکس و کلاس زبان کودک در شهرری؛ مدرسه زبان غزال جدا از کلاس بزرگسال، در جنوب تهران.',
            'focus' => 'فونیکس کودکان شهرری,آموزش زبان کودکان شهرری',
        ],
        7 => [
            'title' => 'خرید کتاب زبان | فروشگاه آموزشگاه غزال',
            'description' => 'خرید کتاب و پکیج آموزشی دوره‌های انگلیسی و آلمانی آموزشگاه زبان غزال در شهرری؛ قیمت به‌روز و تهیه از خود آموزشگاه.',
            'focus' => 'خرید کتاب زبان,فروشگاه آموزشگاه غزال',
        ],
        6143 => [
            'title' => 'تعیین سطح زبان | آموزشگاه غزال شهرری',
            'description' => 'در تعیین سطح شک نکنید. سطح فعلی‌تان را در آموزشگاه زبان غزال شهرری مشخص کنید تا از نقطه درست شروع کنید؛ حضوری و آنلاین.',
            'focus' => 'تعیین سطح زبان,آموزشگاه زبان غزال',
        ],
        6812 => [
            'title' => 'درباره آموزشگاه زبان غزال | شهرری',
            'description' => 'از سال ۱۳۹۵ در شهرری؛ داستان مجوز، مدرسان و مسیر آموزش انگلیسی و آلمانی غزال در جنوب تهران.',
            'focus' => 'آموزشگاه زبان غزال,شهرری',
        ],
        5783 => [
            'title' => 'آموزشگاه زبان غزال | آموزش زبان در جنوب تهران',
            'description' => 'کلاس انگلیسی و آلمانی، TTC و فونیکس کودکان در آموزشگاه زبان غزال شهرری؛ حضوری و آنلاین با تعیین سطح.',
            'focus' => 'آموزشگاه زبان غزال,آموزشگاه زبان جنوب تهران,آموزشگاه زبان شهرری',
        ],
        8356 => [
            'title' => 'دوره‌های زبان انگلیسی و آلمانی | آموزشگاه غزال',
            'description' => 'دوره‌های آموزشی زبان انگلیسی و آلمانی آموزشگاه غزال به‌صورت آنلاین و حضوری در شهرری برگزار می‌شود؛ از تعیین سطح تا TTC.',
            'focus' => 'دوره زبان انگلیسی,دوره زبان آلمانی,آموزشگاه غزال',
        ],
        5317 => [
            'title' => 'تماس با آموزشگاه زبان غزال | شهرری',
            'description' => 'تلفن ۰۲۱۵۵۹۱۲۲۰۶، ایتا ghazalteam@ — شهرری، خیابان شیرمحمدی پلاک ۱۴. شنبه تا پنجشنبه ۹ تا ۲۱ برای تعیین سطح و ثبت‌نام.',
            'focus' => 'تماس آموزشگاه زبان غزال,آموزشگاه زبان شهرری',
        ],
        7196 => [
            'title' => 'تقویم آموزشی آموزشگاه زبان غزال | شهرری',
            'description' => 'تاریخ شروع و پایان ترم‌های حضوری و آنلاین غزال در شهرری؛ برنامه کلاس انگلیسی، آلمانی و TTC را در تقویم آموزشی ببینید.',
            'focus' => 'تقویم آموزشی,آموزشگاه زبان غزال',
        ],
    ];
}

function ghazal_seo_redirect_map() {
    return [
        '/login' => '/?login=true',
        '/login/' => '/?login=true',
        '/leaning-german-why-ghazal-academy-articles' => '/learning-german-why-ghazal-academy-articles/',
        '/leaning-german-why-ghazal-academy-articles/' => '/learning-german-why-ghazal-academy-articles/',
        '/leaning-korean-why-ghazal-academy-articles' => '/learning-korean-why-ghazal-academy-articles/',
        '/leaning-korean-why-ghazal-academy-articles/' => '/learning-korean-why-ghazal-academy-articles/',
        '/mindset-ready-for-fce-reday-for-cae' => '/mindset-ready-for-fce-ready-for-cae/',
        '/mindset-ready-for-fce-reday-for-cae/' => '/mindset-ready-for-fce-ready-for-cae/',
    ];
}

function ghazal_seo_slug_fixes() {
    return [
        8432 => 'learning-german-why-ghazal-academy-articles',
        8448 => 'learning-korean-why-ghazal-academy-articles',
        6522 => 'mindset-ready-for-fce-ready-for-cae',
        6692 => 'پکیج-آموزشی-اختصاصی-غزال',
    ];
}

function ghazal_seo_fix_typos($text) {
    if (!is_string($text) || $text === '') {
        return $text;
    }
    $map = [
        'محصوب' => 'محسوب',
        'پکیچ' => 'پکیج',
        'پکیح' => 'پکیج',
        'کتاب هاب' => 'کتاب‌های',
        'راخت بهشون' => 'راحت بهشون',
        'امتیاز محصوب' => 'امتیاز محسوب',
    ];
    return str_replace(array_keys($map), array_values($map), $text);
}

function ghazal_seo_set_rank_math($post_id, $title, $description, $focus = '') {
    $post_id = (int) $post_id;
    if ($post_id < 1) {
        return;
    }
    if ($title !== '') {
        update_post_meta($post_id, 'rank_math_title', $title);
    }
    if ($description !== '') {
        update_post_meta($post_id, 'rank_math_description', $description);
    }
    if ($focus !== '') {
        update_post_meta($post_id, 'rank_math_focus_keyword', $focus);
    }
    update_post_meta($post_id, 'rank_math_robots', ['index', 'follow']);
}

function ghazal_seo_landing_html() {
    $level = home_url('/تعیین-سطح/');
    $courses = home_url('/english-course/');
    $ttc = home_url('/course-ttc/');
    $inperson = home_url('/in-person-class/');
    $online = home_url('/دوره-آنلاین/');
    $school = home_url('/ghazal-school/');
    $contact = home_url('/contact-us/');
    $map = 'https://maps.google.com/?q=' . GHAZAL_SEO_LAT . ',' . GHAZAL_SEO_LNG;
    return '<h1>آموزشگاه زبان شهرری | غزال جنوب تهران</h1>
<p>اگر دنبال آموزشگاه زبان در شهرری هستید، غزال همین‌جاست: جنوب تهران، خیابان ۲۴ متری شیرمحمدی پلاک ۱۴. کلاس انگلیسی و آلمانی حضوری، کلاس آنلاین، فونیکس کودکان و دوره TTC تربیت مدرس با مجوز آموزش و پرورش از سال ۱۳۹۵ برگزار می‌شود.</p>
<h2>آدرس آموزشگاه زبان غزال در شهرری</h2>
<p>شهرری، خیابان ۲۴ متری، خیابان پیلغوش، خیابان شیرمحمدی (آرد ایران)، پلاک ۱۴</p>
<p>تلفن: <a href="tel:+982155912206">021-55912206</a> — ایتا: ghazalteam@</p>
<p>ساعات کار: شنبه تا پنجشنبه ۹ تا ۲۱</p>
<p><a href="' . esc_url($map) . '">موقعیت روی نقشه گوگل</a></p>
<h2>کلاس زبان شهرری برای چه محله‌هایی مناسب است؟</h2>
<p>زبان‌آموز از دولت‌آباد، باقرشهر، کهریزک، جوانمرد قصاب، صفائیه، فیروزآبادی و بقیه جنوب تهران می‌آید. مسیر کوتاه‌تر از رفتن به مرکز تهران است و برنامه کلاس را با وقت خانواده هماهنگ می‌کنیم.</p>
<h2>کلاس انگلیسی و آلمانی در شهرری</h2>
<p><a href="' . esc_url($inperson) . '">کلاس حضوری</a> در خود آموزشگاه برگزار می‌شود. اگر رفت‌وآمد سخت است، <a href="' . esc_url($online) . '">کلاس آنلاین زبان</a> با پشتیبانی زنده همان برنامه را دارد. جزئیات سطح‌ها در صفحه <a href="' . esc_url($courses) . '">دوره‌های انگلیسی و آلمانی</a> است. برای تمرکز جدا: <a href="' . esc_url(home_url('/kelas-english-shahrrey/')) . '">کلاس انگلیسی شهرری</a> و <a href="' . esc_url(home_url('/kelas-almani-shahrrey/')) . '">کلاس آلمانی شهرری</a>.</p>
<h2>فونیکس کودکان و مدرسه زبان</h2>
<p>برای خردسال و نوجوان، <a href="' . esc_url($school) . '">مدرسه زبان غزال</a> جدا از کلاس بزرگسال است: فونیکس، کلاس تعاملی، و مسیر سنی مشخص.</p>
<h2>دوره TTC تربیت مدرس در شهرری</h2>
<p>اگر می‌خواهید تدریس زبان را یاد بگیرید، <a href="' . esc_url($ttc) . '">دوره TTC آموزشگاه غزال</a> در همین شعبه شهرری برگزار می‌شود؛ رویکرد عملی است نه فقط جزوه.</p>
<h2>چرا این آموزشگاه زبان جنوب تهران</h2>
<p>کلاس‌ها ارزیابی می‌شوند، پشتیبان آموزشی دارد، و قبل از ثبت‌نام <a href="' . esc_url($level) . '">تعیین سطح</a> می‌کنید تا از نقطه درست شروع کنید. بهترین آموزشگاه زبان شهرری برای شما جایی است که مسیر و تایم با زندگی‌تان جور باشد؛ اینجا همان را می‌سازیم.</p>
<h2>سؤالات رایج</h2>
<h3>آموزشگاه زبان شهرری کجاست؟</h3>
<p>غزال در شهرری، خیابان ۲۴ متری شیرمحمدی پلاک ۱۴ است. تلفن ثابت ۰۲۱۵۵۹۱۲۲۰۶.</p>
<h3>از تعیین سطح باید شروع کنم؟</h3>
<p>بله. بدون تعیین سطح کلاس مناسب مشخص نمی‌شود. حضوری در آموزشگاه یا آنلاین ثبت می‌شود.</p>
<h3>کلاس فقط انگلیسی است؟</h3>
<p>انگلیسی و آلمانی، حضوری و آنلاین. کودکان فونیکس جدا دارند.</p>
<p><a href="' . esc_url($level) . '">ثبت‌نام تعیین سطح</a> · <a href="' . esc_url($contact) . '">تماس با آموزشگاه زبان شهرری</a></p>';
}

function ghazal_seo_local_box_html() {
    $local = home_url('/amoozeshgah-zabane-shahrrey/');
    $level = home_url('/تعیین-سطح/');
    return '<aside class="ghazal-seo-local"><p><strong>آموزشگاه زبان شهرری:</strong> غزال در جنوب تهران، شیرمحمدی پلاک ۱۴، کلاس انگلیسی و آلمانی حضوری و آنلاین برگزار می‌کند. <a href="' . esc_url($local) . '">صفحه آموزشگاه زبان شهرری</a> · <a href="' . esc_url($level) . '">تعیین سطح</a></p></aside>';
}

function ghazal_seo_faq_items() {
    return [
        [
            'q' => 'آموزشگاه زبان شهرری کجاست؟',
            'a' => 'آموزشگاه زبان غزال در شهرری، خیابان ۲۴ متری، خیابان شیرمحمدی پلاک ۱۴، جنوب تهران است. تلفن ۰۲۱۵۵۹۱۲۲۰۶.',
        ],
        [
            'q' => 'آیا باید تعیین سطح بدهم؟',
            'a' => 'بله. تعیین سطح حضوری یا آنلاین مشخص می‌کند از کدام کلاس انگلیسی یا آلمانی شروع کنید.',
        ],
        [
            'q' => 'در شهرری کلاس TTC هم دارید؟',
            'a' => 'دوره تربیت مدرس زبان (TTC) در همین آموزشگاه زبان شهرری برگزار می‌شود.',
        ],
        [
            'q' => 'برای کودکان هم کلاس زبان در شهرری هست؟',
            'a' => 'بله. فونیکس و مدرسه زبان غزال برای کودکان و نوجوانان جدا از کلاس بزرگسال است.',
        ],
    ];
}

function ghazal_seo_school_html() {
    $level = home_url('/تعیین-سطح/');
    $local = home_url('/amoozeshgah-zabane-shahrrey/');
    $english = home_url('/kelas-english-shahrrey/');
    return '<h1>فونیکس کودکان شهرری | مدرسه زبان غزال</h1>
<p>مدرسه زبان غزال (Ghazal School) کلاس زبان کودک و نوجوان در شهرری است، جدا از کلاس بزرگسال. فونیکس، بازی و مسیر سنی مشخص تا انگلیسی بخشی از زندگی‌شان شود نه فقط تکلیف.</p>
<h2>فونیکس در آموزشگاه زبان شهرری</h2>
<p>فونیکس صدا و حرف را با هم یاد می‌دهد تا خردسال بتواند بخواند، نه اینکه کلمه را حفظ کند. کلاس در همان شعبه جنوب تهران، شیرمحمدی پلاک ۱۴، برگزار می‌شود.</p>
<h2>برای چه سنی است؟</h2>
<p>خردسال و نوجوان مسیر جدا دارند. نوجوان بعداً می‌تواند به <a href="' . esc_url($english) . '">کلاس انگلیسی شهرری</a> بزرگ‌تر وصل شود. شروع با <a href="' . esc_url($level) . '">تعیین سطح</a> است.</p>
<p><a href="' . esc_url($level) . '">ثبت‌نام تعیین سطح کودک</a> · <a href="' . esc_url($local) . '">آموزشگاه زبان شهرری</a></p>';
}

function ghazal_seo_english_faq_items() {
    return [
        [
            'q' => 'کلاس انگلیسی شهرری کجاست؟',
            'a' => 'آموزشگاه زبان غزال در شهرری، خیابان ۲۴ متری شیرمحمدی پلاک ۱۴، جنوب تهران. تلفن ۰۲۱۵۵۹۱۲۲۰۶.',
        ],
        [
            'q' => 'قبل از کلاس انگلیسی باید تعیین سطح بدهم؟',
            'a' => 'بله. تعیین سطح حضوری یا آنلاین مشخص می‌کند از کدام سطح انگلیسی شروع کنید تا کلاس برایتان سخت یا خیلی ساده نباشد.',
        ],
        [
            'q' => 'کلاس انگلیسی شهرری حضوری است یا آنلاین؟',
            'a' => 'هر دو. حضوری در خود آموزشگاه شهرری، یا آنلاین با پشتیبانی زنده اگر رفت‌وآمد جور نیست.',
        ],
        [
            'q' => 'برای کودک هم کلاس انگلیسی در شهرری دارید؟',
            'a' => 'خردسال فونیکس را در مدرسه زبان غزال می‌خواند؛ این صفحه کلاس انگلیسی نوجوان و بزرگسال است.',
        ],
    ];
}

function ghazal_seo_german_faq_items() {
    return [
        [
            'q' => 'کلاس آلمانی شهرری کجاست؟',
            'a' => 'غزال در شهرری، شیرمحمدی پلاک ۱۴. برای کلاس آلمانی لازم نیست به مرکز تهران بروید. تلفن ۰۲۱۵۵۹۱۲۲۰۶.',
        ],
        [
            'q' => 'آلمانی را از صفر می‌شود شروع کرد؟',
            'a' => 'بله. تعیین سطح مشخص می‌کند مبتدی هستید یا ادامه می‌دهید؛ بعد سطح و ساعت کلاس آلمانی تعیین می‌شود.',
        ],
        [
            'q' => 'کلاس آلمانی حضوری است یا آنلاین؟',
            'a' => 'هر دو حالت در آموزشگاه زبان غزال شهرری برگزار می‌شود.',
        ],
    ];
}

function ghazal_seo_english_html() {
    $level = home_url('/تعیین-سطح/');
    $local = home_url('/amoozeshgah-zabane-shahrrey/');
    $inperson = home_url('/in-person-class/');
    $online = home_url('/دوره-آنلاین/');
    $german = home_url('/kelas-almani-shahrrey/');
    $school = home_url('/ghazal-school/');
    $faq = '';
    foreach (ghazal_seo_english_faq_items() as $row) {
        $faq .= '<h3>' . esc_html($row['q']) . '</h3><p>' . esc_html($row['a']) . '</p>';
    }
    return '<h1>کلاس انگلیسی شهرری | آموزشگاه زبان غزال</h1>
<p>کلاس انگلیسی در شهرری، جنوب تهران، در آموزشگاه غزال برگزار می‌شود: حضوری در شیرمحمدی پلاک ۱۴ یا آنلاین با پشتیبانی زنده. قبل از ثبت‌نام تعیین سطح می‌کنید تا از نقطه درست شروع کنید.</p>
<h2>کلاس انگلیسی حضوری در شهرری</h2>
<p>اگر از دولت‌آباد، باقرشهر، کهریزک، جوانمرد قصاب یا صفائیه می‌آیید، <a href="' . esc_url($inperson) . '">کلاس حضوری</a> رفت‌وآمد به مرکز تهران را کم می‌کند. ساعت‌ها شنبه تا پنجشنبه ۹ تا ۲۱ با برنامه خانواده هماهنگ می‌شود.</p>
<h2>کلاس انگلیسی آنلاین</h2>
<p>همان مسیر آموزشی را <a href="' . esc_url($online) . '">آنلاین</a> هم دارید؛ مناسب کسی که تایم کلاس حضوری جور نیست.</p>
<h2>کودک یا بزرگسال؟</h2>
<p>بزرگسال و نوجوان همین صفحه است. خردسال فونیکس را در <a href="' . esc_url($school) . '">مدرسه زبان غزال</a> می‌خواند. آلمانی جداست: <a href="' . esc_url($german) . '">کلاس آلمانی شهرری</a>.</p>
<h2>سؤالات رایج کلاس انگلیسی شهرری</h2>
' . $faq . '
<p><a href="' . esc_url($level) . '">تعیین سطح انگلیسی</a> · <a href="' . esc_url($local) . '">آموزشگاه زبان شهرری</a></p>';
}

function ghazal_seo_german_html() {
    $level = home_url('/تعیین-سطح/');
    $local = home_url('/amoozeshgah-zabane-shahrrey/');
    $inperson = home_url('/in-person-class/');
    $online = home_url('/دوره-آنلاین/');
    $english = home_url('/kelas-english-shahrrey/');
    $faq = '';
    foreach (ghazal_seo_german_faq_items() as $row) {
        $faq .= '<h3>' . esc_html($row['q']) . '</h3><p>' . esc_html($row['a']) . '</p>';
    }
    return '<h1>کلاس آلمانی شهرری | آموزشگاه زبان غزال</h1>
<p>کلاس آلمانی در شهرری در آموزشگاه غزال برگزار می‌شود؛ حضوری در جنوب تهران یا آنلاین. مسیر از تعیین سطح شروع می‌شود تا سطح و تایم کلاس مشخص باشد.</p>
<h2>چرا آلمانی در شهرری</h2>
<p>برای مهاجرت تحصیلی، کار یا ادامه بعد از انگلیسی. لازم نیست برای کلاس آلمانی تا مرکز تهران بروید. آدرس: شهرری، خیابان شیرمحمدی پلاک ۱۴. تلفن ۰۲۱۵۵۹۱۲۲۰۶.</p>
<h2>حضوری یا آنلاین</h2>
<p><a href="' . esc_url($inperson) . '">حضوری</a> در آموزشگاه، یا <a href="' . esc_url($online) . '">آنلاین</a> با پشتیبانی. انگلیسی جدا در <a href="' . esc_url($english) . '">کلاس انگلیسی شهرری</a> است.</p>
<h2>سؤالات رایج کلاس آلمانی شهرری</h2>
' . $faq . '
<p><a href="' . esc_url($level) . '">تعیین سطح آلمانی</a> · <a href="' . esc_url($local) . '">آموزشگاه زبان شهرری</a></p>';
}

function ghazal_seo_ensure_page($slug, $title, $html, $meta_title, $meta_desc, $focus) {
    $existing = get_page_by_path($slug);
    if ($existing instanceof WP_Post) {
        wp_update_post([
            'ID' => (int) $existing->ID,
            'post_title' => $title,
            'post_content' => $html,
            'post_status' => 'publish',
            'post_name' => $slug,
        ]);
        ghazal_seo_set_rank_math((int) $existing->ID, $meta_title, $meta_desc, $focus);
        return (int) $existing->ID;
    }
    $id = wp_insert_post([
        'post_title' => $title,
        'post_name' => $slug,
        'post_status' => 'publish',
        'post_type' => 'page',
        'post_content' => $html,
        'post_author' => 1,
    ], true);
    if (is_wp_error($id)) {
        return 0;
    }
    ghazal_seo_set_rank_math((int) $id, $meta_title, $meta_desc, $focus);
    return (int) $id;
}

function ghazal_seo_ensure_landing_page() {
    return ghazal_seo_ensure_page(
        'amoozeshgah-zabane-shahrrey',
        'آموزشگاه زبان شهرری',
        ghazal_seo_landing_html(),
        'آموزشگاه زبان شهرری | غزال جنوب تهران',
        'آموزشگاه زبان غزال در شهرری، خیابان ۲۴ متری شیرمحمدی پلاک ۱۴؛ کلاس انگلیسی و آلمانی حضوری و آنلاین، تعیین سطح و دوره TTC در جنوب تهران.',
        'آموزشگاه زبان شهرری,آموزشگاه زبان جنوب تهران,کلاس زبان شهرری'
    );
}

function ghazal_seo_ensure_spoke_pages() {
    $ids = [];
    $ids['english'] = ghazal_seo_ensure_page(
        'kelas-english-shahrrey',
        'کلاس انگلیسی شهرری',
        ghazal_seo_english_html(),
        'کلاس انگلیسی شهرری | آموزشگاه زبان غزال',
        'کلاس انگلیسی حضوری و آنلاین در شهرری، جنوب تهران؛ تعیین سطح، برنامه منعطف و آموزشگاه غزال در شیرمحمدی پلاک ۱۴.',
        'کلاس انگلیسی شهرری,آموزشگاه زبان شهرری'
    );
    $ids['german'] = ghazal_seo_ensure_page(
        'kelas-almani-shahrrey',
        'کلاس آلمانی شهرری',
        ghazal_seo_german_html(),
        'کلاس آلمانی شهرری | آموزشگاه زبان غزال',
        'کلاس آلمانی در شهرری، حضوری یا آنلاین؛ تعیین سطح و برنامه در آموزشگاه زبان غزال جنوب تهران.',
        'کلاس آلمانی شهرری,آموزشگاه زبان شهرری'
    );
    return $ids;
}

function ghazal_seo_enable_product_sitemap() {
    $opt = get_option('rank-math-options-sitemap');
    if (!is_array($opt)) {
        $opt = [];
    }
    $opt['pt_product_sitemap'] = 'on';
    $opt['pt_product_include_images'] = 'on';
    update_option('rank-math-options-sitemap', $opt);
}

function ghazal_seo_draft_dummy_teachers() {
    $q = new WP_Query([
        'post_type' => 'any',
        'post_status' => ['publish', 'draft'],
        'posts_per_page' => 20,
        's' => 'شکرینو',
        'fields' => 'ids',
        'suppress_filters' => true,
    ]);
    foreach ($q->posts as $id) {
        $title = get_the_title($id);
        if (is_string($title) && strpos($title, 'شکرینو') !== false) {
            wp_update_post(['ID' => (int) $id, 'post_status' => 'draft']);
        }
    }
}

function ghazal_seo_apply_persisted($force = false) {
    $flag = get_option('ghazal_seo_applied_version');
    if (!$force && $flag === GHAZAL_SEO_VERSION) {
        return ['skipped' => true, 'version' => GHAZAL_SEO_VERSION];
    }

    $done = ['slugs' => [], 'meta' => [], 'landing' => 0, 'spokes' => []];

    foreach (ghazal_seo_slug_fixes() as $id => $slug) {
        $post = get_post((int) $id);
        if (!$post) {
            continue;
        }
        if ($post->post_name !== $slug) {
            wp_update_post(['ID' => (int) $id, 'post_name' => $slug]);
            $done['slugs'][] = (int) $id;
        }
    }

    foreach (ghazal_seo_meta_map() as $id => $row) {
        if (!get_post($id)) {
            continue;
        }
        ghazal_seo_set_rank_math($id, $row['title'], $row['description'], $row['focus']);
        $done['meta'][] = (int) $id;
    }

    $school = get_post(7713);
    if ($school instanceof WP_Post) {
        wp_update_post([
            'ID' => 7713,
            'post_title' => 'فونیکس کودکان شهرری',
            'post_content' => ghazal_seo_school_html(),
        ]);
        ghazal_seo_set_rank_math(
            7713,
            'فونیکس کودکان شهرری | مدرسه زبان غزال',
            'فونیکس و کلاس زبان کودک در شهرری؛ مدرسه زبان غزال جدا از کلاس بزرگسال، در جنوب تهران.',
            'فونیکس کودکان شهرری,آموزش زبان کودکان شهرری'
        );
    }

    $done['landing'] = ghazal_seo_ensure_landing_page();
    $done['spokes'] = ghazal_seo_ensure_spoke_pages();
    ghazal_seo_enable_product_sitemap();
    ghazal_seo_draft_dummy_teachers();

    update_option('ghazal_seo_applied_version', GHAZAL_SEO_VERSION);
    if (function_exists('flush_rewrite_rules')) {
        flush_rewrite_rules(false);
    }
    if (has_action('litespeed_purge_all')) {
        do_action('litespeed_purge_all');
    }

    return $done;
}

function ghazal_seo_watch_targets() {
    return [
        [
            'id' => 'home',
            'url' => home_url('/'),
            'expect_status' => 200,
            'title_contains' => 'غزال',
            'body_contains' => '35.599064',
            'forbid' => ['محصوب', 'پکیچ'],
        ],
        [
            'id' => 'english-rey',
            'url' => home_url('/kelas-english-shahrrey/'),
            'expect_status' => 200,
            'title_contains' => 'انگلیسی',
            'html_contains' => '<h1',
        ],
        [
            'id' => 'german-rey',
            'url' => home_url('/kelas-almani-shahrrey/'),
            'expect_status' => 200,
            'title_contains' => 'آلمانی',
            'html_contains' => '<h1',
        ],
        [
            'id' => 'landing-rey',
            'url' => home_url('/amoozeshgah-zabane-shahrrey/'),
            'expect_status' => 200,
            'title_contains' => 'شهرری',
            'html_contains' => '<h1',
        ],
        [
            'id' => 'phonics-rey',
            'url' => home_url('/ghazal-school/'),
            'expect_status' => 200,
            'title_contains' => 'فونیکس',
            'html_contains' => '<h1',
        ],
        [
            'id' => 'login',
            'url' => home_url('/login/'),
            'expect_status' => 200,
            'final_contains' => 'login=true',
        ],
        [
            'id' => 'ttc',
            'url' => home_url('/course-ttc/'),
            'expect_status' => 200,
            'title_contains' => 'TTC',
            'html_contains' => 'ghazal-seo',
        ],
        [
            'id' => 'online',
            'url' => home_url('/دوره-آنلاین/'),
            'expect_status' => 200,
            'title_contains' => 'آنلاین',
            'forbid_exact_desc' => '14',
        ],
        [
            'id' => 'contact',
            'url' => home_url('/contact-us/'),
            'expect_status' => 200,
            'title_contains' => 'تماس',
            'forbid_exact_desc' => '14',
        ],
        [
            'id' => 'calendar',
            'url' => home_url('/تقویم-آموزشی/'),
            'expect_status' => 200,
            'title_contains' => 'تقویم',
        ],
        [
            'id' => 'robots',
            'url' => home_url('/robots.txt'),
            'expect_status' => 200,
            'body_contains' => 'sitemap_index.xml',
        ],
        [
            'id' => 'sitemap',
            'url' => home_url('/sitemap_index.xml'),
            'expect_status' => 200,
            'body_contains' => '<sitemapindex',
        ],
    ];
}

function ghazal_seo_watch_title($html) {
    if (!preg_match('#<title[^>]*>(.*?)</title>#is', $html, $m)) {
        return '';
    }
    return trim(html_entity_decode(preg_replace('/\s+/', ' ', $m[1]), ENT_QUOTES, 'UTF-8'));
}

function ghazal_seo_watch_meta_desc($html) {
    if (preg_match('#<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']#is', $html, $m)) {
        return trim(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8'));
    }
    return '';
}

function ghazal_seo_watch_fetch($url) {
    $response = wp_remote_get($url, [
        'timeout' => 20,
        'redirection' => 5,
        'sslverify' => true,
        'user-agent' => 'GhazalSeoWatch/' . GHAZAL_SEO_VERSION,
        'headers' => ['X-Ghazal-Seo-Watch' => '1'],
    ]);
    if (is_wp_error($response)) {
        return [
            'ok' => false,
            'status' => 0,
            'final' => '',
            'body' => '',
            'error' => $response->get_error_message(),
        ];
    }
    $final = $url;
    if (isset($response['http_response']) && is_object($response['http_response'])
        && method_exists($response['http_response'], 'get_response_object')) {
        $ro = $response['http_response']->get_response_object();
        if (is_object($ro) && !empty($ro->url)) {
            $final = (string) $ro->url;
        }
    }
    $code = (int) wp_remote_retrieve_response_code($response);
    $location = (string) wp_remote_retrieve_header($response, 'location');
    if ($code >= 300 && $code < 400 && $location !== '') {
        $final = $location;
    }
    return [
        'ok' => true,
        'status' => $code,
        'final' => $final,
        'body' => (string) wp_remote_retrieve_body($response),
        'error' => '',
    ];
}

function ghazal_seo_watch_run() {
    $fails = [];
    $oks = [];
    foreach (ghazal_seo_watch_targets() as $t) {
        $got = ghazal_seo_watch_fetch($t['url']);
        $id = $t['id'];
        if (!$got['ok'] || $got['status'] < 1) {
            $fails[] = $id . ': fetch ' . ($got['error'] !== '' ? $got['error'] : 'empty');
            continue;
        }
        if ((int) $got['status'] !== (int) $t['expect_status']) {
            $fails[] = $id . ': HTTP ' . $got['status'] . ' expected ' . $t['expect_status'];
            continue;
        }
        $html = $got['body'];
        $title = ghazal_seo_watch_title($html);
        if (!empty($t['title_contains']) && mb_stripos($title, $t['title_contains']) === false) {
            $fails[] = $id . ': title missing «' . $t['title_contains'] . '»';
            continue;
        }
        if (!empty($t['html_contains']) && strpos($html, $t['html_contains']) === false) {
            $fails[] = $id . ': html missing ' . $t['html_contains'];
            continue;
        }
        if (!empty($t['body_contains']) && strpos($html, $t['body_contains']) === false) {
            $fails[] = $id . ': body missing ' . $t['body_contains'];
            continue;
        }
        if (!empty($t['final_contains']) && strpos($got['final'], $t['final_contains']) === false
            && strpos($html, $t['final_contains']) === false) {
            $fails[] = $id . ': redirect missed ' . $t['final_contains'];
            continue;
        }
        if (!empty($t['forbid']) && is_array($t['forbid'])) {
            foreach ($t['forbid'] as $bad) {
                if ($bad !== '' && strpos($html, $bad) !== false) {
                    $fails[] = $id . ': still has «' . $bad . '»';
                    continue 2;
                }
            }
        }
        if (!empty($t['forbid_exact_desc']) && ghazal_seo_watch_meta_desc($html) === $t['forbid_exact_desc']) {
            $fails[] = $id . ': meta description still «' . $t['forbid_exact_desc'] . '»';
            continue;
        }
        $oks[] = $id;
    }
    return [
        'ok' => $fails === [],
        'checked' => count($oks) + count($fails),
        'passed' => $oks,
        'failed' => $fails,
        'at' => gmdate('c'),
    ];
}

function ghazal_seo_mm_config() {
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }
    $paths = [
        WP_CONTENT_DIR . '/ghazal-seo-secret.php',
        dirname(__FILE__) . '/ghazal-seo-secret.php',
    ];
    foreach ($paths as $path) {
        if (is_file($path)) {
            $loaded = include $path;
            if (is_array($loaded)) {
                $cfg = $loaded;
                return $cfg;
            }
        }
    }
    $cfg = [];
    return $cfg;
}

function ghazal_seo_mm_request($method, $path, $payload = null, $token = '') {
    $cfg = ghazal_seo_mm_config();
    $base = rtrim((string) ($cfg['url'] ?? 'https://co.ghazalify.com'), '/');
    $args = [
        'method' => $method,
        'timeout' => 20,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ],
    ];
    if ($token !== '') {
        $args['headers']['Authorization'] = 'Bearer ' . $token;
    }
    if ($payload !== null) {
        $args['body'] = wp_json_encode($payload);
    }
    $response = wp_remote_request($base . $path, $args);
    if (is_wp_error($response)) {
        return ['ok' => false, 'error' => $response->get_error_message(), 'status' => 0, 'json' => [], 'token' => ''];
    }
    $raw = (string) wp_remote_retrieve_body($response);
    $json = json_decode($raw, true);
    $hdr_token = (string) wp_remote_retrieve_header($response, 'token');
    return [
        'ok' => wp_remote_retrieve_response_code($response) >= 200 && wp_remote_retrieve_response_code($response) < 300,
        'status' => (int) wp_remote_retrieve_response_code($response),
        'json' => is_array($json) ? $json : [],
        'token' => $hdr_token,
        'error' => '',
    ];
}

function ghazal_seo_mm_login() {
    $cfg = ghazal_seo_mm_config();
    $user = (string) ($cfg['username'] ?? '');
    $pass = (string) ($cfg['password'] ?? '');
    $existing = (string) ($cfg['token'] ?? '');
    if ($existing !== '') {
        return $existing;
    }
    if ($user === '' || $pass === '') {
        return '';
    }
    $got = ghazal_seo_mm_request('POST', '/api/v4/users/login', [
        'login_id' => $user,
        'password' => $pass,
    ]);
    return (string) ($got['token'] ?? '');
}

function ghazal_seo_watch_message($report, $kind = 'هفتگی') {
    $ok = !empty($report['ok']);
    $status = $ok ? 'سبز' : 'قرمز';
    $lines = [
        '**گزارش سئو ghazalify.com** (' . $kind . ')',
        'وضعیت: **' . $status . '** — ' . (int) ($report['checked'] ?? 0) . ' چک',
    ];
    $labels = [
        'home' => 'خانه',
        'landing-rey' => 'لندینگ شهرری',
        'english-rey' => 'کلاس انگلیسی شهرری',
        'german-rey' => 'کلاس آلمانی شهرری',
        'phonics-rey' => 'فونیکس کودکان',
        'login' => 'لاگین',
        'ttc' => 'TTC',
        'online' => 'دوره آنلاین',
        'contact' => 'تماس',
        'calendar' => 'تقویم',
        'robots' => 'robots.txt',
        'sitemap' => 'sitemap',
    ];
    foreach ((array) ($report['passed'] ?? []) as $id) {
        $lines[] = '• ' . ($labels[$id] ?? $id) . ' ✓';
    }
    foreach ((array) ($report['failed'] ?? []) as $fail) {
        $lines[] = '• ' . $fail;
    }
    if (!empty($report['at'])) {
        $lines[] = 'زمان: `' . $report['at'] . '`';
    }
    return implode("\n", $lines);
}

function ghazal_seo_mm_post($message) {
    $cfg = ghazal_seo_mm_config();
    $channel = (string) ($cfg['channel_id'] ?? '');
    if ($message === '' || $channel === '') {
        return false;
    }
    $token = ghazal_seo_mm_login();
    if ($token === '') {
        return false;
    }
    $got = ghazal_seo_mm_request('POST', '/api/v4/posts', [
        'channel_id' => $channel,
        'message' => $message,
    ], $token);
    return !empty($got['ok']);
}

add_filter('cron_schedules', static function ($schedules) {
    if (!isset($schedules['weekly'])) {
        $schedules['weekly'] = [
            'interval' => 7 * DAY_IN_SECONDS,
            'display' => 'Once Weekly',
        ];
    }
    return $schedules;
});

add_action('init', static function () {
    $force = isset($_GET['ghazal_seo_apply']);
    ghazal_seo_apply_persisted($force);
    if ($force && !headers_sent()) {
        header('X-Ghazal-Seo-Applied: ' . GHAZAL_SEO_VERSION);
    }
    if (!wp_next_scheduled('ghazal_seo_weekly_apply')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'weekly', 'ghazal_seo_weekly_apply');
    }
}, 20);

add_action('ghazal_seo_weekly_apply', static function () {
    ghazal_seo_apply_persisted(true);
    $report = ghazal_seo_watch_run();
    update_option('ghazal_seo_watch_last', $report, false);
    ghazal_seo_mm_post(ghazal_seo_watch_message($report, 'هفتگی'));
    if (!$report['ok']) {
        error_log('Ghazal SEO-WATCH FAIL: ' . implode(' | ', $report['failed']));
    }
});

add_action('template_redirect', static function () {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    $path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    $path = $path === '' ? '/' : $path;
    $map = ghazal_seo_redirect_map();
    if (isset($map[$path])) {
        wp_safe_redirect(home_url($map[$path]), 301);
        exit;
    }
    $decoded = urldecode($path);
    if (isset($map[$decoded])) {
        wp_safe_redirect(home_url($map[$decoded]), 301);
        exit;
    }
    if (preg_match('#/پکیچ-آموزشی-اختصاصی-غزال/?$#u', $decoded)) {
        wp_safe_redirect(home_url('/پکیج-آموزشی-اختصاصی-غزال/'), 301);
        exit;
    }
}, 1);

add_filter('rank_math/frontend/description', static function ($description) {
    $id = (int) get_queried_object_id();
    $map = ghazal_seo_meta_map();
    if ($id && isset($map[$id]['description'])) {
        return $map[$id]['description'];
    }
    $description = html_entity_decode((string) $description, ENT_QUOTES, 'UTF-8');
    $description = preg_replace('/^14$/', '', $description);
    return ghazal_seo_fix_typos($description);
});

add_filter('rank_math/frontend/title', static function ($title) {
    $id = (int) get_queried_object_id();
    $map = ghazal_seo_meta_map();
    if ($id && isset($map[$id]['title'])) {
        return $map[$id]['title'];
    }
    return ghazal_seo_fix_typos((string) $title);
});

add_filter('the_content', static function ($content) {
    if (!is_singular()) {
        return $content;
    }
    $content = ghazal_seo_fix_typos((string) $content);
    $id = (int) get_the_ID();
    $h1s = ghazal_seo_h1_map();
    if (isset($h1s[$id]) && !preg_match('/<h1\b/i', $content)) {
        $content = '<h1 class="ghazal-seo-h1">' . esc_html($h1s[$id]) . '</h1>' . $content;
    }
    if (!is_page('amoozeshgah-zabane-shahrrey') && strpos($content, 'ghazal-seo-local') === false) {
        $box_ids = [5783, 9977, 9729, 10103, 7713, 6143, 6812, 8356];
        if (is_front_page() || in_array($id, $box_ids, true)) {
            $content .= ghazal_seo_local_box_html();
        }
    }
    return $content;
}, 12);

add_filter('elementor/frontend/the_content', static function ($content) {
    $content = ghazal_seo_fix_typos((string) $content);
    $id = (int) get_the_ID();
    $h1s = ghazal_seo_h1_map();
    if (isset($h1s[$id]) && !preg_match('/<h1\b/i', $content)) {
        $content = '<h1 class="ghazal-seo-h1">' . esc_html($h1s[$id]) . '</h1>' . $content;
    }
    if (!is_page('amoozeshgah-zabane-shahrrey') && strpos($content, 'ghazal-seo-local') === false) {
        $box_ids = [5783, 9977, 9729, 10103, 7713, 6143, 6812, 8356];
        if (is_front_page() || in_array($id, $box_ids, true)) {
            $content .= ghazal_seo_local_box_html();
        }
    }
    return $content;
}, 12);

add_action('loop_start', static function () {
    if (is_post_type_archive('teacher') && !did_action('ghazal_seo_teacher_h1')) {
        echo '<h1 class="ghazal-seo-h1">مدرسان آموزشگاه زبان غزال</h1>';
        do_action('ghazal_seo_teacher_h1');
    }
});

add_filter('body_class', static function ($classes) {
    $slugs = [
        'amoozeshgah-zabane-shahrrey',
        'kelas-english-shahrrey',
        'kelas-almani-shahrrey',
        'ghazal-school',
    ];
    foreach ($slugs as $slug) {
        if (is_page($slug)) {
            $classes[] = 'ghazal-seo-spoke';
            break;
        }
    }
    return $classes;
});

add_action('wp_head', static function () {
    echo "<style id=\"ghazal-seo\">.ghazal-seo-h1{font-size:1.75rem;margin:0 0 1rem}.ghazal-seo-local{margin:1.5rem 0;padding:1rem 1.25rem;border:1px solid #e8e8e8;border-radius:8px;background:#fafafa}.shkrn-archive-title-desc.woocommerce-products-header{display:none!important}.page-id-spoke .page-title-shkrn.entry-title,body.ghazal-seo-spoke .page-title-shkrn.entry-title{display:none!important}</style>\n";
    $local_pages = [
        'amoozeshgah-zabane-shahrrey',
        'kelas-english-shahrrey',
        'kelas-almani-shahrrey',
        'ghazal-school',
    ];
    $is_local = is_front_page();
    foreach ($local_pages as $slug) {
        if (is_page($slug)) {
            $is_local = true;
            break;
        }
    }
    if (!$is_local) {
        return;
    }
    $payload = [
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => 'آموزشگاه زبان غزال',
        'url' => home_url('/'),
        'telephone' => '+98-21-55912206',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'خیابان ۲۴ متری، خیابان پیلغوش، خیابان شیرمحمدی، پلاک ۱۴',
            'addressLocality' => 'شهرری',
            'addressRegion' => 'تهران',
            'addressCountry' => 'IR',
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => GHAZAL_SEO_LAT,
            'longitude' => GHAZAL_SEO_LNG,
        ],
        'hasMap' => 'https://maps.google.com/?q=' . GHAZAL_SEO_LAT . ',' . GHAZAL_SEO_LNG,
        'openingHours' => 'Sa-Th 09:00-21:00',
    ];
    echo '<script type="application/ld+json" class="ghazal-seo-jsonld">'
        . wp_json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . "</script>\n";
    $faq_rows = [];
    if (is_page('amoozeshgah-zabane-shahrrey')) {
        $faq_rows = ghazal_seo_faq_items();
    } elseif (is_page('kelas-english-shahrrey')) {
        $faq_rows = ghazal_seo_english_faq_items();
    } elseif (is_page('kelas-almani-shahrrey')) {
        $faq_rows = ghazal_seo_german_faq_items();
    }
    if ($faq_rows !== []) {
        $faq = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];
        foreach ($faq_rows as $row) {
            $faq['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $row['q'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $row['a'],
                ],
            ];
        }
        echo '<script type="application/ld+json" class="ghazal-seo-faq">'
            . wp_json_encode($faq, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            . "</script>\n";
    }
}, 20);

add_filter('woocommerce_page_title', static function ($title) {
    if (function_exists('is_shop') && is_shop()) {
        return 'فروشگاه کتاب آموزشگاه زبان غزال';
    }
    return $title;
});

add_action('template_redirect', static function () {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    ob_start(static function ($html) {
        if (!is_string($html) || $html === '') {
            return $html;
        }
        return ghazal_seo_fix_typos($html);
    });
}, 20);

add_filter('rank_math/frontend/robots', static function ($robots) {
    if (function_exists('is_shop') && is_shop()) {
        $q = new WP_Query([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 1,
            'fields' => 'ids',
            'no_found_rows' => true,
        ]);
        if (!$q->have_posts()) {
            $robots['index'] = 'noindex';
        }
    }
    return $robots;
});

add_filter('rank_math/json_ld', static function ($data, $jsonld = null) {
    unset($jsonld);
    if (!is_array($data)) {
        return $data;
    }
    foreach ($data as $key => $node) {
        if (!is_array($node)) {
            continue;
        }
        $types = isset($node['@type']) ? (array) $node['@type'] : [];
        if (!array_intersect($types, ['Organization', 'EducationalOrganization', 'LocalBusiness'])) {
            continue;
        }
        $data[$key]['geo'] = [
            '@type' => 'GeoCoordinates',
            'latitude' => GHAZAL_SEO_LAT,
            'longitude' => GHAZAL_SEO_LNG,
        ];
        $data[$key]['hasMap'] = 'https://maps.google.com/?q=' . GHAZAL_SEO_LAT . ',' . GHAZAL_SEO_LNG;
        if (empty($node['telephone'])) {
            $data[$key]['telephone'] = '+98-21-55912206';
        }
    }
    return $data;
}, 99, 2);

add_filter('rank_math/frontend/canonical', static function ($canonical) {
    if (!is_string($canonical) || $canonical === '') {
        return $canonical;
    }
    $decoded = urldecode($canonical);
    return esc_url_raw($decoded);
});

add_filter('rank_math/sitemap/excluded_post_types', static function ($types) {
    if (is_array($types)) {
        unset($types['product']);
    }
    return $types;
});

add_action('wp_footer', static function () {
    if (is_admin()) {
        return;
    }
    echo <<<'JS'
<script>
(function(){
  var boxes=document.querySelectorAll('.elementor-countdown-wrapper');
  boxes.forEach(function(box){
    var nums=box.querySelectorAll('.elementor-countdown-digits');
    if(!nums.length) return;
    var allZero=true;
    nums.forEach(function(n){ if(String(n.textContent||'').replace(/\s+/g,'')!=='0') allZero=false; });
    if(allZero){ var w=box.closest('.elementor-widget-countdown')||box; w.style.display='none'; }
  });
})();
</script>
JS;
}, 99);
