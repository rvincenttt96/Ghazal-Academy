<?php
/**
 * One-shot AloCom credential dump. Delete immediately after use.
 */
date_default_timezone_set('Asia/Tehran');
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

$rows = $pdo->query("
    SELECT
        u.id AS panel_user_id,
        u.username AS file_number,
        u.full_name,
        u.phone,
        c.alocom_user_id,
        c.username AS alocom_username,
        c.password_plain,
        c.updated_at
    FROM users u
    LEFT JOIN alocom_credentials c ON c.panel_user_id = u.id
    WHERE u.role = 'student' AND u.is_deleted = 0
    ORDER BY u.username
")->fetchAll(PDO::FETCH_ASSOC);

$class_stmt = $pdo->prepare("
    SELECT cl.title, cl.online_link, cl.class_type, t.title AS term_title
    FROM class_students cs
    JOIN classes cl ON cl.id = cs.class_id
    JOIN terms t ON t.id = cl.term_id
    WHERE cs.user_id = ?
      AND cl.is_deleted = 0
      AND IFNULL(cl.is_archived, 0) = 0
      AND t.is_archived = 0
    ORDER BY cl.title
");

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
    $username = trim((string)($row['alocom_username'] ?? ''));
    if ($username === '' && $code !== '') {
        $username = strtolower($code);
    }
    $password = trim((string)($row['password_plain'] ?? ''));
    if ($password === '' && $username !== '') {
        $password = $username;
    }
    $full = trim(preg_replace('/\s+/u', ' ', (string)$row['full_name']));
    $parts = preg_split('/\s+/u', $full);
    $first = $parts ? array_shift($parts) : '';
    $last = $parts ? implode(' ', $parts) : '';
    $out[] = [
        'panel_user_id' => (int)$row['panel_user_id'],
        'file_number' => $code,
        'full_name' => $full,
        'first_name' => $first,
        'last_name' => $last,
        'phone' => $row['phone'],
        'alocom_user_id' => $row['alocom_user_id'] !== null ? (int)$row['alocom_user_id'] : null,
        'username' => $username,
        'password' => $password,
        'has_stored_password' => $row['password_plain'] !== null && $row['password_plain'] !== '',
        'class' => implode('، ', $titles),
        'link' => implode("\n", array_unique($links)),
        'updated_at' => $row['updated_at'],
    ];
}

echo json_encode(['ok' => true, 'count' => count($out), 'rows' => $out], JSON_UNESCAPED_UNICODE);
