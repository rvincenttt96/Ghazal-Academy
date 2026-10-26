<?php
$key = 'c9f4e1a7b2d68e0c5a1148f3d7b90e26';
if (($_GET['k'] ?? '') !== $key) {
    http_response_code(404);
    exit;
}
date_default_timezone_set('Asia/Tehran');
@set_time_limit(90);
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/alocom-access.php';

$code = preg_replace('/\D+/', '', (string)($_GET['code'] ?? '1795'));
$st = $pdo->prepare("SELECT id, full_name, username, phone, is_deleted FROM users WHERE username = ? AND role = 'student' LIMIT 1");
$st->execute([$code]);
$student = $st->fetch(PDO::FETCH_ASSOC);

$out = [
    'now' => date('Y-m-d H:i:s'),
    'code' => $code,
    'panel' => $student ?: null,
];

$alo_user = null;
if ($student) {
    $alo_user = alocom_locate_student_user($student);
}
$out['alocom_user'] = $alo_user ? [
    'id' => (int)($alo_user['id'] ?? 0),
    'username' => $alo_user['username'] ?? '',
    'status' => $alo_user['status'] ?? null,
    'name' => trim(($alo_user['name'] ?? '') . ' ' . ($alo_user['surname'] ?? '')),
] : null;

$cls = [];
if ($student) {
    $q = $pdo->prepare("
        SELECT c.id, c.title, c.class_type, c.online_link, c.schedule, c.schedule_type
        FROM classes c
        JOIN class_students cs ON cs.class_id = c.id
        WHERE cs.user_id = ? AND (cs.is_deleted = 0 OR cs.is_deleted IS NULL) AND c.is_deleted = 0
    ");
    $q->execute([(int)$student['id']]);
    $cls = $q->fetchAll(PDO::FETCH_ASSOC);
}
$out['panel_classes'] = $cls;

$events = [];
$skip = 0;
do {
    $res = alocom_request('POST', '/api/v1/agents/events/index', ['take' => 50, 'skip' => $skip]);
    $rows = $res['data'] ?? [];
    if (!is_array($rows) || !$rows) {
        break;
    }
    $events = array_merge($events, $rows);
    $n = count($rows);
    $skip += 50;
} while ($n === 50 && $skip < 200);

$matched = [];
foreach ($cls as $class) {
    $event = alocom_match_event($class, $events);
    $row = [
        'class' => $class['title'],
        'link' => $class['online_link'],
        'event_id' => $event['id'] ?? null,
        'event_title' => $event['title'] ?? null,
        'event_status' => $event['status'] ?? null,
        'slug' => $event['slug'] ?? alocom_slug_from_link($event['alocom_link'] ?? ''),
    ];
    if ($event) {
        $detail = alocom_event_detail((int)$event['id']);
        $ev = $detail['event'] ?? $detail;
        $row['guest_access'] = $ev['guest_access'] ?? null;
        $row['start_by_admin'] = $ev['start_by_admin'] ?? null;
        $row['status'] = $ev['status'] ?? ($event['status'] ?? null);
        $row['start'] = $ev['start'] ?? $ev['start_at'] ?? $ev['started_at'] ?? null;
        $uid = (int)($alo_user['id'] ?? 0);
        $hit = false;
        $role = null;
        foreach ($detail['enrolledUsers'] ?? [] as $eu) {
            if ((int)($eu['userid'] ?? 0) === $uid || strtolower((string)($eu['username'] ?? '')) === strtolower($code)) {
                $hit = true;
                $role = $eu['role'] ?? null;
                break;
            }
        }
        $row['enrolled'] = $hit;
        $row['role'] = $role;
        $row['enrolled_count'] = count($detail['enrolledUsers'] ?? []);
    }
    $matched[] = $row;
}
$out['match'] = $matched;

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
