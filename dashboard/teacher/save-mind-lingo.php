<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('teacher');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $teacher_id = $_SESSION['user_id'];
        $class_id = (int)($_POST['class_id'] ?? 0);
        $student_id = (int)($_POST['student_id'] ?? 0);
        
        $comm_dev = (int)($_POST['communication_dev'] ?? 0);
        $learn_eng = (int)($_POST['learning_eng'] ?? 0);
        $learn_cons = (int)($_POST['learning_cons'] ?? 0);
        $pers_growth = (int)($_POST['personal_growth'] ?? 0);
        $teacher_note = trim($_POST['teacher_note'] ?? '');

        if (!$class_id || !$student_id) {
            throw new Exception('اطلاعات کلاس یا دانش‌آموز نامعتبر است.');
        }

        // بررسی محدودیت ۵۰۰ کلمه‌ای برای یادداشت معلم
        $word_count = str_word_count(preg_replace('/[^\p{L}\p{N}\s]/u', '', $teacher_note));
        if ($word_count > 500) {
            throw new Exception('متن یادداشت نمی‌تواند بیشتر از ۵۰۰ کلمه باشد.');
        }

        $stmt = $pdo->prepare("
            INSERT INTO mind_lingo 
            (class_id, student_id, teacher_id, communication_dev, learning_eng, learning_cons, personal_growth, teacher_note) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            communication_dev = VALUES(communication_dev),
            learning_eng = VALUES(learning_eng),
            learning_cons = VALUES(learning_cons),
            personal_growth = VALUES(personal_growth),
            teacher_note = VALUES(teacher_note)
        ");

        $stmt->execute([
            $class_id, $student_id, $teacher_id, 
            $comm_dev, $learn_eng, $learn_cons, $pers_growth, $teacher_note
        ]);

        echo json_encode(['success' => true, 'message' => 'ارزیابی Mind Lingo با موفقیت ثبت شد.']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'درخواست نامعتبر است.']);
}
?>