<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('supervisor');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = (int)$_POST['class_id'];
    $field = $_POST['field'];
    $value = $_POST['value'];

    // لیست فیلدهای مجاز برای جلوگیری از هک (SQL Injection)
    $allowed_fields = [
        'session_1', 'session_2', 'session_3', 'session_4', 'session_5',
        'session_6', 'session_7', 'session_8', 'session_9', 'session_10',
        'session_11', 'session_12', 'session_13',
        'poll_status', 'observation_status', 'interview_status',
        'interview_date', 'interviewer_name', 'pending_students', 'admin_note',
        'session_dates_json', 'makeup_sessions_json',
        'salary_calculated', 'salary_paid'
    ];

    if (!in_array($field, $allowed_fields)) {
        echo json_encode(['success' => false, 'message' => 'فیلد نامعتبر است']);
        exit;
    }

    try {
        // بررسی وجود رکورد برای این کلاس
        $stmt_check = $pdo->prepare("SELECT class_id FROM eas_management WHERE class_id = ?");
        $stmt_check->execute([$class_id]);
        
        if ($stmt_check->rowCount() > 0) {
            // آپدیت رکورد موجود
            $stmt = $pdo->prepare("UPDATE eas_management SET `$field` = ? WHERE class_id = ?");
            $stmt->execute([$value, $class_id]);
        } else {
            // ساخت رکورد جدید اگر نبود
            $stmt = $pdo->prepare("INSERT INTO eas_management (class_id, `$field`) VALUES (?, ?)");
            $stmt->execute([$class_id, $value]);
        }
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>