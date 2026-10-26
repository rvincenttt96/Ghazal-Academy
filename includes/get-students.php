<?php
require_once 'db.php';
require_once 'auth.php';

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
    exit;
}

try {
    // دریافت دانش‌آموزان از جدول users
    $stmt = $pdo->prepare("
        SELECT id, full_name, username as student_code 
        FROM users 
        WHERE role = 'student' AND is_deleted = 0 
        ORDER BY full_name
    ");
    $stmt->execute();
    $students = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'students' => $students]);
    exit;
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطا در بارگذاری دانش‌آموزان']);
    exit;
}
?>