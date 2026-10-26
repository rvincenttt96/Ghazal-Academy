<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/jdate.php';
require_role('supervisor');

header('Content-Type: application/json');

try {
    $class_id = (int)($_GET['class_id'] ?? 0);
    $homework_date = $_GET['date'] ?? '';
    
    if (!$class_id || !$homework_date || $homework_date === '0000-00-00') {
        throw new Exception('تاریخ نامعتبر است.');
    }
    
    $timestamp = strtotime($homework_date);
    if ($timestamp === false || $timestamp <= 0) {
        throw new Exception('تاریخ نامعتبر است.');
    }
    
    
    $stmt = $pdo->prepare("
        SELECT c.title, u.full_name as teacher_name
        FROM classes c
        JOIN users u ON c.teacher_id = u.id
        WHERE c.id = ?
    ");
    $stmt->execute([$class_id]);
    $class_info = $stmt->fetch();
    
    if (!$class_info) {
        throw new Exception('کلاس یافت نشد.');
    }
    
  
    $stmt = $pdo->prepare("
        SELECT u.id, u.full_name, u.username as student_code, 
               COALESCE(h.status, 'not_submitted') as status
        FROM users u
        JOIN class_students cs ON u.id = cs.user_id
        LEFT JOIN homework_status h ON u.id = h.user_id AND h.class_id = ? AND h.homework_date = ?
        WHERE cs.class_id = ? AND u.role = 'student' AND u.is_deleted = 0
        ORDER BY u.full_name
    ");
    $stmt->execute([$class_id, $homework_date, $class_id]);
    $students = $stmt->fetchAll();
    
    $jalali_date = jdate('Y/m/d', $timestamp);
    
    echo json_encode([
        'success' => true,
        'class_info' => $class_info,
        'jalali_date' => $jalali_date,
        'students' => $students
    ]);
    exit;
    
} catch (Exception $e) {
    error_log("Homework Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>