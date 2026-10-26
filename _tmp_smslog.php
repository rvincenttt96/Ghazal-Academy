<?php
header('Content-Type: text/plain; charset=utf-8');
$p = __DIR__ . '/dashboard/supervisor/identity_log.txt';
if (!is_file($p)) { echo "missing\n"; exit; }
$lines = @file($p, FILE_IGNORE_NEW_LINES);
$n = $lines ? count($lines) : 0;
echo "lines=$n\n";
$keep = [];
foreach ($lines ?: [] as $ln) {
  if (preg_match('/welcome|sms|defer_remote|alocom/i', $ln)) $keep[] = $ln;
}
$keep = array_slice($keep, -60);
echo implode("\n", $keep), "\n";
