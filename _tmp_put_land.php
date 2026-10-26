<?php
header('Content-Type: text/plain; charset=utf-8');
if (($_GET['k'] ?? '') !== 'sL9wQ4nM2tY8cR6hB1vK0pD5xJ7fA3uE') { http_response_code(404); echo 'no'; exit; }
$dir = __DIR__ . '/assets/img';
if (!is_dir($dir)) { mkdir($dir, 0755, true); }
$dest = $dir . '/supervisor-landscape.jpg';
$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) < 20000) { http_response_code(400); echo 'short '.strlen((string)$raw); exit; }
file_put_contents($dest, $raw);
echo 'ok '.filesize($dest);
