<?php
header('Content-Type: text/plain; charset=utf-8');
require_once __DIR__ . '/includes/bookstore.php';
$c = bookstore_public_catalog();
echo 'packages=' . count($c) . "\n";
echo 'li3=' . (isset($c['LI3']) ? count($c['LI3']['books']) : 0) . "\n";
echo 'title=' . bookstore_order_title('LI3', ["Let's Go 2", 'Lost']) . "\n";
echo 'full=' . bookstore_order_title('LI3', $c['LI3']['books']) . "\n";
