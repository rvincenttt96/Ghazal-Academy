<?php
require __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/panel-api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_panel_hook();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'شناسه سفارش خالی است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmt = db()->prepare('SELECT id, receipt_path FROM orders WHERE id=? LIMIT 1');
$stmt->execute([$id]);
$order = $stmt->fetch();
$pathName = $order ? basename((string)($order['receipt_path'] ?? '')) : '';
if (!$order || $pathName === '') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'رسید پیدا نشد.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$full = receipts_dir() . '/' . $pathName;
if (!is_file($full)) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(404);
    echo json_encode(['ok' => false, 'error' => 'فایل رسید روی سرور نیست.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = (string)$finfo->file($full);
if (receipt_extension_for_mime($mime) === null) {
    $mime = 'application/octet-stream';
}

header('Content-Type: ' . $mime);
header('X-Content-Type-Options: nosniff');
header('Content-Disposition: inline; filename="' . $pathName . '"');
header('Cache-Control: private, no-store');
readfile($full);
