<?php
$token = 'rc_accsync_2k9';
if (!isset($_GET['k']) || $_GET['k'] !== $token) { http_response_code(403); exit; }
date_default_timezone_set('Asia/Tehran');
set_time_limit(240);
ini_set('max_execution_time', '240');
require __DIR__ . '/includes/db.php';
require __DIR__ . '/includes/jdate.php';
require __DIR__ . '/includes/rollcall.php';
require __DIR__ . '/includes/alocom.php';
require __DIR__ . '/includes/alocom-access.php';
header('Content-Type: application/json; charset=utf-8');
$dry = !isset($_GET['save']) || $_GET['save'] !== '1';
$notify = isset($_GET['notify']) && $_GET['notify'] === '1';
try {
    $out = alocom_sync_class_access($pdo, $dry, $notify && !$dry);
    $summary = [
        'dry_run' => $dry,
        'notified' => $notify && !$dry,
        'guest_closed' => $out['guest_closed'],
        'classes' => [],
        'denied' => $out['denied'],
    ];
    foreach ($out['classes'] as $c) {
        $summary['classes'][] = [
            'class' => $c['class'],
            'event' => $c['event'],
            'link' => $c['link'] ?? '',
            'allowed_count' => count($c['allowed']),
            'denied_count' => count($c['denied']),
            'allowed' => array_map(function ($s) {
                return [
                    'full_name' => $s['full_name'],
                    'username' => $s['username'],
                    'password' => $s['password'] ?? '',
                    'acc' => $s['acc'] ?? '',
                ];
            }, $c['allowed']),
        ];
    }
    echo json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
