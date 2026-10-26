<?php
header('Content-Type: text/plain; charset=utf-8');
$t = (string)@file_get_contents(__DIR__ . '/includes/welcome-overlay.php');
echo 'bytes=' . strlen($t) . ' md5=' . md5($t) . "\n";
echo 'hello=' . (strpos($t, '>Hello<') !== false ? 'yes' : 'no') . "\n";
echo 'welcome=' . (strpos($t, '>Welcome Back<') !== false ? 'yes' : 'no') . "\n";
echo 'tap=' . (strpos($t, '>Tap to enter<') !== false ? 'yes' : 'no') . "\n";
echo 'ltr=' . (strpos($t, 'direction: ltr') !== false ? 'yes' : 'no') . "\n";
echo 'fa_hello=' . (strpos($t, 'سلام') !== false ? 'yes' : 'no') . "\n";
echo 'fa_welcome=' . (strpos($t, 'خوش آمدید') !== false ? 'yes' : 'no') . "\n";
