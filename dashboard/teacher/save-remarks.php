<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('teacher');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('درخواست نامعتبر.');
    }
    
    $class_id = (int)($_POST['class_id'] ?? 0);
    $remarks = $_POST['remarks'] ?? [];
    
    if (!$class_id) {
        throw new Exception('کلاس انتخاب نشده است.');
    }
    
    // بررسی مالکیت کلاس
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ? AND teacher_id = ? AND is_deleted = 0");
    $stmt->execute([$class_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('شما مجاز به ویرایش این کلاس نیستید.');
    }
    
    $pdo->beginTransaction();
    
    // حذف تمام توضیحات قبلی
    $stmt = $pdo->prepare("DELETE FROM remarks WHERE class_id = ?");
    $stmt->execute([$class_id]);
    
    // ذخیره توضیحات جدید
    for ($i = 1; $i <= 13; $i++) {
        $content = trim($remarks[$i] ?? '');
        if (!empty($content)) {
            $stmt = $pdo->prepare("
                INSERT INTO remarks (class_id, remark_number, content) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$class_id, $i, $content]);
        }
    }
    
    $pdo->commit();
    
    // رفرش صفحه
    header("Location: remarks.php?class_id=" . $class_id . "&success=1");
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Save Remarks Error: " . $e->getMessage());
    
    // بازگشت با خطا
    header("Location: remarks.php?class_id=" . $class_id . "&error=" . urlencode($e->getMessage()));
    exit;
}
?>