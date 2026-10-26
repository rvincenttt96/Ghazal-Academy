<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/error-log.php';
$ok = ghazal_error_report('DB-CRASH', 'تست اتصال کانال Error Logs', 'اگر این را می‌بینید ارسال لاگ وصل است.');
$cfg = ghazal_error_config();
$el = $cfg['error_logs'] ?? [];
echo json_encode([
  'fired' => $ok,
  'channel_len' => strlen((string)($el['channel_id'] ?? '')),
  'token_len' => strlen((string)($el['bot_token'] ?? '')),
  'db_has_include' => strpos((string)@file_get_contents(__DIR__.'/includes/db.php'), 'error-log.php') !== false,
], JSON_UNESCAPED_UNICODE);
