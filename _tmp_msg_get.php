<?php
header("Content-Type: text/plain; charset=utf-8");
$root = __DIR__;
$want = $_GET["p"] ?? "";
if ($want === "list") {
    $dirs = [
        "dashboard",
        "dashboard/teacher",
        "dashboard/student",
        "dashboard/supervisor",
        "dashboard/messages",
        "messages",
        "includes",
        "assets/js",
        "assets/css",
    ];
    foreach ($dirs as $d) {
        $path = $root . "/" . $d;
        echo "DIR $d\n";
        if (!is_dir($path)) { echo "  MISSING\n"; continue; }
        $files = scandir($path);
        foreach ($files as $f) {
            if ($f === "." || $f === "..") continue;
            $low = strtolower($f);
            if (strpos($low, "message") !== false || strpos($low, "chat") !== false || strpos($low, "conv") !== false) {
                $full = $path . "/" . $f;
                echo "  " . $f . " " . (is_dir($full) ? "DIR" : filesize($full)) . "\n";
            }
        }
    }
    // also grep filenames containing message in whole tree shallow
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    $n = 0;
    foreach ($it as $file) {
        $rel = substr($file->getPathname(), strlen($root)+1);
        if (strpos($rel, "backups") !== false) continue;
        $low = strtolower($rel);
        if (strpos($low, "message") !== false || preg_match("/chat\\.php$/", $low)) {
            echo "HIT $rel " . ($file->isDir() ? "DIR" : $file->getSize()) . "\n";
            if (++$n > 80) break;
        }
    }
    exit;
}
$p = $_GET["p"] ?? "";
$p = str_replace(["..", "\\"], "", $p);
$full = $root . "/" . $p;
if (!is_file($full)) { http_response_code(404); echo "missing $p"; exit; }
header("Content-Type: text/plain; charset=utf-8");
echo file_get_contents($full);
