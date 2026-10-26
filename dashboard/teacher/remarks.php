<?php
$title = 'توضیحات کلاس';
require_once 'layout.php';
require_once '../../includes/db.php';
require_role('teacher');

$class_id = (int)($_GET['class_id'] ?? 0);
if (!$class_id) {
    header("Location: classes.php?error=کلاسی انتخاب نشده است.");
    exit;
}

// دریافت اطلاعات کلاس
$stmt = $pdo->prepare("SELECT c.title, u.full_name as teacher_name FROM classes c JOIN users u ON c.teacher_id = u.id WHERE c.id = ? AND c.is_deleted = 0");
$stmt->execute([$class_id]);
$class_info = $stmt->fetch();
if (!$class_info) {
    header("Location: classes.php?error=کلاس یافت نشد.");
    exit;
}

// دریافت توضیحات موجود
$remarks = [];
for ($i = 1; $i <= 13; $i++) {
    $remarks[$i] = '';
}

$stmt = $pdo->prepare("SELECT remark_number, content FROM remarks WHERE class_id = ?");
$stmt->execute([$class_id]);
while ($row = $stmt->fetch()) {
    $remarks[$row['remark_number']] = $row['content'];
}
?>
<?php
// نمایش پیام‌های موفقیت/خطا
$success_message = $_GET['success'] ?? null;
$error_message = $_GET['error'] ?? null;
?>

<?php if ($success_message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        توضیحات با موفقیت ذخیره شدند.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error_message): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($error_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<link href="../../assets/css/teacher/remarks.css?v=<?= time() ?>" rel="stylesheet">

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">📝 توضیحات کلاس: <?= htmlspecialchars($class_info['title']) ?></h5>
        <small class="text-white-50">معلم: <?= htmlspecialchars($class_info['teacher_name']) ?></small>
    </div>
    <div class="card-body">
        <form method="POST" action="save-remarks.php">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            
            <div class="row g-3">
                <?php for ($i = 1; $i <= 13; $i++): ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="remark-field">
                        <label class="form-label fw-bold">توضیح <?= $i ?></label>
                        <textarea name="remarks[<?= $i ?>]" class="form-control" rows="3" placeholder="توضیح <?= $i ?> را وارد کنید..."><?= htmlspecialchars($remarks[$i]) ?></textarea>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
            
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">ذخیره توضیحات</button>
                <a href="classes.php" class="btn btn-secondary">بازگشت</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>