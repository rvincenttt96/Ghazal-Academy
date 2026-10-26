<?php
header('Content-Type: application/json');

$host = 'localhost';
$username = 'ghazali1_chat';
$password = 'Ea123456Ea@';
$database = 'ghazali1_chat';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database Connection Failed: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to decode JSON input.']);
    exit;
}
if (empty($data)) {
    echo json_encode(['status' => 'error', 'message' => 'JSON input is empty.']);
    exit;
}

$sender_id = $data['sender_id'] ?? null;
$receiver_id = $data['receiver_id'] ?? null;
$message = $data['message'] ?? null;

if (is_null($sender_id) || is_null($receiver_id) || is_null($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing sender_id, receiver_id, or message in data.']);
    exit;
}

$sql = "INSERT INTO messages (sender_id, receiver_id, message_text, timestamp) VALUES (?, ?, ?, NOW())";
$stmt = $conn->prepare($sql);

$stmt->bind_param("iis", $sender_id, $receiver_id, $message); 

if (!$stmt->execute()) {
    echo json_encode(['status' => 'error', 'message' => 'SQL Execution Failed: ' . $stmt->error]);
} else {
    echo json_encode(['status' => 'success', 'message' => 'Message sent successfully!']);
}


$stmt->close();
$conn->close();
?>
