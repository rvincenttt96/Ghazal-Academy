<?php
header('Content-Type: text/plain; charset=utf-8');
foreach (scandir(__DIR__) as $n) {
  if (substr($n,-4)==='.php') echo $n."\n";
}
