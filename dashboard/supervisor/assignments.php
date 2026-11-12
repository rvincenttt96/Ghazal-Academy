<?php
$title = 'Assignments';
$current_page = 'assignments.php';
require_once 'layout.php';
require_once '../../includes/db.php';
require_once '../../includes/term-plan.php';
require_once '../../includes/assignments.php';
require_role('supervisor');

$schema_error = '';
$classes = [];
try {
    assignments_ensure_schema($pdo);
    $classes = assignments_supervisor_classes($pdo);
} catch (Exception $e) {
    error_log('supervisor assignments.php: ' . $e->getMessage());
    $schema_error = 'الان لیست تکلیف در دسترس نیست. صفحه را تازه کنید.';
}

$selected_id = (int)($_GET['class_id'] ?? 0);
$selected = null;
foreach ($classes as $c) {
    if ((int)$c['id'] === $selected_id) {
        $selected = $c;
        break;
    }
}

$rows = [];
$student_count = 0;
if ($selected && $schema_error === '') {
    $rows = assignments_class_rows($pdo, (int)$selected['id'], 0);
    $student_count = assignments_class_student_count($pdo, (int)$selected['id']);
}
$pub = [];
foreach ($rows as $row) {
    $pub[] = assignments_public_teacher($row);
}
$boot = [
    'ajax' => 'ajax-assignments.php',
    'file' => 'assignment-file.php',
    'class_id' => $selected ? (int)$selected['id'] : 0,
    'session_max' => assignments_session_max(),
    'student_count' => $student_count,
    'assignments' => $pub,
    'score_max' => assignments_score_max(),
    'readonly' => true,
];
?>
<link href="../../assets/css/quiz-builder.css?v=qb7" rel="stylesheet">
<link href="../../assets/css/assignments.css?v=asg4" rel="stylesheet">
<div class="qb-page asg-page" <?= $selected ? 'data-asg-supervisor data-boot="' . assignments_h(json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . '"' : 'data-asg-classes' ?>>
    <div class="qb-head">
        <div>
            <h1><span>Assignments</span></h1>
            <p>تکلیفی که معلم برای جلسه گذاشته، عکس‌ها و ویس پاسخ دانش‌آموز، و وضعیت بررسی معلم (کامل، ناقص یا عدم تحویل) اینجا فقط برای مشاهده است.</p>
        </div>
        <?php if ($selected): ?>
            <a class="qb-btn qb-btn-ghost" href="assignments.php">همه کلاس‌ها</a>
        <?php endif; ?>
    </div>

    <?php if ($schema_error): ?>
        <div class="qb-empty">
            <div class="qb-empty-icon">⏳</div>
            <h5>لیست تکلیف لود نشد</h5>
            <p><?= assignments_h($schema_error) ?></p>
        </div>
    <?php elseif (!$selected): ?>
        <?php if (!$classes): ?>
            <div class="qb-empty">
                <div class="qb-empty-icon">📎</div>
                <h5>کلاسی برای این ترم نیست</h5>
                <p>وقتی کلاس فعال باشد، تکلیف معلم‌ها از اینجا دیده می‌شود.</p>
            </div>
        <?php else: ?>
            <label class="qb-field asg-filter-field">
                <span>جستجوی کلاس یا معلم</span>
                <input type="search" id="asgClassFilter" placeholder="مثلاً PI2 یا نام معلم" autocomplete="off">
            </label>
            <div class="qb-cards" id="asgClassCards">
                <?php foreach ($classes as $class):
                    $asg_n = (int)$class['assignment_count'];
                    $sub_n = (int)$class['submission_count'];
                    $grd_n = (int)$class['graded_count'];
                    $hayRaw = ($class['title'] ?? '') . ' ' . ($class['teacher_name'] ?? '');
                    $hay = function_exists('mb_strtolower') ? mb_strtolower($hayRaw, 'UTF-8') : strtolower($hayRaw);
                ?>
                    <a class="qb-card" href="assignments.php?class_id=<?= (int)$class['id'] ?>" data-hay="<?= assignments_h($hay) ?>">
                        <div>
                            <h2><?= assignments_h($class['title']) ?><?= function_exists('class_live_dot') ? class_live_dot($class) : '' ?></h2>
                            <div class="qb-card-meta">
                                <span><?= assignments_h($class['teacher_name']) ?></span>
                                <span><?= $asg_n ?> تکلیف</span>
                                <span><?= $sub_n ?> تحویل</span>
                                <span><?= $grd_n ?> بررسی‌شده</span>
                            </div>
                        </div>
                        <span class="qb-card-go"><?= $asg_n ? 'بررسی تکلیف ←' : 'هنوز تکلیفی نیست ←' ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="qb-empty" id="asgClassEmpty" hidden>
                <h5>کلاسی با این جستجو نیست</h5>
                <p>عنوان کلاس یا نام معلم را کوتاه‌تر کنید.</p>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="qb-class-bar">
            <div>
                <strong><?= assignments_h($selected['title']) ?></strong>
                <span class="qb-pill qb-pill-ready"><?= assignments_h($selected['teacher_name']) ?></span>
                <span class="qb-pill qb-pill-pending"><?= (int)$student_count ?> دانش‌آموز</span>
            </div>
            <span><?= (int)$selected['assignment_count'] ?> تکلیف · <?= (int)$selected['submission_count'] ?> تحویل · <?= (int)$selected['graded_count'] ?> بررسی‌شده</span>
        </div>
        <div class="asg-sessions" id="asgSessions"></div>
        <section class="qb-exams asg-list" id="asgListWrap">
            <h2>تکلیف‌های این کلاس</h2>
            <div id="asgList"></div>
        </section>
        <article class="qb-exam-view asg-detail" id="asgDetail" hidden></article>
    <?php endif; ?>
</div>
<script src="../../assets/js/supervisor-assignments.js?v=asg4"></script>
<?php require_once 'layout-end.php'; ?>
