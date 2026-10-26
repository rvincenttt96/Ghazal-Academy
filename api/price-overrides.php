<?php
require __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../includes/sell-prices.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed'], JSON_UNESCAPED_UNICODE);
    exit;
}

$hookFile = __DIR__ . '/../config/panel-hook.php';
$hook = is_file($hookFile) ? require $hookFile : [];
$expected = is_array($hook) ? (string)($hook['token'] ?? '') : '';
$given = (string)($_SERVER['HTTP_X_GHAZAL_BOOKS'] ?? '');
if ($expected === '' || $given === '' || !hash_equals($expected, $given)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $map = ghazal_override_map(db());
    echo json_encode([
        'ok' => true,
        'by_title' => $map,
        'count' => count($map),
        'fetched_at' => date('Y-m-d H:i:s'),
    ], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'overrides_failed'], JSON_UNESCAPED_UNICODE);
}
