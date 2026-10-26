<?php
require_once __DIR__ . "/includes/db.php";
require_once __DIR__ . "/includes/german.php";
header("Content-Type: application/json; charset=utf-8");
$out = [];
try {
    $col = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch(PDO::FETCH_ASSOC);
    $out["role_type"] = $col["Type"] ?? null;
} catch (Exception $e) { $out["role_type_err"] = $e->getMessage(); }
try {
    german_ensure_schema($pdo);
    $out["ensure"] = "ok";
} catch (Exception $e) { $out["ensure_err"] = $e->getMessage(); }
try {
    $col = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'")->fetch(PDO::FETCH_ASSOC);
    $out["role_type_after"] = $col["Type"] ?? null;
} catch (Exception $e) { $out["role_after_err"] = $e->getMessage(); }
$st = $pdo->prepare("SELECT id, username, role, full_name FROM users WHERE username=?");
$st->execute(["7974"]);
$out["user"] = $st->fetch(PDO::FETCH_ASSOC);
foreach (["german_fees","german_classes","german_class_students","german_payments"] as $t) {
    try {
        $pdo->query("SELECT 1 FROM `$t` LIMIT 1");
        $out["tables"][$t] = "ok";
    } catch (Exception $e) { $out["tables"][$t] = $e->getMessage(); }
}
echo json_encode($out, JSON_UNESCAPED_UNICODE);
