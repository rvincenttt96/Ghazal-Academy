<?php
/**
 * Mattermost interactive buttons + slash command for the quiz-good bank.
 * Public endpoint; auth is HMAC / slash token, not a panel session.
 */
header('Content-Type: application/json; charset=utf-8');
@set_time_limit(20);

try {
    require_once __DIR__ . '/includes/db.php';
    require_once __DIR__ . '/includes/quiz-builder.php';
    require_once __DIR__ . '/includes/quiz-feedback.php';
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['text' => 'پیکربندی پنل ناقص است.', 'ephemeral_text' => 'پیکربندی پنل ناقص است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$raw = (string)file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) {
    $input = [];
}
if (isset($_POST['payload'])) {
    $decoded = json_decode((string)$_POST['payload'], true);
    if (is_array($decoded)) {
        $input = $decoded;
    }
}
if (!$input && $_POST) {
    $input = $_POST;
}

$cfg = quiz_feedback_config();
$context = is_array($input['context'] ?? null) ? $input['context'] : [];
$action = trim((string)($context['action'] ?? $input['action'] ?? ''));
$is_slash = isset($input['command']) || (isset($input['token']) && isset($input['text']) && !isset($input['context']));

try {
    quiz_feedback_ensure($pdo);
    $who = trim((string)($input['user_name'] ?? $input['user_name'] ?? ''));
    if ($who === '') {
        $who = trim((string)($input['user_id'] ?? 'mattermost'));
    }

    if ($is_slash) {
        $given = trim((string)($input['token'] ?? ''));
        if ($cfg['slash_token'] === '' || $given === '' || !hash_equals($cfg['slash_token'], $given)) {
            throw new Exception('توکن دستور نامعتبر است.');
        }
        $channel = trim((string)($input['channel_id'] ?? ''));
        if ($cfg['channel_id'] !== '' && $channel !== '' && $channel !== $cfg['channel_id']) {
            throw new Exception('این دستور فقط در کانال کوییز چک کار می‌کند.');
        }
        list($act, $n) = quiz_feedback_parse_slash_text($input['text'] ?? '');
        $report = quiz_feedback_latest_report($pdo);
        if (!$report) {
            throw new Exception('هنوز گزارشی از پنل نیامده. اول در Quiz Builder گزارش بزنید.');
        }
        $out = quiz_feedback_add_from_report($pdo, (int)$report['id'], $act, $who, $n);
        $msg = quiz_feedback_human_result($out);
        echo json_encode(quiz_feedback_hook_result($msg), JSON_UNESCAPED_UNICODE);
        exit;
    }

    $report_id = (int)($context['report_id'] ?? $input['report_id'] ?? 0);
    if ($action === '') {
        $action = 'add_question';
    }
    if (!in_array($action, ['add_question', 'add_quiz'], true)) {
        throw new Exception('عملیات نامعتبر است.');
    }
    if ($report_id < 1 || !quiz_feedback_sig_ok($report_id, $action, (string)($context['sig'] ?? ''))) {
        throw new Exception('امضای دکمه نامعتبر است.');
    }
    $out = quiz_feedback_add_from_report($pdo, $report_id, $action, $who, 0);
    $msg = quiz_feedback_human_result($out);
    echo json_encode([
        'ephemeral_text' => $msg,
        'text' => $msg,
        'username' => 'کوییز',
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    $msg = $e->getMessage();
    echo json_encode(quiz_feedback_hook_result($msg, false), JSON_UNESCAPED_UNICODE);
}
