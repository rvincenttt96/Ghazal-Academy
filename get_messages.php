<?php
header('Content-Type: application/json');
session_start();

$host = 'localhost';
$username = 'ghazali1_chat';
$password = 'Ea123456Ea@';
$database = 'ghazali1_chat';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $conn->connect_error]);
    exit;
}

$sender_id = $_GET['sender_id'] ?? null;
$receiver_id = $_GET['receiver_id'] ?? null;

if (is_null($sender_id) || is_null($receiver_id)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing sender_id or receiver_id in GET request.']);
    exit;
}

$sql = "
SELECT 
    m.message_text, 
    u.username AS sender_name, 
    m.timestamp
FROM 
    messages m
JOIN 
    users u ON m.sender_id = u.id  -- فرض می‌کنیم نام کاربری در جدول users با ستون 'id' ذخیره می‌شود
WHERE 
    (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?)
ORDER BY 
    m.timestamp ASC
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Prepare Failed: ' . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("iiii", $sender_id, $receiver_id, $receiver_id, $sender_id);

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Execution Failed: ' . $stmt->error]);
} else {
    $result = $stmt->get_result();
    $messages = [];
    
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'messages' => $messages]);
}

$stmt->close();
$conn->close();
?>
