<?php
header("Content-Type: application/json; charset=utf-8");
if (!isset($_GET["t"]) || (string)$_GET["t"] !== "wall-look-9k2m") { http_response_code(404); echo json_encode(["ok"=>false]); exit; }
require_once __DIR__ . "/includes/db.php";
$st = $pdo->query("SELECT id, username, full_name, role, is_deleted FROM users WHERE is_deleted=0 AND (full_name LIKE '%اخوان%' OR full_name LIKE '%Akhavan%' OR full_name LIKE '%akhavan%' OR full_name LIKE '%مرتضی%' OR full_name LIKE '%Morteza%' OR full_name LIKE '%matin%' OR full_name LIKE '%Matin%' OR username IN ('4752','matin','m1377','5011')) ORDER BY role, id");
$rows = $st->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(["ok"=>true, "n"=>count($rows), "users"=>$rows], JSON_UNESCAPED_UNICODE);
