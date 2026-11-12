<?php
ini_set('display_errors', '0');
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/assignments.php';
require_role('student');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    assignments_json(['success' => false, 'message' => 'درخواست نامعتبر.'], 405);
}

try {
    assignments_ensure_schema($pdo);
} catch (Exception $e) {
    assignments_json(['success' => false, 'message' => 'جدول تکلیف آماده نیست.'], 500);
}

if (assignments_oversized_post()) {
    assignments_json([
        'success' => false,
        'message' => 'حجم فایل از سقف سرور بیشتر است (حداکثر ' . assignments_bytes_label(assignments_upload_limit()) . ').',
    ], 400);
}

$student_id = (int)($_SESSION['user_id'] ?? 0);
$action = trim((string)($_POST['action'] ?? ''));
$assignment_id = (int)($_POST['id'] ?? 0);

try {
    if ($action === 'upload') {
        $rawFiles = !empty($_FILES['files']) ? $_FILES['files'] : ($_FILES['file'] ?? []);
        $row = assignments_submit($pdo, $student_id, $assignment_id, $rawFiles);
        $files = ($row && !empty($row['submission_id']))
            ? assignments_submission_files_by_sub($pdo, (int)$row['submission_id'])
            : [];
        assignments_json([
            'success' => true,
            'message' => 'فایل تحویل شد.',
            'assignment' => assignments_public_student($row, $files),
        ]);
    }

    if ($action === 'delete_file') {
        $file_id = (int)($_POST['file_id'] ?? 0);
        $row = assignments_delete_student_file($pdo, $student_id, $assignment_id, $file_id);
        $files = ($row && !empty($row['submission_id']))
            ? assignments_submission_files_by_sub($pdo, (int)$row['submission_id'])
            : [];
        assignments_json([
            'success' => true,
            'message' => 'فایل حذف شد.',
            'assignment' => assignments_public_student($row, $files),
        ]);
    }

    assignments_json(['success' => false, 'message' => 'عملیات نامعتبر.'], 400);
} catch (Exception $e) {
    assignments_json(['success' => false, 'message' => $e->getMessage()], 400);
}
