<?php
require __DIR__ . '/includes/bootstrap.php';
require_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(404);
    exit('رسید پیدا نشد.');
}

$stmt = db()->prepare('SELECT id, receipt_path FROM orders WHERE id=? LIMIT 1');
$stmt->execute([$id]);
$order = $stmt->fetch();
$pathName = $order ? basename((string)($order['receipt_path'] ?? '')) : '';
if (!$order || $pathName === '') {
    http_response_code(404);
    exit('رسید پیدا نشد.');
}

$full = receipts_dir() . '/' . $pathName;
if (!is_file($full)) {
    http_response_code(404);
    exit('فایل رسید روی سرور نیست.');
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
