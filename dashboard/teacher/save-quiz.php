<?php
require_once '../../includes/auth.php';
require_role('teacher');
require_once '../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: quiz-scores.php?error=دسترسی غیرمجاز");
    exit;
}

$class_id = (int)($_POST['class_id'] ?? 0);
$scores = $_POST['scores'] ?? [];

if (!$class_id || empty($scores)) {
    header("Location: quiz-scores.php?error=هیچ نمره‌ای وارد نشده است.");
    exit;
}

try {
    foreach ($scores as $student_id => $score) {
        if ($score === '' || $score === null) continue;
        $score = (float)$score;
        if ($score < 0 || $score > 100) continue; // اعتبارسنجی

        $stmt = $pdo->prepare("
            INSERT INTO quiz_scores (class_id, student_id, score, recorded_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$class_id, $student_id, $score, $_SESSION['user_id']]);
    }

    header("Location: quiz-scores.php?class_id=" . $class_id . "&success=نمرات با موفقیت ثبت شدند.");
	exit;

} catch (Exception $e) {
	header("Location: quiz-scores.php?class_id=" . $class_id . "&error=خطا در ثبت نمرات.");
	exit;
}
?>