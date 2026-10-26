<?php
$title = 'Remarks';
require_once 'layout.php';
require_once '../../includes/db.php';
require_role('supervisor');

$class_id = (int)($_GET['class_id'] ?? 0);
if (!$class_id) {
    header("Location: classes.php?error=کلاسی انتخاب نشده است.");
    exit;
}

$stmt = $pdo->prepare("
    SELECT c.title, u.full_name as teacher_name, t.title as term_title 
    FROM classes c 
    JOIN users u ON c.teacher_id = u.id 
    JOIN terms t ON c.term_id = t.id 
    WHERE c.id = ? AND c.is_deleted = 0
");
$stmt->execute([$class_id]);
$class_info = $stmt->fetch();
if (!$class_info) {
    header("Location: classes.php?error=کلاس یافت نشد.");
    exit;
}

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

<link href="../../assets/css/supervisor/remarks-view.css?v=<?= time() ?>" rel="stylesheet">

<div class="card">
    <div class="card-header bg-danger text-white d-flex justify-content-between py-2">
        <h5>
		<svg style="width:25px;height:25px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path opacity=".4" fill="currentColor" d=""/><path fill="currentColor" d="M24 72a24 24 0 1 0 0 48 24 24 0 1 0 0-48zm120 8c-8.8 0-16 7.2-16 16s7.2 16 16 16l352 0c8.8 0 16-7.2 16-16s-7.2-16-16-16L144 80zm0 160c-8.8 0-16 7.2-16 16s7.2 16 16 16l352 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-352 0zm0 160c-8.8 0-16 7.2-16 16s7.2 16 16 16l352 0c8.8 0 16-7.2 16-16s-7.2-16-16-16l-352 0zM48 256a24 24 0 1 0 -48 0 24 24 0 1 0 48 0zM24 392a24 24 0 1 0 0 48 24 24 0 1 0 0-48z"/></svg>
		  Remarks Class: <?= htmlspecialchars($class_info['title']) ?></h5>
        <div>
            Term: <?= htmlspecialchars($class_info['term_title']) ?> | 
            Instructor: <?= htmlspecialchars($class_info['teacher_name']) ?>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <?php for ($i = 1; $i <= 13; $i++): ?>
                <?php if (!empty($remarks[$i])): ?>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="remark-item overflow-auto">
                        <h6 class="fw-bold mb-2 text-white ">Description <?= $i ?></h6>
						<hr>
                        <p class="mb-0 text-white"><?= nl2br(htmlspecialchars($remarks[$i])) ?></p>
                    </div>
                </div>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
        
        <?php if (empty(array_filter($remarks))): ?>
        <div class="text-center py-4 text-muted">
            <i class="fas fa-comment-slash fa-2x mb-2"></i>
            <p>توضیحی برای این کلاس ثبت نشده است.</p>
        </div>
        <?php endif; ?>
        
        <div class="mt-4">
            <a href="classes.php" class="btn btn-danger px-5">Back To List</a>
        </div>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>