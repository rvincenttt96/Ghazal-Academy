<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('supervisor');

header('Content-Type: application/json');

try {
    $class_id = (int)($_GET['class_id'] ?? 0);
    if (!$class_id) {
        throw new Exception('شناسه کلاس نامعتبر است.');
    }
    
    // دریافت اطلاعات کلاس و معلم
    $stmt = $pdo->prepare("
        SELECT c.title as class_title, u.full_name as teacher_name, u.phone as teacher_phone
        FROM classes c
        JOIN users u ON c.teacher_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$class_id]);
    $class_info = $stmt->fetch();
    
    if (!$class_info) {
        throw new Exception('کلاس یافت نشد.');
    }
    
    // ✅ اصلاح شده: از جدول users با role='student' استفاده می‌کنه
    $stmt = $pdo->prepare("
        SELECT u.full_name, u.username as student_code, u.phone
        FROM users u
        JOIN class_students cs ON u.id = cs.user_id
        WHERE cs.class_id = ? AND u.role = 'student'
        ORDER BY u.full_name
    ");
    $stmt->execute([$class_id]);
    $students = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'class_info' => $class_info,
        'students' => $students
    ]);
    exit;
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>