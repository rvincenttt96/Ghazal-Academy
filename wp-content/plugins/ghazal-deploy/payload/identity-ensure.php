<?php
/**
 * Internal identity hook. Called by accounting / WordPress with a shared secret.
 */
date_default_timezone_set('Asia/Tehran');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/identity-sync.php';

header('Content-Type: application/json; charset=utf-8');

$given = $_SERVER['HTTP_X_GHAZAL_IDENTITY'] ?? ($_GET['k'] ?? '');
if (!identity_valid_secret($given)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = $_POST;
}

$action = (string)($input['action'] ?? $_GET['action'] ?? 'sync');

try {
    if ($action === 'repair_acc') {
        $result = identity_repair_acc_codes($pdo);
        echo json_encode(['ok' => true, 'result' => $result], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'backfill') {
        $dry = !empty($input['dry_run']) || !empty($_GET['dry_run']);
        $limit = (int)($input['limit'] ?? $_GET['limit'] ?? 0);
        $offset = (int)($input['offset'] ?? $_GET['offset'] ?? 0);
        $result = identity_backfill($pdo, $dry, $limit, $offset);
        echo json_encode(['ok' => true, 'result' => $result], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = identity_sync_student($pdo, [
        'source' => $input['source'] ?? 'hook',
        'code' => $input['code'] ?? '',
        'previous_code' => $input['previous_code'] ?? '',
        'first_name' => $input['first_name'] ?? '',
        'last_name' => $input['last_name'] ?? '',
        'full_name' => $input['full_name'] ?? '',
        'phone' => $input['phone'] ?? '',
        'password' => $input['password'] ?? '',
        'acc_student_id' => $input['acc_student_id'] ?? null,
        'reset_password' => !empty($input['reset_password']),
        'create_if_missing' => true,
    ]);
    echo json_encode($result, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    identity_log('ensure error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
