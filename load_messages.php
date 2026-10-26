<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['nickname'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'کاربر لاگین نشده است.']);
    exit;
}
$messages_file = 'messages.txt';

if (!file_exists($messages_file)) {
    http_response_code(200);
    echo json_encode(['success' => true, 'messages' => []]);
    exit;
}
$messages = [];
$handle = fopen($messages_file, 'r');
if ($handle) {
    while (($line = fgets($handle)) !== false) {
        $message = json_decode($line, true);
        if ($message) {
            $messages[] = $message;
        }
    }
    fclose($handle);
}
usort($messages, function($a, $b) {
    return strtotime($a['timestamp']) - strtotime($b['timestamp']);
});
http_response_code(200);
echo json_encode([
    'success' => true,
    'messages' => $messages
]);
?>
