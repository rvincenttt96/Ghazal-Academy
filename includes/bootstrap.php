<?php
$appConfig = require __DIR__ . '/../config/app.php';
$dbConfig  = require __DIR__ . '/../config/database.php';

date_default_timezone_set($appConfig['timezone'] ?? 'Asia/Tehran');

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_name($appConfig['session_name'] ?? 'bookstore_admin');
    session_set_cookie_params([
        'httponly' => true,
        'secure' => $secure,
        'samesite' => 'Lax',
        'path' => '/',
    ]);
    session_start();
}

function db(): PDO
{
    static $pdo = null;
    global $dbConfig;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=%s',
        $dbConfig['host'],
        $dbConfig['dbname'],
        $dbConfig['charset'] ?? 'utf8mb4'
    );

    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    ensure_bookstore_order_schema($pdo);

    return $pdo;
}

function e($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function bookstore_brand_title(): string
{
    return 'کتابفروشی آموزشگاه غزال';
}

function document_title(string $page = ''): string
{
    $brand = bookstore_brand_title();
    $page = trim($page);
    $text = ($page === '' || $page === 'خانه' || $page === 'ورود') ? $brand : ($page . ' — ' . $brand);
    return "\u{202B}" . $text . "\u{202C}";
}

function render_tab_head(): void
{
    $logo = 'assets/img/ghazal-logo.png?v=2';
    echo '<link rel="icon" type="image/png" sizes="192x192" href="' . e($logo) . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . e($logo) . '">' . "\n";
    echo '<link rel="shortcut icon" href="favicon.ico">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . e($logo) . '">' . "\n";
}

function redirect(string $path)
{
    header('Location: ' . $path);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('درخواست نامعتبر است. صفحه را تازه‌سازی و دوباره تلاش کنید.');
    }
}

function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_auth(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function current_admin(): ?array
{
    if (!is_logged_in()) return null;
    $stmt = db()->prepare('SELECT id, username, display_name FROM admins WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch() ?: null;
}

function admin_count(): int
{
    try {
        return (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function pull_flashes(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function status_label(string $status): string
{
    switch ($status) {
        case 'awaiting_receipt': return 'در انتظار رسید';
        case 'pending': return 'جدید';
        case 'approved': return 'آماده خرید';
        case 'ordered': return 'خرید شد';
        case 'cancelled': return 'لغو شده';
        default: return $status;
    }
}

function source_label($source): string
{
    switch ((string)$source) {
        case 'panel': return 'پنل دانش‌آموز';
        case 'eitaa': return 'ایتا';
        case 'manual': return 'ثبت دستی';
        default: return $source ? (string)$source : '—';
    }
}

function order_items_grouped(PDO $pdo, array $orderIds): array
{
    $out = [];
    $orderIds = array_values(array_filter(array_map('intval', $orderIds)));
    if ($orderIds === []) {
        return $out;
    }
    $ph = implode(',', array_fill(0, count($orderIds), '?'));
    $stmt = $pdo->prepare("SELECT order_id, book_title_snapshot, quantity, unit_price FROM order_items WHERE order_id IN ($ph) ORDER BY id");
    $stmt->execute($orderIds);
    foreach ($stmt->fetchAll() as $row) {
        $oid = (int)$row['order_id'];
        $out[$oid][] = $row;
    }
    return $out;
}

function status_class(string $status): string
{
    switch ($status) {
        case 'awaiting_receipt': return 'muted';
        case 'pending': return 'warning';
        case 'approved': return 'info';
        case 'ordered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'muted';
    }
}

function normalize_mobile(string $mobile): string
{
    $mobile = normalize_digits($mobile);
    $mobile = preg_replace('/\D+/', '', $mobile);
    if (substr($mobile, 0, 3) === '0098' && strlen($mobile) === 14) {
        $mobile = '0' . substr($mobile, 4);
    } elseif (substr($mobile, 0, 2) === '98' && strlen($mobile) === 12) {
        $mobile = '0' . substr($mobile, 2);
    }
    return $mobile;
}

function make_tracking_code(): string
{
    return 'BK-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
}

function make_batch_code(): string
{
    return 'PB-' . date('Ymd-His') . '-' . strtoupper(bin2hex(random_bytes(2)));
}

function old(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

function normalize_digits(string $value): string
{
    return strtr($value, [
        '۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
        '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9',
    ]);
}

function &settings_runtime(): array
{
    static $cache = [];
    return $cache;
}

function get_setting(string $key, $default = null)
{
    $cache =& settings_runtime();
    if (array_key_exists($key, $cache)) {
        return $cache[$key];
    }
    try {
        $stmt = db()->prepare('SELECT setting_value FROM settings WHERE setting_key=? LIMIT 1');
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        $cache[$key] = ($value === false) ? $default : $value;
    } catch (Throwable $e) {
        $cache[$key] = $default;
    }
    return $cache[$key];
}

function set_setting(string $key, ?string $value): void
{
    $stmt = db()->prepare('INSERT INTO settings (setting_key,setting_value,updated_at) VALUES (?,?,NOW()) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value),updated_at=NOW()');
    $stmt->execute([$key, $value]);
    $cache =& settings_runtime();
    $cache[$key] = $value;
}

function create_order_record(PDO $pdo, array $data): array
{
    $termId = (int)($data['term_id'] ?? 0);
    $stmt = $pdo->prepare('SELECT id,title,is_active FROM terms WHERE id=? LIMIT 1');
    $stmt->execute([$termId]);
    $term = $stmt->fetch();
    if (!$term) {
        throw new RuntimeException('ترم انتخاب‌شده پیدا نشد.');
    }

    $stmt = $pdo->prepare('SELECT b.id,b.title,tb.quantity FROM term_books tb JOIN books b ON b.id=tb.book_id WHERE tb.term_id=? AND b.is_active=1 ORDER BY b.title');
    $stmt->execute([$termId]);
    $items = $stmt->fetchAll();
    if (!$items) {
        throw new RuntimeException('برای این ترم هیچ کتاب فعالی تعریف نشده است.');
    }

    $tracking = make_tracking_code();
    for ($i = 0; $i < 10; $i++) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE tracking_code=?');
        $check->execute([$tracking]);
        if ((int)$check->fetchColumn() === 0) break;
        $tracking = make_tracking_code();
    }

    $firstName = trim((string)($data['first_name'] ?? ''));
    $lastName = trim((string)($data['last_name'] ?? ''));
    $mobile = normalize_mobile((string)($data['mobile'] ?? ''));
    $eitaaUserId = $data['eitaa_user_id'] ?? null;
    $source = in_array(($data['source'] ?? 'manual'), ['manual','eitaa','panel'], true) ? $data['source'] : 'manual';
    $delivery = validate_order_delivery(
        $data['delivery_type'] ?? 'pickup',
        (string)($data['address'] ?? ''),
        (string)($data['postal_code'] ?? '')
    );
    if (empty($delivery['ok'])) {
        throw new RuntimeException((string)($delivery['error'] ?? 'نوع تحویل معتبر نیست.'));
    }

    ensure_bookstore_order_schema($pdo);

    $pdo->prepare('INSERT INTO orders (tracking_code,first_name,last_name,mobile,eitaa_user_id,term_id,term_title_snapshot,status,source,delivery_type,address,postal_code,sell_total,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())')
        ->execute([
            $tracking,
            $firstName,
            $lastName,
            $mobile,
            $eitaaUserId,
            $termId,
            $term['title'],
            'pending',
            $source,
            $delivery['type'],
            $delivery['address'] !== '' ? $delivery['address'] : null,
            $delivery['postal_code'] !== '' ? $delivery['postal_code'] : null,
            0,
        ]);

    $orderId = (int)$pdo->lastInsertId();
    $ins = $pdo->prepare('INSERT INTO order_items (order_id,book_id,book_title_snapshot,quantity) VALUES (?,?,?,?)');
    foreach ($items as $item) {
        $ins->execute([$orderId,$item['id'],$item['title'],max(1,(int)$item['quantity'])]);
    }

    return [
        'id' => $orderId,
        'tracking_code' => $tracking,
        'term_title' => $term['title'],
    ];
}

function unique_tracking_code(PDO $pdo): string
{
    $tracking = make_tracking_code();
    for ($i = 0; $i < 10; $i++) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE tracking_code=?');
        $check->execute([$tracking]);
        if ((int)$check->fetchColumn() === 0) {
            return $tracking;
        }
        $tracking = make_tracking_code();
    }
    return $tracking;
}

function ensure_nullable_int_column(PDO $pdo, string $table, string $column): void
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    if ($table === '' || $column === '') {
        return;
    }
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE " . $pdo->quote($column));
    $col = $stmt->fetch();
    if (!$col || strtoupper((string)$col['Null']) === 'YES') {
        return;
    }
    $type = (string)$col['Type'];
    $pdo->exec("ALTER TABLE `$table` MODIFY `$column` $type NULL");
}

function ensure_orders_source_column(PDO $pdo): void
{
    $stmt = $pdo->query("SHOW COLUMNS FROM `orders` LIKE " . $pdo->quote('source'));
    $col = $stmt->fetch();
    if (!$col) {
        return;
    }
    $type = strtolower((string)$col['Type']);
    if (str_starts_with($type, 'enum') || !str_contains($type, 'varchar')) {
        $pdo->exec("ALTER TABLE `orders` MODIFY `source` VARCHAR(30) NOT NULL DEFAULT 'manual'");
    }
}

function ensure_orders_student_code_column(PDO $pdo): void
{
    $stmt = $pdo->query("SHOW COLUMNS FROM `orders` LIKE " . $pdo->quote('student_code'));
    if ($stmt->fetch()) {
        return;
    }
    $pdo->exec("ALTER TABLE `orders` ADD COLUMN `student_code` VARCHAR(20) NULL DEFAULT NULL AFTER `mobile`");
    try {
        $pdo->exec("ALTER TABLE `orders` ADD INDEX `idx_student_code` (`student_code`)");
    } catch (Throwable $e) {
        // index may already exist
    }
}

function ensure_column_if_missing(PDO $pdo, string $table, string $column, string $ddl): void
{
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $column = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
    if ($table === '' || $column === '') {
        return;
    }
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table` LIKE " . $pdo->quote($column));
    if ($stmt->fetch()) {
        return;
    }
    $pdo->exec("ALTER TABLE `$table` ADD COLUMN $ddl");
}

function ensure_orders_delivery_columns(PDO $pdo): void
{
    ensure_column_if_missing($pdo, 'orders', 'delivery_type', "`delivery_type` VARCHAR(20) NOT NULL DEFAULT 'pickup'");
    ensure_column_if_missing($pdo, 'orders', 'address', '`address` TEXT NULL');
    ensure_column_if_missing($pdo, 'orders', 'postal_code', '`postal_code` VARCHAR(20) NULL DEFAULT NULL');
    ensure_column_if_missing($pdo, 'orders', 'sell_total', '`sell_total` INT NOT NULL DEFAULT 0');
    ensure_column_if_missing($pdo, 'order_items', 'unit_price', '`unit_price` INT NULL DEFAULT NULL');
}

function ensure_orders_status_flexible(PDO $pdo): void
{
    $stmt = $pdo->query("SHOW COLUMNS FROM `orders` LIKE " . $pdo->quote('status'));
    $col = $stmt->fetch();
    if (!$col) {
        return;
    }
    $type = strtolower((string)$col['Type']);
    if (str_starts_with($type, 'enum')) {
        $pdo->exec("ALTER TABLE `orders` MODIFY `status` VARCHAR(32) NOT NULL DEFAULT 'pending'");
    }
}

function ensure_orders_receipt_columns(PDO $pdo): void
{
    ensure_column_if_missing($pdo, 'orders', 'postage_fee', '`postage_fee` INT NOT NULL DEFAULT 0');
    ensure_column_if_missing($pdo, 'orders', 'receipt_path', '`receipt_path` VARCHAR(255) NULL DEFAULT NULL');
    ensure_column_if_missing($pdo, 'orders', 'receipt_uploaded_at', '`receipt_uploaded_at` DATETIME NULL DEFAULT NULL');
    ensure_column_if_missing($pdo, 'orders', 'registered_at', '`registered_at` DATETIME NULL DEFAULT NULL');
}

function ensure_orders_delivered_columns(PDO $pdo): void
{
    ensure_column_if_missing($pdo, 'orders', 'delivered', '`delivered` TINYINT(1) NOT NULL DEFAULT 0');
    ensure_column_if_missing($pdo, 'orders', 'delivered_at', '`delivered_at` DATETIME NULL DEFAULT NULL');
}

function order_is_delivered(array $order): bool
{
    return (int)($order['delivered'] ?? 0) === 1;
}

function acc_bookstore_delivered_flag($value): bool
{
    if (is_bool($value)) {
        return $value;
    }
    if (is_numeric($value)) {
        return (int)$value === 1;
    }
    $s = strtolower(trim((string)$value));
    return in_array($s, ['1', 'true', 'yes', 'on'], true);
}

function ensure_settings_table(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        setting_key VARCHAR(64) NOT NULL,
        setting_value TEXT NULL,
        updated_at DATETIME NULL,
        PRIMARY KEY (setting_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function postage_fee_amount(): int
{
    $raw = normalize_digits((string)get_setting('postage_fee', '150000'));
    $n = (int)preg_replace('/\D+/', '', $raw);
    return max(0, $n);
}

function payment_info(): array
{
    return [
        'card' => trim((string)get_setting('payment_card', '6219861926801571')),
        'bank' => trim((string)get_setting('payment_bank', '')),
        'holder' => trim((string)get_setting('payment_holder', '')),
        'note' => trim((string)get_setting('payment_note', '')),
    ];
}

function receipts_dir(): string
{
    $dir = dirname(__DIR__) . '/uploads/receipts';
    if (!is_dir($dir)) {
        mkdir($dir, 0750, true);
    }
    return $dir;
}

function order_postage_fee(array $order): int
{
    if (normalize_order_delivery_type($order['delivery_type'] ?? 'pickup') !== 'post') {
        return 0;
    }
    return max(0, (int)($order['postage_fee'] ?? 0));
}

function order_grand_total(array $order): int
{
    return max(0, (int)($order['sell_total'] ?? 0)) + order_postage_fee($order);
}

function format_postage_label($amount, $type): string
{
    if (normalize_order_delivery_type($type) !== 'post') {
        return 'ندارد (تحویل حضوری)';
    }
    $n = (int)$amount;
    if ($n <= 0) {
        return 'رایگان';
    }
    return format_toman_amount($n);
}

function order_has_receipt(array $order): bool
{
    return trim((string)($order['receipt_path'] ?? '')) !== '';
}

function public_order_row(array $order, array $items = []): array
{
    $dtype = normalize_order_delivery_type($order['delivery_type'] ?? 'pickup');
    $postage = order_postage_fee($order);
    $sell = max(0, (int)($order['sell_total'] ?? 0));
    $status = (string)($order['status'] ?? '');
    $itemRows = [];
    foreach ($items as $item) {
        $unit = $item['unit_price'] ?? null;
        $itemRows[] = [
            'title' => (string)($item['book_title_snapshot'] ?? ''),
            'quantity' => max(1, (int)($item['quantity'] ?? 1)),
            'unit_price' => $unit !== null && $unit !== '' ? (int)$unit : null,
            'unit_price_label' => format_toman_amount($unit ?? 0),
        ];
    }
    return [
        'id' => (int)($order['id'] ?? 0),
        'tracking_code' => (string)($order['tracking_code'] ?? ''),
        'title' => display_package_title($order['term_title_snapshot'] ?? ''),
        'status' => $status,
        'status_label' => status_label($status),
        'delivery_type' => $dtype,
        'delivery_label' => order_delivery_label($dtype),
        'address' => (string)($order['address'] ?? ''),
        'postal_code' => (string)($order['postal_code'] ?? ''),
        'sell_total' => $sell,
        'sell_total_label' => format_toman_amount($sell),
        'postage_fee' => $postage,
        'postage_label' => format_postage_label($postage, $dtype),
        'grand_total' => $sell + $postage,
        'grand_total_label' => format_toman_amount($sell + $postage),
        'has_receipt' => order_has_receipt($order),
        'delivered' => order_is_delivered($order),
        'delivered_at' => order_is_delivered($order) ? (string)($order['delivered_at'] ?? '') : '',
        'created_at' => (string)($order['created_at'] ?? ''),
        'receipt_uploaded_at' => (string)($order['receipt_uploaded_at'] ?? ''),
        'registered_at' => (string)($order['registered_at'] ?? ''),
        'items' => $itemRows,
    ];
}

function list_panel_student_orders(PDO $pdo, string $studentCode): array
{
    $studentCode = normalize_student_code($studentCode);
    if ($studentCode === '') {
        return [];
    }
    ensure_bookstore_order_schema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE student_code=? AND source='panel' ORDER BY id DESC LIMIT 80");
    $stmt->execute([$studentCode]);
    $orders = $stmt->fetchAll();
    $items = order_items_grouped($pdo, array_column($orders, 'id'));
    $out = [];
    foreach ($orders as $order) {
        $out[] = public_order_row($order, $items[(int)$order['id']] ?? []);
    }
    return $out;
}

function acc_bookstore_order_row(array $order, array $items = []): array
{
    $row = public_order_row($order, $items);
    $row['student_code'] = normalize_student_code((string)($order['student_code'] ?? ''));
    $row['first_name'] = (string)($order['first_name'] ?? '');
    $row['last_name'] = (string)($order['last_name'] ?? '');
    $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
    $row['mobile'] = (string)($order['mobile'] ?? '');
    $row['source'] = (string)($order['source'] ?? '');
    $row['source_label'] = source_label($order['source'] ?? '');
    $row['approved_at'] = (string)($order['approved_at'] ?? '');
    return $row;
}

function list_acc_bookstore_orders(PDO $pdo, array $statuses, string $q = '', int $limit = 300): array
{
    ensure_bookstore_order_schema($pdo);
    $allowed = ['approved', 'ordered'];
    $statuses = array_values(array_filter($statuses, static function ($status) use ($allowed) {
        return in_array($status, $allowed, true);
    }));
    if ($statuses === []) {
        $statuses = ['approved', 'ordered'];
    }
    $limit = max(1, min(400, $limit));
    $where = ['o.status IN (' . implode(',', array_fill(0, count($statuses), '?')) . ')'];
    $params = $statuses;
    $q = trim($q);
    if ($q !== '') {
        $where[] = '(o.first_name LIKE ? OR o.last_name LIKE ? OR o.mobile LIKE ? OR o.tracking_code LIKE ? OR o.student_code LIKE ? OR o.term_title_snapshot LIKE ?)';
        $like = '%' . $q . '%';
        array_push($params, $like, $like, $like, $like, $like, $like);
    }
    $sql = 'SELECT o.* FROM orders o WHERE ' . implode(' AND ', $where) . ' ORDER BY o.id DESC LIMIT ' . $limit;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();
    $items = order_items_grouped($pdo, array_column($orders, 'id'));
    $out = [];
    foreach ($orders as $order) {
        $out[] = acc_bookstore_order_row($order, $items[(int)$order['id']] ?? []);
    }
    return $out;
}

function acc_bookstore_status_counts(PDO $pdo): array
{
    ensure_bookstore_order_schema($pdo);
    $out = ['approved' => 0, 'ordered' => 0];
    $stmt = $pdo->query("SELECT status, COUNT(*) AS n FROM orders WHERE status IN ('approved','ordered') GROUP BY status");
    foreach ($stmt->fetchAll() as $row) {
        $status = (string)($row['status'] ?? '');
        if (isset($out[$status])) {
            $out[$status] = (int)$row['n'];
        }
    }
    return $out;
}

function set_acc_bookstore_delivered(PDO $pdo, int $id, bool $delivered): array
{
    ensure_bookstore_order_schema($pdo);
    if ($id <= 0) {
        return ['ok' => false, 'error' => 'شناسه سفارش خالی است.', 'http' => 400];
    }
    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    if (!$order) {
        return ['ok' => false, 'error' => 'سفارش پیدا نشد.', 'http' => 404];
    }
    $status = (string)($order['status'] ?? '');
    if (!in_array($status, ['approved', 'ordered'], true)) {
        return ['ok' => false, 'error' => 'فقط سفارش تأییدشده یا خریدشده را می‌شود تحویل زد.', 'http' => 409];
    }
    if ($delivered) {
        $pdo->prepare('UPDATE orders SET delivered=1, delivered_at=NOW(), updated_at=NOW() WHERE id=?')->execute([$id]);
    } else {
        $pdo->prepare('UPDATE orders SET delivered=0, delivered_at=NULL, updated_at=NOW() WHERE id=?')->execute([$id]);
    }
    $stmt->execute([$id]);
    $order = $stmt->fetch();
    $items = order_items_grouped($pdo, [$id]);
    return [
        'ok' => true,
        'order' => acc_bookstore_order_row($order ?: [], $items[$id] ?? []),
    ];
}

function receipt_extension_for_mime(string $mime): ?string
{
    switch ($mime) {
        case 'image/jpeg':
            return 'jpg';
        case 'image/png':
            return 'png';
        case 'image/webp':
            return 'webp';
        case 'application/pdf':
            return 'pdf';
        default:
            return null;
    }
}

function store_order_receipt(PDO $pdo, string $tracking, string $studentCode, array $file): array
{
    $tracking = trim($tracking);
    $studentCode = normalize_student_code($studentCode);
    if ($tracking === '' || $studentCode === '') {
        throw new RuntimeException('کد سفارش یا شماره پرونده خالی است.');
    }
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        throw new RuntimeException('فایل رسید انتخاب نشده است.');
    }
    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new RuntimeException('بارگذاری رسید نامعتبر است.');
    }
    $size = (int)($file['size'] ?? 0);
    if ($size <= 0 || $size > 6 * 1024 * 1024) {
        throw new RuntimeException('حجم رسید باید کمتر از ۶ مگابایت باشد.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmp);
    $ext = receipt_extension_for_mime($mime);
    if ($ext === null) {
        throw new RuntimeException('فقط عکس (JPG, PNG, WEBP) یا PDF پذیرفته می‌شود.');
    }

    ensure_bookstore_order_schema($pdo);
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE tracking_code=? AND student_code=? LIMIT 1");
    $stmt->execute([$tracking, $studentCode]);
    $order = $stmt->fetch();
    if (!$order) {
        throw new RuntimeException('این سفارش پیدا نشد.');
    }
    if (($order['status'] ?? '') !== 'awaiting_receipt') {
        throw new RuntimeException('این سفارش دیگر منتظر رسید نیست.');
    }

    $name = (int)$order['id'] . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = receipts_dir() . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('ذخیره رسید انجام نشد.');
    }

    $old = trim((string)($order['receipt_path'] ?? ''));
    $pdo->prepare("UPDATE orders SET status='pending', receipt_path=?, receipt_uploaded_at=NOW(), registered_at=NOW(), updated_at=NOW() WHERE id=? AND status='awaiting_receipt'")
        ->execute([$name, (int)$order['id']]);

    if ($old !== '' && $old !== $name) {
        $oldPath = receipts_dir() . '/' . basename($old);
        if (is_file($oldPath)) {
            @unlink($oldPath);
        }
    }

    $stmt = $pdo->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
    $stmt->execute([(int)$order['id']]);
    $fresh = $stmt->fetch() ?: $order;
    $items = order_items_grouped($pdo, [(int)$order['id']]);
    return public_order_row($fresh, $items[(int)$order['id']] ?? []);
}

function ensure_bookstore_order_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    ensure_nullable_int_column($pdo, 'orders', 'term_id');
    ensure_nullable_int_column($pdo, 'order_items', 'book_id');
    ensure_orders_source_column($pdo);
    ensure_orders_student_code_column($pdo);
    ensure_orders_delivery_columns($pdo);
    ensure_orders_status_flexible($pdo);
    ensure_orders_receipt_columns($pdo);
    ensure_orders_delivered_columns($pdo);
    ensure_settings_table($pdo);
    require_once __DIR__ . '/sell-prices.php';
    ensure_sell_overrides_table($pdo);
    require_once __DIR__ . '/packages.php';
    ensure_package_books_table($pdo);
}

function display_package_title($title): string
{
    $title = trim((string)$title);
    $title = preg_replace('/\s+package$/i', '', $title) ?? $title;
    return trim($title);
}

function normalize_order_delivery_type($type): string
{
    return trim((string)$type) === 'post' ? 'post' : 'pickup';
}

function normalize_postal_code($code): string
{
    $code = normalize_digits(trim((string)$code));
    return preg_replace('/\D+/', '', $code) ?? '';
}

function validate_order_delivery($type, string $address, string $postal): array
{
    $type = normalize_order_delivery_type($type);
    $address = trim($address);
    $postal = normalize_postal_code($postal);
    if ($type !== 'post') {
        return ['ok' => true, 'type' => 'pickup', 'address' => '', 'postal_code' => ''];
    }
    $len = function_exists('mb_strlen') ? mb_strlen($address, 'UTF-8') : strlen($address);
    if ($len < 8) {
        return ['ok' => false, 'error' => 'برای ارسال پستی، آدرس کامل را وارد کنید.'];
    }
    if (!preg_match('/^\d{10}$/', $postal)) {
        return ['ok' => false, 'error' => 'کد پستی باید ۱۰ رقم باشد.'];
    }
    return ['ok' => true, 'type' => 'post', 'address' => $address, 'postal_code' => $postal];
}

function order_delivery_label($type): string
{
    return $type === 'post' ? 'ارسال با پست' : 'تحویل حضوری';
}

function format_toman_amount($amount): string
{
    if ($amount === null || $amount === '' || (int)$amount <= 0) {
        return '—';
    }
    return number_format((int)$amount, 0, '.', ',') . ' تومان';
}

function normalize_panel_items($items): array
{
    $out = [];
    if (!is_array($items)) {
        return $out;
    }
    foreach ($items as $item) {
        $title = '';
        $unit = null;
        if (is_string($item) || is_numeric($item)) {
            $title = trim((string)$item);
        } elseif (is_array($item)) {
            $title = trim((string)($item['title'] ?? ''));
            $price = $item['unit_price'] ?? $item['price'] ?? null;
            if ($price !== null && $price !== '') {
                $unit = (int)$price;
                if ($unit <= 0) {
                    $unit = null;
                }
            }
        }
        if ($title === '') {
            continue;
        }
        $exists = false;
        foreach ($out as $row) {
            if ($row['title'] === $title) {
                $exists = true;
                break;
            }
        }
        if (!$exists) {
            $out[] = ['title' => $title, 'unit_price' => $unit];
        }
    }
    return $out;
}

function normalize_student_code(string $code): string
{
    $code = normalize_digits(trim($code));
    return preg_replace('/\s+/', '', $code) ?? '';
}

function student_file_number($code): string
{
    $code = normalize_student_code((string)$code);
    return $code !== '' ? $code : '—';
}

function create_panel_package_order(PDO $pdo, array $data): array
{
    $packageTitle = trim((string)($data['package_title'] ?? ''));
    if ($packageTitle === '') {
        throw new RuntimeException('پکیج انتخاب نشده است.');
    }

    $firstName = trim((string)($data['first_name'] ?? ''));
    $lastName = trim((string)($data['last_name'] ?? ''));
    if ($firstName === '') {
        throw new RuntimeException('نام دانش‌آموز خالی است.');
    }
    if ($lastName === '') {
        $lastName = '—';
    }

    $mobile = normalize_mobile((string)($data['mobile'] ?? ''));
    $studentCode = normalize_student_code((string)($data['student_code'] ?? ''));
    $tracking = unique_tracking_code($pdo);
    $delivery = validate_order_delivery(
        $data['delivery_type'] ?? 'pickup',
        (string)($data['address'] ?? ''),
        (string)($data['postal_code'] ?? '')
    );
    if (empty($delivery['ok'])) {
        throw new RuntimeException((string)($delivery['error'] ?? 'نوع تحویل معتبر نیست.'));
    }

    ensure_bookstore_order_schema($pdo);

    $items = normalize_panel_items($data['items'] ?? []);
    if ($items === []) {
        $items = [['title' => $packageTitle, 'unit_price' => null]];
    }
    $sellTotal = (int)($data['sell_total'] ?? 0);
    if ($sellTotal <= 0) {
        foreach ($items as $item) {
            if ($item['unit_price'] !== null) {
                $sellTotal += (int)$item['unit_price'];
            }
        }
    }
    $status = (($data['status'] ?? '') === 'awaiting_receipt') ? 'awaiting_receipt' : 'pending';
    $postageFee = 0;
    if ($delivery['type'] === 'post') {
        if (isset($data['postage_fee']) && $data['postage_fee'] !== '' && $data['postage_fee'] !== null) {
            $postageFee = max(0, (int)$data['postage_fee']);
        } else {
            $postageFee = postage_fee_amount();
        }
        if ($postageFee > 5000000) {
            $postageFee = postage_fee_amount();
        }
    }

    $pdo->prepare('INSERT INTO orders (tracking_code,first_name,last_name,mobile,student_code,eitaa_user_id,term_id,term_title_snapshot,status,source,delivery_type,address,postal_code,sell_total,postage_fee,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW())')
        ->execute([
            $tracking,
            $firstName,
            $lastName,
            $mobile,
            $studentCode !== '' ? $studentCode : null,
            null,
            null,
            $packageTitle,
            $status,
            'panel',
            $delivery['type'],
            $delivery['address'] !== '' ? $delivery['address'] : null,
            $delivery['postal_code'] !== '' ? $delivery['postal_code'] : null,
            $sellTotal,
            $postageFee,
        ]);

    $orderId = (int)$pdo->lastInsertId();
    $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id,book_id,book_title_snapshot,quantity,unit_price) VALUES (?,?,?,?,?)');
    foreach ($items as $item) {
        $itemStmt->execute([$orderId, null, $item['title'], 1, $item['unit_price']]);
    }

    return [
        'id' => $orderId,
        'tracking_code' => $tracking,
        'term_title' => $packageTitle,
        'delivery_type' => $delivery['type'],
        'sell_total' => $sellTotal,
        'postage_fee' => $postageFee,
        'status' => $status,
        'grand_total' => $sellTotal + $postageFee,
    ];
}
