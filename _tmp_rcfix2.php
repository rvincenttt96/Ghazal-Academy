<?php
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Tehran");
if (!isset($_GET["t"]) || (string)$_GET["t"] !== "rcfix-7f3a9c2e") { http_response_code(404); echo json_encode(["ok"=>false]); exit; }
require_once __DIR__ . "/includes/db.php";
$alo = @file_get_contents(__DIR__ . "/includes/alocom.php");
$today = date("Y-m-d");
$st = $pdo->prepare("SELECT a.class_id, c.title, a.user_id, u.full_name, a.status, a.is_present FROM attendance a JOIN classes c ON c.id=a.class_id JOIN users u ON u.id=a.user_id WHERE a.class_id IN (351,349,355) AND a.class_date=? ORDER BY a.class_id, u.full_name");
$st->execute([$today]);
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
$log = @file_get_contents(__DIR__ . "/dashboard/supervisor/cron_alocom_log.txt");
$tail = $log ? array_slice(explode("\n", trim($log)), -25) : [];
echo json_encode([
  "ok" => true,
  "now" => date("Y-m-d H:i:s"),
  "student_retry" => $alo && strpos($alo, "retry until a student joins") !== false,
  "bytes" => $alo ? strlen($alo) : 0,
  "att" => $rows,
  "log_tail" => $tail,
], JSON_UNESCAPED_UNICODE);
