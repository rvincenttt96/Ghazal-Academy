<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['nickname'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'کاربر لاگین نشده است.']);
    exit;
}

$message_content = trim($_POST['message_content'] ?? '');

if (empty($message_content)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'محتوای پیام نمی‌تواند خالی باشد.']);
    exit;
}

$messages_file = 'messages.txt';
$timestamp = date('Y-m-d H:i:s');
$sender_id = $_SESSION['user_id'];
$sender_name = $_SESSION['nickname'];

$message_data = [
    'timestamp' => $timestamp,
    'sender_id' => $sender_id,
    'sender_name' => $sender_name,
    'content' => $message_content
];

$message_json = json_encode($message_data) . "\n";
file_put_contents($messages_file, $message_json, FILE_APPEND | LOCK_EX);

http_response_code(200);
echo json_encode(['success' => true, 'message' => 'پیام با موفقیت ارسال شد.']);
?>
