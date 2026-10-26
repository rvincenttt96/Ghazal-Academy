<?php
$title = 'دانش‌آموزان کلاس';
require_once 'layout.php';
require_once '../../includes/db.php';

$class_id = (int)($_GET['class_id'] ?? 0);

if (!$class_id) {
    header("Location: index.php?error=کلاسی انتخاب نشده است.");
    exit;
}

// اطلاعات کلاس
$stmt = $pdo->prepare("SELECT title FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class_title = $stmt->fetchColumn();

if (!$class_title) {
    header("Location: index.php?error=کلاس یافت نشد.");
    exit;
}

// لیست دانش‌آموزان کلاس
$stmt = $pdo->prepare("
    SELECT 
        u.full_name,
        u.username AS student_code
    FROM users u
    JOIN class_students cs ON u.id = cs.user_id
    WHERE 
        cs.class_id = ?
        AND u.role = 'student'
        AND u.is_deleted = 0
    ORDER BY u.full_name
");
$stmt->execute([$class_id]);
$students = $stmt->fetchAll();
?>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0">دانش‌آموزان کلاس: <?= htmlspecialchars($class_title) ?></h5>
    </div>
    <div class="card-body">
        <?php if (empty($students)): ?>
            <div class="alert alert-warning">
                دانش‌آموزی در این کلاس وجود ندارد.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>نام دانش‌آموز</th>
                            <th>کد دانش‌آموزی</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= htmlspecialchars($student['full_name']) ?></td>
                            <td><?= htmlspecialchars($student['student_code']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-3">
            بازگشت به داشبورد
        </a>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>
