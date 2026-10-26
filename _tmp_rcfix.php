<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Tehran');
@set_time_limit(180);
error_reporting(E_ALL);
ini_set('display_errors', '0');

$want = 'rcfix-7f3a9c2e';
if (!isset($_GET['t']) || (string)$_GET['t'] !== $want) {
    http_response_code(404);
    echo json_encode(['ok' => false]);
    exit;
}

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/terms.php';
require_once __DIR__ . '/includes/jdate.php';
require_once __DIR__ . '/includes/rollcall.php';
require_once __DIR__ . '/includes/alocom.php';

$now = time();
$today = date('Y-m-d', $now);
$w = (int)date('w', $now);
$today_type = ($w === 5) ? 'friday' : (in_array($w, [6, 1, 3], true) ? 'even' : 'odd');
$mode = isset($_GET['mode']) ? (string)$_GET['mode'] : 'status';
$force_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 351;

$alo = @file_get_contents(__DIR__ . '/includes/alocom.php');
$rc = @file_get_contents(__DIR__ . '/includes/rollcall.php');
$code = [
    'alocom_bytes' => $alo ? strlen($alo) : 0,
    'retry_until_end' => $alo && strpos($alo, 'retry until session ends') !== false,
    'private_in_scan' => $alo && strpos($alo, "class_type = 'private'") !== false,
    'complete_saved_only' => $rc && strpos($rc, "'saved', 'already_saved'") !== false,
];

$log = @file_get_contents(__DIR__ . '/dashboard/supervisor/cron_alocom_log.txt');
$log_tail = $log ? array_slice(explode("\n", trim($log)), -40) : [];

$sql = "
    SELECT c.id, c.title, c.schedule, c.schedule_type, c.class_type, c.online_link,
           c.is_archived, t.title AS term_title
    FROM classes c
    JOIN terms t ON c.term_id = t.id
    WHERE c.is_deleted = 0 AND IFNULL(c.is_archived, 0) = 0 AND " . term_sql_teaching('t') . "
      AND (
        c.class_type = 'online'
        OR (c.class_type = 'private' AND IFNULL(c.online_link, '') <> '')
      )
    ORDER BY c.schedule, c.id
";
$classes = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$rows = [];
foreach ($classes as $c) {
    $times = alocom_parse_schedule($c['schedule']);
    $start = $times ? (int)$times[0] : 0;
    $end = $times ? (int)$times[1] : 0;
    $open = $start ? ($start + 30 * 60) : 0;
    $close = ($end > $open) ? $end : ($start ? ($start + 90 * 60) : 0);
    $in_live = $start && $now >= $start && $now < $end;
    $in_win = $start && $now >= $open && $now < $close;
    $att = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE class_id=? AND class_date=?');
    $att->execute([$c['id'], $today]);
    $att_n = (int)$att->fetchColumn();
    $attempt = null;
    try {
        $st = $pdo->prepare('SELECT status, sent_at FROM alocom_rollcall_attempts WHERE class_id=? AND class_date=?');
        $st->execute([$c['id'], $today]);
        $attempt = $st->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Throwable $e) {
        $attempt = ['error' => $e->getMessage()];
    }
    $complete = false;
    if ($attempt && empty($attempt['error'])) {
        $complete = in_array((string)($attempt['status'] ?? ''), ['saved', 'already_saved'], true);
    }
    $rows[] = [
        'id' => (int)$c['id'],
        'title' => $c['title'],
        'type' => $c['class_type'],
        'sched' => $c['schedule'],
        'stype' => $c['schedule_type'],
        'today' => $c['schedule_type'] === $today_type,
        'in_live' => (bool)$in_live,
        'in_win' => (bool)$in_win,
        'start' => $start ? date('H:i', $start) : null,
        'end' => $end ? date('H:i', $end) : null,
        'win' => $open ? (date('H:i', $open) . '-' . date('H:i', $close)) : null,
        'att' => $att_n,
        'attempt' => $attempt,
        'complete' => $complete,
        'link' => trim((string)$c['online_link']) !== '',
    ];
}

$today_rows = array_values(array_filter($rows, static function ($c) { return !empty($c['today']); }));
$due_rows = array_values(array_filter($today_rows, static function ($c) { return !empty($c['in_win']) && empty($c['complete']); }));

$out = [
    'ok' => true,
    'now' => date('Y-m-d H:i:s', $now),
    'w' => $w,
    'today_type' => $today_type,
    'code' => $code,
    'today_n' => count($today_rows),
    'due_retry_n' => count($due_rows),
    'today' => $today_rows,
    'due_retry' => $due_rows,
    'log_bytes' => $log ? strlen($log) : 0,
    'log_tail' => $log_tail,
    'mode' => $mode,
];

if ($mode === 'status') {
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $dry = ($mode !== 'save');
    $sync = alocom_sync_due_classes($pdo, $now, $force_id > 0 ? $force_id : null, $dry);
    $out['sync'] = $sync;
    $out['sync_dry'] = $dry;
    $out['force_id'] = $force_id;
} catch (Throwable $e) {
    $out['sync_error'] = $e->getMessage();
}

$log2 = @file_get_contents(__DIR__ . '/dashboard/supervisor/cron_alocom_log.txt');
$out['log_tail_after'] = $log2 ? array_slice(explode("\n", trim($log2)), -20) : [];

if ($force_id > 0) {
    $att = $pdo->prepare('SELECT COUNT(*) FROM attendance WHERE class_id=? AND class_date=?');
    $att->execute([$force_id, $today]);
    $out['force_att'] = (int)$att->fetchColumn();
    $st = $pdo->prepare('SELECT status, sent_at FROM alocom_rollcall_attempts WHERE class_id=? AND class_date=?');
    $st->execute([$force_id, $today]);
    $out['force_attempt'] = $st->fetch(PDO::FETCH_ASSOC) ?: null;
    $st = $pdo->prepare("SELECT status, COUNT(*) n FROM attendance WHERE class_id=? AND class_date=? GROUP BY status");
    $st->execute([$force_id, $today]);
    $out['force_att_by'] = $st->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($out, JSON_UNESCAPED_UNICODE);
