<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_role('teacher');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'دسترسی غیرمجاز']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'داده‌های نامعتبر']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM quiz_scores WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطای سرور']);
}
?>