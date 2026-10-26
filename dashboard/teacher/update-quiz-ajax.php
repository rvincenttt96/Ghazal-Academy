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
$score = (float)($_POST['score'] ?? -1);

if (!$id || $score < 0 || $score > 100) {
    echo json_encode(['success' => false, 'message' => 'داده‌های نامعتبر']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE quiz_scores SET score = ?, recorded_at = NOW() WHERE id = ?");
    $stmt->execute([$score, $id]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'خطای سرور']);
}
?>