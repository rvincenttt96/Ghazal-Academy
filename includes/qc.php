<?php
/**
 * Teacher Quality Control (QC) & Educational Administration System (EAS) Engine
 * Ghazal Educational Academy
 *
 * Automated Class-Level QC (6 items x 10 = 60 marks):
 *  1. Roll Call (10): submitted within start_time + 30 min of session date (-1 per late/missing session)
 *  2. Quizzes & Final Marks (10): S.4, S.6, S.7, S.8, S.11 before next session start; S.13 (4 marks) before S.13 end (-1 per milestone session)
 *  3. Session Plan (10): S.1..12 before next session start, S.13 before S.13 end (-1 per incomplete session)
 *  4. Error Gravity (10): submitted before S.8 start (0 or 10, all-or-nothing)
 *  5. Assignment Review (10): student submissions uploaded before S.N+1 reviewed before S.N+1 end (-1 per late/ungraded submission)
 *  6. General Term Plan Voice (10): voice note recorded/uploaded before S.3 start (0 or 10, all-or-nothing)
 *
 * Supervisor Term-Level QC (4 items x 10 = 40 marks, default 0):
 *  - Observation (10)
 *  - Poll (10)
 *  - Subs (10)
 *  - Courses Attendance (10)
 *
 * Overall Teacher QC Mark (100):
 *  Average of active class QC scores (out of 60) + Supervisor manual marks (out of 40)
 */

if (!defined('QC_VERSION')) {
    define('QC_VERSION', '1.0.0');
}

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/terms.php';
require_once __DIR__ . '/jalali.php';
require_once __DIR__ . '/class-live.php';
require_once __DIR__ . '/quizzes.php';
require_once __DIR__ . '/term-plan.php';

function qc_ensure_schema(PDO $pdo) {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    try {
        // 1. Table for supervisor manual marks (per teacher per term)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS teacher_qc_supervisor_scores (
                teacher_id INT NOT NULL,
                term_id INT NOT NULL,
                observation DECIMAL(4,1) NOT NULL DEFAULT 0.0,
                poll DECIMAL(4,1) NOT NULL DEFAULT 0.0,
                subs DECIMAL(4,1) NOT NULL DEFAULT 0.0,
                courses_attendance DECIMAL(4,1) NOT NULL DEFAULT 0.0,
                notes TEXT NULL,
                updated_by INT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (teacher_id, term_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // 2. Table for QC SMS alerts log (prevent duplicate sends)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS qc_sms_alerts_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                teacher_id INT NOT NULL,
                class_id INT NOT NULL,
                item_key VARCHAR(64) NOT NULL,
                phone VARCHAR(32) NOT NULL,
                sent_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                UNIQUE KEY uk_teacher_class_item (teacher_id, class_id, item_key),
                KEY idx_teacher (teacher_id),
                KEY idx_class (class_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // 3. Table for General Term Plan voice recordings/uploads
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS term_plan_voice (
                id INT AUTO_INCREMENT PRIMARY KEY,
                class_id INT NOT NULL,
                teacher_id INT NOT NULL,
                audio_path VARCHAR(255) NOT NULL,
                original_name VARCHAR(255) NULL,
                file_size INT NOT NULL DEFAULT 0,
                duration INT NOT NULL DEFAULT 0,
                recorded_at DATETIME NULL,
                is_deleted TINYINT NOT NULL DEFAULT 0,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uq_tpv_class (class_id),
                KEY idx_tpv_teacher (teacher_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // 4. Table for QC parameter exemptions per class (neutralized parameters)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS qc_class_exemptions (
                class_id INT NOT NULL,
                item_key VARCHAR(32) NOT NULL,
                exempted_by INT NULL,
                created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (class_id, item_key),
                KEY idx_class (class_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // 3. Ensure timestamps on attendance table
        $col = $pdo->query("SHOW COLUMNS FROM attendance LIKE 'created_at'")->fetch();
        if (!$col) {
            $pdo->exec("ALTER TABLE attendance ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        }
        $col2 = $pdo->query("SHOW COLUMNS FROM attendance LIKE 'updated_at'")->fetch();
        if (!$col2) {
            $pdo->exec("ALTER TABLE attendance ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }

        // 4. Ensure timestamps on student_quizzes table
        $col3 = $pdo->query("SHOW COLUMNS FROM student_quizzes LIKE 'created_at'")->fetch();
        if (!$col3) {
            $pdo->exec("ALTER TABLE student_quizzes ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        }
        $col4 = $pdo->query("SHOW COLUMNS FROM student_quizzes LIKE 'updated_at'")->fetch();
        if (!$col4) {
            $pdo->exec("ALTER TABLE student_quizzes ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }

        // 5. Ensure timestamps on error_gravity
        $col5 = $pdo->query("SHOW COLUMNS FROM error_gravity LIKE 'created_at'")->fetch();
        if (!$col5) {
            $pdo->exec("ALTER TABLE error_gravity ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP");
        }
        $col6 = $pdo->query("SHOW COLUMNS FROM error_gravity LIKE 'updated_at'")->fetch();
        if (!$col6) {
            $pdo->exec("ALTER TABLE error_gravity ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }

    } catch (Exception $e) {
        error_log('qc_ensure_schema: ' . $e->getMessage());
    }
}

/**
 * Convert any Gregorian Y-m-d to Jalali string Y/m/d with zero padding
 */
function qc_format_jalali($ymd) {
    if (!$ymd || $ymd === '0000-00-00') {
        return '';
    }
    $p = jalali_from_ymd($ymd);
    if (!$p) {
        return '';
    }
    return $p[0] . '/' . jalali_pad($p[1]) . '/' . jalali_pad($p[2]);
}

/**
 * Compute concrete calendar and session dates for a class based on EAS and schedule
 */
function eas_compute_class_session_dates(PDO $pdo, array $class, $total_sessions = null) {
    $class_type = strtolower(trim((string)($class['class_type'] ?? '')));
    if ($total_sessions === null) {
        $total_sessions = ($class_type === 'private') ? 10 : 13;
    }

    $start = trim((string)($class['starts_on'] ?? $class['term_starts_on'] ?? ''));
    if ($start === '' || $start === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
        if (!empty($class['term_id'])) {
            $st = $pdo->prepare("SELECT starts_on FROM terms WHERE id = ?");
            $st->execute([(int)$class['term_id']]);
            $start = trim((string)($st->fetchColumn() ?: ''));
        }
    }
    if ($start === '' || $start === '0000-00-00' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start)) {
        $start = date('Y-m-d');
    }

    // Generate class calendar dates according to schedule_type (even: Sat/Mon/Wed, odd: Sun/Tue/Thu)
    $class_days = [];
    $ts = strtotime($start . ' 12:00:00');
    $guard = 0;
    while (count($class_days) < ($total_sessions + 20) && $guard < 300) {
        $guard++;
        if (term_plan_day_matches($class, $ts)) {
            $class_days[] = date('Y-m-d', $ts);
        }
        $ts = strtotime('+1 day', $ts);
    }

    $makeups = [];
    if (!empty($class['makeup_sessions_json'])) {
        $makeups = is_array($class['makeup_sessions_json'])
            ? $class['makeup_sessions_json']
            : (json_decode($class['makeup_sessions_json'], true) ?: []);
    }

    $saved_dates = [];
    if (!empty($class['session_dates_json'])) {
        $saved_dates = is_array($class['session_dates_json'])
            ? $class['session_dates_json']
            : (json_decode($class['session_dates_json'], true) ?: []);
    }

    $sched_str = trim((string)($class['schedule'] ?: ($class['class_time'] ?? '')));

    $sessions = [];
    $cal_index = 0;

    for ($i = 1; $i <= $total_sessions; $i++) {
        $status = (int)($class['session_' . $i] ?? 0); // 0: unset, 1: held, 2: cancelled
        $is_cancelled = ($status === 2);

        $cal_day = $class_days[$cal_index] ?? date('Y-m-d', strtotime("$start +" . ($i * 2) . " days"));
        $cal_index++;

        $makeup_raw = trim((string)($makeups['makeup_' . $i] ?? ''));
        $makeup_ymd = null;
        if ($makeup_raw !== '') {
            $makeup_ymd = term_plan_parse_date_value($makeup_raw);
        }

        // If cancelled without an explicit makeup date on another day,
        // this cancelled day was skipped and the session shifted forward.
        $session_ymd = $cal_day;
        if ($makeup_ymd && $is_cancelled) {
            $session_ymd = $makeup_ymd;
        }

        $jalali_date = qc_format_jalali($session_ymd);

        $day_ts = strtotime($session_ymd . ' 12:00:00');
        $window = class_live_parse_schedule($sched_str, $day_ts);
        $start_ts = $window ? (int)$window[0] : strtotime($session_ymd . ' 16:00:00');
        $end_ts = $window ? (int)$window[1] : ($start_ts + 90 * 60);

        $sessions[$i] = [
            'session_no' => $i,
            'status' => $status,
            'is_held' => ($status === 1),
            'is_cancelled' => $is_cancelled,
            'date_ymd' => $session_ymd,
            'date_jalali' => $jalali_date,
            'start_ts' => $start_ts,
            'end_ts' => $end_ts,
            'makeup_raw' => $makeup_raw,
            'makeup_ymd' => $makeup_ymd,
        ];
    }

    return $sessions;
}

/**
 * Retrieve list of exempted parameter keys for a given class
 * @return array list of keys e.g. ['error_gravity', 'voice_plan']
 */
function qc_get_class_exemptions(PDO $pdo, $class_id) {
    qc_ensure_schema($pdo);
    $st = $pdo->prepare("SELECT item_key FROM qc_class_exemptions WHERE class_id = ?");
    $st->execute([(int)$class_id]);
    return $st->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

/**
 * Toggle exemption status of a parameter for a class
 * When exempt, the parameter is neutralized and its marks are redistributed across active parameters.
 * @return array ['success' => bool, 'is_exempt' => bool]
 */
function qc_toggle_class_exemption(PDO $pdo, $class_id, $item_key, $user_id = null) {
    qc_ensure_schema($pdo);
    $class_id = (int)$class_id;
    $item_key = trim((string)$item_key);
    $allowed = ['roll_call', 'quizzes', 'session_plan', 'error_gravity', 'assignments', 'voice_plan'];
    if (!in_array($item_key, $allowed, true)) {
        return ['success' => false, 'error' => 'invalid_item'];
    }

    $st = $pdo->prepare("SELECT 1 FROM qc_class_exemptions WHERE class_id = ? AND item_key = ?");
    $st->execute([$class_id, $item_key]);
    if ($st->fetch()) {
        $del = $pdo->prepare("DELETE FROM qc_class_exemptions WHERE class_id = ? AND item_key = ?");
        $del->execute([$class_id, $item_key]);
        return ['success' => true, 'is_exempt' => false];
    } else {
        $ins = $pdo->prepare("INSERT INTO qc_class_exemptions (class_id, item_key, exempted_by) VALUES (?, ?, ?)");
        $ins->execute([$class_id, $item_key, $user_id ? (int)$user_id : null]);
        return ['success' => true, 'is_exempt' => true];
    }
}

/**
 * Calculate the 6 automated QC scores for a single class.
 * Returns an array with individual marks (0..10 each), total_score (0..60), and detailed audits.
 * Neutralized parameters are exempted, and their marks are proportionally redistributed among active items.
 */
function qc_calculate_class(PDO $pdo, array $class) {
    qc_ensure_schema($pdo);
    $now = time();
    $class_id = (int)($class['id'] ?? $class['main_class_id'] ?? 0);
    $teacher_id = (int)($class['teacher_id'] ?? 0);

    $exemptions = qc_get_class_exemptions($pdo, $class_id);
    $is_exempt = function($k) use ($exemptions) {
        return in_array($k, $exemptions, true);
    };

    $total_sessions = (strtolower(trim((string)($class['class_type'] ?? ''))) === 'private') ? 10 : 13;
    $sessions = eas_compute_class_session_dates($pdo, $class, $total_sessions);

    // Active students in class
    $st_std = $pdo->prepare("
        SELECT u.id, u.full_name, u.username
        FROM users u
        JOIN class_students cs ON u.id = cs.user_id
        WHERE cs.class_id = ? AND u.role = 'student' AND IFNULL(u.is_deleted, 0) = 0
    ");
    $st_std->execute([$class_id]);
    $students = $st_std->fetchAll(PDO::FETCH_ASSOC);
    $student_ids = array_column($students, 'id');

    // -------------------------------------------------------------
    // ITEM 1: Roll Call (10 marks)
    // Deadline: start_time + 30 min of session date. -1 per late/missing session.
    // -------------------------------------------------------------
    $rc_score = 10;
    $rc_deductions = [];
    $rc_passed = 0;
    $rc_total_due = 0;

    // Load all attendance records for this class
    $st_att = $pdo->prepare("
        SELECT class_date, user_id, is_present, status, created_at, updated_at
        FROM attendance
        WHERE class_id = ?
        ORDER BY class_date ASC, id ASC
    ");
    $st_att->execute([$class_id]);
    $attendance_by_date = [];
    foreach ($st_att->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $attendance_by_date[$row['class_date']][] = $row;
    }
    // Also get ordered list of distinct dates where attendance was recorded (matching attendance.php)
    $taken_dates = array_keys($attendance_by_date);
    sort($taken_dates);

    // Load any missing-alert records for this class (official T+30 late trigger from cron_rollcall_alert)
    $alerts_by_date = [];
    try {
        $st_alerts = $pdo->prepare("
            SELECT class_date, sent_at
            FROM rollcall_missing_alerts
            WHERE class_id = ?
        ");
        $st_alerts->execute([$class_id]);
        foreach ($st_alerts->fetchAll(PDO::FETCH_ASSOC) as $arow) {
            $alerts_by_date[$arow['class_date']] = $arow;
        }
    } catch (Exception $e) {
        $alerts_by_date = [];
    }

    for ($i = 1; $i <= $total_sessions; $i++) {
        $sess = $sessions[$i];
        if ($sess['is_cancelled']) {
            continue;
        }
        $deadline_ts = $sess['start_ts'] + 30 * 60;
        if ($now < $deadline_ts) {
            // Not due yet
            continue;
        }
        $rc_total_due++;
        $date_ymd = $sess['date_ymd'];

        // Match attendance records: either on exact calendar date, or by session sequence ($i - 1)
        $records = $attendance_by_date[$date_ymd] ?? [];
        $matched_date = $date_ymd;
        if (empty($records) && isset($taken_dates[$i - 1])) {
            $matched_date = $taken_dates[$i - 1];
            $records = $attendance_by_date[$matched_date] ?? [];
        }

        if (empty($records)) {
            $rc_score -= 1;
            $rc_deductions[] = [
                'session' => $i,
                'date' => $sess['date_jalali'],
                'reason' => "جلسه $i ({$sess['date_jalali']}): عدم ثبت رول‌کال در مهلت ۳۰ دقیقه‌ای پس از شروع کلاس",
            ];
            continue;
        }

        // Attendance was taken! Now check whether it was submitted within the 30-minute window.
        $is_late = false;
        $late_reason = '';

        // 1. Check if cron triggered a late alert (official T+30 ping)
        if (isset($alerts_by_date[$matched_date]) || isset($alerts_by_date[$date_ymd])) {
            $is_late = true;
            $late_reason = "ثبت رول‌کال پس از مهلت ۳۰ دقیقه (ارسال هشدار تأخیر)";
        } else {
            // 2. Check recorded timestamp if available and reliable
            $earliest_ts = null;
            $created_date = null;
            foreach ($records as $r) {
                if (!empty($r['created_at'])) {
                    $ts = strtotime($r['created_at']);
                    if ($earliest_ts === null || $ts < $earliest_ts) {
                        $earliest_ts = $ts;
                        $created_date = substr($r['created_at'], 0, 10);
                    }
                }
            }

            // Note on historical data:
            // The created_at column was added to table `attendance` on 2026-11-14.
            // Any past session (matched_date < 2026-11-14) where created_date is 2026-11-14
            // was backfilled during migration and must NOT be treated as late.
            if ($earliest_ts !== null) {
                $is_historical_backfill = ($created_date === '2026-11-14' && $matched_date < '2026-11-14');
                if (!$is_historical_backfill) {
                    if ($created_date > $matched_date) {
                        // Submitted on a later day than class day!
                        $is_late = true;
                        $late_reason = "ثبت رول‌کال در تاریخی پس از روز برگزاری کلاس ($created_date)";
                    } elseif ($created_date === $matched_date && $earliest_ts > ($deadline_ts + 90)) {
                        // Submitted on class day, but after start_time + 30 min (+ 90 sec grace)
                        $is_late = true;
                        $late_reason = "ثبت رول‌کال با تأخیر (پس از پایان مهلت ۳۰ دقیقه)";
                    }
                }
            }
        }

        if ($is_late) {
            $rc_score -= 1;
            $rc_deductions[] = [
                'session' => $i,
                'date' => $sess['date_jalali'],
                'reason' => "جلسه $i ({$sess['date_jalali']}): $late_reason",
            ];
        } else {
            $rc_passed++;
        }
    }
    $rc_score = max(0, $rc_score);

    // -------------------------------------------------------------
    // ITEM 2: Quizzes & Final Marks (10 marks)
    // S.4, S.6, S.7, S.8, S.11 before next session start; S.13 (4 marks) before S.13 end.
    // Absent students exempt. -1 per milestone session if incomplete/late.
    // -------------------------------------------------------------
    $quiz_score = 10;
    $quiz_deductions = [];

    // Load student quizzes
    $st_q = $pdo->prepare("
        SELECT user_id, quiz_1, quiz_2, quiz_3, quiz_4, quiz_5, quiz_6, quiz_7, quiz_8, quiz_9, created_at, updated_at
        FROM student_quizzes
        WHERE class_id = ?
    ");
    $st_q->execute([$class_id]);
    $quizzes_by_user = [];
    foreach ($st_q->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $quizzes_by_user[(int)$row['user_id']] = $row;
    }

    $quiz_milestones = [
        4  => ['col' => 'quiz_1', 'next' => 5,  'label' => 'کوییز ۱ (جلسه ۴)'],
        6  => ['col' => 'quiz_2', 'next' => 7,  'label' => 'کوییز ۲ (جلسه ۶)'],
        7  => ['col' => 'quiz_3', 'next' => 8,  'label' => 'R/W ۱ (جلسه ۷)'],
        8  => ['col' => 'quiz_4', 'next' => 9,  'label' => 'کوییز ۳ (جلسه ۸)'],
        11 => ['col' => 'quiz_5', 'next' => 12, 'label' => 'کوییز ۴ (جلسه ۱۱)'],
    ];

    foreach ($quiz_milestones as $m_session => $m_info) {
        if (!isset($sessions[$m_session]) || !isset($sessions[$m_info['next']])) {
            continue;
        }
        $next_start = $sessions[$m_info['next']]['start_ts'];
        if ($now < $next_start) {
            continue; // Not due yet
        }

        // Who was present in milestone session?
        $m_date = $sessions[$m_session]['date_ymd'];
        $m_att = $attendance_by_date[$m_date] ?? [];
        if (empty($m_att) && isset($taken_dates[$m_session - 1])) {
            $m_att = $attendance_by_date[$taken_dates[$m_session - 1]] ?? [];
        }
        $present_users = [];
        foreach ($m_att as $row) {
            if (($row['status'] ?? '') === 'present' || (int)($row['is_present'] ?? 0) === 1) {
                $present_users[] = (int)$row['user_id'];
            }
        }

        // If no attendance was taken, consider active students
        $check_users = !empty($present_users) ? $present_users : $student_ids;
        $missing = false;
        $col_name = $m_info['col'];

        foreach ($check_users as $uid) {
            $val = $quizzes_by_user[$uid][$col_name] ?? null;
            if ($val === null || $val === '') {
                $missing = true;
                break;
            }
        }

        if ($missing) {
            $quiz_score -= 1;
            $quiz_deductions[] = [
                'session' => $m_session,
                'milestone' => $m_info['label'],
                'reason' => "عدم ثبت نمرات {$m_info['label']} تا قبل از شروع جلسه {$m_info['next']}",
            ];
        }
    }

    // Session 13 final marks (Quiz 5, R/W 2, Presence, Class Activity)
    if (isset($sessions[13])) {
        $s13_end = $sessions[13]['end_ts'];
        if ($now >= $s13_end) {
            $s13_missing = false;
            foreach ($student_ids as $uid) {
                $row = $quizzes_by_user[$uid] ?? [];
                if (!quizzes_filled($row['quiz_6'] ?? null) ||
                    !quizzes_filled($row['quiz_7'] ?? null) ||
                    !quizzes_filled($row['quiz_8'] ?? null) ||
                    !quizzes_filled($row['quiz_9'] ?? null)) {
                    $s13_missing = true;
                    break;
                }
            }
            if ($s13_missing) {
                $quiz_score -= 1;
                $quiz_deductions[] = [
                    'session' => 13,
                    'milestone' => 'نمرات ۴ گانه پایان ترم جلسه ۱۳',
                    'reason' => 'عدم ثبت کامل نمرات ۴ گانه جلسه ۱۳ (کوییز ۵، R/W ۲، حضور، فعالیت کلاسی) تا قبل از پایان جلسه ۱۳',
                ];
            }
        }
    }
    $quiz_score = max(0, $quiz_score);

    // -------------------------------------------------------------
    // ITEM 3: Session Plan (10 marks)
    // S.1..12 before next session start, S.13 before S.13 end. -1 per incomplete session.
    // -------------------------------------------------------------
    $sp_score = 10;
    $sp_deductions = [];

    // Load tasks and checks for this class
    $st_tasks = $pdo->prepare("
        SELECT t.id, t.session_no, c.status, c.teacher_remark, c.method
        FROM term_plan_tasks t
        LEFT JOIN term_plan_task_checks c ON t.id = c.task_id AND c.class_id = t.class_id
        WHERE t.class_id = ?
        ORDER BY t.session_no ASC, t.sort_order ASC
    ");
    $st_tasks->execute([$class_id]);
    $tasks_by_session = [];
    foreach ($st_tasks->fetchAll(PDO::FETCH_ASSOC) as $t) {
        $tasks_by_session[(int)$t['session_no']][] = $t;
    }

    for ($i = 1; $i <= $total_sessions; $i++) {
        if (!isset($sessions[$i])) {
            continue;
        }
        $deadline_ts = ($i < $total_sessions && isset($sessions[$i + 1]))
            ? $sessions[$i + 1]['start_ts']
            : $sessions[$i]['end_ts'];

        if ($now < $deadline_ts) {
            continue; // Not due yet
        }

        $session_tasks = $tasks_by_session[$i] ?? [];
        if (empty($session_tasks)) {
            // No tasks defined for this session
            continue;
        }

        $session_incomplete = false;
        foreach ($session_tasks as $task) {
            $t_status = $task['status'] ?? 'pending';
            if (!in_array($t_status, ['done', 'missed'], true)) {
                $session_incomplete = true;
                break;
            }
        }

        if ($session_incomplete) {
            $sp_score -= 1;
            $sp_deductions[] = [
                'session' => $i,
                'reason' => "جلسه $i: سشن‌پلن در مهلت مقرر (تا قبل از جلسه بعدی) تکمیل/ثبت نشده است",
            ];
        }
    }
    $sp_score = max(0, $sp_score);

    // -------------------------------------------------------------
    // ITEM 4: Error Gravity (10 marks, all-or-nothing 0 or 10)
    // Must be submitted before Session 8 start.
    // -------------------------------------------------------------
    $eg_score = 10;
    $eg_status = 'pending';
    $eg_reason = '';
    $s8_start = isset($sessions[8]) ? $sessions[8]['start_ts'] : ($sessions[1]['start_ts'] + 7 * 86400 * 2);

    $st_eg = $pdo->prepare("
        SELECT id, content, created_at, updated_at
        FROM error_gravity
        WHERE class_id = ? AND teacher_id = ? AND IFNULL(is_deleted, 0) = 0 AND TRIM(IFNULL(content, '')) != ''
        ORDER BY id DESC LIMIT 1
    ");
    $st_eg->execute([$class_id, $teacher_id]);
    $eg_row = $st_eg->fetch(PDO::FETCH_ASSOC);

    if ($eg_row) {
        $eg_ts = strtotime($eg_row['created_at'] ?: ($eg_row['updated_at'] ?: 'now'));
        if ($eg_ts <= ($s8_start + 120)) {
            $eg_score = 10;
            $eg_status = 'passed';
            $eg_reason = 'ارور گرویتی به موقع قبل از جلسه هشتم ثبت شد.';
        } else {
            $eg_score = 0;
            $eg_status = 'failed';
            $eg_reason = 'ارور گرویتی با تأخیر (پس از شروع جلسه هشتم) ثبت شد.';
        }
    } else {
        if ($now >= $s8_start) {
            $eg_score = 0;
            $eg_status = 'failed';
            $eg_reason = 'عدم ثبت ارور گرویتی تا قبل از شروع جلسه هشتم.';
        } else {
            $eg_score = 10;
            $eg_status = 'pending';
            $eg_reason = 'در انتظار ثبت تا قبل از جلسه هشتم.';
        }
    }

    // -------------------------------------------------------------
    // ITEM 5: Assignment Review (10 marks)
    // Submissions uploaded before Session N+1 must be graded before Session N+1 end.
    // -1 per late/ungraded student submission.
    // -------------------------------------------------------------
    $asg_score = 10;
    $asg_deductions = [];

    $st_asg = $pdo->prepare("
        SELECT a.id, a.session_no, sub.student_id, sub.submitted_at, sub.graded_at, sub.score, sub.feedback
        FROM assignments a
        JOIN assignment_submissions sub ON a.id = sub.assignment_id
        WHERE a.class_id = ?
    ");
    $st_asg->execute([$class_id]);
    $submissions = $st_asg->fetchAll(PDO::FETCH_ASSOC);

    foreach ($submissions as $sub) {
        $sess_n = (int)$sub['session_no'];
        $next_n = $sess_n + 1;
        if (!isset($sessions[$next_n])) {
            continue;
        }
        $next_start = $sessions[$next_n]['start_ts'];
        $next_end = $sessions[$next_n]['end_ts'];

        $sub_at = strtotime($sub['submitted_at'] ?? 'now');
        // Only evaluate if student uploaded before Session N+1 start
        if ($sub_at >= $next_start) {
            continue;
        }

        if ($now >= $next_end) {
            $graded_at = $sub['graded_at'] ? strtotime($sub['graded_at']) : null;
            $has_grade = ($graded_at !== null) || ($sub['score'] !== null && $sub['score'] !== '') || ($sub['feedback'] !== null && trim((string)$sub['feedback']) !== '');

            if (!$has_grade || ($graded_at !== null && $graded_at > ($next_end + 120))) {
                $asg_score -= 1;
                $asg_deductions[] = [
                    'session' => $sess_n,
                    'student_id' => $sub['student_id'],
                    'reason' => "تکلیف جلسه $sess_n برای دانش‌آموز تا قبل از پایان جلسه $next_n بررسی نشده است",
                ];
            }
        }
    }
    $asg_score = max(0, $asg_score);

    // -------------------------------------------------------------
    // ITEM 6: General Term Plan Voice (10 marks, all-or-nothing 0 or 10)
    // Must be uploaded/recorded before Session 3 start.
    // -------------------------------------------------------------
    $voice_score = 10;
    $voice_status = 'pending';
    $voice_reason = '';
    $s3_start = isset($sessions[3]) ? $sessions[3]['start_ts'] : ($sessions[1]['start_ts'] + 2 * 86400 * 2);

    $st_v = $pdo->prepare("
        SELECT id, audio_path, recorded_at, created_at
        FROM term_plan_voice
        WHERE class_id = ? AND teacher_id = ? AND IFNULL(is_deleted, 0) = 0
        ORDER BY id DESC LIMIT 1
    ");
    $st_v->execute([$class_id, $teacher_id]);
    $voice_row = $st_v->fetch(PDO::FETCH_ASSOC);

    if ($voice_row) {
        $v_ts = strtotime($voice_row['recorded_at'] ?: ($voice_row['created_at'] ?: 'now'));
        if ($v_ts <= ($s3_start + 120)) {
            $voice_score = 10;
            $voice_status = 'passed';
            $voice_reason = 'ویس پلن کلی به موقع قبل از جلسه سوم آپلود/ضبط شد.';
        } else {
            $voice_score = 0;
            $voice_status = 'failed';
            $voice_reason = 'ویس پلن کلی با تأخیر (پس از شروع جلسه سوم) آپلود شد.';
        }
    } else {
        if ($now >= $s3_start) {
            $voice_score = 0;
            $voice_status = 'failed';
            $voice_reason = 'عدم ضبط/آپلود ویس پلن کلی تا قبل از جلسه سوم.';
        } else {
            $voice_score = 10;
            $voice_status = 'pending';
            $voice_reason = 'در انتظار ضبط/آپلود تا قبل از شروع جلسه سوم.';
        }
    }

    $items = [
        'roll_call' => [
            'name' => 'رول‌کال',
            'title' => 'Roll Call',
            'max' => 10,
            'score' => $rc_score,
            'is_exempt' => $is_exempt('roll_call'),
            'deductions' => $rc_deductions,
            'passed_count' => $rc_passed,
            'total_due' => $rc_total_due,
        ],
        'quizzes' => [
            'name' => 'نمره‌دهی‌ها و کوییز',
            'title' => 'Quizzes & Final Marks',
            'max' => 10,
            'score' => $quiz_score,
            'is_exempt' => $is_exempt('quizzes'),
            'deductions' => $quiz_deductions,
        ],
        'session_plan' => [
            'name' => 'سشن پلن',
            'title' => 'Session Plan',
            'max' => 10,
            'score' => $sp_score,
            'is_exempt' => $is_exempt('session_plan'),
            'deductions' => $sp_deductions,
        ],
        'error_gravity' => [
            'name' => 'ارور گرویتی',
            'title' => 'Error Gravity',
            'max' => 10,
            'score' => $eg_score,
            'is_exempt' => $is_exempt('error_gravity'),
            'status' => $eg_status,
            'reason' => $eg_reason,
        ],
        'assignments' => [
            'name' => 'بررسی تکالیف',
            'title' => 'Assignments Review',
            'max' => 10,
            'score' => $asg_score,
            'is_exempt' => $is_exempt('assignments'),
            'deductions' => $asg_deductions,
        ],
        'voice_plan' => [
            'name' => 'ویس پلن کلی',
            'title' => 'General Term Plan Voice',
            'max' => 10,
            'score' => $voice_score,
            'is_exempt' => $is_exempt('voice_plan'),
            'status' => $voice_status,
            'reason' => $voice_reason,
            'has_voice' => !empty($voice_row),
            'voice_path' => $voice_row['audio_path'] ?? '',
        ],
    ];

    // Redistribution calculation:
    // If one or more parameters are neutralized (is_exempt = true), their 10 marks are
    // proportionally redistributed among the remaining active parameters so that
    // the total class score is always scaled out of 60.
    $active_max = 0;
    $active_earned = 0;
    $active_count = 0;
    $exempt_count = 0;

    foreach ($items as $k => $item) {
        if (!empty($item['is_exempt'])) {
            $exempt_count++;
            continue;
        }
        $active_count++;
        $active_max += 10;
        $active_earned += (float)$item['score'];
    }

    if ($active_max > 0) {
        $total_score = round(($active_earned / $active_max) * 60, 1);
    } else {
        // All items exempt
        $total_score = 60.0;
    }

    return [
        'class_id' => $class_id,
        'class_title' => $class['title'] ?? ($class['class_title'] ?? ''),
        'teacher_id' => $teacher_id,
        'total_score' => $total_score, // out of 60
        'active_count' => $active_count,
        'exempt_count' => $exempt_count,
        'items' => $items,
        'sessions' => $sessions,
    ];
}

/**
 * Retrieve supervisor manual scores for a teacher in a term (default 0 for all 4 items)
 */
function qc_get_teacher_supervisor_scores(PDO $pdo, $teacher_id, $term_id) {
    qc_ensure_schema($pdo);
    $st = $pdo->prepare("
        SELECT observation, poll, subs, courses_attendance, notes, updated_at
        FROM teacher_qc_supervisor_scores
        WHERE teacher_id = ? AND term_id = ?
    ");
    $st->execute([(int)$teacher_id, (int)$term_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);

    return [
        'observation' => (float)($row['observation'] ?? 0.0),
        'poll' => (float)($row['poll'] ?? 0.0),
        'subs' => (float)($row['subs'] ?? 0.0),
        'courses_attendance' => (float)($row['courses_attendance'] ?? 0.0),
        'notes' => (string)($row['notes'] ?? ''),
        'updated_at' => $row['updated_at'] ?? null,
        'total' => (float)(($row['observation'] ?? 0) + ($row['poll'] ?? 0) + ($row['subs'] ?? 0) + ($row['courses_attendance'] ?? 0)), // out of 40
    ];
}

/**
 * Save supervisor manual scores for a teacher in a term
 */
function qc_save_teacher_supervisor_scores(PDO $pdo, $teacher_id, $term_id, array $data, $supervisor_id) {
    qc_ensure_schema($pdo);
    $obs = min(10.0, max(0.0, (float)($data['observation'] ?? 0.0)));
    $poll = min(10.0, max(0.0, (float)($data['poll'] ?? 0.0)));
    $subs = min(10.0, max(0.0, (float)($data['subs'] ?? 0.0)));
    $att = min(10.0, max(0.0, (float)($data['courses_attendance'] ?? 0.0)));
    $notes = trim((string)($data['notes'] ?? ''));

    $st = $pdo->prepare("
        INSERT INTO teacher_qc_supervisor_scores
            (teacher_id, term_id, observation, poll, subs, courses_attendance, notes, updated_by, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
            observation = VALUES(observation),
            poll = VALUES(poll),
            subs = VALUES(subs),
            courses_attendance = VALUES(courses_attendance),
            notes = VALUES(notes),
            updated_by = VALUES(updated_by),
            updated_at = NOW()
    ");
    $st->execute([
        (int)$teacher_id,
        (int)$term_id,
        $obs,
        $poll,
        $subs,
        $att,
        $notes,
        (int)$supervisor_id,
    ]);

    return true;
}

/**
 * Calculate the overall teacher QC Mark (out of 100).
 * Overall = Class Automated Average (out of 60) + Supervisor Manual Score (out of 40)
 */
function qc_calculate_teacher_overall(PDO $pdo, $teacher_id, $term_id = null) {
    qc_ensure_schema($pdo);
    $teacher_id = (int)$teacher_id;

    // Get active classes for this teacher in the term
    $sql = "
        SELECT c.*, t.starts_on, t.ends_on, e.session_1, e.session_2, e.session_3, e.session_4,
               e.session_5, e.session_6, e.session_7, e.session_8, e.session_9, e.session_10,
               e.session_11, e.session_12, e.session_13, e.session_dates_json, e.makeup_sessions_json
        FROM classes c
        JOIN terms t ON c.term_id = t.id
        LEFT JOIN eas_management e ON c.id = e.class_id
        WHERE c.teacher_id = ?
          AND c.is_deleted = 0
          AND IFNULL(c.is_archived, 0) = 0
    ";
    $params = [$teacher_id];
    if ($term_id) {
        $sql .= " AND c.term_id = ?";
        $params[] = (int)$term_id;
    } else {
        $sql .= " AND " . term_sql_teaching('t');
    }
    $sql .= " ORDER BY c.id DESC";

    $st = $pdo->prepare($sql);
    $st->execute($params);
    $classes = $st->fetchAll(PDO::FETCH_ASSOC);

    $class_results = [];
    $total_classes_score = 0;
    foreach ($classes as $cl) {
        $res = qc_calculate_class($pdo, $cl);
        $class_results[] = $res;
        $total_classes_score += $res['total_score'];
    }

    $class_count = count($classes);
    $classes_avg_60 = $class_count > 0 ? round($total_classes_score / $class_count, 1) : 60.0;

    // Actual term id
    if (!$term_id && !empty($classes[0]['term_id'])) {
        $term_id = (int)$classes[0]['term_id'];
    }
    if (!$term_id) {
        $teaching_term = term_pick_teaching(term_open_rows($pdo));
        $term_id = $teaching_term ? (int)$teaching_term['id'] : 0;
    }

    $sup_scores = qc_get_teacher_supervisor_scores($pdo, $teacher_id, $term_id);
    $sup_total_40 = $sup_scores['total'];

    $overall_100 = round($classes_avg_60 + $sup_total_40, 1);

    return [
        'teacher_id' => $teacher_id,
        'term_id' => $term_id,
        'class_count' => $class_count,
        'classes' => $class_results,
        'classes_avg_60' => $classes_avg_60,
        'supervisor_scores' => $sup_scores,
        'supervisor_total_40' => $sup_total_40,
        'overall_mark' => $overall_100, // out of 100
    ];
}


/**
 * Send an SMS alert via Faraz SMS pattern 'cyedV1fZDy' if not already sent.
 * Restricted to Iranian daytime hours (08:00 to 22:30).
 */
function qc_send_sms_alert(PDO $pdo, $teacher_id, $class_id, $item_key, $teacher_name, $class_title, $item_label, $phone) {
    qc_ensure_schema($pdo);

    $teacher_id = (int)$teacher_id;
    $class_id = (int)$class_id;
    $item_key = trim((string)$item_key);
    $phone = trim((string)$phone);

    if ($teacher_id <= 0 || empty($item_key) || empty($phone)) {
        return false;
    }

    // Normalize phone number (ensure standard 09... format)
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) === 10 && substr($phone, 0, 1) === '9') {
        $phone = '0' . $phone;
    }
    if (strlen($phone) !== 11 || substr($phone, 0, 2) !== '09') {
        return false;
    }

    // Check if alert was already sent
    $st_check = $pdo->prepare("
        SELECT id FROM qc_sms_alerts_log
        WHERE teacher_id = ? AND class_id = ? AND item_key = ?
    ");
    $st_check->execute([$teacher_id, $class_id, $item_key]);
    if ($st_check->fetchColumn()) {
        return false; // Already notified
    }

    // Working hours constraint: 08:00 to 22:30 Asia/Tehran
    date_default_timezone_set('Asia/Tehran');
    $hour = (int)date('G');
    $min = (int)date('i');
    if ($hour < 8 || $hour > 22 || ($hour === 22 && $min > 30)) {
        return false; // Outside allowed hours
    }

    // Clean strings and respect character limits
    $clean_name = mb_substr(trim((string)$teacher_name), 0, 35, 'UTF-8');
    $clean_item = mb_substr(trim((string)$item_label), 0, 35, 'UTF-8');
    $clean_class = mb_substr(trim((string)$class_title), 0, 30, 'UTF-8');

    if (!function_exists('sendFarazSMS')) {
        require_once __DIR__ . '/sms.php';
    }

    $sms_res = sendFarazSMS($phone, 'cyedV1fZDy', [
        'name' => $clean_name,
        'item' => $clean_item,
        'class' => $clean_class,
    ]);

    // Record in log to avoid duplicate sends
    $st_ins = $pdo->prepare("
        INSERT IGNORE INTO qc_sms_alerts_log (teacher_id, class_id, item_key, phone, sent_at)
        VALUES (?, ?, ?, ?, NOW())
    ");
    $st_ins->execute([$teacher_id, $class_id, $item_key, $phone]);

    return !empty($sms_res['http_code']) && $sms_res['http_code'] >= 200 && $sms_res['http_code'] < 300;
}

/**
 * Scan active classes in teaching term for missing QC parameters and dispatch SMS alerts.
 */
function qc_check_and_dispatch_sms_alerts(PDO $pdo) {
    qc_ensure_schema($pdo);

    // Working hours check: 08:00 to 22:30 Asia/Tehran
    date_default_timezone_set('Asia/Tehran');
    $hour = (int)date('G');
    $min = (int)date('i');
    if ($hour < 8 || $hour > 22 || ($hour === 22 && $min > 30)) {
        return ['skipped' => 'outside_working_hours', 'sent' => 0];
    }

    // Load active classes with teacher phone
    $stmt = $pdo->prepare("
        SELECT c.id, c.title, c.schedule, c.schedule_type, c.class_type, c.teacher_id, c.term_id,
               u.full_name as teacher_name, u.phone as teacher_phone
        FROM classes c
        JOIN terms t ON c.term_id = t.id
        JOIN users u ON c.teacher_id = u.id
        WHERE c.is_deleted = 0 AND " . term_sql_teaching('t') . "
          AND IFNULL(c.is_archived, 0) = 0
          AND IFNULL(u.is_deleted, 0) = 0
    ");
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sent_count = 0;

    foreach ($classes as $cl) {
        $teacher_phone = trim((string)($cl['teacher_phone'] ?? ''));
        if (empty($teacher_phone)) {
            continue;
        }

        $calc = qc_calculate_class($pdo, $cl);
        $cid = (int)$cl['id'];
        $tid = (int)$cl['teacher_id'];
        $tname = $cl['teacher_name'];
        $ctitle = $cl['title'];

        // 1. Missing Roll Call sessions (only if roll_call not exempt)
        if (empty($calc['items']['roll_call']['is_exempt']) && !empty($calc['items']['roll_call']['deductions'])) {
            foreach ($calc['items']['roll_call']['deductions'] as $d) {
                $sess_no = $d['session'] ?? 1;
                $ok = qc_send_sms_alert($pdo, $tid, $cid, "rollcall_s{$sess_no}", $tname, $ctitle, "رول‌کال جلسه {$sess_no}", $teacher_phone);
                if ($ok) $sent_count++;
            }
        }

        // 2. Missing Quizzes / Final Marks (only if quizzes not exempt)
        if (empty($calc['items']['quizzes']['is_exempt']) && !empty($calc['items']['quizzes']['deductions'])) {
            foreach ($calc['items']['quizzes']['deductions'] as $d) {
                $col = $d['milestone'] ?? 'quiz';
                $label = $d['label'] ?? 'کوییز';
                $ok = qc_send_sms_alert($pdo, $tid, $cid, "quiz_{$col}", $tname, $ctitle, "نمرات {$label}", $teacher_phone);
                if ($ok) $sent_count++;
            }
        }

        // 3. Missing Session Plans (only if session_plan not exempt)
        if (empty($calc['items']['session_plan']['is_exempt']) && !empty($calc['items']['session_plan']['deductions'])) {
            foreach ($calc['items']['session_plan']['deductions'] as $d) {
                $sess_no = $d['session'] ?? 1;
                $ok = qc_send_sms_alert($pdo, $tid, $cid, "sessionplan_s{$sess_no}", $tname, $ctitle, "سشن پلن جلسه {$sess_no}", $teacher_phone);
                if ($ok) $sent_count++;
            }
        }

        // 4. Missing Error Gravity (only if error_gravity not exempt and failed)
        if (empty($calc['items']['error_gravity']['is_exempt']) && ($calc['items']['error_gravity']['status'] ?? '') === 'failed') {
            $ok = qc_send_sms_alert($pdo, $tid, $cid, "error_gravity", $tname, $ctitle, "ارور گرویتی", $teacher_phone);
            if ($ok) $sent_count++;
        }

        // 5. Missing Assignments (only if assignments not exempt)
        if (empty($calc['items']['assignments']['is_exempt']) && !empty($calc['items']['assignments']['deductions'])) {
            foreach ($calc['items']['assignments']['deductions'] as $d) {
                $sess_no = $d['session'] ?? 1;
                $ok = qc_send_sms_alert($pdo, $tid, $cid, "assignment_s{$sess_no}", $tname, $ctitle, "تکالیف جلسه {$sess_no}", $teacher_phone);
                if ($ok) $sent_count++;
            }
        }

        // 6. Missing Voice Plan (only if voice_plan not exempt and failed)
        if (empty($calc['items']['voice_plan']['is_exempt']) && ($calc['items']['voice_plan']['status'] ?? '') === 'failed') {
            $ok = qc_send_sms_alert($pdo, $tid, $cid, "voice_plan", $tname, $ctitle, "ویس پلن کلی ترم", $teacher_phone);
            if ($ok) $sent_count++;
        }
    }

    return ['sent' => $sent_count];
}
