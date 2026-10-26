<?php
/**
 * API for Saving Data to Database
 * File: api-save.php
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { exit(); }

require_once 'db.php'; // اتصال دیتابیس شما

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if ($action === 'add_student') {
    try {
        $pdo->beginTransaction();

        $studentKey = $data['student_key'];
        $studentName = $data['student_name'];
        $parentName = $data['parent_name'];
        $parentPhone = $data['parent_phone'];

        // ۱. ثبت اطلاعات والدین (اگر شماره موبایل تکراری نباشد)
        $stmtParent = $pdo->prepare("SELECT id FROM parents WHERE phone = ?");
        $stmtParent->execute([$parentPhone]);
        $existingParent = $stmtParent->fetch();

        if ($existingParent) {
            $parentId = $existingParent['id'];
        } else {
            $insertParent = $pdo->prepare("INSERT INTO parents (name, phone) VALUES (?, ?)");
            $insertParent->execute([$parentName, $parentPhone]);
            $parentId = $pdo->lastInsertId();
        }

        // ۲. ثبت اطلاعات دانش‌آموز و اتصال آن به والدین
        $insertStudent = $pdo->prepare("INSERT INTO students (student_key, name, parent_id) VALUES (?, ?, ?)");
        $insertStudent->execute([$studentKey, $studentName, $parentId]);

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "دانش‌آموز با موفقیت در دیتابیس ثبت شد."]);

    } catch (Exception $e) {
        if ($pdo->inTransaction()) { $pdo->rollBack(); }
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "خطای دیتابیس: " . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "عملیات نامعتبر است."]);
}
?>