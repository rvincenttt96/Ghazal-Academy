<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/terms.php';
require_once '../../includes/term-plan.php';
require_role('teacher');

$teacher_id = (int)($_SESSION['user_id'] ?? 0);
$class_id = (int)($_GET['class_id'] ?? 0);
$class = $class_id ? term_plan_find_class($pdo, $class_id, $teacher_id) : null;
if (!$class) {
    header('Location: term-plan.php?error=' . urlencode('کلاس مورد نظر یافت نشد.'));
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
    margin-bottom: 16px;
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
.tp-voice-body {
    display: flex;
    flex-direction: column;
    gap: 14px;
}
.tp-voice-controls {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}
.tp-rec-btn {
    background: linear-gradient(135deg, #e11d48, #be123c);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 12px;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.tp-rec-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(225, 29, 72, 0.4);
    color: white;
}
.tp-rec-btn.is-recording {
    background: #dc2626;
    animation: tpPulse 1.2s infinite;
}
@keyframes tpPulse {
    0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0.7); }
    70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(220, 38, 38, 0); }
    100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 38, 38, 0); }
}
.tp-voice-timer {
    font-family: monospace;
    font-size: 1.1rem;
    font-weight: 700;
    color: #f43f5e;
    display: none;
}
.tp-file-btn {
    background: rgba(255, 255, 255, 0.08);
    color: #e2e8f0;
    border: 1px solid rgba(255, 255, 255, 0.15);
    padding: 10px 18px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}
.tp-file-btn:hover {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}
.tp-audio-player {
    width: 100%;
    max-width: 500px;
    border-radius: 30px;
}
</style>

<div class="tp-page"
     data-tp-root
     data-role="teacher"
     data-class-id="<?= (int)$class['id'] ?>"
     data-ajax="ajax-term-plan.php"
     data-methods="<?= term_plan_h(json_encode(term_plan_methods(), JSON_UNESCAPED_UNICODE)) ?>">

    <div class="tp-head">
        <div class="tp-crumb">
            <a class="tp-btn tp-btn-ghost" href="term-plan.php">بازگشت</a>
            <div>
                <h1><span>Term</span> Plan</h1>
                <p><?= term_plan_h($class['title']) ?> · جلسه <?= (int)$progress['elapsed'] ?> از <?= (int)$progress['total'] ?></p>
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

    <!-- General Term Plan Voice Section (QC Item: 10 marks before S.3) -->
    <div class="tp-voice-card" id="tpVoiceSection">
        <div class="tp-voice-head">
            <h2>🎙 General Term Plan Voice (ویس پلن کلی ترم)</h2>
            <div class="d-flex align-items-center gap-2">
                <span class="tp-voice-pill">مهلت: قبل از جلسه ۳ (۱۰ نمره QC)</span>
                <?php if ($tp_voice): ?>
                    <span class="badge bg-success">✓ ثبت شده</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark">در انتظار ضبط</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="tp-voice-body">
            <p class="text-muted small mb-1">معلم گرامی، لطفاً پلن کلی ترم، متد تدریس و اهداف آموزشی کلاس را در قالب ویس ضبط یا آپلود فرمایید. این ویس توسط سوپروایزر بررسی خواهد شد.</p>

            <div id="tpVoicePlayerWrap" style="<?= $tp_voice ? '' : 'display:none;' ?>">
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <audio id="tpVoiceAudio" class="tp-audio-player" controls src="<?= $tp_voice ? htmlspecialchars($tp_voice['audio_path']) : '' ?>"></audio>
                    <?php if ($tp_voice && !empty($tp_voice['recorded_at'])): ?>
                        <small class="text-info">تاریخ ثبت: <?= htmlspecialchars($tp_voice['recorded_at']) ?></small>
                    <?php endif; ?>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleReRecord()">تغییر ویس</button>
                </div>
            </div>

            <div id="tpVoiceRecordWrap" style="<?= $tp_voice ? 'display:none;' : '' ?>">
                <div class="tp-voice-controls">
                    <button type="button" class="tp-rec-btn" id="btnRecordToggle" onclick="toggleMicRecording()">
                        <span id="recIcon">🔴</span> <span id="recText">شروع ضبط مستقیم</span>
                    </button>
                    <span class="tp-voice-timer" id="tpTimer">00:00</span>

                    <label class="tp-file-btn mb-0">
                        📁 انتخاب فایل صوتی
                        <input type="file" id="audioFileInput" accept="audio/*" style="display:none;" onchange="handleAudioFileSelect(this)">
                    </label>

                    <button type="button" class="btn btn-success" id="btnSaveVoice" style="display:none;" onclick="saveSelectedAudio()">
                        ✓ ذخیره و ارسال ویس
                    </button>
                </div>
                <div id="previewWrap" class="mt-2" style="display:none;">
                    <small class="text-muted d-block mb-1">پیش‌نمایش قبل از ارسال:</small>
                    <audio id="tpVoicePreview" class="tp-audio-player" controls></audio>
                </div>
            </div>
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

            <?php $sup_remark = trim((string)$session['supervisor_remark']); ?>
            <div class="tp-sup-box<?= $sup_remark !== '' ? ' has-remark' : '' ?>">
                <label>Supervisor Remark</label>
                <?php if ($sup_remark !== ''): ?>
                    <div class="tp-sup-text"><?= nl2br(term_plan_h($sup_remark), false) ?></div>
                <?php else: ?>
                    <p class="tp-sup-empty">سوپروایزر هنوز ریمارکی برای این جلسه ننوشته.</p>
                <?php endif; ?>
            </div>

            <div class="tp-tasks">
                <?php if (!$session['tasks']): ?>
                    <p class="tp-no-tasks tp-sup-empty">سوپروایزر تسکی تعیین نکرده. از Add Task استفاده کنید.</p>
                <?php else: ?>
                    <?php foreach ($session['tasks'] as $task): ?>
                        <?= term_plan_render_task_row($task, 'teacher') ?>
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
<script>
let mediaRecorder = null;
let audioChunks = [];
let recordedBlob = null;
let selectedFile = null;
let timerInterval = null;
let recordSeconds = 0;

function toggleReRecord() {
    document.getElementById('tpVoiceRecordWrap').style.display = 'block';
}

function updateTimerDisplay() {
    const m = String(Math.floor(recordSeconds / 60)).padStart(2, '0');
    const s = String(recordSeconds % 60).padStart(2, '0');
    document.getElementById('tpTimer').textContent = `${m}:${s}`;
}

async function toggleMicRecording() {
    const btn = document.getElementById('btnRecordToggle');
    const timer = document.getElementById('tpTimer');
    const recText = document.getElementById('recText');
    const recIcon = document.getElementById('recIcon');

    if (mediaRecorder && mediaRecorder.state === 'recording') {
        mediaRecorder.stop();
        btn.classList.remove('is-recording');
        recText.textContent = 'ضبط مجدد';
        recIcon.textContent = '🔄';
        clearInterval(timerInterval);
        return;
    }

    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        audioChunks = [];
        mediaRecorder = new MediaRecorder(stream);

        mediaRecorder.ondataavailable = e => {
            if (e.data.size > 0) audioChunks.push(e.data);
        };

        mediaRecorder.onstop = () => {
            stream.getTracks().forEach(t => t.stop());
            recordedBlob = new Blob(audioChunks, { type: 'audio/webm' });
            selectedFile = null;

            const preview = document.getElementById('tpVoicePreview');
            preview.src = URL.createObjectURL(recordedBlob);
            document.getElementById('previewWrap').style.display = 'block';
            document.getElementById('btnSaveVoice').style.display = 'inline-block';
        };

        mediaRecorder.start();
        btn.classList.add('is-recording');
        recText.textContent = 'توقف ضبط';
        recIcon.textContent = '⏹';
        timer.style.display = 'inline-block';
        recordSeconds = 0;
        updateTimerDisplay();
        timerInterval = setInterval(() => {
            recordSeconds++;
            updateTimerDisplay();
        }, 1000);

    } catch (err) {
        alert('دسترسی به میکروفون امکان‌پذیر نشد. لطفاً دسترسی مرورگر را بررسی فرمایید.');
    }
}

function handleAudioFileSelect(input) {
    if (input.files && input.files[0]) {
        selectedFile = input.files[0];
        recordedBlob = null;
        const preview = document.getElementById('tpVoicePreview');
        preview.src = URL.createObjectURL(selectedFile);
        document.getElementById('previewWrap').style.display = 'block';
        document.getElementById('btnSaveVoice').style.display = 'inline-block';
    }
}

function saveSelectedAudio() {
    const btn = document.getElementById('btnSaveVoice');
    btn.disabled = true;
    btn.textContent = 'در حال ذخیره‌سازی...';

    const fd = new FormData();
    fd.append('action', 'upload');
    fd.append('class_id', '<?= (int)$class['id'] ?>');

    if (recordedBlob) {
        fd.append('audio_file', recordedBlob, 'general_plan_voice.webm');
    } else if (selectedFile) {
        fd.append('audio_file', selectedFile);
    } else {
        alert('لطفاً ابتدا ویس ضبط کنید یا فایل صوتی انتخاب کنید.');
        btn.disabled = false;
        btn.textContent = '✓ ذخیره و ارسال ویس';
        return;
    }

    fetch('ajax-term-plan-voice.php', {
        method: 'POST',
        body: fd
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert(data.message || 'ویس با موفقیت ثبت شد.');
            location.reload();
        } else {
            alert('خطا: ' + (data.message || 'ذخیره نشد'));
            btn.disabled = false;
            btn.textContent = '✓ ذخیره و ارسال ویس';
        }
    })
    .catch(err => {
        alert('خطا در برقراری ارتباط با سرور.');
        btn.disabled = false;
        btn.textContent = '✓ ذخیره و ارسال ویس';
    });
}
</script>
<?php require_once 'layout-end.php'; ?>
