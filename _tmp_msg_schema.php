<?php
header("Content-Type: text/plain; charset=utf-8");
require_once __DIR__ . "/includes/db.php";
$tables = ["conversations", "conversation_participants", "messages"];
foreach ($tables as $t) {
    echo "==== $t ====\n";
    try {
        $row = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);
        echo ($row["Create Table"] ?? json_encode($row)) . "\n";
        $c = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "COUNT $c\n";
    } catch (Exception $e) {
        echo "ERR " . $e->getMessage() . "\n";
    }
}
echo "==== columns messages ====\n";
foreach ($pdo->query("SHOW COLUMNS FROM messages") as $c) {
    echo $c["Field"]." ".$c["Type"]." ".$c["Null"]." ".$c["Key"]." ".$c["Default"]."\n";
}
echo "==== columns conversations ====\n";
foreach ($pdo->query("SHOW COLUMNS FROM conversations") as $c) {
    echo $c["Field"]." ".$c["Type"]." ".$c["Null"]." ".$c["Key"]." ".$c["Default"]."\n";
}
echo "==== columns conversation_participants ====\n";
foreach ($pdo->query("SHOW COLUMNS FROM conversation_participants") as $c) {
    echo $c["Field"]." ".$c["Type"]." ".$c["Null"]." ".$c["Key"]." ".$c["Default"]."\n";
}
echo "==== grep teacher_available ====\n";
$api = file_get_contents(__DIR__."/includes/messages/message-api.php");
echo (strpos($api, "get_teacher_available_users") !== false) ? "api has teacher_available\n" : "api MISSING teacher_available\n";
echo (strpos($api, "supervisor") !== false) ? "api mentions supervisor\n" : "api no supervisor role filter\n";
$stu = glob(__DIR__."/dashboard/student/*");
echo "==== student files with message ====\n";
foreach ($stu as $f) {
    $n = basename($f);
    if (stripos($n, "message") !== false) echo $n, "\n";
}
$lay = file_get_contents(__DIR__."/dashboard/student/layout.php");
echo "student layout messages.php link: ", (strpos($lay, "messages.php")!==false?"yes":"no"), "\n";
$sup = file_get_contents(__DIR__."/dashboard/supervisor/layout.php");
echo "supervisor layout messages.php link: ", (strpos($sup, "messages.php")!==false?"yes":"no"), "\n";
echo "supervisor layout messages: ", substr_count(strtolower($sup), "message"), "\n";
