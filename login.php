<?php
require __DIR__ . '/includes/bootstrap.php';
if (admin_count() === 0) {
    redirect('setup-admin.php');
}
if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = (int)$admin['id'];
        session_regenerate_id(true);
        redirect('dashboard.php');
    }
    $error = 'نام کاربری یا رمز عبور صحیح نیست.';
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="robots" content="noindex,nofollow">
  <title><?= e(document_title('ورود')) ?></title>
  <link rel="stylesheet" href="assets/css/app.css?v=0.7.5">
  <?php render_tab_head(); ?>
</head>
<body class="lg-app">
<div class="lg-orbs" aria-hidden="true">
  <span class="lg-orb lg-orb-a"></span>
  <span class="lg-orb lg-orb-b"></span>
  <span class="lg-orb lg-orb-c"></span>
  <span class="lg-orb lg-orb-d"></span>
  <span class="lg-orb lg-orb-e"></span>
  <div class="lg-grid-floor"></div>
</div>
<div class="login-wrap">
  <form class="login-card glass" method="post">
    <div class="brand">
      <img class="brand-logo" src="assets/img/ghazal-logo.png" alt="غزال" width="46" height="46">
      <div>
        <strong>کتاب غزال</strong>
        <small>سفارش‌های پنل دانش‌آموز</small>
      </div>
    </div>
    <h1>ورود دفتر</h1>
    <p>اینجا سفارش کتاب دانش‌آموز را می‌بینید و خرید از اینجا بوک را ثبت می‌کنید.</p>
    <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field full"><label>نام کاربری</label><input class="input" name="username" dir="ltr" autocomplete="username" required></div>
      <div class="field full"><label>رمز عبور</label><input class="input" type="password" name="password" dir="ltr" autocomplete="current-password" required></div>
    </div>
    <div class="actions"><button class="btn primary block" type="submit">ورود</button></div>
  </form>
</div>
</body>
</html>
