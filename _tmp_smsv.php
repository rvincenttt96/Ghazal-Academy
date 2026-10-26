<?php
header('Content-Type: text/plain; charset=utf-8');
$e = (string)@file_get_contents(__DIR__ . '/identity-ensure.php');
$s = (string)@file_get_contents(__DIR__ . '/includes/identity-sync.php');
$r = (string)@file_get_contents(__DIR__ . '/includes/rollcall.php');
echo 'ensure abort=' . (strpos($e, 'ignore_user_abort(true)') !== false ? 'yes' : 'no') . ' md5=' . md5($e) . "\n";
echo 'sync pending=' . (strpos($s, 'student_welcome_sms_pending') !== false ? 'yes' : 'no') . ' queue=' . (strpos($s, 'function identity_queue_welcome_sms') !== false ? 'yes' : 'no') . ' md5=' . md5($s) . "\n";
echo 'roll ltr_abs=' . (strpos($r, 'rollcall_sms_ltr_isolate($student_name, 38)') !== false ? 'yes' : 'no') . ' mark_after=' . (strpos($r, "if (!rollcall_send_absence_sms") !== false ? 'yes' : 'no') . ' md5=' . md5($r) . "\n";
