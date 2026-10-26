<?php
header("Content-Type: text/plain; charset=utf-8");
$root = __DIR__;
$p = $root . "/includes/messages";
foreach (scandir($p) as $f) {
  if ($f==="."||$f==="..") continue;
  echo $f, " ", filesize($p."/".$f), "\n";
}
echo "--- student layout grep ---\n";
$lay = file_get_contents($root."/dashboard/student/layout.php");
foreach (["messages","chat","conversation"] as $k) {
  echo $k, " ", (strpos($lay,$k)!==false?"yes":"no"), "\n";
}
echo "--- supervisor layout grep ---\n";
$lay = file_get_contents($root."/dashboard/supervisor/layout.php");
foreach (["messages","chat","conversation"] as $k) {
  echo $k, " count=", substr_count(strtolower($lay), $k), "\n";
}
echo "--- teacher messages.php first lines ---\n";
