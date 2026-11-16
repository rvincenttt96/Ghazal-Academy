<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/terms.php';
require_once '../../includes/term-plan.php';
require_role('supervisor');

$class_id = (int)($_GET['class_id'] ?? 0);
$class = $class_id ? term_plan_find_class($pdo, $class_id) : null;
if (!$class) {
    header('Location: term-plan.php?error=' . urlencode('کلاس یافت نشد.'));
    exit;
}

$title = 'Term Plan';
$current_page = 'term-plan-details.php';
require_once 'layout.php';

list($progress, $sessions) = term_plan_sessions_payload($pdo, $class);
$badge = term_plan_day_badge($class);
$time = term_plan_display_time($class);

// Load General Term Plan voice if any
require_once '../../includes/qc.php';
qc_ensure_schema($pdo);
$st_v = $pdo->prepare("SELECT * FROM term_plan_voice WHERE class_id = ? AND IFNULL(is_deleted, 0) = 0 ORDER BY id DESC LIMIT 1");
$st_v->execute([(int)$class['id']]);
$tp_voice = $st_v->fetch(PDO::FETCH_ASSOC);
?>
<link href="../../assets/css/term-plan.css?v=tp2" rel="stylesheet">
<style>
.tp-voice-card {
    background: rgba(255, 255, 255, 0.04);
    border: 1px solid rgba(255, 255, 255, 0.12);
    backdrop-filter: blur(12px);
    border-radius: 18px;
    padding: 20px 24px;
    margin-bottom: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}
.tp-voice-head {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 14px;
}
.tp-voice-head h2 {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
    color: #fff;
}
.tp-voice-pill {
    background: rgba(225, 29, 72, 0.15);
    color: #fb7185;
    border: 1px solid rgba(225, 29, 72, 0.3);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 700;
}
.tp-audio-player {
    width: 100%;
    max-width: 500px;
    border-radius: 30px;
}
</style>
<div class="tp-page"
     data-tp-root
     data-role="supervisor"
     data-class-id="<?= (int)$class['id'] ?>"
     data-ajax="ajax-term-plan.php"
     data-methods="<?= term_plan_h(json_encode(term_plan_methods(), JSON_UNESCAPED_UNICODE)) ?>">

    <div class="tp-head">
        <div class="tp-crumb">
            <a class="tp-btn tp-btn-ghost" href="term-plan.php">بازگشت</a>
            <div>
                <h1><span>Term</span> Plan</h1>
                <p><?= term_plan_h($class['title']) ?> · <?= term_plan_h($class['teacher_name'] ?? '') ?></p>
            </div>
        </div>
        <div class="tp-meta">
            <div class="tp-badges">
                <?= term_plan_type_badge_html($class) ?>
                <span class="day-badge <?= $badge[0] ?>"><?= term_plan_h($badge[1]) ?></span>
                <span class="time-badge"><?= term_plan_h($time) ?></span>
            </div>
        </div>
    </div>

    <!-- General Term Plan Voice Section (QC Item) -->
    <div class="tp-voice-card">
        <div class="tp-voice-head">
            <h2>🎙 General Term Plan Voice (ویس پلن کلی ترم معلم)</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="tp-voice-pill">مهلت: قبل از جلسه ۳ (۱۰ نمره QC)</span>
                <?php if ($tp_voice): ?>
                    <span class="badge bg-success">✓ بارگذاری شده</span>
                <?php else: ?>
                    <span class="badge bg-danger">هنوز بارگذاری نشده</span>
                <?php endif; ?>
            </div>
        </div>
        <div>
            <?php if ($tp_voice): ?>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <audio class="tp-audio-player" controls src="<?= htmlspecialchars($tp_voice['audio_path']) ?>"></audio>
                    <?php if (!empty($tp_voice['recorded_at'])): ?>
                        <small class="text-info">تاریخ ثبت: <?= htmlspecialchars($tp_voice['recorded_at']) ?></small>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p class="text-muted small mb-0">معلم هنوز ویس پلن کلی را برای این کلاس آپلود/ضبط نکرده است.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="tp-sessions">
        <?php foreach ($sessions as $session):
            $n = (int)$session['session_no'];
            $elapsed_cls = !empty($session['elapsed']) ? ' is-elapsed' : '';
        ?>
        <article class="tp-session<?= $elapsed_cls ?>" data-session="<?= $n ?>">
            <div class="tp-session-top">
                <h3 class="tp-session-no">Session <em><?= sprintf('%02d', $n) ?></em></h3>
                <?php if ($session['date_label'] !== ''): ?>
                    <div class="tp-session-date"><?= term_plan_h($session['date_label']) ?></div>
                <?php endif; ?>
            </div>

            <div class="tp-sup-box<?= trim((string)$session['supervisor_remark']) !== '' ? ' has-remark' : '' ?>">
                <div class="tp-sup-head">
                    <label>Supervisor Remark</label>
                    <span class="tp-sup-save-status" aria-live="polite"></span>
                </div>
                <textarea class="tp-sup-remark" rows="3" placeholder="ریمارک این جلسه برای معلم"><?= term_plan_h($session['supervisor_remark']) ?></textarea>
                <button type="button" class="tp-btn tp-btn-red tp-sup-save">ذخیره ریمارک</button>
            </div>

            <div class="tp-tasks">
                <?php if (!$session['tasks']): ?>
                    <p class="tp-no-tasks tp-sup-empty">هنوز تسکی برای این جلسه نیست. از اکسل یا Add Task اضافه کنید.</p>
                <?php else: ?>
                    <?php foreach ($session['tasks'] as $task): ?>
                        <?= term_plan_render_task_row($task, 'supervisor') ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="tp-add">
                <input class="tp-input tp-add-input" placeholder="تسک جدید برای این جلسه">
                <button type="button" class="tp-btn tp-btn-red tp-add-btn">Add Task</button>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</div>
<script src="../../assets/js/term-plan.js?v=tp2"></script>
<?php require_once 'layout-end.php'; ?>
