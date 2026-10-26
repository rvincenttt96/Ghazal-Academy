<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/identity-sync.php';
echo 'fn=' . (function_exists('identity_sms_ltr_isolate') ? 'yes' : 'no') . "\n";
$n = identity_sms_ltr_isolate('Emad', 38) . ' ';
$c = identity_sms_ltr_isolate('1816', 12);
echo 'name_space=' . (substr($n, -1) === ' ' ? 'yes' : 'no') . "\n";
echo 'name_lri=' . ((strpos($n, "\u{2066}") !== false && strpos($n, "\u{2069}") !== false) ? 'yes' : 'no') . "\n";
echo 'code_lri=' . ((strpos($c, "\u{2066}") !== false && strpos($c, "\u{2069}") !== false) ? 'yes' : 'no') . "\n";
