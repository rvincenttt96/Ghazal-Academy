<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/identity-sync.php';
echo 'fn=' . (function_exists('identity_maybe_welcome_sms') ? 'yes' : 'no') . "\n";
echo 'pattern=' . identity_welcome_pattern_code() . "\n";
$cfg = rollcall_config();
$got = (string)(($cfg['sms']['welcome_pattern_code'] ?? ''));
echo 'cfg=' . ($got === 'RMej2aBV8v' ? 'ok' : 'missing') . "\n";
echo 'status=' . rollcall_pattern_status(identity_welcome_pattern_code()) . "\n";
