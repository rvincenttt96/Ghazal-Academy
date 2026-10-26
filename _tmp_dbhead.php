<?php
header('Content-Type: text/plain; charset=utf-8');
$lines = explode("\n", (string)file_get_contents(__DIR__.'/includes/db.php'));
for ($i=0;$i<min(12,count($lines));$i++) {
    $l = $lines[$i];
    if (preg_match('/pass|secret|pwd|token|key\s*=/i', $l)) {
        echo ($i+1).": [redacted]\n";
    } else {
        echo ($i+1).": ".$l."\n";
    }
}
