<?php
session_start();

if (!isset($_SESSION['temp_user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nickname'])) {
    $nickname = trim($_POST['nickname']);
    
    if (!empty($nickname) && mb_strlen($nickname, 'UTF-8') <= 20) {
        $_SESSION['user_id'] = $_SESSION['temp_user_id']; 
        $_SESSION['nickname'] = $nickname; 
        
        unset($_SESSION['temp_user_id']); 
        
        header('Location: index.php'); 
        exit;
    } else {
        $error = 'نام نمایشی باید وارد شود و حداکثر ۲۰ کاراکتر باشد.';
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعیین نام نمایشی</title>
    <style>
        body { font-family: Tahoma, sans-serif; direction: rtl; text-align: right; background-color: #f4f4f9; }
        .login-container { width: 350px; margin: 100px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        input[type="text"] { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>انتخاب نام نمایشی</h2>
        <p>سلام کاربر "<?= htmlspecialchars($_SESSION['temp_user_id']) ?>"، لطفاً نامی که می‌خواهید در چت روم با آن شناخته شوید را وارد کنید.</p>
        <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
        <form action="set_nickname.php" method="POST">
            <input type="text" name="nickname" placeholder="نام نمایشی شما (مثلاً: مهندس حسینی)" required>
            <button type="submit">ورود به چت</button>
        </form>
    </div>
</body>
</html>
