<?php
require __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/services/EitaaUniomService.php';
require_once __DIR__ . '/services/EitaaOrderBot.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'method_not_allowed']);
    exit;
}

$expected = (string)get_setting('eitaa_webhook_secret', '');
$provided = (string)($_GET['key'] ?? '');
if ($expected === '' || $provided === '' || !hash_equals($expected, $provided)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden']);
    exit;
}

if ((string)get_setting('eitaa_enabled', '0') !== '1') {
    echo json_encode(['ok' => true, 'ignored' => 'disabled']);
    exit;
}

$raw = file_get_contents('php://input');
$update = json_decode((string)$raw, true);
if (!is_array($update)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'invalid_json']);
    exit;
}

try {
    $apiKey = (string)get_setting('eitaa_api_key', '');
    $baseUrl = (string)get_setting('eitaa_api_base_url', 'https://api.uniom.ir/bot');
    $academy = (string)get_setting('eitaa_academy_name', 'آموزشگاه زبان غزل');
    $service = new EitaaUniomService($apiKey, $baseUrl);
    $bot = new EitaaOrderBot(db(), $service, $academy);
    $bot->processUpdate($update);
    echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    error_log('Eitaa webhook fatal: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'internal_error']);
}
