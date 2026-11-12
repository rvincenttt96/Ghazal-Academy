<?php
/**
 * Per-session class assignments with student multi-file (up to 5 images + voice/audio + docs) upload.
 * Separate from homework_status (attendance ticks) and Quiz Marks.
 */

function assignments_h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function assignments_json($payload, $code = 200) {
    http_response_code((int)$code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function assignments_session_max() {
    return 14;
}

function assignments_max_images() {
    return 5;
}

function assignments_max_audio() {
    return 5;
}

function assignments_max_files() {
    return 8;
}

function assignments_ini_bytes($val) {
    $val = trim((string)$val);
    if ($val === '') {
        return 0;
    }
    $n = (float)$val;
    $u = strtolower(substr($val, -1));
    if ($u === 'g') {
        $n *= 1024 * 1024 * 1024;
    } elseif ($u === 'm') {
        $n *= 1024 * 1024;
    } elseif ($u === 'k') {
        $n *= 1024;
    }
    return (int)$n;
}

function assignments_upload_limit() {
    $hard = 20 * 1024 * 1024;
    $candidates = [
        assignments_ini_bytes(ini_get('upload_max_filesize')),
        assignments_ini_bytes(ini_get('post_max_size')),
        $hard,
    ];
    $candidates = array_filter($candidates);
    return $candidates ? min($candidates) : $hard;
}

function assignments_bytes_label($n) {
    $n = (int)$n;
    if ($n < 1024) {
        return $n . ' B';
    }
    if ($n < 1024 * 1024) {
        return round($n / 1024, 1) . ' KB';
    }
    return round($n / (1024 * 1024), 1) . ' MB';
}

function assignments_htaccess_body() {
    return "<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n"
        . "<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n";
}

function assignments_storage_dir() {
    $dir = dirname(__DIR__) . '/uploads/assignments';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $ht = $dir . '/.htaccess';
    if (!is_file($ht)) {
        @file_put_contents($ht, assignments_htaccess_body());
    }
    $deny = $dir . '/index.html';
    if (!is_file($deny)) {
        @file_put_contents($deny, '');
    }
    return $dir;
}

function assignments_fail_fast(PDO $pdo) {
    try {
        $pdo->exec('SET SESSION lock_wait_timeout = 3');
    } catch (Exception $e) {
        // ignore
    }
    try {
        $pdo->exec('SET SESSION innodb_lock_wait_timeout = 3');
    } catch (Exception $e) {
        // ignore
    }
}

function assignments_ensure_schema(PDO $pdo) {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    assignments_storage_dir();
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS assignments (
                id INT AUTO_INCREMENT PRIMARY KEY,
                class_id INT NOT NULL,
                teacher_id INT NOT NULL,
                session_no TINYINT UNSIGNED NOT NULL,
                title VARCHAR(180) NOT NULL,
                body MEDIUMTEXT,
                teacher_stored_name VARCHAR(255) NULL,
                teacher_original_name VARCHAR(255) NULL,
                teacher_mime VARCHAR(80) NULL,
                teacher_size INT NOT NULL DEFAULT 0,
                due_at DATE NULL,
                status VARCHAR(16) NOT NULL DEFAULT 'published',
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                KEY idx_asg_class (class_id),
                KEY idx_asg_teacher (teacher_id),
                KEY idx_asg_session (class_id, session_no)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS assignment_submissions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                assignment_id INT NOT NULL,
                class_id INT NOT NULL,
                student_id INT NOT NULL,
                stored_name VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                mime VARCHAR(80) NOT NULL DEFAULT '',
                file_size INT NOT NULL DEFAULT 0,
                submitted_at DATETIME NULL,
                score DECIMAL(4,1) NULL DEFAULT NULL,
                feedback VARCHAR(400) NULL DEFAULT NULL,
                graded_at DATETIME NULL,
                graded_by INT NULL,
                UNIQUE KEY uq_asg_student (assignment_id, student_id),
                KEY idx_asub_class (class_id),
                KEY idx_asub_student (student_id),
                KEY idx_asub_asg (assignment_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        assignments_ensure_grade_columns($pdo);
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS assignment_submission_files (
                id INT AUTO_INCREMENT PRIMARY KEY,
                submission_id INT NOT NULL,
                assignment_id INT NOT NULL,
                student_id INT NOT NULL,
                file_kind VARCHAR(16) NOT NULL DEFAULT 'doc',
                stored_name VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NOT NULL,
                mime VARCHAR(100) NOT NULL DEFAULT '',
                file_size INT NOT NULL DEFAULT 0,
                created_at DATETIME NULL,
                KEY idx_asgf_sub (submission_id),
                KEY idx_asgf_asg (assignment_id),
                KEY idx_asgf_stu (assignment_id, student_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        assignments_backfill_submission_files($pdo);
    } catch (Exception $e) {
        error_log('assignments schema: ' . $e->getMessage());
        throw $e;
    }
}

function assignments_backfill_submission_files(PDO $pdo) {
    try {
        $pdo->exec("
            INSERT INTO assignment_submission_files
                (submission_id, assignment_id, student_id, file_kind, stored_name, original_name, mime, file_size, created_at)
            SELECT
                s.id,
                s.assignment_id,
                s.student_id,
                CASE
                    WHEN LOWER(SUBSTRING_INDEX(s.stored_name, '.', -1)) IN ('jpg','jpeg','png','webp','gif','heic','heif') THEN 'image'
                    WHEN LOWER(SUBSTRING_INDEX(s.stored_name, '.', -1)) IN ('mp3','m4a','wav','ogg','oga','opus','webm','aac','amr','3gp','flac') THEN 'audio'
                    ELSE 'doc'
                END,
                s.stored_name,
                s.original_name,
                s.mime,
                s.file_size,
                COALESCE(s.submitted_at, NOW())
            FROM assignment_submissions s
            LEFT JOIN assignment_submission_files f ON f.submission_id = s.id
            WHERE s.stored_name <> '' AND f.id IS NULL
        ");
    } catch (Exception $e) {
        error_log('assignments backfill files: ' . $e->getMessage());
    }
}

function assignments_ensure_grade_columns(PDO $pdo) {
    $have = [];
    try {
        $st = $pdo->query('SHOW COLUMNS FROM assignment_submissions');
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $col) {
            $have[strtolower((string)$col['Field'])] = true;
        }
    } catch (Exception $e) {
        return;
    }
    $alters = [
        'score' => 'ALTER TABLE assignment_submissions ADD COLUMN score DECIMAL(4,1) NULL DEFAULT NULL',
        'feedback' => 'ALTER TABLE assignment_submissions ADD COLUMN feedback VARCHAR(400) NULL DEFAULT NULL',
        'graded_at' => 'ALTER TABLE assignment_submissions ADD COLUMN graded_at DATETIME NULL',
        'graded_by' => 'ALTER TABLE assignment_submissions ADD COLUMN graded_by INT NULL',
        'review_status' => 'ALTER TABLE assignment_submissions ADD COLUMN review_status VARCHAR(24) NULL DEFAULT NULL',
    ];
    foreach ($alters as $name => $sql) {
        if (!empty($have[$name])) {
            continue;
        }
        try {
            $pdo->exec($sql);
        } catch (Exception $e) {
            error_log('assignments grade column ' . $name . ': ' . $e->getMessage());
        }
    }
}

function assignments_score_max() {
    return 20;
}

function assignments_today() {
    $tz = date_default_timezone_get();
    if ($tz !== 'Asia/Tehran') {
        date_default_timezone_set('Asia/Tehran');
    }
    return date('Y-m-d');
}

function assignments_now() {
    $tz = date_default_timezone_get();
    if ($tz !== 'Asia/Tehran') {
        date_default_timezone_set('Asia/Tehran');
    }
    return date('Y-m-d H:i:s');
}

function assignments_human_date($ymd) {
    $ymd = trim((string)$ymd);
    if ($ymd === '' || strpos($ymd, '0000-00-00') === 0) {
        return '';
    }
    $ts = strtotime(substr($ymd, 0, 10) . ' 12:00:00');
    if (!$ts) {
        return $ymd;
    }
    if (function_exists('jdate')) {
        return jdate('Y/m/d', $ts);
    }
    $j = __DIR__ . '/jdate.php';
    if (is_file($j)) {
        require_once $j;
        if (function_exists('jdate')) {
            return jdate('Y/m/d', $ts);
        }
    }
    return date('Y/m/d', $ts);
}

function assignments_human_datetime($dt) {
    $dt = trim((string)$dt);
    if ($dt === '') {
        return '';
    }
    $ts = strtotime($dt);
    if (!$ts) {
        return $dt;
    }
    if (function_exists('jdate')) {
        return jdate('Y/m/d H:i', $ts);
    }
    $j = __DIR__ . '/jdate.php';
    if (is_file($j)) {
        require_once $j;
        if (function_exists('jdate')) {
            return jdate('Y/m/d H:i', $ts);
        }
    }
    return date('Y/m/d H:i', $ts);
}

function assignments_image_ext() {
    return ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif'];
}

function assignments_audio_ext() {
    return ['mp3', 'm4a', 'wav', 'ogg', 'oga', 'opus', 'webm', 'aac', 'amr', '3gp', 'flac'];
}

function assignments_doc_ext() {
    return ['pdf', 'doc', 'docx', 'zip'];
}

function assignments_allowed_ext() {
    return array_merge(assignments_image_ext(), assignments_audio_ext(), assignments_doc_ext());
}

function assignments_accept_attr() {
    return '.pdf,.jpg,.jpeg,.png,.webp,.gif,.heic,.heif,.mp3,.m4a,.wav,.ogg,.oga,.opus,.webm,.aac,.amr,.3gp,.flac,.doc,.docx,.zip,image/*,audio/*';
}

function assignments_file_kind_by_ext($ext) {
    $ext = strtolower(trim((string)$ext));
    if (in_array($ext, assignments_image_ext(), true)) {
        return 'image';
    }
    if (in_array($ext, assignments_audio_ext(), true)) {
        return 'audio';
    }
    return 'doc';
}

function assignments_mime_ok($mime, $ext) {
    $mime = strtolower(trim((string)$mime));
    if (($semi = strpos($mime, ';')) !== false) {
        $mime = trim(substr($mime, 0, $semi));
    }
    $ext = strtolower(trim((string)$ext));
    $map = [
        'pdf' => ['application/pdf'],
        'jpg' => ['image/jpeg', 'image/pjpeg'],
        'jpeg' => ['image/jpeg', 'image/pjpeg'],
        'png' => ['image/png', 'image/x-png'],
        'webp' => ['image/webp'],
        'gif' => ['image/gif'],
        'heic' => ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence', 'application/octet-stream'],
        'heif' => ['image/heic', 'image/heif', 'image/heic-sequence', 'image/heif-sequence', 'application/octet-stream'],
        'mp3' => ['audio/mpeg', 'audio/mp3', 'audio/x-mpeg', 'audio/mpeg3', 'audio/x-mpeg-3', 'application/octet-stream'],
        'm4a' => ['audio/mp4', 'audio/x-m4a', 'audio/m4a', 'video/mp4', 'application/mp4', 'audio/aac', 'audio/x-hx-aac-adts', 'application/octet-stream'],
        'aac' => ['audio/aac', 'audio/x-aac', 'audio/x-hx-aac-adts', 'audio/mp4', 'video/mp4', 'application/octet-stream'],
        'wav' => ['audio/wav', 'audio/x-wav', 'audio/wave', 'audio/vnd.wave', 'application/octet-stream'],
        'ogg' => ['audio/ogg', 'video/ogg', 'application/ogg', 'audio/x-ogg', 'audio/opus', 'audio/x-opus+ogg', 'application/octet-stream'],
        'oga' => ['audio/ogg', 'video/ogg', 'application/ogg', 'audio/x-ogg', 'audio/opus', 'audio/x-opus+ogg', 'application/octet-stream'],
        'opus' => ['audio/opus', 'audio/ogg', 'application/ogg', 'video/ogg', 'audio/x-opus+ogg', 'application/octet-stream'],
        'webm' => ['audio/webm', 'video/webm', 'application/octet-stream'],
        'amr' => ['audio/amr', 'audio/amr-wb', 'application/octet-stream'],
        '3gp' => ['audio/3gpp', 'video/3gpp', 'audio/3gpp2', 'video/3gpp2', 'audio/mp4', 'video/mp4', 'application/octet-stream'],
        'flac' => ['audio/flac', 'audio/x-flac', 'application/octet-stream'],
        'doc' => ['application/msword', 'application/cdfv2', 'application/vnd.ms-office', 'application/octet-stream'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/octet-stream',
        ],
        'zip' => ['application/zip', 'application/x-zip-compressed', 'application/octet-stream'],
    ];
    if (!isset($map[$ext])) {
        return false;
    }
    if ($mime === '' || $mime === 'application/octet-stream') {
        return true;
    }
    return in_array($mime, $map[$ext], true);
}

function assignments_canonical_mime($mime, $ext) {
    $ext = strtolower(trim((string)$ext));
    $audioCanonical = [
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mp4',
        'aac' => 'audio/aac',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'opus' => 'audio/ogg',
        'webm' => 'audio/webm',
        'amr' => 'audio/amr',
        '3gp' => 'audio/3gpp',
        'flac' => 'audio/flac',
    ];
    if (isset($audioCanonical[$ext])) {
        return $audioCanonical[$ext];
    }
    $mime = strtolower(trim((string)$mime));
    if (($semi = strpos($mime, ';')) !== false) {
        $mime = trim(substr($mime, 0, $semi));
    }
    if ($mime === '' || $mime === 'application/octet-stream') {
        $imgCanonical = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            'gif' => 'image/gif',
            'heic' => 'image/heic',
            'heif' => 'image/heif',
            'pdf' => 'application/pdf',
        ];
        return $imgCanonical[$ext] ?? 'application/octet-stream';
    }
    return $mime;
}

function assignments_ext($name) {
    $name = strtolower((string)$name);
    $pos = strrpos($name, '.');
    if ($pos === false) {
        return '';
    }
    return preg_replace('/[^a-z0-9]/', '', substr($name, $pos + 1));
}

function assignments_safe_orig($name) {
    $name = basename(str_replace('\\', '/', (string)$name));
    $name = preg_replace('/[\x00-\x1F\x7F]/', '', $name);
    if (function_exists('mb_substr')) {
        $name = mb_substr($name, 0, 180, 'UTF-8');
    } else {
        $name = substr($name, 0, 180);
    }
    $name = trim($name);
    return $name !== '' ? $name : 'file';
}

function assignments_file_path($stored) {
    $name = basename((string)$stored);
    if ($name === '' || $name !== (string)$stored || strpos($name, '..') !== false) {
        return '';
    }
    return assignments_storage_dir() . '/' . $name;
}

function assignments_delete_stored($stored) {
    $path = assignments_file_path($stored);
    if ($path !== '' && is_file($path)) {
        @unlink($path);
    }
}

function assignments_store_upload(array $file, $prefix) {
    $limit = assignments_upload_limit();
    $err = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
        throw new Exception('حجم فایل از سقف سرور بیشتر است (حداکثر ' . assignments_bytes_label($limit) . ').');
    }
    if ($err !== UPLOAD_ERR_OK) {
        throw new Exception('آپلود فایل انجام نشد.');
    }
    $tmp = (string)($file['tmp_name'] ?? '');
    if ($tmp === '' || !is_uploaded_file($tmp)) {
        throw new Exception('فایل آپلود معتبر نیست.');
    }
    $size = (int)($file['size'] ?? 0);
    if ($size <= 0) {
        throw new Exception('فایل خالی است.');
    }
    if ($size > $limit) {
        throw new Exception('حجم هر فایل حداکثر ' . assignments_bytes_label($limit) . ' است.');
    }
    $orig = assignments_safe_orig($file['name'] ?? 'file');
    $ext = assignments_ext($orig);
    if (!in_array($ext, assignments_allowed_ext(), true)) {
        throw new Exception('فقط عکس، ویس (فایل صوتی)، PDF، Word یا ZIP پذیرفته می‌شود.');
    }
    $mime = '';
    if (function_exists('finfo_open')) {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        if ($fi) {
            $mime = (string)finfo_file($fi, $tmp);
            finfo_close($fi);
        }
    }
    if ($mime === '') {
        $mime = (string)($file['type'] ?? '');
    }
    if (!assignments_mime_ok($mime, $ext)) {
        throw new Exception('نوع فایل مجاز نیست (' . $orig . ').');
    }
    $canonicalMime = assignments_canonical_mime($mime, $ext);
    $kind = assignments_file_kind_by_ext($ext);
    $stored = preg_replace('/[^a-z0-9_-]/i', '', (string)$prefix) . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $dest = assignments_storage_dir() . '/' . $stored;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new Exception('ذخیره فایل روی سرور انجام نشد.');
    }
    @chmod($dest, 0644);
    return [
        'file_kind' => $kind,
        'stored_name' => $stored,
        'original_name' => $orig,
        'mime' => $canonicalMime,
        'file_size' => $size,
    ];
}

function assignments_disposition($orig, $inline = false) {
    $orig = assignments_safe_orig($orig);
    $ascii = preg_replace('/[^\x20-\x7E]/', '_', $orig);
    $ascii = str_replace('"', '', $ascii);
    if ($ascii === '' || $ascii === '_') {
        $ascii = 'file';
    }
    $type = $inline ? 'inline' : 'attachment';
    return $type . '; filename="' . $ascii . '"; filename*=UTF-8\'\'' . rawurlencode($orig);
}

function assignments_can_inline($stored, $mime) {
    $ext = assignments_ext($stored);
    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
        return true;
    }
    if (in_array($ext, assignments_audio_ext(), true)) {
        return true;
    }
    return false;
}

function assignments_send_file($stored, $orig, $mime, $inline = false) {
    $path = assignments_file_path($stored);
    if ($path === '' || !is_file($path)) {
        http_response_code(404);
        echo 'فایل پیدا نشد.';
        exit;
    }
    $ext = assignments_ext($stored);
    $mime = assignments_canonical_mime($mime, $ext);
    $allowInline = $inline && assignments_can_inline($stored, $mime);
    $size = (int)filesize($path);

    header('X-Content-Type-Options: nosniff');
    header("Content-Security-Policy: default-src 'none'; img-src 'self'; media-src 'self'; style-src 'unsafe-inline'");
    header('Content-Type: ' . $mime);
    header('Content-Disposition: ' . assignments_disposition($orig, $allowInline));
    header('Accept-Ranges: bytes');
    header('Cache-Control: private, max-age=300');

    $range = isset($_SERVER['HTTP_RANGE']) ? trim((string)$_SERVER['HTTP_RANGE']) : '';
    if ($allowInline && $size > 0 && preg_match('/^bytes=(\d*)-(\d*)$/i', $range, $m)) {
        $start = $m[1] !== '' ? (int)$m[1] : 0;
        $end = $m[2] !== '' ? (int)$m[2] : ($size - 1);
        if ($m[1] === '' && $m[2] !== '') {
            $suffix = (int)$m[2];
            $start = max(0, $size - $suffix);
            $end = $size - 1;
        }
        if ($end >= $size) {
            $end = $size - 1;
        }
        if ($start > $end || $start < 0 || $start >= $size) {
            http_response_code(416);
            header('Content-Range: bytes */' . $size);
            exit;
        }
        $length = $end - $start + 1;
        http_response_code(206);
        header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
        header('Content-Length: ' . $length);
        $fp = @fopen($path, 'rb');
        if ($fp) {
            if ($start > 0) {
                fseek($fp, $start);
            }
            $left = $length;
            while ($left > 0 && !feof($fp)) {
                $chunk = fread($fp, min(65536, $left));
                if ($chunk === false || $chunk === '') {
                    break;
                }
                echo $chunk;
                $left -= strlen($chunk);
            }
            fclose($fp);
        }
        exit;
    }

    header('Content-Length: ' . $size);
    readfile($path);
    exit;
}

function assignments_teacher_classes(PDO $pdo, $teacher_id) {
    if (!function_exists('term_plan_teacher_classes')) {
        require_once __DIR__ . '/term-plan.php';
    }
    return term_plan_teacher_classes($pdo, $teacher_id);
}

function assignments_find_class(PDO $pdo, $class_id, $teacher_id = 0) {
    if (!function_exists('term_plan_find_class')) {
        require_once __DIR__ . '/term-plan.php';
    }
    return term_plan_find_class($pdo, $class_id, $teacher_id);
}

function assignments_class_sql() {
    if (!function_exists('term_sql_teaching')) {
        require_once __DIR__ . '/terms.php';
    }
    return "c.is_deleted = 0 AND IFNULL(c.is_archived, 0) = 0 AND " . term_sql_teaching('t');
}

function assignments_student_in_class(PDO $pdo, $student_id, $class_id) {
    $st = $pdo->prepare("
        SELECT c.id
        FROM classes c
        JOIN class_students cs ON cs.class_id = c.id
        JOIN terms t ON t.id = c.term_id
        WHERE c.id = ? AND cs.user_id = ? AND " . assignments_class_sql() . "
        LIMIT 1
    ");
    $st->execute([(int)$class_id, (int)$student_id]);
    return (bool)$st->fetchColumn();
}

function assignments_class_roster(PDO $pdo, $class_id) {
    $st = $pdo->prepare("
        SELECT u.id, u.full_name, u.username
        FROM users u
        JOIN class_students cs ON cs.user_id = u.id
        WHERE cs.class_id = ?
        ORDER BY u.full_name ASC
    ");
    $st->execute([(int)$class_id]);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function assignments_class_student_count(PDO $pdo, $class_id) {
    $st = $pdo->prepare("SELECT COUNT(*) FROM class_students WHERE class_id = ?");
    $st->execute([(int)$class_id]);
    return (int)$st->fetchColumn();
}

function assignments_due_state(array $row) {
    $due = trim((string)($row['due_at'] ?? ''));
    if ($due === '' || strpos($due, '0000-00-00') === 0) {
        return ['overdue' => false, 'late_submit' => false];
    }
    $due = substr($due, 0, 10);
    $submitted = trim((string)($row['submitted_at'] ?? ''));
    $has = $submitted !== '' || trim((string)($row['sub_stored_name'] ?? '')) !== '';
    $late = false;
    if ($has && $submitted !== '') {
        $late = substr($submitted, 0, 10) > $due;
    }
    return [
        'overdue' => !$has && assignments_today() > $due,
        'late_submit' => $late,
    ];
}

function assignments_status_label($key) {
    $map = [
        'pending' => 'تحویل نشده',
        'submitted' => 'تحویل شد',
        'late' => 'دیر تحویل شد',
        'overdue' => 'مهلت گذشته',
    ];
    return $map[$key] ?? 'تحویل نشده';
}

function assignments_review_status_label($key) {
    $map = [
        'complete' => 'کامل',
        'incomplete' => 'ناقص',
        'not_submitted' => 'عدم تحویل',
    ];
    return $map[$key] ?? '';
}

function assignments_parse_review_status($raw) {
    $val = trim((string)$raw);
    if ($val === '') {
        return '';
    }
    if (!in_array($val, ['complete', 'incomplete', 'not_submitted'], true)) {
        throw new Exception('وضعیت تکلیف نامعتبر است.');
    }
    return $val;
}

function assignments_format_score($score) {
    if ($score === null || $score === '') {
        return '';
    }
    $n = round((float)$score, 1);
    if (abs($n - round($n)) < 0.05) {
        return (string)(int)round($n);
    }
    return rtrim(rtrim(number_format($n, 1, '.', ''), '0'), '.');
}

function assignments_score_label($score, $max = null) {
    $txt = assignments_format_score($score);
    if ($txt === '') {
        return '';
    }
    $max = $max === null ? assignments_score_max() : (int)$max;
    return $txt . ' از ' . $max;
}

function assignments_parse_score($raw) {
    $raw = trim(str_replace(',', '.', (string)$raw));
    if ($raw === '') {
        return null;
    }
    if (!is_numeric($raw)) {
        throw new Exception('نمره باید عدد باشد.');
    }
    $n = round((float)$raw, 1);
    $max = assignments_score_max();
    if ($n < 0 || $n > $max) {
        throw new Exception('نمره باید بین ۰ و ' . $max . ' باشد.');
    }
    return $n;
}

function assignments_grade_fields(array $row, $score_key = 'score', $feedback_key = 'feedback', $graded_key = 'graded_at', $review_key = 'review_status') {
    $reviewStatus = trim((string)($row[$review_key] ?? ''));
    $reviewLabel = assignments_review_status_label($reviewStatus);
    $graded = $reviewLabel !== '';
    return [
        'graded' => $graded,
        'review_status' => $graded ? $reviewStatus : '',
        'review_label' => $reviewLabel,
        'score' => null,
        'score_label' => $reviewLabel,
        'feedback' => (string)($row[$feedback_key] ?? ''),
        'graded_label' => $graded ? assignments_human_datetime($row[$graded_key] ?? '') : '',
    ];
}

function assignments_public_file_item(array $f) {
    $stored = (string)($f['stored_name'] ?? '');
    $ext = assignments_ext($stored !== '' ? $stored : ($f['original_name'] ?? ''));
    $kind = (string)($f['file_kind'] ?? '');
    if ($kind === '') {
        $kind = assignments_file_kind_by_ext($ext);
    }
    return [
        'id' => (int)($f['id'] ?? 0),
        'kind' => $kind,
        'ext' => $ext,
        'can_inline' => assignments_can_inline($stored, $f['mime'] ?? ''),
        'name' => (string)($f['original_name'] ?? 'file'),
        'size' => (int)($f['file_size'] ?? 0),
        'size_label' => assignments_bytes_label((int)($f['file_size'] ?? 0)),
        'created_label' => assignments_human_datetime($f['created_at'] ?? ''),
    ];
}

function assignments_submission_files_by_sub(PDO $pdo, $submission_id) {
    $sid = (int)$submission_id;
    if ($sid <= 0) {
        return [];
    }
    try {
        $st = $pdo->prepare("
            SELECT * FROM assignment_submission_files
            WHERE submission_id = ?
            ORDER BY id ASC
        ");
        $st->execute([$sid]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

function assignments_submission_files_by_asg(PDO $pdo, $assignment_id) {
    $aid = (int)$assignment_id;
    if ($aid <= 0) {
        return [];
    }
    try {
        $st = $pdo->prepare("
            SELECT * FROM assignment_submission_files
            WHERE assignment_id = ?
            ORDER BY id ASC
        ");
        $st->execute([$aid]);
        $grouped = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $grouped[(int)$row['submission_id']][] = $row;
        }
        return $grouped;
    } catch (Exception $e) {
        return [];
    }
}

function assignments_find_submission_file(PDO $pdo, $file_id) {
    $fid = (int)$file_id;
    if ($fid <= 0) {
        return null;
    }
    $st = $pdo->prepare("SELECT * FROM assignment_submission_files WHERE id = ? LIMIT 1");
    $st->execute([$fid]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function assignments_build_public_files(array $rawFiles, $fallbackStored = '', $fallbackOrig = '', $fallbackSize = 0, $fallbackMime = '', $fallbackAt = '') {
    $out = [];
    foreach ($rawFiles as $f) {
        $out[] = assignments_public_file_item($f);
    }
    if (!$out && trim((string)$fallbackStored) !== '') {
        $out[] = assignments_public_file_item([
            'id' => 0,
            'stored_name' => $fallbackStored,
            'original_name' => $fallbackOrig !== '' ? $fallbackOrig : 'file',
            'file_size' => (int)$fallbackSize,
            'mime' => $fallbackMime,
            'created_at' => $fallbackAt,
        ]);
    }
    return $out;
}

function assignments_public_teacher(array $row) {
    $tStored = trim((string)($row['teacher_stored_name'] ?? ''));
    $tExt = assignments_ext($tStored);
    return [
        'id' => (int)$row['id'],
        'class_id' => (int)$row['class_id'],
        'session_no' => (int)$row['session_no'],
        'title' => (string)$row['title'],
        'body' => (string)($row['body'] ?? ''),
        'due_at' => (!empty($row['due_at']) && strpos((string)$row['due_at'], '0000-00-00') !== 0)
            ? substr((string)$row['due_at'], 0, 10)
            : null,
        'due_label' => assignments_human_date($row['due_at'] ?? ''),
        'has_teacher_file' => $tStored !== '',
        'teacher_file_kind' => $tStored !== '' ? assignments_file_kind_by_ext($tExt) : '',
        'teacher_file_inline' => $tStored !== '' && assignments_can_inline($tStored, $row['teacher_mime'] ?? ''),
        'teacher_file_name' => (string)($row['teacher_original_name'] ?? ''),
        'teacher_file_size_label' => assignments_bytes_label((int)($row['teacher_size'] ?? 0)),
        'submission_count' => (int)($row['submission_count'] ?? 0),
        'graded_count' => (int)($row['graded_count'] ?? 0),
        'student_count' => (int)($row['student_count'] ?? 0),
        'score_max' => assignments_score_max(),
        'teacher_name' => (string)($row['teacher_name'] ?? ''),
        'created_at' => (string)($row['created_at'] ?? ''),
        'created_label' => assignments_human_datetime($row['created_at'] ?? ''),
    ];
}

function assignments_public_student(array $row, $rawFiles = null) {
    $due = assignments_due_state($row);
    $has = trim((string)($row['sub_stored_name'] ?? '')) !== ''
        || (!empty($row['sub_file_count']) && (int)$row['sub_file_count'] > 0)
        || (is_array($rawFiles) && count($rawFiles) > 0);
    $key = !$has ? ($due['overdue'] ? 'overdue' : 'pending') : ($due['late_submit'] ? 'late' : 'submitted');

    $files = [];
    if ($has) {
        $files = assignments_build_public_files(
            is_array($rawFiles) ? $rawFiles : [],
            $row['sub_stored_name'] ?? '',
            $row['sub_original_name'] ?? '',
            (int)($row['sub_file_size'] ?? 0),
            $row['sub_mime'] ?? '',
            $row['submitted_at'] ?? ''
        );
    }
    $imgCount = 0;
    $audCount = 0;
    foreach ($files as $f) {
        if ($f['kind'] === 'image') {
            $imgCount++;
        } elseif ($f['kind'] === 'audio') {
            $audCount++;
        }
    }
    $fileCount = isset($row['sub_file_count']) && $rawFiles === null
        ? max((int)$row['sub_file_count'], count($files))
        : count($files);

    $tStored = trim((string)($row['teacher_stored_name'] ?? ''));
    $tExt = assignments_ext($tStored);

    return [
        'id' => (int)$row['id'],
        'class_id' => (int)$row['class_id'],
        'class_title' => (string)($row['class_title'] ?? ''),
        'session_no' => (int)$row['session_no'],
        'title' => (string)$row['title'],
        'body' => (string)($row['body'] ?? ''),
        'due_at' => (!empty($row['due_at']) && strpos((string)$row['due_at'], '0000-00-00') !== 0)
            ? substr((string)$row['due_at'], 0, 10)
            : null,
        'due_label' => assignments_human_date($row['due_at'] ?? ''),
        'has_teacher_file' => $tStored !== '',
        'teacher_file_kind' => $tStored !== '' ? assignments_file_kind_by_ext($tExt) : '',
        'teacher_file_inline' => $tStored !== '' && assignments_can_inline($tStored, $row['teacher_mime'] ?? ''),
        'teacher_file_name' => (string)($row['teacher_original_name'] ?? ''),
        'teacher_file_size_label' => assignments_bytes_label((int)($row['teacher_size'] ?? 0)),
        'submitted' => $has,
        'submitted_at' => $has ? (string)($row['submitted_at'] ?? '') : null,
        'submitted_label' => $has ? assignments_human_datetime($row['submitted_at'] ?? '') : '',
        'file_name' => (string)($row['sub_original_name'] ?? ''),
        'file_size_label' => assignments_bytes_label((int)($row['sub_file_size'] ?? 0)),
        'files' => $files,
        'file_count' => $has ? $fileCount : 0,
        'image_count' => $imgCount,
        'audio_count' => $audCount,
        'max_images' => assignments_max_images(),
        'max_audio' => assignments_max_audio(),
        'max_files' => assignments_max_files(),
        'status_key' => $key,
        'status_label' => assignments_status_label($key),
        'cta' => $has ? 'مشاهده و ویرایش تحویل' : 'تحویل تکلیف',
    ] + assignments_grade_fields($row, 'sub_score', 'sub_feedback', 'sub_graded_at', 'sub_review_status');
}

function assignments_class_rows(PDO $pdo, $class_id, $teacher_id = 0) {
    $sql = "
        SELECT a.*, u.full_name AS teacher_name,
               (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.id AND s.stored_name <> '') AS submission_count,
               (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.id AND s.review_status IS NOT NULL AND s.review_status <> '') AS graded_count
        FROM assignments a
        LEFT JOIN users u ON u.id = a.teacher_id
        WHERE a.class_id = ?
    ";
    $params = [(int)$class_id];
    if ((int)$teacher_id > 0) {
        $sql .= ' AND a.teacher_id = ?';
        $params[] = (int)$teacher_id;
    }
    $sql .= ' ORDER BY a.session_no ASC, a.id DESC';
    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    $n = assignments_class_student_count($pdo, $class_id);
    foreach ($rows as &$row) {
        $row['student_count'] = $n;
    }
    unset($row);
    return $rows;
}

function assignments_find_teacher(PDO $pdo, $id, $teacher_id = 0) {
    $sql = "
        SELECT a.*, u.full_name AS teacher_name,
               (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.id AND s.stored_name <> '') AS submission_count,
               (SELECT COUNT(*) FROM assignment_submissions s WHERE s.assignment_id = a.id AND s.review_status IS NOT NULL AND s.review_status <> '') AS graded_count
        FROM assignments a
        LEFT JOIN users u ON u.id = a.teacher_id
        WHERE a.id = ?
    ";
    $params = [(int)$id];
    if ((int)$teacher_id > 0) {
        $sql .= ' AND a.teacher_id = ?';
        $params[] = (int)$teacher_id;
    }
    $sql .= ' LIMIT 1';
    $st = $pdo->prepare($sql);
    $st->execute($params);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return null;
    }
    if ((int)$teacher_id === 0 && !assignments_find_class($pdo, (int)$row['class_id'], 0)) {
        return null;
    }
    $row['student_count'] = assignments_class_student_count($pdo, (int)$row['class_id']);
    return $row;
}

function assignments_supervisor_classes(PDO $pdo) {
    $st = $pdo->query("
        SELECT c.id, c.title, c.schedule, c.schedule_type, c.class_type,
               u.full_name AS teacher_name,
               COUNT(DISTINCT a.id) AS assignment_count,
               COUNT(DISTINCT CASE WHEN s.stored_name <> '' THEN s.id END) AS submission_count,
               COUNT(DISTINCT CASE WHEN s.review_status IS NOT NULL AND s.review_status <> '' THEN s.id END) AS graded_count
        FROM classes c
        JOIN users u ON c.teacher_id = u.id
        LEFT JOIN terms t ON t.id = c.term_id
        LEFT JOIN assignments a ON a.class_id = c.id AND a.status = 'published'
        LEFT JOIN assignment_submissions s ON s.assignment_id = a.id
        WHERE " . assignments_class_sql() . "
        GROUP BY c.id, c.title, c.schedule, c.schedule_type, c.class_type, u.full_name
        ORDER BY c.title
    ");
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

function assignments_parse_due($raw) {
    $raw = trim((string)$raw);
    if ($raw === '') {
        return null;
    }
    if (!preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $raw, $m)) {
        throw new Exception('تاریخ مهلت نامعتبر است.');
    }
    if (!checkdate((int)$m[2], (int)$m[3], (int)$m[1])) {
        throw new Exception('تاریخ مهلت نامعتبر است.');
    }
    return $raw;
}

function assignments_clip($s, $max) {
    $s = trim((string)$s);
    if (function_exists('mb_substr')) {
        return mb_substr($s, 0, $max, 'UTF-8');
    }
    return substr($s, 0, $max);
}

function assignments_save(PDO $pdo, $teacher_id, $class_id, array $input, $file = null) {
    $id = (int)($input['id'] ?? 0);
    $session = (int)($input['session_no'] ?? 0);
    if ($session < 1 || $session > assignments_session_max()) {
        throw new Exception('شماره جلسه باید بین ۱ و ۱۴ باشد.');
    }
    $title = assignments_clip($input['title'] ?? '', 180);
    if ($title === '' || (function_exists('mb_strlen') ? mb_strlen($title, 'UTF-8') : strlen($title)) < 2) {
        throw new Exception('عنوان تکلیف را بنویسید.');
    }
    $body = assignments_clip($input['body'] ?? '', 8000);
    $due = assignments_parse_due($input['due_at'] ?? '');
    $class = assignments_find_class($pdo, $class_id, $teacher_id);
    if (!$class) {
        throw new Exception('کلاس یافت نشد.');
    }

    $existing = null;
    if ($id > 0) {
        $existing = assignments_find_teacher($pdo, $id, $teacher_id);
        if (!$existing || (int)$existing['class_id'] !== (int)$class_id) {
            throw new Exception('این تکلیف پیدا نشد.');
        }
    }

    $upload = null;
    if (is_array($file) && (int)($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
        $prefix = $id > 0 ? ('t' . $id) : ('tnew' . $teacher_id);
        $upload = assignments_store_upload($file, $prefix);
    }

    if ($id > 0) {
        $st = $pdo->prepare("
            UPDATE assignments
            SET session_no = ?, title = ?, body = ?, due_at = ?, status = 'published'
            WHERE id = ? AND teacher_id = ?
        ");
        $st->execute([$session, $title, $body, $due, $id, (int)$teacher_id]);
        if ($upload) {
            assignments_delete_stored($existing['teacher_stored_name'] ?? '');
            $pdo->prepare("
                UPDATE assignments
                SET teacher_stored_name = ?, teacher_original_name = ?, teacher_mime = ?, teacher_size = ?
                WHERE id = ? AND teacher_id = ?
            ")->execute([
                $upload['stored_name'],
                $upload['original_name'],
                $upload['mime'],
                $upload['file_size'],
                $id,
                (int)$teacher_id,
            ]);
        }
        return assignments_find_teacher($pdo, $id, $teacher_id);
    }

    $st = $pdo->prepare("
        INSERT INTO assignments
            (class_id, teacher_id, session_no, title, body, due_at, status)
        VALUES (?, ?, ?, ?, ?, ?, 'published')
    ");
    $st->execute([(int)$class_id, (int)$teacher_id, $session, $title, $body, $due]);
    $new_id = (int)$pdo->lastInsertId();
    if ($upload) {
        $final = 't' . $new_id . '_' . bin2hex(random_bytes(4)) . '.' . assignments_ext($upload['stored_name']);
        $from = assignments_file_path($upload['stored_name']);
        $to = assignments_storage_dir() . '/' . $final;
        if ($from && is_file($from) && @rename($from, $to)) {
            $upload['stored_name'] = $final;
        }
        $pdo->prepare("
            UPDATE assignments
            SET teacher_stored_name = ?, teacher_original_name = ?, teacher_mime = ?, teacher_size = ?
            WHERE id = ?
        ")->execute([
            $upload['stored_name'],
            $upload['original_name'],
            $upload['mime'],
            $upload['file_size'],
            $new_id,
        ]);
    }
    return assignments_find_teacher($pdo, $new_id, $teacher_id);
}

function assignments_clear_teacher_file(PDO $pdo, $id, $teacher_id) {
    $row = assignments_find_teacher($pdo, $id, $teacher_id);
    if (!$row) {
        throw new Exception('این تکلیف پیدا نشد.');
    }
    assignments_delete_stored($row['teacher_stored_name'] ?? '');
    $pdo->prepare("
        UPDATE assignments
        SET teacher_stored_name = NULL, teacher_original_name = NULL, teacher_mime = NULL, teacher_size = 0
        WHERE id = ? AND teacher_id = ?
    ")->execute([(int)$id, (int)$teacher_id]);
    return assignments_find_teacher($pdo, $id, $teacher_id);
}

function assignments_delete(PDO $pdo, $id, $teacher_id) {
    $row = assignments_find_teacher($pdo, $id, $teacher_id);
    if (!$row) {
        throw new Exception('این تکلیف پیدا نشد.');
    }
    $seen = [];
    try {
        $stf = $pdo->prepare("SELECT stored_name FROM assignment_submission_files WHERE assignment_id = ?");
        $stf->execute([(int)$id]);
        foreach ($stf->fetchAll(PDO::FETCH_COLUMN) as $stored) {
            $stored = (string)$stored;
            if ($stored !== '' && empty($seen[$stored])) {
                $seen[$stored] = true;
                assignments_delete_stored($stored);
            }
        }
        $pdo->prepare("DELETE FROM assignment_submission_files WHERE assignment_id = ?")->execute([(int)$id]);
    } catch (Exception $e) {
        // ignore if child table missing
    }
    $st = $pdo->prepare("SELECT stored_name FROM assignment_submissions WHERE assignment_id = ?");
    $st->execute([(int)$id]);
    foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $stored) {
        $stored = (string)$stored;
        if ($stored !== '' && empty($seen[$stored])) {
            $seen[$stored] = true;
            assignments_delete_stored($stored);
        }
    }
    $pdo->prepare("DELETE FROM assignment_submissions WHERE assignment_id = ?")->execute([(int)$id]);
    assignments_delete_stored($row['teacher_stored_name'] ?? '');
    $pdo->prepare("DELETE FROM assignments WHERE id = ? AND teacher_id = ?")->execute([(int)$id, (int)$teacher_id]);
}

function assignments_submissions(PDO $pdo, array $asg) {
    $roster = assignments_class_roster($pdo, (int)$asg['class_id']);
    $st = $pdo->prepare("SELECT * FROM assignment_submissions WHERE assignment_id = ?");
    $st->execute([(int)$asg['id']]);
    $by = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $by[(int)$row['student_id']] = $row;
    }
    $filesBySub = assignments_submission_files_by_asg($pdo, (int)$asg['id']);
    $out = [];
    $done = 0;
    foreach ($roster as $stu) {
        $sub = $by[(int)$stu['id']] ?? null;
        $sid = $sub ? (int)$sub['id'] : 0;
        $pubFiles = [];
        if ($sub) {
            $pubFiles = assignments_build_public_files(
                $filesBySub[$sid] ?? [],
                $sub['stored_name'] ?? '',
                $sub['original_name'] ?? '',
                (int)($sub['file_size'] ?? 0),
                $sub['mime'] ?? '',
                $sub['submitted_at'] ?? ''
            );
        }
        $has = $sub && (trim((string)($sub['stored_name'] ?? '')) !== '' || !empty($pubFiles));
        if ($has) {
            $done++;
        }
        $state = assignments_due_state([
            'due_at' => $asg['due_at'] ?? '',
            'submitted_at' => $has ? ($sub['submitted_at'] ?? '') : '',
            'sub_stored_name' => $has ? ($sub['stored_name'] ?? '') : '',
        ]);
        $key = !$has ? ($state['overdue'] ? 'overdue' : 'pending') : ($state['late_submit'] ? 'late' : 'submitted');
        $grade = $sub ? assignments_grade_fields($sub) : [
            'graded' => false,
            'review_status' => '',
            'review_label' => '',
            'score' => null,
            'score_label' => '',
            'feedback' => '',
            'graded_label' => '',
        ];
        $out[] = [
            'student_id' => (int)$stu['id'],
            'full_name' => (string)($stu['full_name'] ?? ''),
            'username' => (string)($stu['username'] ?? ''),
            'submitted' => $has,
            'submission_id' => $has ? (int)$sub['id'] : 0,
            'file_name' => $has ? (string)$sub['original_name'] : '',
            'file_size_label' => $has ? assignments_bytes_label((int)$sub['file_size']) : '',
            'files' => $pubFiles,
            'file_count' => count($pubFiles),
            'submitted_label' => $has ? assignments_human_datetime($sub['submitted_at'] ?? '') : '',
            'status_key' => $key,
            'status_label' => assignments_status_label($key),
        ] + $grade;
    }
    return [
        'rows' => $out,
        'submitted' => $done,
        'total' => count($roster),
    ];
}

function assignments_find_submission(PDO $pdo, $submission_id) {
    $st = $pdo->prepare("SELECT * FROM assignment_submissions WHERE id = ? LIMIT 1");
    $st->execute([(int)$submission_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function assignments_grade(PDO $pdo, $teacher_id, $submission_id, $review_raw, $feedback, $assignment_id = 0, $student_id = 0) {
    $submission_id = (int)$submission_id;
    $assignment_id = (int)$assignment_id;
    $student_id = (int)$student_id;

    $sub = $submission_id > 0 ? assignments_find_submission($pdo, $submission_id) : null;
    if ($sub) {
        $assignment_id = (int)$sub['assignment_id'];
        $student_id = (int)$sub['student_id'];
    }
    $asg = assignments_find_teacher($pdo, $assignment_id, $teacher_id);
    if (!$asg) {
        throw new Exception('اجازه بررسی این تکلیف را ندارید.');
    }
    if ($student_id <= 0) {
        throw new Exception('دانش‌آموز مشخص نیست.');
    }

    $review = assignments_parse_review_status($review_raw);
    $note = assignments_clip($feedback, 400);
    $note = $note !== '' ? $note : null;

    if (!$sub) {
        $st = $pdo->prepare("SELECT * FROM assignment_submissions WHERE assignment_id = ? AND student_id = ? LIMIT 1");
        $st->execute([$assignment_id, $student_id]);
        $sub = $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    if ($review === '') {
        if ($sub) {
            $pdo->prepare("
                UPDATE assignment_submissions
                SET review_status = NULL, score = NULL, feedback = ?, graded_at = NULL, graded_by = NULL
                WHERE id = ?
            ")->execute([$note, (int)$sub['id']]);
        }
    } else {
        $now = assignments_now();
        if ($sub) {
            $pdo->prepare("
                UPDATE assignment_submissions
                SET review_status = ?, score = NULL, feedback = ?, graded_at = ?, graded_by = ?
                WHERE id = ?
            ")->execute([$review, $note, $now, (int)$teacher_id, (int)$sub['id']]);
        } else {
            $pdo->prepare("
                INSERT INTO assignment_submissions
                    (assignment_id, class_id, student_id, stored_name, original_name, mime, file_size, submitted_at, score, feedback, graded_at, graded_by, review_status)
                VALUES (?, ?, ?, '', '', '', 0, NULL, NULL, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    review_status = VALUES(review_status),
                    score = NULL,
                    feedback = VALUES(feedback),
                    graded_at = VALUES(graded_at),
                    graded_by = VALUES(graded_by)
            ")->execute([$assignment_id, (int)$asg['class_id'], $student_id, $note, $now, (int)$teacher_id, $review]);
        }
    }

    $fresh = assignments_find_teacher($pdo, (int)$asg['id'], $teacher_id);
    return [
        'assignment' => $fresh,
        'pack' => assignments_submissions($pdo, $fresh ?: $asg),
        'message' => $review === '' ? 'وضعیت تکلیف برداشته شد.' : ('وضعیت تکلیف ثبت شد: ' . assignments_review_status_label($review)),
    ];
}

function assignments_student_list_sql($with_sub) {
    $select = "
        SELECT a.id, a.class_id, a.teacher_id, a.session_no, a.title, a.body,
               a.teacher_stored_name, a.teacher_original_name, a.teacher_mime, a.teacher_size,
               a.due_at, a.status, a.created_at, c.title AS class_title
    ";
    if ($with_sub) {
        $select .= ",
               s.id AS submission_id, s.stored_name AS sub_stored_name,
               s.original_name AS sub_original_name, s.mime AS sub_mime, s.file_size AS sub_file_size,
               s.submitted_at, s.score AS sub_score, s.feedback AS sub_feedback,
               s.graded_at AS sub_graded_at, s.review_status AS sub_review_status, s.review_status AS sub_review_status,
               (SELECT COUNT(*) FROM assignment_submission_files sf WHERE sf.submission_id = s.id) AS sub_file_count";
    } else {
        $select .= ",
               NULL AS submission_id, '' AS sub_stored_name, '' AS sub_original_name, '' AS sub_mime,
               0 AS sub_file_size, NULL AS submitted_at,
               NULL AS sub_score, '' AS sub_feedback, NULL AS sub_graded_at, '' AS sub_review_status, 0 AS sub_file_count";
    }
    $from = "
        FROM assignments a
        JOIN classes c ON c.id = a.class_id
        JOIN class_students cs ON cs.class_id = c.id AND cs.user_id = ?
        JOIN terms t ON t.id = c.term_id
    ";
    if ($with_sub) {
        $from .= " LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = ?";
    }
    return $select . $from . "
        WHERE a.status = 'published' AND " . assignments_class_sql() . "
        ORDER BY c.title ASC, a.session_no ASC, a.id DESC
    ";
}

function assignments_student_list(PDO $pdo, $student_id) {
    assignments_fail_fast($pdo);
    $sid = (int)$student_id;
    try {
        $st = $pdo->prepare(assignments_student_list_sql(true));
        $st->execute([$sid, $sid]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log('assignments_student_list: ' . $e->getMessage());
        try {
            $st = $pdo->prepare(assignments_student_list_sql(false));
            $st->execute([$sid]);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e2) {
            error_log('assignments_student_list fallback: ' . $e2->getMessage());
            return [];
        }
    }
}

function assignments_find_student(PDO $pdo, $id, $student_id) {
    assignments_fail_fast($pdo);
    $st = $pdo->prepare("
        SELECT a.*, c.title AS class_title,
               s.id AS submission_id, s.stored_name AS sub_stored_name,
               s.original_name AS sub_original_name, s.mime AS sub_mime, s.file_size AS sub_file_size,
               s.submitted_at, s.score AS sub_score, s.feedback AS sub_feedback,
               s.graded_at AS sub_graded_at, s.review_status AS sub_review_status
        FROM assignments a
        JOIN classes c ON c.id = a.class_id
        JOIN class_students cs ON cs.class_id = c.id AND cs.user_id = ?
        JOIN terms t ON t.id = c.term_id
        LEFT JOIN assignment_submissions s ON s.assignment_id = a.id AND s.student_id = ?
        WHERE a.id = ? AND a.status = 'published' AND " . assignments_class_sql() . "
        LIMIT 1
    ");
    $st->execute([(int)$student_id, (int)$student_id, (int)$id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function assignments_normalize_files_input($raw) {
    if (!is_array($raw) || !isset($raw['error'])) {
        return [];
    }
    if (!is_array($raw['error'])) {
        if ((int)$raw['error'] === UPLOAD_ERR_NO_FILE) {
            return [];
        }
        return [$raw];
    }
    $list = [];
    foreach ($raw['error'] as $k => $err) {
        if ((int)$err === UPLOAD_ERR_NO_FILE) {
            continue;
        }
        $list[] = [
            'name' => $raw['name'][$k] ?? 'file',
            'type' => $raw['type'][$k] ?? '',
            'tmp_name' => $raw['tmp_name'][$k] ?? '',
            'error' => $err,
            'size' => $raw['size'][$k] ?? 0,
        ];
    }
    return $list;
}

function assignments_sync_submission_primary(PDO $pdo, $submission_id) {
    $sid = (int)$submission_id;
    if ($sid <= 0) {
        return;
    }
    $files = assignments_submission_files_by_sub($pdo, $sid);
    if (!$files) {
        $pdo->prepare("DELETE FROM assignment_submissions WHERE id = ?")->execute([$sid]);
        return;
    }
    $first = $files[0];
    $totalSize = 0;
    foreach ($files as $f) {
        $totalSize += (int)($f['file_size'] ?? 0);
    }
    $pdo->prepare("
        UPDATE assignment_submissions
        SET stored_name = ?, original_name = ?, mime = ?, file_size = ?
        WHERE id = ?
    ")->execute([
        (string)$first['stored_name'],
        (string)$first['original_name'],
        substr((string)($first['mime'] ?? ''), 0, 80),
        $totalSize,
        $sid,
    ]);
}

function assignments_submit(PDO $pdo, $student_id, $assignment_id, array $rawFiles) {
    $asg = assignments_find_student($pdo, $assignment_id, $student_id);
    if (!$asg) {
        throw new Exception('این تکلیف برای شما باز نیست.');
    }
    $incoming = assignments_normalize_files_input($rawFiles);
    if (!$incoming) {
        throw new Exception('یک فایل یا ویس انتخاب کنید.');
    }

    $existingSubId = (int)($asg['submission_id'] ?? 0);
    $existingFiles = $existingSubId > 0 ? assignments_submission_files_by_sub($pdo, $existingSubId) : [];
    if ($existingSubId > 0 && !$existingFiles && trim((string)($asg['sub_stored_name'] ?? '')) !== '') {
        assignments_backfill_submission_files($pdo);
        $existingFiles = assignments_submission_files_by_sub($pdo, $existingSubId);
    }

    $curImages = 0;
    $curAudio = 0;
    foreach ($existingFiles as $ef) {
        $ek = (string)($ef['file_kind'] ?? '');
        if ($ek === '') {
            $ek = assignments_file_kind_by_ext(assignments_ext($ef['stored_name'] ?? ''));
        }
        if ($ek === 'image') {
            $curImages++;
        } elseif ($ek === 'audio') {
            $curAudio++;
        }
    }
    $curTotal = count($existingFiles);

    $newImages = 0;
    $newAudio = 0;
    foreach ($incoming as $f) {
        $ext = assignments_ext(assignments_safe_orig($f['name'] ?? 'file'));
        $k = assignments_file_kind_by_ext($ext);
        if ($k === 'image') {
            $newImages++;
        } elseif ($k === 'audio') {
            $newAudio++;
        }
    }

    $maxImages = assignments_max_images();
    $maxAudio = assignments_max_audio();
    $maxFiles = assignments_max_files();

    if ($curImages + $newImages > $maxImages) {
        if ($curImages >= $maxImages) {
            throw new Exception('حداکثر ' . $maxImages . ' عکس مجاز است و الان تکمیل شده. برای آپلود عکس جدید ابتدا یکی از عکس‌های قبلی را حذف کنید.');
        }
        $rem = $maxImages - $curImages;
        throw new Exception('حداکثر ' . $maxImages . ' عکس برای هر تکلیف مجاز است (الان می‌توانید حداکثر ' . $rem . ' عکس دیگر اضافه کنید).');
    }
    if ($curAudio + $newAudio > $maxAudio) {
        throw new Exception('حداکثر ' . $maxAudio . ' فایل ویس برای هر تکلیف مجاز است.');
    }
    if ($curTotal + count($incoming) > $maxFiles) {
        throw new Exception('حداکثر ' . $maxFiles . ' فایل برای هر تکلیف مجاز است.');
    }

    $storedBatch = [];
    $prefix = 's' . (int)$student_id . 'a' . (int)$assignment_id;
    try {
        foreach ($incoming as $f) {
            $up = assignments_store_upload($f, $prefix);
            if ($up) {
                $storedBatch[] = $up;
            }
        }
        if (!$storedBatch) {
            throw new Exception('فایلی برای ذخیره انتخاب نشد.');
        }

        $now = assignments_now();
        $first = $storedBatch[0];
        $pdo->prepare("
            INSERT INTO assignment_submissions
                (assignment_id, class_id, student_id, stored_name, original_name, mime, file_size, submitted_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                submitted_at = VALUES(submitted_at),
                score = NULL,
                feedback = NULL,
                graded_at = NULL,
                graded_by = NULL
        ")->execute([
            (int)$asg['id'],
            (int)$asg['class_id'],
            (int)$student_id,
            $first['stored_name'],
            $first['original_name'],
            substr((string)$first['mime'], 0, 80),
            (int)$first['file_size'],
            $now,
        ]);

        $freshRow = assignments_find_student($pdo, $assignment_id, $student_id);
        $subId = (int)($freshRow['submission_id'] ?? 0);
        if ($subId <= 0) {
            throw new Exception('ثبت تحویل انجام نشد.');
        }

        $insFile = $pdo->prepare("
            INSERT INTO assignment_submission_files
                (submission_id, assignment_id, student_id, file_kind, stored_name, original_name, mime, file_size, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($storedBatch as $up) {
            $insFile->execute([
                $subId,
                (int)$asg['id'],
                (int)$student_id,
                $up['file_kind'],
                $up['stored_name'],
                $up['original_name'],
                substr((string)$up['mime'], 0, 100),
                (int)$up['file_size'],
                $now,
            ]);
        }

        assignments_sync_submission_primary($pdo, $subId);
    } catch (Exception $e) {
        foreach ($storedBatch as $up) {
            assignments_delete_stored($up['stored_name'] ?? '');
        }
        throw $e;
    }

    return assignments_find_student($pdo, $assignment_id, $student_id);
}

function assignments_delete_student_file(PDO $pdo, $student_id, $assignment_id, $file_id) {
    $asg = assignments_find_student($pdo, $assignment_id, $student_id);
    if (!$asg) {
        throw new Exception('این تکلیف برای شما باز نیست.');
    }
    $subId = (int)($asg['submission_id'] ?? 0);
    if ($subId <= 0) {
        throw new Exception('فایلی برای حذف وجود ندارد.');
    }
    $fid = (int)$file_id;
    if ($fid > 0) {
        $f = assignments_find_submission_file($pdo, $fid);
        if (!$f || (int)$f['submission_id'] !== $subId || (int)$f['student_id'] !== (int)$student_id) {
            throw new Exception('این فایل پیدا نشد.');
        }
        assignments_delete_stored($f['stored_name'] ?? '');
        $pdo->prepare("DELETE FROM assignment_submission_files WHERE id = ? AND submission_id = ?")
            ->execute([$fid, $subId]);
    } else {
        // Legacy fallback when file_id is 0
        $old = trim((string)($asg['sub_stored_name'] ?? ''));
        if ($old !== '') {
            assignments_delete_stored($old);
        }
        $pdo->prepare("DELETE FROM assignment_submission_files WHERE submission_id = ?")->execute([$subId]);
    }

    $remaining = assignments_submission_files_by_sub($pdo, $subId);
    if (!$remaining) {
        $pdo->prepare("DELETE FROM assignment_submissions WHERE id = ? AND student_id = ?")
            ->execute([$subId, (int)$student_id]);
    } else {
        $pdo->prepare("
            UPDATE assignment_submissions
            SET submitted_at = ?, score = NULL, feedback = NULL, graded_at = NULL, graded_by = NULL
            WHERE id = ? AND student_id = ?
        ")->execute([assignments_now(), $subId, (int)$student_id]);
        assignments_sync_submission_primary($pdo, $subId);
    }

    return assignments_find_student($pdo, $assignment_id, $student_id);
}

function assignments_oversized_post() {
    $len = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
    return $len > 0 && empty($_POST) && empty($_FILES);
}
