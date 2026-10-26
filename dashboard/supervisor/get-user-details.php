<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('supervisor');

header('Content-Type: application/json; charset=utf-8');

try {
    $user_id = (int)($_GET['user_id'] ?? 0);
    $user_type = $_GET['user_type'] ?? '';
    
    if (!$user_id || !in_array($user_type, ['teacher', 'student'])) {
        throw new Exception('اطلاعات نامعتبر است.');
    }
    
    $stmt = $pdo->prepare("
        SELECT id, username, full_name, phone, avatar, role 
        FROM users 
        WHERE id = ? AND role = ? AND is_deleted = 0
    ");
    $stmt->execute([$user_id, $user_type]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        throw new Exception('کاربر یافت نشد.');
    }
    
    echo json_encode(['success' => true, 'user' => $user]);
    exit;
    
} catch (Exception $e) {
    error_log("Get User Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}
?>