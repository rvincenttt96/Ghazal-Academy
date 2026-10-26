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

$code = normalize_student_code((string)($_GET['student_code'] ?? ''));
if ($code === '') {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'شماره پرونده خالی است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    echo json_encode([
        'ok' => true,
        'orders' => list_panel_student_orders(db(), $code),
        'postage_fee' => postage_fee_amount(),
        'payment' => payment_info(),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('student-orders: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'خواندن سفارش‌ها انجام نشد.'], JSON_UNESCAPED_UNICODE);
}
