<?php
require __DIR__ . '/includes/bootstrap.php';
require_auth();
$id = (int)($_GET['id'] ?? 0);
redirect($id ? ('purchase-report.php?tab=history&id=' . $id) : 'purchase-report.php?tab=history');
