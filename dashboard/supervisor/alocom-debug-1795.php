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
$fix = !empty($_GET['fix']);

$st = $pdo->prepare("SELECT id, full_name, username, phone FROM users WHERE username = ? AND role = 'student' LIMIT 1");
$st->execute([$code]);
$student = $st->fetch(PDO::FETCH_ASSOC);

$cls = $pdo->prepare("
    SELECT c.id, c.title, c.class_type, c.online_link, c.schedule
    FROM classes c
    JOIN class_students cs ON cs.class_id = c.id
    WHERE cs.user_id = ? AND c.is_deleted = 0
      AND (cs.is_deleted = 0 OR cs.is_deleted IS NULL)
    ORDER BY c.id DESC LIMIT 1
");
$cls->execute([(int)($student['id'] ?? 0)]);
$class = $cls->fetch(PDO::FETCH_ASSOC);

$out = [
    'code' => $code,
    'panel_student' => $student,
    'panel_class' => $class,
    'cred' => $student ? alocom_saved_credential($pdo, (int)$student['id']) : null,
];

$alo_user = $code !== '' ? alocom_find_user_by_username($code) : null;
if (!$alo_user && $student) {
    $alo_user = alocom_locate_student_user($student);
}
$out['alocom_user'] = $alo_user ? [
    'id' => $alo_user['id'] ?? null,
    'username' => $alo_user['username'] ?? null,
    'name' => $alo_user['name'] ?? null,
    'surname' => $alo_user['surname'] ?? null,
    'status' => $alo_user['status'] ?? null,
    'keys' => array_keys($alo_user),
] : null;

$event = $class ? alocom_find_event_for_class($class) : null;
$out['event'] = $event ? [
    'id' => $event['id'] ?? null,
    'title' => $event['title'] ?? null,
    'slug' => $event['slug'] ?? null,
    'status' => $event['status'] ?? null,
    'guest_access' => $event['guest_access'] ?? null,
    'alocom_link' => $event['alocom_link'] ?? null,
] : null;

$detail = null;
if (!empty($event['id'])) {
    $detail = alocom_event_detail((int)$event['id']);
    $ev = $detail['event'] ?? $detail;
    $out['event']['guest_access'] = $ev['guest_access'] ?? null;
    $out['event']['status'] = $ev['status'] ?? null;
    $out['event']['max_user'] = $ev['max_user'] ?? null;
    $out['event']['otp'] = $ev['otp'] ?? null;
    $hits = [];
    $uid = (int)($alo_user['id'] ?? 0);
    foreach ($detail['enrolledUsers'] ?? [] as $eu) {
        $eu_uid = (int)($eu['userid'] ?? $eu['user_id'] ?? $eu['id'] ?? 0);
        $uname = strtolower((string)($eu['username'] ?? ''));
        $full = trim((string)($eu['name'] ?? '') . ' ' . (string)($eu['surname'] ?? ''));
        if ($uid && $eu_uid === $uid) {
            $hits[] = $eu;
        } elseif ($uname === strtolower($code)) {
            $hits[] = $eu;
        } elseif (stripos($full, 'Carl') !== false || stripos($full, 'Johnson') !== false) {
            $hits[] = $eu;
        }
    }
    $out['enrolled_hits'] = $hits;
    $out['enrolled_count'] = count($detail['enrolledUsers'] ?? []);
    $out['enrolled_sample'] = array_slice($detail['enrolledUsers'] ?? [], 0, 5);
}

$out['steps'] = [];
if ($fix && $student && $class) {
    try {
        $account = alocom_ensure_student_account($pdo, $student);
        $out['steps'][] = ['ensure' => $account];
        $uid = (int)($account['id'] ?? 0);
        if ($uid > 0) {
            try {
                alocom_set_user_status($uid, true);
                $out['steps'][] = ['status' => 1];
            } catch (Exception $e) {
                $out['steps'][] = ['status_error' => $e->getMessage()];
            }
            $pw_set = alocom_try_set_password($uid, alocom_password_candidates($code));
            $out['steps'][] = ['password_set' => $pw_set];
            alocom_store_credential($pdo, (int)$student['id'], $uid, $code, $pw_set ?: $code);
        }
        if (!empty($event['id']) && $uid > 0) {
            $eid = (int)$event['id'];
            $payloads = [
                ['users' => [['userid' => $uid, 'role' => 'participant']]],
                ['users' => [['user_id' => $uid, 'role' => 'participant']]],
                ['users' => [['id' => $uid, 'role' => 'participant']]],
                ['userids' => [$uid]],
            ];
            foreach ($payloads as $i => $body) {
                try {
                    $res = alocom_request('POST', '/api/v1/agents/events/' . $eid . '/add-users', $body);
                    $out['steps'][] = ['add' => $i, 'ok' => true, 'res_keys' => is_array($res) ? array_keys($res) : $res];
                    break;
                } catch (Exception $e) {
                    $out['steps'][] = ['add' => $i, 'ok' => false, 'error' => $e->getMessage()];
                }
            }
            $detail2 = alocom_event_detail($eid);
            $uid_hits = [];
            foreach ($detail2['enrolledUsers'] ?? [] as $eu) {
                $eu_uid = (int)($eu['userid'] ?? $eu['user_id'] ?? 0);
                if ($eu_uid === $uid) {
                    $uid_hits[] = $eu;
                }
            }
            $out['enrolled_after'] = $uid_hits;
            $out['enrolled_count_after'] = count($detail2['enrolledUsers'] ?? []);
        }
    } catch (Exception $e) {
        $out['fix_error'] = $e->getMessage();
    }
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
