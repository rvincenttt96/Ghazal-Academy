<?php
$title = 'جزئیات کلاس';
require_once 'layout.php';
require_once '../../includes/db.php';
require_role('supervisor');

$class_id = (int)($_GET['class_id'] ?? 0);
$section = $_GET['section'] ?? 'students'; // students, attendance, report, session, error

if (!$class_id) {
    header("Location: terms.php?error=کلاسی انتخاب نشده است.");
    exit;
}

// دریافت اطلاعات کلاس
$stmt = $pdo->prepare("
    SELECT c.title, c.schedule, c.schedule_type, c.class_type, c.online_link,
           c.session_plan, c.error_gravity,
           t.title as term_title, t.year,
           u.full_name as teacher_name
    FROM classes c
    JOIN terms t ON c.term_id = t.id
    JOIN users u ON c.teacher_id = u.id
    WHERE c.id = ? AND c.is_deleted = 0
");
$stmt->execute([$class_id]);
$class_info = $stmt->fetch();

if (!$class_info) {
    header("Location: terms.php?error=کلاس یافت نشد.");
    exit;
}
?>

<link href="../../assets/css/supervisor/class-details.css?v=<?= time() ?>" rel="stylesheet">

<div class="dashboard-header">
    <h1 class="mb-0">جزئیات کلاس: <?= htmlspecialchars($class_info['title']) ?></h1>
    <small class="text-muted">
        ترم: <?= htmlspecialchars($class_info['term_title']) ?> (<?= $class_info['year'] ?>) | 
        معلم: <?= htmlspecialchars($class_info['teacher_name']) ?>
    </small>
</div>

<!-- منوی سریع -->
<div class="btn-group mb-4" role="group">
    <a href="?class_id=<?= $class_id ?>&section=students" 
       class="btn btn-outline-primary <?= $section == 'students' ? 'active' : '' ?>">
        دانش‌آموزان
    </a>
    <a href="?class_id=<?= $class_id ?>&section=attendance" 
       class="btn btn-outline-info <?= $section == 'attendance' ? 'active' : '' ?>">
        حضور و غیاب
    </a>
    <a href="?class_id=<?= $class_id ?>&section=report" 
       class="btn btn-outline-success <?= $section == 'report' ? 'active' : '' ?>">
        کارنامه
    </a>
    <a href="?class_id=<?= $class_id ?>&section=session" 
       class="btn btn-outline-warning <?= $section == 'session' ? 'active' : '' ?>">
        Session Plan
    </a>
    <a href="?class_id=<?= $class_id ?>&section=error" 
       class="btn btn-outline-danger <?= $section == 'error' ? 'active' : '' ?>">
        Error Gravity
    </a>
</div>

<!-- دکمه بازگشت -->
<div class="mb-4">
    <a href="javascript:history.back()" class="btn btn-secondary">&larr; بازگشت</a>
</div>

<?php
// نمایش بخش مربوطه
switch ($section) {
    case 'students':
        // لیست دانش‌آموزان
        $stmt = $pdo->prepare("
            SELECT u.id, u.full_name, u.username as student_code
            FROM users u
            JOIN class_students cs ON u.id = cs.user_id
            WHERE cs.class_id = ? AND u.role = 'student' AND u.is_deleted = 0
            ORDER BY u.full_name
        ");
        $stmt->execute([$class_id]);
        $students = $stmt->fetchAll();
        ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">لیست دانش‌آموزان (<?= count($students) ?> نفر)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($students)): ?>
                    <p class="text-center text-muted">دانش‌آموزی در این کلاس وجود ندارد.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>نام دانش‌آموز</th>
                                    <th>کد دانش‌آموز</th>
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
            </div>
        </div>
        <?php
        break;

    case 'attendance':
        // حضور و غیاب
        $stmt = $pdo->prepare("
            SELECT u.full_name, a.attendance_data
            FROM attendance a
            JOIN users u ON a.user_id = u.id
            WHERE a.class_id = ?
            ORDER BY u.full_name
        ");
        $stmt->execute([$class_id]);
        $attendance_records = $stmt->fetchAll();
        ?>
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">حضور و غیاب</h5>
            </div>
            <div class="card-body">
                <?php if (empty($attendance_records)): ?>
                    <p class="text-center text-muted">رکورد حضور و غیاب موجود نیست.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>دانش‌آموز</th>
                                    <th>وضعیت حضور</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance_records as $record): ?>
                                <tr>
                                    <td><?= htmlspecialchars($record['full_name']) ?></td>
                                    <td>
                                        <?php
                                        $attendance_data = json_decode($record['attendance_data'], true);
                                        if ($attendance_data && is_array($attendance_data)) {
                                            echo "تعداد جلسات: " . count($attendance_data);
                                        } else {
                                            echo htmlspecialchars($record['attendance_data'] ?? '-');
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        break;

    case 'report':
        // کارنامه
        $stmt = $pdo->prepare("
            SELECT 
                u.full_name,
                sq.quiz_1, sq.quiz_2, sq.quiz_3, sq.quiz_4, sq.quiz_5, 
                sq.quiz_6, sq.quiz_7, sq.quiz_8, sq.quiz_9,
                ss.supervisor_score,
                ss.submitted_at
            FROM users u
            LEFT JOIN student_quizzes sq ON u.id = sq.user_id AND sq.class_id = ?
            LEFT JOIN supervisor_scores ss ON u.id = ss.user_id AND ss.class_id = ?
            JOIN class_students cs ON u.id = cs.user_id
            WHERE cs.class_id = ? AND u.role = 'student'
            ORDER BY u.full_name
        ");
        $stmt->execute([$class_id, $class_id, $class_id]);
        $grades = $stmt->fetchAll();
        ?>
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">کارنامه</h5>
            </div>
            <div class="card-body">
                <?php if (empty($grades)): ?>
                    <p class="text-center text-muted">نمره‌ای ثبت نشده است.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>دانش‌آموز</th>
                                    <th>کوییز ۱</th>
                                    <th>کوییز ۲</th>
                                    <th>R/W</th>
                                    <th>کوییز ۳</th>
                                    <th>کوییز ۴</th>
                                    <th>کوییز ۵</th>
                                    <th>R/W</th>
                                    <th>حضور</th>
                                    <th>فعالیت</th>
                                    <th>جمع</th>
                                    <th>نمره سوپروایزر</th>
                                    <th>تاریخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grades as $grade): 
                                    $quizzes = [
                                        $grade['quiz_1'], $grade['quiz_2'], $grade['quiz_3'],
                                        $grade['quiz_4'], $grade['quiz_5'], $grade['quiz_6'],
                                        $grade['quiz_7'], $grade['quiz_8'], $grade['quiz_9']
                                    ];
                                    $total = array_sum(array_filter($quizzes, function($v) { return $v !== null; }));
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($grade['full_name']) ?></td>
                                    <?php foreach ($quizzes as $quiz): ?>
                                        <td><?= $quiz !== null ? number_format($quiz, 0) : '-' ?></td>
                                    <?php endforeach; ?>
                                    <td><?= number_format($total, 0) ?></td>
                                    <td><?= $grade['supervisor_score'] !== null ? number_format($grade['supervisor_score'], 0) : '-' ?></td>
                                    <td><?= $grade['submitted_at'] ? date('Y/m/d', strtotime($grade['submitted_at'])) : '-' ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        break;

    case 'session':
        // Session Plan
        ?>
        <div class="card">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">Session Plan</h5>
            </div>
            <div class="card-body">
                <?php if (empty($class_info['session_plan'])): ?>
                    <p class="text-center text-muted">Session Plan ثبت نشده است.</p>
                <?php else: ?>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($class_info['session_plan'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        break;

    case 'error':
        // Error Gravity
        ?>
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">Error Gravity</h5>
            </div>
            <div class="card-body">
                <?php if (empty($class_info['error_gravity'])): ?>
                    <p class="text-center text-muted">Error Gravity ثبت نشده است.</p>
                <?php else: ?>
                    <div class="bg-light p-3 rounded">
                        <?= nl2br(htmlspecialchars($class_info['error_gravity'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
        break;
}
?>

<?php require_once 'layout-end.php'; ?>