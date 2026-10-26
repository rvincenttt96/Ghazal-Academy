<?php
$k = $_GET['f'] ?? '';
if ($k !== 'rollcall-config.php') { http_response_code(404); exit; }
$path = __DIR__ . '/includes/rollcall-config.php';
if (!is_file($path)) { http_response_code(404); exit; }
header('Content-Type: text/plain; charset=utf-8');
echo file_get_contents($path);
