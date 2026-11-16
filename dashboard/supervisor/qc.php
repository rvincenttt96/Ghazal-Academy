<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/terms.php';
require_once '../../includes/qc.php';
require_role('supervisor');

$title = 'Teacher QC Management';
$current_page = 'qc.php';
require_once 'layout.php';

qc_ensure_schema($pdo);

// Available terms
$open_terms = term_open_rows($pdo);
$selected_term_id = (int)($_GET['term_id'] ?? 0);
if (!$selected_term_id) {
    $teaching_term = term_pick_teaching($open_terms);
    $selected_term_id = $teaching_term ? (int)$teaching_term['id'] : (!empty($open_terms[0]['id']) ? (int)$open_terms[0]['id'] : 0);
}

// Find all teachers who have classes in this term (or all active teachers)
$st_teachers = $pdo->prepare("
    SELECT DISTINCT u.id, u.full_name, u.username, u.avatar
    FROM users u
    JOIN classes c ON u.id = c.teacher_id
    WHERE u.role = 'teacher'
      AND IFNULL(u.is_deleted, 0) = 0
      AND c.term_id = ?
      AND c.is_deleted = 0
    ORDER BY u.full_name ASC
");
$st_teachers->execute([$selected_term_id]);
$teachers = $st_teachers->fetchAll(PDO::FETCH_ASSOC);

// If no teachers in that term, load all teachers
if (empty($teachers)) {
    $st_all = $pdo->query("SELECT id, full_name, username, avatar FROM users WHERE role = 'teacher' AND IFNULL(is_deleted, 0) = 0 ORDER BY full_name ASC");
    $teachers = $st_all->fetchAll(PDO::FETCH_ASSOC);
}

// Pre-calculate QC data for each teacher
$teacher_data = [];
foreach ($teachers as $t) {
    $t_qc = qc_calculate_teacher_overall($pdo, $t['id'], $selected_term_id);
    $teacher_data[] = [
        'teacher' => $t,
        'qc' => $t_qc,
    ];
}
?>

<style>
.sqc-shell {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding-bottom: 50px;
}
.sqc-header-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1.5px solid rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(14px);
    border-radius: 20px;
    padding: 24px;
}
.sqc-teacher-card {
    background: rgba(255, 255, 255, 0.035);
    border: 1px solid rgba(255, 255, 255, 0.09);
    border-radius: 18px;
    padding: 20px 24px;
    transition: all 0.2s ease;
}
.sqc-teacher-card:hover {
    border-color: rgba(244, 63, 94, 0.35);
}
.sqc-score-badge {
    font-size: 1.35rem;
    font-weight: 900;
    font-family: 'YekanBakhFaNum', sans-serif;
    padding: 6px 16px;
    border-radius: 14px;
    display: inline-block;
}
.sqc-num-input {
    width: 65px;
    text-align: center;
    font-weight: 700;
    font-family: 'YekanBakhFaNum', monospace;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    border-radius: 10px;
    padding: 5px;
}
.sqc-num-input:focus {
    background: rgba(255, 255, 255, 0.15);
    border-color: #f43f5e;
    color: #fff;
    outline: none;
}
.sqc-item-label {
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
    margin-bottom: 3px;
    display: block;
}
</style>

<div class="container-fluid sqc-shell">

    <!-- Header & Filters -->
    <div class="sqc-header-card">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1 class="h3 fw-bold text-white mb-1">🎯 سیستم کنترل کیفیت اساتید (Teacher QC)</h1>
                <p class="text-muted small mb-0">نمرات کلاسی بر اساس الگوریتم ۶ گانه خودکار و ارزیابی مستقیم سوپروایزر (۴۰ نمره)</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <form method="GET" class="d-flex align-items-center gap-2">
                    <label class="text-muted small mb-0">ترم:</label>
                    <select name="term_id" class="form-select form-select-sm bg-dark text-white border-secondary" onchange="this.form.submit()">
                        <?php foreach ($open_terms as $ot): ?>
                            <option value="<?= (int)$ot['id'] ?>" <?= $selected_term_id == $ot['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ot['title'] ?? ('ترم ' . $ot['id'])) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>

        <hr class="my-3 border-secondary">

        <div class="row align-items-center g-3">
            <div class="col-md-6">
                <input type="text" id="teacherSearch" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="جستجوی نام یا نام‌کاربری معلم..." oninput="filterTeachers()">
            </div>
            <div class="col-md-6 text-md-start">
                <span class="badge bg-secondary">تعداد اساتید: <span id="teacherCount"><?= count($teacher_data) ?></span></span>
            </div>
        </div>
    </div>

    <!-- Teacher Cards List -->
    <div class="d-flex flex-column gap-3" id="teachersContainer">
        <?php foreach ($teacher_data as $td): 
            $t = $td['teacher'];
            $qc = $td['qc'];
            $tid = (int)$t['id'];
            $sup = $qc['supervisor_scores'];
            $ov = $qc['overall_mark'];

            $ov_badge_class = 'bg-danger text-white';
            if ($ov >= 90) $ov_badge_class = 'bg-success text-white';
            elseif ($ov >= 75) $ov_badge_class = 'bg-info text-dark';
            elseif ($ov >= 60) $ov_badge_class = 'bg-warning text-dark';
        ?>
        <div class="sqc-teacher-card" data-teacher-name="<?= htmlspecialchars(mb_strtolower($t['full_name'] . ' ' . $t['username'], 'UTF-8')) ?>">
            <div class="row align-items-center g-3">
                <!-- Teacher Info -->
                <div class="col-lg-3 col-md-4">
                    <div class="d-flex align-items-center gap-3">
                        <img src="<?= $t['avatar'] ? htmlspecialchars($t['avatar']) : '../../uploads/avatars/daweid-icon-7797704_640.png' ?>" 
                             class="rounded-circle" width="50" height="50" style="object-fit:cover;">
                        <div>
                            <div class="fw-bold text-white"><?= htmlspecialchars($t['full_name']) ?></div>
                            <small class="text-muted"><?= htmlspecialchars($t['username']) ?> · <?= count($qc['classes']) ?> کلاس</small>
                        </div>
                    </div>
                </div>

                <!-- Automated Class Average -->
                <div class="col-lg-2 col-md-3 text-center">
                    <span class="sqc-item-label">میانگین کلاس‌ها</span>
                    <span class="h5 fw-bold text-warning mb-0" style="font-family:'YekanBakhFaNum'"><?= $qc['classes_avg_60'] ?></span>
                    <small class="text-muted"> / ۶۰</small>
                </div>

                <!-- Supervisor 4 Manual Inputs -->
                <div class="col-lg-5 col-md-5">
                    <form onsubmit="saveSupervisorScores(event, <?= $tid ?>)" class="d-flex align-items-center justify-content-between flex-wrap gap-2" id="form-sup-<?= $tid ?>">
                        <input type="hidden" name="teacher_id" value="<?= $tid ?>">
                        <input type="hidden" name="term_id" value="<?= $selected_term_id ?>">

                        <div>
                            <span class="sqc-item-label">Observation</span>
                            <input type="number" step="0.5" min="0" max="10" name="observation" class="sqc-num-input" value="<?= $sup['observation'] ?>">
                        </div>
                        <div>
                            <span class="sqc-item-label">Poll</span>
                            <input type="number" step="0.5" min="0" max="10" name="poll" class="sqc-num-input" value="<?= $sup['poll'] ?>">
                        </div>
                        <div>
                            <span class="sqc-item-label">Subs</span>
                            <input type="number" step="0.5" min="0" max="10" name="subs" class="sqc-num-input" value="<?= $sup['subs'] ?>">
                        </div>
                        <div>
                            <span class="sqc-item-label">Attendance</span>
                            <input type="number" step="0.5" min="0" max="10" name="courses_attendance" class="sqc-num-input" value="<?= $sup['courses_attendance'] ?>">
                        </div>

                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn btn-sm btn-outline-info" title="ذخیره نمرات ارزیابی">
                                ✓ ذخیره
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Overall Mark & Drilldown Button -->
                <div class="col-lg-2 col-md-12 text-center text-lg-start d-flex align-items-center justify-content-center justify-content-lg-end gap-3">
                    <div class="text-center">
                        <span class="sqc-item-label">نمره کل QC</span>
                        <span class="sqc-score-badge <?= $ov_badge_class ?>" id="badge-overall-<?= $tid ?>"><?= $ov ?></span>
                    </div>
                    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#drillDownSup-<?= $tid ?>">
                        کلاس‌ها ▼
                    </button>
                </div>
            </div>

            <!-- Drilldown of Classes for this teacher -->
            <div class="collapse mt-3" id="drillDownSup-<?= $tid ?>">
                <div class="p-3 rounded" style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.06);">
                    <h6 class="text-white fw-bold mb-3">ریز نمرات ۶ پارامتر کلاسی برای اساتید:</h6>
                    <?php if (empty($qc['classes'])): ?>
                        <small class="text-muted">کلاسی در این ترم برای این استاد یافت نشد.</small>
                    <?php else: ?>
                        <div class="row g-2">
                            <?php foreach ($qc['classes'] as $c): 
                                $it = $c['items'];
                            ?>
                            <div class="col-md-6">
                                <div class="p-2 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-white small"><?= htmlspecialchars($c['class_title']) ?></strong>
                                        <span class="badge bg-dark border border-secondary"><?= $c['total_score'] ?> / ۶۰</span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap small">
                                        <span class="badge bg-secondary">رول‌کال: <?= $it['roll_call']['score'] ?>/۱۰</span>
                                        <span class="badge bg-secondary">کوییز: <?= $it['quizzes']['score'] ?>/۱۰</span>
                                        <span class="badge bg-secondary">سشن پلن: <?= $it['session_plan']['score'] ?>/۱۰</span>
                                        <span class="badge bg-secondary">ارور گرویتی: <?= $it['error_gravity']['score'] ?>/۱۰</span>
                                        <span class="badge bg-secondary">تکالیف: <?= $it['assignments']['score'] ?>/۱۰</span>
                                        <span class="badge bg-secondary">ویس پلن: <?= $it['voice_plan']['score'] ?>/۱۰</span>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
function filterTeachers() {
    const q = (document.getElementById('teacherSearch').value || '').trim().toLowerCase();
    const cards = document.querySelectorAll('.sqc-teacher-card');
    let visible = 0;
    cards.forEach(c => {
        const name = c.getAttribute('data-teacher-name') || '';
        if (q === '' || name.includes(q)) {
            c.style.display = 'block';
            visible++;
        } else {
            c.style.display = 'none';
        }
    });
    document.getElementById('teacherCount').textContent = visible;
}

function saveSupervisorScores(e, teacherId) {
    e.preventDefault();
    const form = document.getElementById('form-sup-teacher-' + teacherId) || document.getElementById('form-sup-' + teacherId);
    const fd = new FormData(form);

    fetch('ajax_qc_save.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const badge = document.getElementById('badge-overall-' + teacherId);
            if (badge && data.overall_mark !== undefined) {
                badge.textContent = data.overall_mark;
                badge.className = 'sqc-score-badge ' + (
                    data.overall_mark >= 90 ? 'bg-success text-white' :
                    data.overall_mark >= 75 ? 'bg-info text-dark' :
                    data.overall_mark >= 60 ? 'bg-warning text-dark' : 'bg-danger text-white'
                );
            }
            alert(data.message || 'ارزیابی ذخیره شد.');
        } else {
            alert('خطا: ' + (data.message || 'خطا در ذخیره'));
        }
    })
    .catch(err => {
        alert('خطا در برقراری ارتباط با سرور.');
    });
}
</script>

<?php require_once 'layout-end.php'; ?>
