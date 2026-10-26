<?php
require __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/panel-api.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($method !== 'GET' && $method !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_panel_hook();

try {
    $pdo = db();
    if ($method === 'POST') {
        $input = panel_json_body();
        $id = (int)($input['id'] ?? $_GET['id'] ?? 0);
        if (!array_key_exists('delivered', $input)) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'وضعیت تحویل کتاب مشخص نیست.'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        $result = set_acc_bookstore_delivered($pdo, $id, acc_bookstore_delivered_flag($input['delivered']));
        if (empty($result['ok'])) {
            http_response_code((int)($result['http'] ?? 400));
            echo json_encode([
                'ok' => false,
                'error' => (string)($result['error'] ?? 'ثبت تحویل کتاب انجام نشد.'),
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit;
    }

    $rawStatus = trim((string)($_GET['status'] ?? 'approved,ordered'));
    $statuses = [];
    foreach (explode(',', $rawStatus) as $part) {
        $part = strtolower(trim($part));
        if ($part !== '') {
            $statuses[] = $part;
        }
    }
    $q = trim((string)($_GET['q'] ?? ''));
    $limit = (int)($_GET['limit'] ?? 300);

    echo json_encode([
        'ok' => true,
        'orders' => list_acc_bookstore_orders($pdo, $statuses, $q, $limit),
        'counts' => acc_bookstore_status_counts($pdo),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('acc-orders: ' . $e->getMessage());
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'خواندن سفارش‌های کتابفروشی انجام نشد.'], JSON_UNESCAPED_UNICODE);
}
