<?php
if (($_SERVER['HTTP_X_PUT'] ?? '') !== 'f28d33f6f26c4a7841cd88b2a1b026b0') { http_response_code(404); exit; }
$f = rtrim(sys_get_temp_dir(), '/') . '/ghazal_live_packages.json';
@unlink($f);
echo 'ok cache';
