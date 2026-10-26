<?php
header('Content-Type: text/plain; charset=utf-8');
$p=__DIR__.'/index.php';
echo file_get_contents($p);
