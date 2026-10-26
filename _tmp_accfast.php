<?php
header('Content-Type: text/plain; charset=utf-8');
$a = @file_get_contents(__DIR__ . '/identity-ensure.php');
$b = @file_get_contents(__DIR__ . '/includes/identity-sync.php');
echo 'ensure bytes=' . strlen((string)$a) . ' defer=' . (strpos((string)$a, 'defer_remote') !== false ? 'yes' : 'no') . ' welcome=' . (strpos((string)$a, "'welcome_sms'") !== false ? 'yes' : 'no') . "\n";
echo 'sync bytes=' . strlen((string)$b) . ' defer=' . (strpos((string)$b, 'defer_remote') !== false ? 'yes' : 'no') . ' deferred=' . (strpos((string)$b, "skipped' => 'deferred'") !== false ? 'yes' : 'no') . "\n";
