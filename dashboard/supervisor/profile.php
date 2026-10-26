<?php
$title = 'پروفایل من';
require_once 'layout.php';
require_once '../../includes/db.php';

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// دریافت اطلاعات فعلی کاربر
$stmt = $pdo->prepare("SELECT full_name, avatar FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: index.php?error=کاربر یافت نشد.");
    exit;
}

// پردازش آپلود
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $upload_dir = '../../uploads/avatars/';
    $file = $_FILES['avatar'];
    $error = '';

    // بررسی خطا
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'خطا در آپلود فایل.';
    } elseif ($file['size'] > 2 * 1024 * 1024) { // 2MB
        $error = 'حجم فایل نباید بیشتر از 2 مگابایت باشد.';
    } else {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowed_types)) {
            $error = 'فقط فرمت‌های JPG، PNG و WebP مجاز هستند.';
        } else {
            // نام جدید فایل (برای جلوگیری از تداخل)
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $new_filename = 'teacher_' . $user_id . '_' . time() . '.' . $ext;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // حذف عکس قبلی (اگر وجود داشت)
                if ($user['avatar']) {
                    $old_path = '../../' . ltrim($user['avatar'], '/');
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }

                // ذخیره مسیر جدید در دیتابیس
                $avatar_url = '/uploads/avatars/' . $new_filename;
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                $stmt->execute([$avatar_url, $user_id]);

                // آپدیت سشن
                $_SESSION['avatar'] = $avatar_url;

                $message = 'عکس پروفایل با موفقیت آپلود شد.';

                // رفرش داده‌های کاربر
                $user['avatar'] = $avatar_url;
            } else {
                $error = 'خطا در ذخیره فایل.';
            }
        }
    }
}
?>
<link href="../../assets/css/teacher/profile.css?v=<?= time() ?>" rel="stylesheet">
<div class="card profile-card">
    <div class="card-header text-white">
        <h5 class="mb-0">My Profile</h5>
    </div>
    <div class="card-body">

        <?php if ($message): ?>
            <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <div class="text-center mb-4">
			<div class="profile-avatar-container">
            <img src="<?= $user['avatar'] ?? 'https://cdn-icons-png.flaticon.com/512/149/149071.png' ?>" 
                 alt="عکس پروفایل" 
                 class="profile-avatar"
                 width="120" height="120"
                 style="object-fit: cover;">
				 </div>
            <h5 class="profile-name"><?= htmlspecialchars($user['full_name']) ?></h5>
        </div>

        <form method="POST" enctype="multipart/form-data" class="profile-form">
            <div class="mb-3">
                <label class="form-label">New Profile Picture</label>
                <input type="file" name="avatar" class="form-control" accept="image/*" required>
                <div class="form-text">Formats: JPG, PNG, WebP — Max 2MB </div>
            </div>
			<div class="d-flex gap-2 flex-wrap">
            <button type="submit" class="btn profile-upload-btn">Upload</button>
            <a href="index.php" class="btn profile-action-btn">Back to Dashboard</a>
			</div>
        </form>

    </div>
</div>

<?php require_once 'layout-end.php'; ?>