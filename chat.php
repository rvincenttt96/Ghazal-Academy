<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

// دریافت پیام از فرم
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST["message"];

    // ذخیره پیام در پایگاه داده
    $sql = "INSERT INTO messages (user_id, message) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $_SESSION["user_id"], $message);
    $stmt->execute();

    // دریافت پیام‌های جدید
    $sql = "SELECT * FROM messages ORDER BY id DESC";
    $result = $conn->query($sql);
    $messages = $result->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat</title>
</head>
<body>
    <form method="post" action="chat.php">
        <input type="text" name="message" placeholder="Enter message">
        <button type="submit">Send</button>
    </form>

    <?php
    if ($messages) {
        foreach ($messages as $message) {
            $user = $conn->query("SELECT username FROM users WHERE id = $message[user_id]");
            $user_result = $user->fetch_assoc();
            echo "<p><strong>" . htmlspecialchars($user_result["username"]) . ":</strong> " . htmlspecialchars($message["message"]) . "</p>";
        }
    }
    ?>
</body>
</html>
