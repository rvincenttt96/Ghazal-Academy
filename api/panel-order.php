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
$input = panel_json_body();

try {
    $pdo = db();
    $pdo->beginTransaction();
    $order = create_panel_package_order($pdo, [
        'first_name' => $input['first_name'] ?? '',
        'last_name' => $input['last_name'] ?? '',
        'mobile' => $input['mobile'] ?? '',
        'package_title' => $input['package_title'] ?? '',
        'student_code' => $input['student_code'] ?? '',
        'items' => $input['items'] ?? [],
        'delivery_type' => $input['delivery_type'] ?? 'pickup',
        'address' => $input['address'] ?? '',
        'postal_code' => $input['postal_code'] ?? '',
        'sell_total' => $input['sell_total'] ?? 0,
        'postage_fee' => $input['postage_fee'] ?? 0,
        'status' => $input['status'] ?? 'awaiting_receipt',
    ]);
    $pdo->commit();
    echo json_encode([
        'ok' => true,
        'id' => $order['id'],
        'tracking_code' => $order['tracking_code'],
        'term_title' => $order['term_title'],
        'status' => $order['status'] ?? 'awaiting_receipt',
        'delivery_type' => $order['delivery_type'] ?? 'pickup',
        'sell_total' => $order['sell_total'] ?? 0,
        'postage_fee' => $order['postage_fee'] ?? 0,
        'grand_total' => $order['grand_total'] ?? 0,
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('panel-order: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
