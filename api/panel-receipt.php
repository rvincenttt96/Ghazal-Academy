<?php
require __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/panel-api.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_panel_hook();

$tracking = trim((string)($_POST['tracking_code'] ?? ''));
$studentCode = (string)($_POST['student_code'] ?? '');
$file = $_FILES['receipt'] ?? null;
if (!is_array($file)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'فایل رسید را انتخاب کنید.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $order = store_order_receipt(db(), $tracking, $studentCode, $file);
    echo json_encode([
        'ok' => true,
        'order' => $order,
        'tracking_code' => $order['tracking_code'],
        'status' => $order['status'],
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('panel-receipt: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
