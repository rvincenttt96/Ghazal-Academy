<?php
require_once __DIR__ . "/includes/db.php";
header("Content-Type: application/json; charset=utf-8");
$st = $pdo->prepare("SELECT id, username, role, full_name, is_deleted FROM users WHERE username = ? LIMIT 1");
$st->execute(["7974"]);
$row = $st->fetch(PDO::FETCH_ASSOC);
echo json_encode($row ?: ["missing" => true], JSON_UNESCAPED_UNICODE);
