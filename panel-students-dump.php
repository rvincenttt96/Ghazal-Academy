<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/rollcall.php';
header('Content-Type: application/json; charset=utf-8');
$config = rollcall_config();
$secret = (string)($config['identity']['secret'] ?? '');
$given = $_SERVER['HTTP_X_GHAZAL_IDENTITY'] ?? '';
if ($secret === '' || $given === '' || !hash_equals($secret, $given)) {
    http_response_code(403);
    echo json_encode(['ok' => false]);
    exit;
}
$rows = $pdo->query("SELECT id, username, full_name, phone FROM users WHERE role='student' AND is_deleted=0 ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['ok' => true, 'count' => count($rows), 'rows' => $rows], JSON_UNESCAPED_UNICODE);
