<?php
header('Content-Type: text/plain; charset=utf-8');
if (($_GET['k'] ?? '') !== 'nR8qK2wL5pX0sB7cH3dJ9mF1yU6tA4vE') { http_response_code(404); echo 'no'; exit; }
$allow = ['wall-akhavan-arthur.jpg'];
$name = basename((string)($_GET['f'] ?? ''));
if (!in_array($name, $allow, true)) { http_response_code(400); echo 'bad name'; exit; }
$dir = __DIR__ . '/assets/img';
if (!is_dir($dir)) { mkdir($dir, 0755, true); }
$raw = file_get_contents('php://input');
if ($raw === false || strlen($raw) < 20000) { http_response_code(400); echo 'short '.strlen((string)$raw); exit; }
file_put_contents($dir . '/' . $name, $raw);
echo 'ok '.filesize($dir . '/' . $name);
