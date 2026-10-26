<?php
/**
 * One-shot: set AloCom passwords to file number (fallback Ghazal+number), dump logins, then delete this file.
 */
date_default_timezone_set('Asia/Tehran');
set_time_limit(180);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/rollcall.php';
require_once __DIR__ . '/includes/alocom-access.php';

header('Content-Type: application/json; charset=utf-8');

$config = rollcall_config();
$secret = (string)($config['identity']['secret'] ?? '');
$given = $_SERVER['HTTP_X_GHAZAL_IDENTITY'] ?? ($_GET['k'] ?? '');
if ($secret === '' || $given === '' || !hash_equals($secret, $given)) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'forbidden'], JSON_UNESCAPED_UNICODE);
    exit;
}

alocom_ensure_tables($pdo);

$do_reset = !empty($_GET['reset']) || !empty($_POST['reset']);
$updated = [];
$failed = [];

if ($do_reset) {
    $creds = $pdo->query("
        SELECT c.panel_user_id, c.alocom_user_id, c.username, c.password_plain, u.full_name, u.username AS file_number
        FROM alocom_credentials c
        JOIN users u ON u.id = c.panel_user_id
        WHERE u.role = 'student' AND u.is_deleted = 0
    ")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($creds as $row) {
        $username = alocom_readable_username($row['file_number'] ?: $row['username']);
        $candidates = alocom_password_candidates($username);
        try {
            $password = alocom_try_set_password((int)$row['alocom_user_id'], $candidates);
            alocom_store_credential($pdo, (int)$row['panel_user_id'], (int)$row['alocom_user_id'], $username, $password);
            $updated[] = [
                'file_number' => $row['file_number'],
                'username' => $username,
                'password' => $password,
            ];
        } catch (Exception $e) {
            $failed[] = [
                'file_number' => $row['file_number'],
                'error' => $e->getMessage(),
            ];
        }
    }
}

$sync = alocom_sync_class_access($pdo, false, false);

$class_stmt = $pdo->prepare("
    SELECT cl.title, cl.online_link
    FROM class_students cs
    JOIN classes cl ON cl.id = cs.class_id
    JOIN terms t ON t.id = cl.term_id
    WHERE cs.user_id = ?
      AND cl.is_deleted = 0
      AND IFNULL(cl.is_archived, 0) = 0
      AND t.is_archived = 0
    ORDER BY cl.title
");

$rows = $pdo->query("
    SELECT
        u.id AS panel_user_id,
        u.username AS file_number,
        u.full_name,
        c.username AS alocom_username,
        c.password_plain
    FROM alocom_credentials c
    JOIN users u ON u.id = c.panel_user_id
    WHERE u.role = 'student' AND u.is_deleted = 0
    ORDER BY u.username
")->fetchAll(PDO::FETCH_ASSOC);

$out = [];
foreach ($rows as $row) {
    $code = trim((string)$row['file_number']);
    if ($code !== '' && preg_match('/^[0-9]{3,8}$/', $code) && (int)$code >= 5000) {
        continue;
    }
    $class_stmt->execute([$row['panel_user_id']]);
    $classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
    $titles = [];
    $links = [];
    foreach ($classes as $cl) {
        $titles[] = $cl['title'];
        if (!empty($cl['online_link'])) {
            $links[] = $cl['online_link'];
        }
    }
    $full = trim(preg_replace('/\s+/u', ' ', (string)$row['full_name']));
    $parts = preg_split('/\s+/u', $full);
    $first = $parts ? array_shift($parts) : '';
    $last = $parts ? implode(' ', $parts) : '';
    $out[] = [
        'file_number' => $code,
        'full_name' => $full,
        'first_name' => $first,
        'last_name' => $last,
        'username' => $row['alocom_username'],
        'password' => $row['password_plain'],
        'class' => implode('، ', $titles),
        'link' => implode("\n", array_unique($links)),
    ];
}

echo json_encode([
    'ok' => true,
    'reset' => $do_reset,
    'updated' => count($updated),
    'failed' => $failed,
    'count' => count($out),
    'rows' => $out,
    'denied' => $sync['denied'] ?? [],
], JSON_UNESCAPED_UNICODE);
