<?php
$f = $_GET['f'] ?? '';
$map = [
  'welcome' => __DIR__ . '/includes/welcome-overlay.php',
  'login' => __DIR__ . '/login.php',
  'index' => __DIR__ . '/index.php',
];
if (!isset($map[$f]) || !is_file($map[$f])) { http_response_code(404); echo 'missing'; exit; }
header('Content-Type: text/plain; charset=utf-8');
echo file_get_contents($map[$f]);
