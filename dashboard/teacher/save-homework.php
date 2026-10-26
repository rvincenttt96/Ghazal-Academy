<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/jdate.php';
require_role('teacher');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: homework.php");
    exit;
}

$class_id = (int)($_POST['class_id'] ?? 0);
$homework_date = trim($_POST['homework_date'] ?? '');
$homework = $_POST['homework'] ?? [];

if (!$class_id || empty($homework_date)) {
    header("Location: homework-students.php?class_id=$class_id&error=تاریخ را انتخاب کنید.");
    exit;
}

$gregorian_date = jdate_to_gregorian($homework_date);
if (!$gregorian_date) {
    header("Location: homework-students.php?class_id=$class_id&error=تاریخ نامعتبر است.");
    exit;
}

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        SELECT u.id FROM users u
        JOIN class_students cs ON u.id = cs.user_id
        WHERE cs.class_id = ? AND u.role = 'student' AND u.is_deleted = 0
    ");
    $stmt->execute([$class_id]);
    $all_students = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($all_students as $user_id) {
        $status_value = $homework[$user_id] ?? 'not_submitted';
        if (!in_array($status_value, ['complete', 'incomplete', 'not_submitted'])) {
            $status_value = 'not_submitted';
        }

		$stmt = $pdo->prepare("
			INSERT INTO homework_status (class_id, user_id, homework_date, status)
			VALUES (?, ?, ?, ?)
			ON DUPLICATE KEY UPDATE status = ?
		");
		$stmt->execute([$class_id, $user_id, $gregorian_date, $status_value, $status_value]);
    }

    $pdo->commit();
    header("Location: homework.php?success=وضعیت تکالیف با موفقیت ثبت شد.");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("Homework Save Error: " . $e->getMessage());
    header("Location: homework-students.php?class_id=$class_id&error=خطا در ثبت: " . $e->getMessage());
    exit;
}
?>