<?php
header('Content-Type: text/plain; charset=utf-8');
$hits = [];
$roots = [
    '/home/ghazali1/domains/ghazalify.com/public_html/wp-content',
    '/home/ghazali1/domains/ghazalify.com/public_html',
];
foreach ($roots as $root) {
    if (!is_dir($root)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    $n = 0;
    foreach ($it as $f) {
        $n++;
        if ($n > 8000) break;
        $name = $f->getFilename();
        if ($name === 'class-farazsms.php' || $name === 'ghazal-otp-sms.php' || $name === 'ghazal-identity-secret.php') {
            $hits[] = $f->getPathname();
        }
    }
}
echo json_encode($hits, JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT);
