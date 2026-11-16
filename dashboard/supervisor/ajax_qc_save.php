<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/qc.php';
require_role('supervisor');

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'متد نامعتبر است.']);
    exit;
}

$supervisor_id = (int)($_SESSION['user_id'] ?? 0);
$teacher_id = (int)($_POST['teacher_id'] ?? 0);
$term_id = (int)($_POST['term_id'] ?? 0);

if (!$teacher_id || !$term_id) {
    echo json_encode(['success' => false, 'message' => 'اطلاعات معلم یا ترم نامعتبر است.']);
    exit;
}

$obs = (float)($_POST['observation'] ?? 0);
$poll = (float)($_POST['poll'] ?? 0);
$subs = (float)($_POST['subs'] ?? 0);
$att = (float)($_POST['courses_attendance'] ?? 0);
$notes = trim((string)($_POST['notes'] ?? ''));

$saved = qc_save_teacher_supervisor_scores($pdo, $teacher_id, $term_id, [
    'observation' => $obs,
    'poll' => $poll,
    'subs' => $subs,
    'courses_attendance' => $att,
    'notes' => $notes,
], $supervisor_id);

if ($saved) {
    // Re-calculate overall mark to return
    $overall_data = qc_calculate_teacher_overall($pdo, $teacher_id, $term_id);
    echo json_encode([
        'success' => true,
        'message' => 'ارزیابی سوپروایزر با موفقیت ذخیره شد.',
        'supervisor_total' => $overall_data['supervisor_total_40'],
        'overall_mark' => $overall_data['overall_mark'],
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'خطا در ذخیره‌سازی ارزیابی.']);
}
