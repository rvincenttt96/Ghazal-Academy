<?php
header('Content-Type: text/plain; charset=utf-8');
$ov=file_get_contents(__DIR__.'/includes/welcome-overlay.php');
if (preg_match_all('/setTimeout\([^)]+\)|wb-lock|is-leaving|seconds|ms/', $ov, $m)) {
  print_r($m[0]);
}
$i=strrpos($ov,'<script');
echo substr($ov, $i if $i!==false else -2000);
