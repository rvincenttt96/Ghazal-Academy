<?php
session_start();

// پاک کردن session
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// پاک کردن کوکی‌های "مرا به خاطر بسپار"
setcookie('remember_username', '', time() - 3600, "/");
setcookie('remember_password', '', time() - 3600, "/");

// تخریب session
session_destroy();

// ریدایرکت به صفحه لاگین
header("Location: index.php?message=با موفقیت خارج شدید.");
exit;
?>