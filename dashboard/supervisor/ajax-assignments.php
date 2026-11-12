<?php
ini_set('display_errors', '0');
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/term-plan.php';
require_once '../../includes/assignments.php';
require_role('supervisor');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    assignments_json(['success' => false, 'message' => 'درخواست نامعتبر.'], 405);
}

try {
    assignments_ensure_schema($pdo);
} catch (Exception $e) {
    assignments_json(['success' => false, 'message' => 'جدول تکلیف آماده نیست.'], 500);
}

$action = trim((string)($_POST['action'] ?? ''));
$class_id = (int)($_POST['class_id'] ?? 0);

try {
    if ($action === 'list') {
        if (!$class_id || !assignments_find_class($pdo, $class_id, 0)) {
            throw new Exception('کلاس یافت نشد.');
        }
        $items = [];
        foreach (assignments_class_rows($pdo, $class_id, 0) as $row) {
            $items[] = assignments_public_teacher($row);
        }
        assignments_json([
            'success' => true,
            'assignments' => $items,
            'student_count' => assignments_class_student_count($pdo, $class_id),
        ]);
    }

    if ($action === 'submissions') {
        $asg = assignments_find_teacher($pdo, (int)($_POST['id'] ?? 0), 0);
        if (!$asg) {
            throw new Exception('این تکلیف پیدا نشد.');
        }
        $pack = assignments_submissions($pdo, $asg);
        assignments_json([
            'success' => true,
            'assignment' => assignments_public_teacher($asg),
            'submissions' => $pack['rows'],
            'submitted' => $pack['submitted'],
            'total' => $pack['total'],
            'graded' => (int)($asg['graded_count'] ?? 0),
        ]);
    }

    assignments_json(['success' => false, 'message' => 'عملیات نامعتبر.'], 400);
} catch (Exception $e) {
    assignments_json(['success' => false, 'message' => $e->getMessage()], 400);
}
