<?php
require __DIR__ . '/includes/bootstrap.php';

if (admin_count() > 0) {
    redirect('login.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $displayName = trim($_POST['display_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirm'] ?? '';

    if ($username === '' || $displayName === '' || $password === '') {
        $error = 'همه فیلدها الزامی هستند.';
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {
        $error = 'نام کاربری باید حداقل ۳ کاراکتر و فقط شامل حروف انگلیسی، عدد، نقطه، خط تیره یا زیرخط باشد.';
    } elseif (strlen($password) < 8) {
        $error = 'رمز عبور حداقل باید ۸ کاراکتر باشد.';
    } elseif ($password !== $password2) {
        $error = 'تکرار رمز عبور یکسان نیست.';
    } else {
        $stmt = db()->prepare('INSERT INTO admins (username, display_name, password_hash, created_at) VALUES (?, ?, ?, NOW())');
        $stmt->execute([$username, $displayName, password_hash($password, PASSWORD_DEFAULT)]);
        $_SESSION['admin_id'] = (int) db()->lastInsertId();
        session_regenerate_id(true);
        flash('success', 'حساب مدیر ساخته شد.');
        redirect('dashboard.php');
    }
}
?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="robots" content="noindex,nofollow"><title><?= e(document_title('ساخت مدیر')) ?></title>
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
  <form class="login-card glass" method="post" autocomplete="off">
    <div class="brand"><img class="brand-logo" src="assets/img/ghazal-logo.png" alt="غزال" width="46" height="46"><div><strong>کتاب غزال</strong><small>ساخت اولین حساب دفتر</small></div></div>
    <h1>اولین مدیر را بسازید</h1>
    <p>این صفحه فقط تا قبل از ساخت اولین حساب باز است.</p>
    <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
    <?= csrf_field() ?>
    <div class="form-grid">
      <div class="field full"><label>نام نمایشی</label><input class="input" name="display_name" value="<?= e(old('display_name')) ?>" required></div>
      <div class="field full"><label>نام کاربری</label><input class="input" name="username" dir="ltr" value="<?= e(old('username')) ?>" required></div>
      <div class="field full"><label>رمز عبور</label><input class="input" type="password" name="password" dir="ltr" required></div>
      <div class="field full"><label>تکرار رمز عبور</label><input class="input" type="password" name="password_confirm" dir="ltr" required></div>
    </div>
    <div class="actions"><button class="btn primary block" type="submit">ساخت حساب و ورود</button></div>
  </form>
</div>
</body></html>
