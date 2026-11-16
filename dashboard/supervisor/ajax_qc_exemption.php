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
$class_id = (int)($_POST['class_id'] ?? 0);
$item_key = trim((string)($_POST['item_key'] ?? ''));

if (!$class_id || empty($item_key)) {
    echo json_encode(['success' => false, 'message' => 'پارامترهای درخواست ناقص است.']);
    exit;
}

// Fetch class details
$st_cl = $pdo->prepare("
    SELECT c.*, t.starts_on, t.ends_on, e.session_1, e.session_2, e.session_3, e.session_4,
           e.session_5, e.session_6, e.session_7, e.session_8, e.session_9, e.session_10,
           e.session_11, e.session_12, e.session_13, e.session_dates_json, e.makeup_sessions_json
    FROM classes c
    JOIN terms t ON c.term_id = t.id
    LEFT JOIN eas_management e ON c.id = e.class_id
    WHERE c.id = ?
");
$st_cl->execute([$class_id]);
$class_row = $st_cl->fetch(PDO::FETCH_ASSOC);

if (!$class_row) {
    echo json_encode(['success' => false, 'message' => 'کلاس مورد نظر یافت نشد.']);
    exit;
}

$res = qc_toggle_class_exemption($pdo, $class_id, $item_key, $supervisor_id);
if (!$res || empty($res['success'])) {
    echo json_encode(['success' => false, 'message' => 'خطا در تغییر وضعیت پارامتر.']);
    exit;
}

$is_exempt = (bool)$res['is_exempt'];

// Recalculate class score with the new exemption status
$class_calc = qc_calculate_class($pdo, $class_row);

// Recalculate teacher overall score
$teacher_id = (int)$class_row['teacher_id'];
$term_id = (int)$class_row['term_id'];
$teacher_qc = qc_calculate_teacher_overall($pdo, $teacher_id, $term_id);

$item_score = $class_calc['items'][$item_key]['score'] ?? 0;

echo json_encode([
    'success' => true,
    'class_id' => $class_id,
    'teacher_id' => $teacher_id,
    'item_key' => $item_key,
    'is_exempt' => $is_exempt,
    'item_score' => $item_score,
    'class_score' => $class_calc['total_score'],
    'classes_avg_60' => $teacher_qc['classes_avg_60'],
    'overall_mark' => $teacher_qc['overall_mark'],
    'active_count' => $class_calc['active_count'] ?? 6,
    'exempt_count' => $class_calc['exempt_count'] ?? 0,
    'message' => $is_exempt
        ? 'پارامتر با موفقیت برای این کلاس بی‌اثر شد و نمره آن بین سایر پارامترهای فعال توزیع گردید.'
        : 'پارامتر برای این کلاس مجدداً فعال گردید.',
]);
