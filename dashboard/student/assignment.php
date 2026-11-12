<?php
$title = 'Assignment';
$current_page = 'assignment.php';
require_once 'layout.php';
require_once '../../includes/db.php';
require_once '../../includes/assignments.php';
require_role('student');

$student_id = (int)$_SESSION['user_id'];
$id = (int)($_GET['id'] ?? 0);
$item = null;
$page_error = '';
try {
    assignments_ensure_schema($pdo);
    if ($id > 0) {
        $row = assignments_find_student($pdo, $id, $student_id);
        if ($row) {
            $rawFiles = !empty($row['submission_id'])
                ? assignments_submission_files_by_sub($pdo, (int)$row['submission_id'])
                : [];
            $item = assignments_public_student($row, $rawFiles);
        }
    }
} catch (Exception $e) {
    error_log('student assignment.php: ' . $e->getMessage());
    $page_error = 'الان این تکلیف در دسترس نیست. صفحه را تازه کنید.';
}

$limit = assignments_upload_limit();
$maxImages = assignments_max_images();
$maxAudio = assignments_max_audio();
$maxFiles = assignments_max_files();
$boot = [
    'ajax' => 'ajax-assignments.php',
    'file' => 'assignment-file.php',
    'id' => $item ? (int)$item['id'] : 0,
    'max_bytes' => $limit,
    'max_label' => assignments_bytes_label($limit),
    'max_images' => $maxImages,
    'max_audio' => $maxAudio,
    'max_files' => $maxFiles,
    'image_exts' => assignments_image_ext(),
    'audio_exts' => assignments_audio_ext(),
    'assignment' => $item,
];
$pill = $item ? ($item['status_key'] === 'submitted' ? 'ready' : ($item['status_key'] === 'late' ? 'draft' : ($item['status_key'] === 'overdue' ? 'failed' : 'pending'))) : '';
?>
<link href="../../assets/css/quiz-builder.css?v=qb7" rel="stylesheet">
<link href="../../assets/css/assignments.css?v=asg3" rel="stylesheet">
<div class="qb-page asg-page asg-student-detail" data-asg-student data-boot="<?= assignments_h(json_encode($boot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) ?>">
    <div class="qb-head">
        <div>
            <h1><span>Assignment</span></h1>
            <p>تا حداکثر <?= (int)$maxImages ?> عکس، فایل ویس یا فایل پاسخ را همین‌جا بفرستید. در صورت افزودن یا حذف فایل، نمره قبلی ریست می‌شود تا معلم دوباره بررسی کند.</p>
        </div>
        <a class="qb-btn qb-btn-ghost" href="assignments.php">همه تکلیف‌ها</a>
    </div>

    <?php if ($page_error): ?>
        <div class="qb-empty">
            <div class="qb-empty-icon">⏳</div>
            <h5>تکلیف لود نشد</h5>
            <p><?= assignments_h($page_error) ?></p>
        </div>
    <?php elseif (!$item): ?>
        <div class="qb-empty">
            <div class="qb-empty-icon">📎</div>
            <h5>این تکلیف برای شما نیست</h5>
            <p>یا حذف شده، یا متعلق به کلاس دیگری است.</p>
        </div>
    <?php else: ?>
        <div class="qb-class-bar">
            <div>
                <strong><?= assignments_h($item['class_title']) ?></strong>
                <span class="qb-pill qb-pill-<?= assignments_h($pill) ?>" id="asgStatusPill"><?= assignments_h($item['status_label']) ?></span>
            </div>
            <span>جلسه <?= (int)$item['session_no'] ?></span>
        </div>

        <article class="qb-exam-view asg-prompt">
            <h2><?= assignments_h($item['title']) ?></h2>
            <div class="qb-card-meta">
                <?php if ($item['due_label'] !== ''): ?>
                    <span>مهلت <?= assignments_h($item['due_label']) ?></span>
                <?php else: ?>
                    <span>بدون مهلت مشخص</span>
                <?php endif; ?>
            </div>
            <?php if (trim($item['body']) !== ''): ?>
                <div class="asg-body"><?= nl2br(assignments_h($item['body'])) ?></div>
            <?php else: ?>
                <p class="qb-hint">معلم شرح جداگانه‌ای ننوشته؛ اگر پیوست دارد همان را ببینید.</p>
            <?php endif; ?>
            <?php if ($item['has_teacher_file']): ?>
                <div class="asg-teacher-att">
                    <?php if (($item['teacher_file_kind'] ?? '') === 'audio' && !empty($item['teacher_file_inline'])): ?>
                        <audio class="asg-audio-player" controls preload="metadata" src="assignment-file.php?kind=teacher&amp;id=<?= (int)$item['id'] ?>&amp;inline=1"></audio>
                    <?php endif; ?>
                    <p>
                        <a class="qb-btn" href="assignment-file.php?kind=teacher&amp;id=<?= (int)$item['id'] ?>">
                            دانلود پیوست معلم · <?= assignments_h($item['teacher_file_name']) ?>
                            (<?= assignments_h($item['teacher_file_size_label']) ?>)
                        </a>
                    </p>
                </div>
            <?php endif; ?>
            <div id="asgGradeBox" class="asg-grade-read" <?= !empty($item['graded']) ? '' : 'hidden' ?>>
                <?php if (!empty($item['graded'])): ?>
                    <strong>نمره معلم: <?= assignments_h($item['score_label']) ?></strong>
                    <?php if ($item['graded_label'] !== ''): ?>
                        <span><?= assignments_h($item['graded_label']) ?></span>
                    <?php endif; ?>
                    <?php if (trim((string)$item['feedback']) !== ''): ?>
                        <p><?= nl2br(assignments_h($item['feedback'])) ?></p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </article>

        <section class="qb-builder asg-upload" id="asgUploadBox">
            <div class="asg-upload-head">
                <h2>تحویل شما</h2>
                <div class="asg-quotas" id="asgQuotas">
                    <span class="qb-pill qb-pill-ready" id="asgImgQuota">عکس‌ها: <?= (int)($item['image_count'] ?? 0) ?> از <?= (int)$maxImages ?></span>
                    <span class="qb-pill qb-pill-pending" id="asgAudioQuota">ویس: <?= (int)($item['audio_count'] ?? 0) ?></span>
                </div>
            </div>

            <div id="asgCurrent" class="asg-current" <?= !empty($item['files']) ? '' : 'hidden' ?>></div>

            <form id="asgUploadForm">
                <label class="qb-field">
                    <span>انتخاب عکس، ویس یا فایل (حداکثر <?= (int)$maxImages ?> عکس · هر فایل تا <?= assignments_h(assignments_bytes_label($limit)) ?>)</span>
                    <input type="file" name="files[]" id="asgFile" accept="<?= assignments_h(assignments_accept_attr()) ?>" multiple>
                </label>
                <div id="asgSelectedHint" class="qb-hint" hidden></div>
                <div class="asg-form-actions">
                    <button type="submit" class="qb-btn qb-btn-red" id="asgUploadBtn"><?= !empty($item['submitted']) ? 'افزودن فایل به تحویل' : 'آپلود و تحویل' ?></button>
                </div>
            </form>

            <div class="asg-recorder" id="asgRecorder">
                <div class="asg-recorder-top">
                    <span class="asg-recorder-title">ارسال ویس مستقیم</span>
                    <span class="qb-hint">می‌توانید ویس را مستقیم همین‌جا ضبط و ارسال کنید یا فایل صوتی آماده انتخاب کنید.</span>
                </div>
                <div class="asg-recorder-controls">
                    <button type="button" class="qb-btn" id="asgRecStartBtn">🎙️ شروع ضبط ویس</button>
                    <button type="button" class="qb-btn qb-btn-red" id="asgRecStopBtn" hidden>⏹ پایان ضبط</button>
                    <button type="button" class="qb-btn qb-btn-ghost" id="asgRecCancelBtn" hidden>انصراف</button>
                    <span class="asg-rec-live" id="asgRecLive" hidden><i class="asg-rec-dot"></i> <b id="asgRecTimer">00:00</b></span>
                </div>
                <div class="asg-recorder-preview" id="asgRecPreview" hidden>
                    <audio class="asg-audio-player" id="asgRecAudio" controls></audio>
                    <div class="asg-form-actions">
                        <button type="button" class="qb-btn qb-btn-red" id="asgRecSendBtn">ارسال این ویس</button>
                        <button type="button" class="qb-btn qb-btn-ghost" id="asgRecDiscardBtn">حذف و ضبط مجدد</button>
                    </div>
                </div>
            </div>

            <div class="qb-alert" id="asgAlert" hidden></div>
            <div class="qb-progress" id="asgProgress" hidden>
                <div class="qb-progress-bar"></div>
                <span id="asgProgressText">در حال آپلود…</span>
            </div>
        </section>
    <?php endif; ?>
</div>
<script src="../../assets/js/student-assignments.js?v=asg3"></script>
<?php require_once 'layout-end.php'; ?>
