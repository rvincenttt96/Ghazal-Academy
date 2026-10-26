<?php
header('Content-Type: text/plain; charset=utf-8');
$files = [
    __DIR__ . '/includes/error-log.php',
    __DIR__ . '/error-ping.php',
    __DIR__ . '/includes/sms.php',
    __DIR__ . '/includes/rollcall.php',
    __DIR__ . '/includes/identity-sync.php',
    __DIR__ . '/includes/alocom.php',
    __DIR__ . '/includes/accounting.php',
    __DIR__ . '/identity-ensure.php',
    __DIR__ . '/includes/db.php',
];
$out = [];
foreach ($files as $f) {
    $out[basename($f)] = [
        'exists' => is_file($f),
        'bytes' => is_file($f) ? filesize($f) : 0,
    ];
}
$lint = [];
foreach ($files as $f) {
    if (!is_file($f)) continue;
    $src = file_get_contents($f);
    // cheap brace check
    $lint[basename($f)] = substr_count($src, '{') - substr_count($src, '}');
}
$cfg = require __DIR__ . '/includes/rollcall-config.php';
$el = $cfg['error_logs'] ?? [];
$db = file_get_contents(__DIR__ . '/includes/db.php');
echo json_encode([
    'files' => $out,
    'brace_delta' => $lint,
    'channel_len' => strlen((string)($el['channel_id'] ?? '')),
    'token_len' => strlen((string)($el['bot_token'] ?? '')),
    'db_include' => strpos($db, "error-log.php") !== false,
    'sms_fail_hook' => strpos(file_get_contents(__DIR__.'/includes/sms.php'), 'ghazal_error_sms_fail') !== false,
], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
