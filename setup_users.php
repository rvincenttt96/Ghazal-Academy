<?php
// تنظیمات اتصال به دیتابیس (اینها را با تنظیمات خود جایگزین کنید)
$servername = "localhost";
$db_username = "ghazali1_chat"; // نام کاربری دیتابیس
$db_password = "Ea123456Ea@";     // رمز عبور دیتابیس
$dbname = "ghazali1_chat";

// 1. ایجاد اتصال
$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// لیست 10 کاربر نمونه (رمز عبور ساده برای مثال)
$users_data = [
    ["Rasool", "pass123"],
    ["Emad", "pass123"],
    ["mohammad", "pass123"],
    ["Samira", "pass123"],
    ["Matin", "pass123"],
    ["Nariman", "pass123"],
    ["user7", "pass123"],
    ["user8", "pass123"],
    ["user9", "pass123"],
    ["user10", "pass123"],
];

$stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashed_password);

foreach ($users_data as $user) {
    $username = $user[0];
    // هش کردن رمز عبور برای امنیت
    $hashed_password = password_hash($user[1], PASSWORD_DEFAULT);
    
    if ($stmt->execute()) {
        echo "User $username inserted successfully.\n";
    } else {
        // این خطا معمولاً زمانی رخ می دهد که کاربر از قبل وجود داشته باشد (UNIQUE constraint)
        echo "Error inserting $username: " . $conn->error . "\n";
    }
}

$stmt->close();
$conn->close();
?>
