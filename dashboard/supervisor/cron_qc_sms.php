<?php
// تنظیم منطقه زمانی روی تهران
date_default_timezone_set('Asia/Tehran');

@set_time_limit(180);
ignore_user_abort(true);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/terms.php';
require_once __DIR__ . '/../../includes/jdate.php';
require_once __DIR__ . '/../../includes/sms.php';
require_once __DIR__ . '/../../includes/qc.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $res = qc_check_and_dispatch_sms_alerts($pdo);
    echo json_encode([
        'status' => 'success',
        'result' => $res,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    error_log(date('[Y-m-d H:i:s] ') . "Cron QC SMS Error: " . $e->getMessage() . "\n", 3, __DIR__ . '/cron_error_log.txt');
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
