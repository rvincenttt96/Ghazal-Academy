<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_once '../../includes/qc.php';
require_role('supervisor');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = (int)($_POST['class_id'] ?? 0);
    $field = $_POST['field'] ?? '';
    $value = $_POST['value'] ?? '';

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

    if (!$class_id || !in_array($field, $allowed_fields, true)) {
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

        // اگر وضعیت جلسه‌ای یا تاریخ جبرانی تغییر کرد، تاریخ جلسات مجدداً محاسبه و برگردانده شود
        $new_dates = [];
        if (strpos($field, 'session_') === 0 || $field === 'makeup_sessions_json') {
            $st_cl = $pdo->prepare("
                SELECT c.*, t.starts_on, t.ends_on, e.*
                FROM classes c
                JOIN terms t ON c.term_id = t.id
                LEFT JOIN eas_management e ON c.id = e.class_id
                WHERE c.id = ?
            ");
            $st_cl->execute([$class_id]);
            $cl_row = $st_cl->fetch(PDO::FETCH_ASSOC);
            if ($cl_row) {
                $comp = eas_compute_class_session_dates($pdo, $cl_row);
                $save_json = [];
                foreach ($comp as $num => $info) {
                    $new_dates[$num] = $info['date_jalali'];
                    $save_json['date_' . $num] = $info['date_jalali'];
                }
                $up_dates = $pdo->prepare("UPDATE eas_management SET session_dates_json = ? WHERE class_id = ?");
                $up_dates->execute([json_encode($save_json, JSON_UNESCAPED_UNICODE), $class_id]);
            }
        }

        echo json_encode(['success' => true, 'new_dates' => $new_dates]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>