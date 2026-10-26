<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('supervisor');

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $student_id = (int)($input['student_id'] ?? 0);
    $class_id = (int)($input['class_id'] ?? 0);
    $class_date = $input['date'] ?? '';
    $reason = trim($input['reason'] ?? '');
    
    if (!$student_id || !$class_id || !$class_date || !$reason) {
        throw new Exception('لطفاً تمامی اطلاعات را پر کنید.');
    }
    
    // اعتبارسنجی تاریخ
    if (!strtotime($class_date)) {
        throw new Exception('تاریخ نامعتبر است.');
    }
    
    // ✅ بررسی وجود کلاس (بدون بررسی مالکیت)
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ?");
    $stmt->execute([$class_id]);
    if (!$stmt->fetch()) {
        throw new Exception('کلاس یافت نشد.');
    }
    
    // ✅ بروزرسانی علت غیبت (با user_id صحیح)
    $stmt = $pdo->prepare("
        INSERT INTO attendance (user_id, class_id, class_date, status, reason)
        VALUES (?, ?, ?, 'absent', ?)
        ON DUPLICATE KEY UPDATE reason = ?, updated_at = NOW()
    ");
    $stmt->execute([$student_id, $class_id, $class_date, $reason, $reason]);
    
    echo json_encode(['success' => true, 'message' => 'علت غیبت با موفقیت ثبت شد.']);
    exit;
    
} catch (Exception $e) {
    error_log("Save Reason Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>