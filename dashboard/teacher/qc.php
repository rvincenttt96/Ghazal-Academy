<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/terms.php';
require_once '../../includes/qc.php';
require_role('teacher');

$teacher_id = (int)($_SESSION['user_id'] ?? 0);
$title = 'Teacher QC';
$current_page = 'qc.php';
require_once 'layout.php';

qc_ensure_schema($pdo);

// Teaching term
$teaching_term = term_pick_teaching(term_open_rows($pdo));
$term_id = $teaching_term ? (int)$teaching_term['id'] : 0;
$term_title = $teaching_term ? ($teaching_term['title'] ?? 'ترم جاری') : 'ترم جاری';

$qc = qc_calculate_teacher_overall($pdo, $teacher_id, $term_id);
$overall = $qc['overall_mark'];
$classes_avg = $qc['classes_avg_60'];
$sup_scores = $qc['supervisor_scores'];
$sup_total = $qc['supervisor_total_40'];

$badge_text = 'نیاز به بهبود';
$badge_class = 'bg-danger';
if ($overall >= 90) {
    $badge_text = 'عالی (Excellent)';
    $badge_class = 'bg-success';
} elseif ($overall >= 75) {
    $badge_text = 'خوب (Good)';
    $badge_class = 'bg-info text-dark';
} elseif ($overall >= 60) {
    $badge_text = 'متوسط (Acceptable)';
    $badge_class = 'bg-warning text-dark';
}
?>

<style>
.qc-shell {
    display: flex;
    flex-direction: column;
    gap: 24px;
    padding-bottom: 50px;
}
.qc-hero-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0.02) 100%);
    border: 1.5px solid rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(16px);
    border-radius: 24px;
    padding: 30px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.3);
}
.qc-score-big {
    font-size: 3.4rem;
    font-weight: 900;
    line-height: 1;
    font-family: 'YekanBakhFaNum', sans-serif;
    background: linear-gradient(135deg, #f43f5e, #fb7185, #38bdf8);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.qc-stat-box {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 16px;
    padding: 16px 20px;
    text-align: center;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-sizing: border-box;
}
.qc-class-card {
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 22px 24px;
    transition: all 0.25s ease;
}
.qc-class-card:hover {
    border-color: rgba(244, 63, 94, 0.4);
    transform: translateY(-2px);
}
.qc-item-pill {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 14px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    min-height: 112px;
    box-sizing: border-box;
    transition: all 0.2s ease;
}
.qc-item-pill.is-perfect {
    border-color: rgba(34, 197, 94, 0.4);
    background: rgba(34, 197, 94, 0.06);
}
.qc-item-pill.has-deduction {
    border-color: rgba(244, 63, 94, 0.4);
    background: rgba(244, 63, 94, 0.07);
}
.qc-pill-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 22px;
}
.qc-pill-title {
    font-size: 0.82rem;
    color: #94a3b8;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.qc-pill-body {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin: 4px 0;
}
.qc-pill-score {
    font-size: 1.3rem;
    font-weight: 800;
    font-family: 'YekanBakhFaNum', sans-serif;
    color: #fff;
    line-height: 1.1;
}
.qc-pill-footer {
    min-height: 24px;
    display: flex;
    align-items: center;
    justify-content: flex-start;
}
.qc-deduction-badge {
    background: rgba(225, 29, 72, 0.2);
    color: #fb7185;
    font-size: 0.72rem;
    padding: 2px 7px;
    border-radius: 6px;
    font-weight: 700;
    border: 1px solid rgba(225, 29, 72, 0.4);
    white-space: nowrap;
    display: inline-block;
}
.qc-perfect-badge {
    background: rgba(34, 197, 94, 0.15);
    color: #4ade80;
    font-size: 0.72rem;
    padding: 2px 7px;
    border-radius: 6px;
    font-weight: 700;
    border: 1px solid rgba(34, 197, 94, 0.3);
    white-space: nowrap;
    display: inline-block;
}
.qc-status-badge {
    background: rgba(148, 163, 184, 0.12);
    color: #94a3b8;
    font-size: 0.7rem;
    padding: 2px 6px;
    border-radius: 6px;
    border: 1px solid rgba(148, 163, 184, 0.25);
    white-space: nowrap;
    display: inline-block;
}
.qc-sup-card {
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    padding: 24px;
}
</style>

<div class="container-fluid qc-shell">

    <!-- Hero Card -->
    <div class="qc-hero-card">
        <div class="row align-items-center g-4">
            <div class="col-lg-5 text-center text-lg-end">
                <span class="badge bg-secondary mb-2 px-3 py-2"><?= htmlspecialchars($term_title) ?></span>
                <h1 class="h3 fw-bold text-white mb-2">سیستم کنترل کیفیت اساتید (Teacher QC)</h1>
                <p class="text-muted small mb-3">ارزیابی خودکار پارامترهای کلاسی به همراه بازخورد و ارزیابی مستقیم سوپروایزر</p>
                <div class="d-flex align-items-center justify-content-center justify-content-lg-start gap-3">
                    <span class="badge <?= $badge_class ?> px-3 py-2 fs-6"><?= $badge_text ?></span>
                    <span class="text-muted small">تعداد کلاس‌های فعال: <strong><?= count($qc['classes']) ?></strong></span>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="row g-3 align-items-stretch">
                    <div class="col-md-4 d-flex flex-column">
                        <div class="qc-stat-box">
                            <small class="text-muted d-block mb-1">نمره کل QC Overall</small>
                            <div class="qc-score-big"><?= $overall ?></div>
                            <small class="text-muted">از ۱۰۰ نمره</small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <div class="qc-stat-box">
                            <small class="text-muted d-block mb-1">میانگین کلاس‌ها</small>
                            <div class="h2 fw-bold text-white mb-0" style="font-family:'YekanBakhFaNum'"><?= $classes_avg ?></div>
                            <small class="text-muted">از ۶۰ نمره</small>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex flex-column">
                        <div class="qc-stat-box">
                            <small class="text-muted d-block mb-1">ارزیابی سوپروایزر</small>
                            <div class="h2 fw-bold text-info mb-0" style="font-family:'YekanBakhFaNum'"><?= $sup_total ?></div>
                            <small class="text-muted">از ۴۰ نمره</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supervisor Section (40 marks) -->
    <div class="qc-sup-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
            <div>
                <h3 class="h5 fw-bold text-white mb-1">📋 ارزیابی مستقیم سوپروایزر (Supervisor Assessment)</h3>
                <small class="text-muted">۴ آیتم کلی ارزیابی برای ترم جاری (مجموعاً ۴۰ نمره)</small>
            </div>
            <span class="badge bg-info text-dark px-3 py-2 fs-6">نمره دریافتی: <?= $sup_total ?> از ۴۰</span>
        </div>

        <div class="row g-3 align-items-stretch">
            <div class="col-md-3 col-sm-6 d-flex flex-column">
                <div class="qc-item-pill <?= $sup_scores['observation'] >= 10 ? 'is-perfect' : '' ?>">
                    <div class="qc-pill-header">
                        <span class="qc-pill-title">👁 ارزیابی کلاسی (Observation)</span>
                    </div>
                    <div class="qc-pill-body">
                        <span class="qc-pill-score"><?= $sup_scores['observation'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                    </div>
                    <div class="qc-pill-footer">
                        <?php if ($sup_scores['observation'] >= 10): ?>
                            <span class="qc-perfect-badge">✓ کامل</span>
                        <?php elseif ($sup_scores['observation'] > 0): ?>
                            <span class="qc-status-badge">ثبت شده</span>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:0.72rem;">در انتظار ثبت</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 d-flex flex-column">
                <div class="qc-item-pill <?= $sup_scores['poll'] >= 10 ? 'is-perfect' : '' ?>">
                    <div class="qc-pill-header">
                        <span class="qc-pill-title">📊 نظرسنجی زبان‌آموزان (Poll)</span>
                    </div>
                    <div class="qc-pill-body">
                        <span class="qc-pill-score"><?= $sup_scores['poll'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                    </div>
                    <div class="qc-pill-footer">
                        <?php if ($sup_scores['poll'] >= 10): ?>
                            <span class="qc-perfect-badge">✓ کامل</span>
                        <?php elseif ($sup_scores['poll'] > 0): ?>
                            <span class="qc-status-badge">ثبت شده</span>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:0.72rem;">در انتظار ثبت</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 d-flex flex-column">
                <div class="qc-item-pill <?= $sup_scores['subs'] >= 10 ? 'is-perfect' : '' ?>">
                    <div class="qc-pill-header">
                        <span class="qc-pill-title">🔄 همکاری و ساب (Subs)</span>
                    </div>
                    <div class="qc-pill-body">
                        <span class="qc-pill-score"><?= $sup_scores['subs'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                    </div>
                    <div class="qc-pill-footer">
                        <?php if ($sup_scores['subs'] >= 10): ?>
                            <span class="qc-perfect-badge">✓ کامل</span>
                        <?php elseif ($sup_scores['subs'] > 0): ?>
                            <span class="qc-status-badge">ثبت شده</span>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:0.72rem;">در انتظار ثبت</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 d-flex flex-column">
                <div class="qc-item-pill <?= $sup_scores['courses_attendance'] >= 10 ? 'is-perfect' : '' ?>">
                    <div class="qc-pill-header">
                        <span class="qc-pill-title">📅 دوره‌ها و جلسات (Courses Attendance)</span>
                    </div>
                    <div class="qc-pill-body">
                        <span class="qc-pill-score"><?= $sup_scores['courses_attendance'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                    </div>
                    <div class="qc-pill-footer">
                        <?php if ($sup_scores['courses_attendance'] >= 10): ?>
                            <span class="qc-perfect-badge">✓ کامل</span>
                        <?php elseif ($sup_scores['courses_attendance'] > 0): ?>
                            <span class="qc-status-badge">ثبت شده</span>
                        <?php else: ?>
                            <span class="text-muted" style="font-size:0.72rem;">در انتظار ثبت</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($sup_scores['notes'])): ?>
            <div class="mt-4 p-3 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08);">
                <small class="text-muted d-block mb-1">یادداشت سوپروایزر:</small>
                <div class="text-white small"><?= nl2br(htmlspecialchars($sup_scores['notes'])) ?></div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Classes Automated QC Section (60 marks per class) -->
    <div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h3 class="h5 fw-bold text-white mb-1">🏫 عملکرد کلاس‌ها (۶ پارامتر خودکار - هر کلاس ۶۰ نمره)</h3>
                <small class="text-muted">نمرات بر اساس زمان‌بندی دقیق هر جلسه و موعد مقرر محاسبه می‌گردد.</small>
            </div>
        </div>

        <?php if (empty($qc['classes'])): ?>
            <div class="alert alert-secondary">هیچ کلاس فعالی برای شما در این ترم ثبت نشده است.</div>
        <?php else: ?>
            <div class="d-flex flex-column gap-3">
                <?php foreach ($qc['classes'] as $idx => $cl): 
                    $cid = $cl['class_id'];
                    $items = $cl['items'];
                ?>
                <div class="qc-class-card">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-secondary">کلاس <?= $idx + 1 ?></span>
                            <h4 class="h6 fw-bold text-white mb-0"><?= htmlspecialchars($cl['class_title']) ?></h4>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-dark border border-secondary px-3 py-2 fs-6">
                                نمره کلاس: <strong class="text-warning"><?= $cl['total_score'] ?></strong> / ۶۰
                            </span>
                            <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#drillDown-<?= $cid ?>">
                                ریز گزارش جلسات ▼
                            </button>
                        </div>
                    </div>

                    <!-- 6 items grid -->
                    <div class="row g-2 align-items-stretch">
                        <div class="col-md-2 col-6 d-flex flex-column">
                            <div class="qc-item-pill <?= $items['roll_call']['score'] >= 10 ? 'is-perfect' : 'has-deduction' ?>">
                                <div class="qc-pill-header">
                                    <span class="qc-pill-title">۱. رول‌کال</span>
                                </div>
                                <div class="qc-pill-body">
                                    <span class="qc-pill-score"><?= $items['roll_call']['score'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                                </div>
                                <div class="qc-pill-footer">
                                    <?php if (!empty($items['roll_call']['deductions'])): ?>
                                        <span class="qc-deduction-badge"><?= count($items['roll_call']['deductions']) ?> مورد تأخیر</span>
                                    <?php else: ?>
                                        <span class="qc-perfect-badge">✓ کامل</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 d-flex flex-column">
                            <div class="qc-item-pill <?= $items['quizzes']['score'] >= 10 ? 'is-perfect' : 'has-deduction' ?>">
                                <div class="qc-pill-header">
                                    <span class="qc-pill-title">۲. نمره‌دهی‌ها</span>
                                </div>
                                <div class="qc-pill-body">
                                    <span class="qc-pill-score"><?= $items['quizzes']['score'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                                </div>
                                <div class="qc-pill-footer">
                                    <?php if (!empty($items['quizzes']['deductions'])): ?>
                                        <span class="qc-deduction-badge"><?= count($items['quizzes']['deductions']) ?> مورد تأخیر</span>
                                    <?php else: ?>
                                        <span class="qc-perfect-badge">✓ کامل</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 d-flex flex-column">
                            <div class="qc-item-pill <?= $items['session_plan']['score'] >= 10 ? 'is-perfect' : 'has-deduction' ?>">
                                <div class="qc-pill-header">
                                    <span class="qc-pill-title">۳. سشن پلن</span>
                                </div>
                                <div class="qc-pill-body">
                                    <span class="qc-pill-score"><?= $items['session_plan']['score'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                                </div>
                                <div class="qc-pill-footer">
                                    <?php if (!empty($items['session_plan']['deductions'])): ?>
                                        <span class="qc-deduction-badge"><?= count($items['session_plan']['deductions']) ?> مورد تأخیر</span>
                                    <?php else: ?>
                                        <span class="qc-perfect-badge">✓ کامل</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 d-flex flex-column">
                            <div class="qc-item-pill <?= $items['error_gravity']['score'] >= 10 ? 'is-perfect' : 'has-deduction' ?>">
                                <div class="qc-pill-header">
                                    <span class="qc-pill-title">۴. ارور گرویتی</span>
                                </div>
                                <div class="qc-pill-body">
                                    <span class="qc-pill-score"><?= $items['error_gravity']['score'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                                </div>
                                <div class="qc-pill-footer">
                                    <?php if ($items['error_gravity']['score'] >= 10): ?>
                                        <span class="qc-perfect-badge">✓ ثبت شده</span>
                                    <?php else: ?>
                                        <span class="qc-deduction-badge"><?= ($items['error_gravity']['status'] ?? '') === 'pending' ? 'در انتظار ثبت' : 'عدم ثبت' ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 d-flex flex-column">
                            <div class="qc-item-pill <?= $items['assignments']['score'] >= 10 ? 'is-perfect' : 'has-deduction' ?>">
                                <div class="qc-pill-header">
                                    <span class="qc-pill-title">۵. بررسی تکالیف</span>
                                </div>
                                <div class="qc-pill-body">
                                    <span class="qc-pill-score"><?= $items['assignments']['score'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                                </div>
                                <div class="qc-pill-footer">
                                    <?php if (!empty($items['assignments']['deductions'])): ?>
                                        <span class="qc-deduction-badge"><?= count($items['assignments']['deductions']) ?> مورد تأخیر</span>
                                    <?php else: ?>
                                        <span class="qc-perfect-badge">✓ کامل</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 col-6 d-flex flex-column">
                            <div class="qc-item-pill <?= $items['voice_plan']['score'] >= 10 ? 'is-perfect' : 'has-deduction' ?>">
                                <div class="qc-pill-header">
                                    <span class="qc-pill-title">۶. ویس پلن کلی</span>
                                </div>
                                <div class="qc-pill-body">
                                    <span class="qc-pill-score"><?= $items['voice_plan']['score'] ?> <small class="text-muted fs-6">/ ۱۰</small></span>
                                </div>
                                <div class="qc-pill-footer">
                                    <?php if ($items['voice_plan']['score'] >= 10): ?>
                                        <span class="qc-perfect-badge">✓ ثبت شده</span>
                                    <?php else: ?>
                                        <span class="qc-deduction-badge"><?= ($items['voice_plan']['status'] ?? '') === 'pending' ? 'در انتظار ارسال' : 'عدم ارسال' ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Collapsible Drill-down -->
                    <div class="collapse mt-3" id="drillDown-<?= $cid ?>">
                        <div class="p-3 rounded" style="background: rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.06);">
                            <h6 class="text-info fw-bold mb-2">جزئیات و دلایل کسر نمره:</h6>
                            <ul class="list-unstyled mb-0 small">
                                <?php
                                $has_any_deduction = false;
                                foreach ($items['roll_call']['deductions'] as $d) {
                                    $has_any_deduction = true;
                                    echo "<li class='text-danger mb-1'>• رول‌کال: {$d['reason']} (-۱ نمره)</li>";
                                }
                                foreach ($items['quizzes']['deductions'] as $d) {
                                    $has_any_deduction = true;
                                    echo "<li class='text-danger mb-1'>• نمرات: {$d['reason']} (-۱ نمره)</li>";
                                }
                                foreach ($items['session_plan']['deductions'] as $d) {
                                    $has_any_deduction = true;
                                    echo "<li class='text-danger mb-1'>• سشن پلن: {$d['reason']} (-۱ نمره)</li>";
                                }
                                if ($items['error_gravity']['score'] == 0) {
                                    $has_any_deduction = true;
                                    echo "<li class='text-danger mb-1'>• ارور گرویتی: {$items['error_gravity']['reason']} (۰ از ۱۰)</li>";
                                }
                                foreach ($items['assignments']['deductions'] as $d) {
                                    $has_any_deduction = true;
                                    echo "<li class='text-danger mb-1'>• بررسی تکالیف: {$d['reason']} (-۱ نمره)</li>";
                                }
                                if ($items['voice_plan']['score'] == 0) {
                                    $has_any_deduction = true;
                                    echo "<li class='text-danger mb-1'>• ویس پلن کلی: {$items['voice_plan']['reason']} (۰ از ۱۰)</li>";
                                }
                                if (!$has_any_deduction) {
                                    echo "<li class='text-success'>✓ تبریک! هیچ کسر نمره‌ای در این کلاس ثبت نشده و تمام پارامترها سر وقت انجام شده‌اند.</li>";
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'layout-end.php'; ?>
