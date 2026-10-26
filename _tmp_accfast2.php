<?php
header('Content-Type: text/plain; charset=utf-8');
$e = (string)@file_get_contents(__DIR__ . '/identity-ensure.php');
$s = (string)@file_get_contents(__DIR__ . '/includes/identity-sync.php');
echo 'ensure md5='.md5($e).' defer_count='.substr_count($e, 'defer_remote')."\n";
echo 'sync md5='.md5($s).' defer_fn='.substr_count($s, "if ($defer_remote)").' skipped='.substr_count($s, "skipped' => 'deferred'")."\n";
$tmp = sys_get_temp_dir() . '/gz_id_lint.php';
file_put_contents($tmp, $s);
$out = [];
$code = 1;
if (function_exists('exec')) {
  exec('php -l '.escapeshellarg($tmp).' 2>&1', $out, $code);
  echo 'sync_lint=' . implode(' | ', $out) . ' code=' . $code . "\n";
} else {
  echo "sync_lint=exec-disabled\n";
}
@unlink($tmp);
