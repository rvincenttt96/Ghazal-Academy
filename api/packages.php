<?php
require __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/panel-api.php';
require_once __DIR__ . '/../includes/packages.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_panel_hook();

try {
    $packages = ghazal_live_packages(db());
    echo json_encode([
        'ok' => true,
        'packages' => $packages,
        'postage_fee' => postage_fee_amount(),
        'payment' => payment_info(),
        'fetched_at' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'packages_failed'], JSON_UNESCAPED_UNICODE);
}
