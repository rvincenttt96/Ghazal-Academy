<?php
session_start();
$VALID_USERNAME = 'shared_user'; 
$VALID_PASSWORD = '123456';     
$VALID_PASSWORD_HASH = md5($VALID_PASSWORD); 

if (isset($_SESSION['user_id']) && isset($_SESSION['nickname'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === $VALID_USERNAME && md5($password) === $VALID_PASSWORD_HASH) {
        $_SESSION['temp_user_id'] = $username; 
        
            header('Location: set_nickname.php'); 
        // }
        exit;
    } else {
        $error = 'اعتبارنامه (Username/Password) اشتباه است. لطفاً مجدداً تلاش کنید.';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ورود به چت روم</title>
    <style>
        body { font-family: Tahoma, sans-serif; direction: rtl; text-align: right; background-color: #f4f4f9; }
        .login-container { width: 350px; margin: 100px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input[type="text"], input[type="password"] { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>ورود به سیستم</h2>
        <p>لطفاً با اعتبارنامه مشترک وارد شوید.</p>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form action="login.php" method="POST">
            <input type="text" name="username" placeholder="نام کاربری (مثلاً: shared_user)" required>
            <input type="password" name="password" placeholder="رمز عبور (مثلاً: 123456)" required>
            <button type="submit">ورود</button>
        </form>
    </div>
</body>
</html>
