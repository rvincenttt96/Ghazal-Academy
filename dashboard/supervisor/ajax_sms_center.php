<?php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/sms.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Ensure supervisor authentication
if (empty($_SESSION['logged_in']) && function_exists('check_remember_me')) {
    check_remember_me($pdo);
}
if (empty($_SESSION['logged_in']) || ($_SESSION['role'] ?? '') !== 'supervisor') {
    echo json_encode(['ok' => false, 'error' => 'دسترسی غیرمجاز (لطفاً مجدداً وارد شوید)'], JSON_UNESCAPED_UNICODE);
    exit;
}

sms_ensure_schema($pdo);

$action = trim((string)($_REQUEST['action'] ?? ''));
$supervisor_id = (int)($_SESSION['user']['id'] ?? 0);

try {
    switch ($action) {
        // =========================================================
        // 1. GET BALANCE
        // =========================================================
        case 'get_balance':
            $bal = sms_get_balance();
            echo json_encode($bal, JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 2. GET PATTERNS
        // =========================================================
        case 'get_patterns':
            $page = max(1, (int)($_GET['page'] ?? 1));
            $res = sms_get_patterns($page);
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 3. CREATE PATTERN
        // =========================================================
        case 'create_pattern':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('متد نامعتبر است');
            }
            $desc = trim((string)($_POST['description'] ?? ''));
            $text = trim((string)($_POST['text'] ?? ''));
            $website = trim((string)($_POST['website'] ?? 'https://ghazalify.com'));

            if ($desc === '') {
                throw new Exception('توضیحات پترن الزامی است');
            }
            if ($text === '') {
                throw new Exception('متن پترن الزامی است');
            }
            if (mb_strlen($text) < 10) {
                throw new Exception('متن پترن بسیار کوتاه است');
            }

            $res = sms_create_pattern($desc, $text, $website ?: 'https://ghazalify.com');
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 4. GET AUDIENCE LIST (For Recipient Selection)
        // =========================================================
        case 'get_audience_meta':
            // Classes list
            $st_cls = $pdo->query("
                SELECT c.id, c.title, c.schedule, u.full_name as teacher_name,
                       (SELECT COUNT(*) FROM class_students cs JOIN users us ON cs.user_id = us.id WHERE cs.class_id = c.id AND us.role = 'student' AND IFNULL(us.is_deleted, 0) = 0) as student_count
                FROM classes c
                LEFT JOIN users u ON c.teacher_id = u.id
                WHERE c.is_deleted = 0
                ORDER BY c.title ASC
            ");
            $classes = $st_cls->fetchAll(PDO::FETCH_ASSOC);

            // Counts
            $teacher_count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'teacher' AND IFNULL(is_deleted, 0) = 0")->fetchColumn();
            $student_count = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role = 'student' AND IFNULL(is_deleted, 0) = 0")->fetchColumn();

            echo json_encode([
                'ok' => true,
                'classes' => $classes,
                'stats' => [
                    'teachers' => $teacher_count,
                    'students' => $student_count,
                ]
            ], JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 5. SEND SMS (Single or Bulk, Pattern or Simple)
        // =========================================================
        case 'send_sms':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('متد نامعتبر است');
            }
            $send_type = trim((string)($_POST['send_type'] ?? 'pattern')); // 'pattern' or 'simple'
            $target_type = trim((string)($_POST['target_type'] ?? 'manual')); // 'manual', 'teachers', 'students', 'class'
            $class_id = (int)($_POST['class_id'] ?? 0);
            $pattern_code = trim((string)($_POST['pattern_code'] ?? ''));
            $message_text = trim((string)($_POST['message_text'] ?? ''));
            $raw_manual_phones = trim((string)($_POST['manual_phones'] ?? ''));
            $vars = isset($_POST['variables']) && is_array($_POST['variables']) ? $_POST['variables'] : [];

            $recipients = [];

            // Build recipients list
            if ($target_type === 'manual') {
                if ($raw_manual_phones === '') {
                    throw new Exception('لطفاً حداقل یک شماره موبایل وارد کنید');
                }
                $lines = preg_split('/[\r\n,;]+/', $raw_manual_phones);
                foreach ($lines as $line) {
                    $p = sms_normalize_phone($line);
                    if ($p !== '') {
                        $recipients[] = ['phone' => $p, 'name' => 'گیرنده دستی', 'role' => 'manual'];
                    }
                }
            } elseif ($target_type === 'teachers') {
                $st = $pdo->query("SELECT id, full_name, username, phone FROM users WHERE role = 'teacher' AND IFNULL(is_deleted, 0) = 0");
                while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
                    $phone = !empty($r['phone']) ? $r['phone'] : $r['username'];
                    $p = sms_normalize_phone($phone);
                    if ($p !== '') {
                        $recipients[] = ['phone' => $p, 'name' => $r['full_name'], 'role' => 'teacher'];
                    }
                }
            } elseif ($target_type === 'students') {
                $st = $pdo->query("SELECT id, full_name, username, phone FROM users WHERE role = 'student' AND IFNULL(is_deleted, 0) = 0");
                while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
                    $phone = !empty($r['phone']) ? $r['phone'] : $r['username'];
                    $p = sms_normalize_phone($phone);
                    if ($p !== '') {
                        $recipients[] = ['phone' => $p, 'name' => $r['full_name'], 'role' => 'student'];
                    }
                }
            } elseif ($target_type === 'class') {
                if ($class_id <= 0) {
                    throw new Exception('لطفاً کلاس را انتخاب کنید');
                }
                $st = $pdo->prepare("
                    SELECT u.id, u.full_name, u.username, u.phone
                    FROM class_students cs
                    JOIN users u ON cs.user_id = u.id
                    WHERE cs.class_id = ? AND u.role = 'student' AND IFNULL(u.is_deleted, 0) = 0
                ");
                $st->execute([$class_id]);
                while ($r = $st->fetch(PDO::FETCH_ASSOC)) {
                    $phone = !empty($r['phone']) ? $r['phone'] : $r['username'];
                    $p = sms_normalize_phone($phone);
                    if ($p !== '') {
                        $recipients[] = ['phone' => $p, 'name' => $r['full_name'], 'role' => 'student'];
                    }
                }
            }

            if (empty($recipients)) {
                throw new Exception('هیچ شماره موبایل معتبری برای ارسال یافت نشد');
            }

            // Deduplicate by phone
            $unique_recipients = [];
            foreach ($recipients as $rec) {
                $unique_recipients[$rec['phone']] = $rec;
            }
            $recipients = array_values($unique_recipients);

            $success_count = 0;
            $failed_count = 0;
            $errors = [];

            foreach ($recipients as $rec) {
                $phone = $rec['phone'];
                $rname = $rec['name'];
                $rrole = $rec['role'];

                if ($send_type === 'pattern') {
                    if ($pattern_code === '') {
                        throw new Exception('کد پترن الزامی است');
                    }

                    // Dynamically map variables if specified
                    $active_vars = [];
                    foreach ($vars as $k => $v) {
                        $v_str = trim((string)$v);
                        // Dynamic placeholder replacement
                        $v_str = str_replace(
                            ['{name}', '{student_name}', '{teacher_name}'],
                            $rname,
                            $v_str
                        );
                        $active_vars[$k] = $v_str;
                    }

                    $res = sendFarazSMS($phone, $pattern_code, $active_vars);
                    $ok = !empty($res['http_code']) && $res['http_code'] >= 200 && $res['http_code'] < 300;

                    sms_log_manual($pdo, [
                        'sender_id' => $supervisor_id,
                        'send_type' => 'pattern',
                        'pattern_code' => $pattern_code,
                        'recipient_phone' => $phone,
                        'recipient_name' => $rname,
                        'recipient_role' => $rrole,
                        'message_text' => null,
                        'variables_json' => $active_vars,
                        'status' => $ok ? 'sent' : 'failed',
                        'response_info' => $res,
                    ]);

                    if ($ok) {
                        $success_count++;
                    } else {
                        $failed_count++;
                        $errors[] = $phone . ': ' . ($res['curl_error'] ?: 'خطای وب‌سرویس');
                    }
                } else {
                    // Simple text send
                    if ($message_text === '') {
                        throw new Exception('متن پیامک الزامی است');
                    }
                    $custom_text = str_replace(
                        ['{name}', '{student_name}', '{teacher_name}'],
                        $rname,
                        $message_text
                    );
                    $res = sms_send_once($phone, $custom_text);
                    $ok = !empty($res['ok']);

                    sms_log_manual($pdo, [
                        'sender_id' => $supervisor_id,
                        'send_type' => 'simple',
                        'pattern_code' => null,
                        'recipient_phone' => $phone,
                        'recipient_name' => $rname,
                        'recipient_role' => $rrole,
                        'message_text' => $custom_text,
                        'variables_json' => null,
                        'status' => $ok ? 'sent' : 'failed',
                        'response_info' => $res,
                    ]);

                    if ($ok) {
                        $success_count++;
                    } else {
                        $failed_count++;
                        $errors[] = $phone . ': ' . ($res['curl_error'] ?: 'خطا در ارسال');
                    }
                }
            }

            echo json_encode([
                'ok' => true,
                'total' => count($recipients),
                'success' => $success_count,
                'failed' => $failed_count,
                'sample_errors' => array_slice($errors, 0, 5),
            ], JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 6. GET AUTOMATIONS
        // =========================================================
        case 'get_automations':
            $st = $pdo->query("SELECT * FROM sms_automations ORDER BY id ASC");
            $rows = $st->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['ok' => true, 'automations' => $rows], JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 7. TOGGLE AUTOMATION
        // =========================================================
        case 'toggle_automation':
            $id = (int)($_POST['id'] ?? 0);
            $active = !empty($_POST['is_active']) ? 1 : 0;
            if ($id <= 0) throw new Exception('شناسه اتومیشن نامعتبر است');

            $st = $pdo->prepare("UPDATE sms_automations SET is_active = ?, updated_at = NOW() WHERE id = ?");
            $st->execute([$active, $id]);
            echo json_encode(['ok' => true, 'is_active' => $active], JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 8. SAVE AUTOMATION (Create / Edit)
        // =========================================================
        case 'save_automation':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('متد نامعتبر است');
            }
            $id = (int)($_POST['id'] ?? 0);
            $title = trim((string)($_POST['title'] ?? ''));
            $trigger = trim((string)($_POST['trigger_event'] ?? ''));
            $audience = trim((string)($_POST['target_audience'] ?? 'student'));
            $send_type = trim((string)($_POST['send_type'] ?? 'pattern'));
            $pattern_code = trim((string)($_POST['pattern_code'] ?? ''));
            $simple_text = trim((string)($_POST['simple_text'] ?? ''));
            $offset = (int)($_POST['time_offset_min'] ?? 0);
            $vars_map = trim((string)($_POST['variables_mapping'] ?? ''));

            if ($title === '') throw new Exception('عنوان اتومیشن الزامی است');
            if ($trigger === '') throw new Exception('رویداد محرک الزامی است');

            if ($id > 0) {
                $st = $pdo->prepare("
                    UPDATE sms_automations 
                    SET title = ?, trigger_event = ?, target_audience = ?, send_type = ?, pattern_code = ?, simple_text = ?, variables_mapping = ?, time_offset_min = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $st->execute([$title, $trigger, $audience, $send_type, $pattern_code ?: null, $simple_text ?: null, $vars_map ?: null, $offset, $id]);
            } else {
                $st = $pdo->prepare("
                    INSERT INTO sms_automations 
                    (title, trigger_event, target_audience, send_type, pattern_code, simple_text, variables_mapping, time_offset_min, is_active, total_sent)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, 0)
                ");
                $st->execute([$title, $trigger, $audience, $send_type, $pattern_code ?: null, $simple_text ?: null, $vars_map ?: null, $offset]);
                $id = (int)$pdo->lastInsertId();
            }

            echo json_encode(['ok' => true, 'id' => $id], JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 9. DELETE AUTOMATION
        // =========================================================
        case 'delete_automation':
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) throw new Exception('شناسه نامعتبر است');
            $st = $pdo->prepare("DELETE FROM sms_automations WHERE id = ?");
            $st->execute([$id]);
            echo json_encode(['ok' => true], JSON_UNESCAPED_UNICODE);
            exit;

        // =========================================================
        // 10. GET LOGS (Unified SMS Logs)
        // =========================================================
        case 'get_logs':
            $log_type = trim((string)($_GET['log_type'] ?? 'all')); // 'all', 'manual', 'qc', 'absence', 'late'
            $page = max(1, (int)($_GET['page'] ?? 1));
            $limit = 30;
            $offset = ($page - 1) * $limit;

            $items = [];
            $total_count = 0;

            if ($log_type === 'manual') {
                $st_c = $pdo->query("SELECT COUNT(*) FROM sms_manual_logs");
                $total_count = (int)$st_c->fetchColumn();

                $st = $pdo->prepare("
                    SELECT id, recipient_phone as phone, recipient_name as recipient, recipient_role as role,
                           send_type, pattern_code, message_text, status, created_at as sent_at,
                           'دستی سوپروایزر' as source_label
                    FROM sms_manual_logs
                    ORDER BY id DESC
                    LIMIT ? OFFSET ?
                ");
                $st->bindValue(1, $limit, PDO::PARAM_INT);
                $st->bindValue(2, $offset, PDO::PARAM_INT);
                $st->execute();
                $items = $st->fetchAll(PDO::FETCH_ASSOC);

            } elseif ($log_type === 'qc') {
                $st_c = $pdo->query("SELECT COUNT(*) FROM qc_sms_alerts_log");
                $total_count = (int)$st_c->fetchColumn();

                $st = $pdo->prepare("
                    SELECT q.id, q.phone, u.full_name as recipient, 'teacher' as role,
                           'pattern' as send_type, 'cyedV1fZDy' as pattern_code,
                           CONCAT('کنترل کیفیت: ', q.item_key, ' (کلاس ', IFNULL(c.title, q.class_id), ')') as message_text,
                           'sent' as status, q.sent_at,
                           'هشدار هوشمند QC' as source_label
                    FROM qc_sms_alerts_log q
                    LEFT JOIN users u ON q.teacher_id = u.id
                    LEFT JOIN classes c ON q.class_id = c.id
                    ORDER BY q.id DESC
                    LIMIT ? OFFSET ?
                ");
                $st->bindValue(1, $limit, PDO::PARAM_INT);
                $st->bindValue(2, $offset, PDO::PARAM_INT);
                $st->execute();
                $items = $st->fetchAll(PDO::FETCH_ASSOC);

            } elseif ($log_type === 'absence') {
                $st_c = $pdo->query("SELECT COUNT(*) FROM attendance_sms_log");
                $total_count = (int)$st_c->fetchColumn();

                $st = $pdo->prepare("
                    SELECT 0 as id, IFNULL(u.phone, u.username) as phone, u.full_name as recipient, 'parent' as role,
                           'pattern' as send_type, 'aX3d0ttYHc' as pattern_code,
                           CONCAT('اعلام غیبت تاریخ ', a.class_date, ' (کلاس ', IFNULL(c.title, a.class_id), ')') as message_text,
                           'sent' as status, a.sent_at,
                           'غیبت دانش‌آموز' as source_label
                    FROM attendance_sms_log a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN classes c ON a.class_id = c.id
                    ORDER BY a.sent_at DESC
                    LIMIT ? OFFSET ?
                ");
                $st->bindValue(1, $limit, PDO::PARAM_INT);
                $st->bindValue(2, $offset, PDO::PARAM_INT);
                $st->execute();
                $items = $st->fetchAll(PDO::FETCH_ASSOC);

            } elseif ($log_type === 'late') {
                $st_c = $pdo->query("SELECT COUNT(*) FROM attendance_late_sms_log");
                $total_count = (int)$st_c->fetchColumn();

                $st = $pdo->prepare("
                    SELECT 0 as id, IFNULL(u.phone, u.username) as phone, u.full_name as recipient, 'parent' as role,
                           'pattern' as send_type, '188246' as pattern_code,
                           CONCAT('اعلام تأخیر تاریخ ', l.class_date, ' (کلاس ', IFNULL(c.title, l.class_id), ')') as message_text,
                           'sent' as status, l.sent_at,
                           'تأخیر دانش‌آموز' as source_label
                    FROM attendance_late_sms_log l
                    LEFT JOIN users u ON l.user_id = u.id
                    LEFT JOIN classes c ON l.class_id = c.id
                    ORDER BY l.sent_at DESC
                    LIMIT ? OFFSET ?
                ");
                $st->bindValue(1, $limit, PDO::PARAM_INT);
                $st->bindValue(2, $offset, PDO::PARAM_INT);
                $st->execute();
                $items = $st->fetchAll(PDO::FETCH_ASSOC);

            } else {
                // ALL COMBINED (Query each table safely to prevent collation conflicts)
                $fetch_limit = $limit * $page + 10;

                $st1 = $pdo->prepare("
                    SELECT id, recipient_phone as phone, recipient_name as recipient, recipient_role as role,
                           send_type, pattern_code, message_text, status, created_at as sent_at,
                           'دستی سوپروایزر' as source_label
                    FROM sms_manual_logs
                    ORDER BY id DESC
                    LIMIT ?
                ");
                $st1->bindValue(1, $fetch_limit, PDO::PARAM_INT);
                $st1->execute();
                $r1 = $st1->fetchAll(PDO::FETCH_ASSOC);

                $st2 = $pdo->prepare("
                    SELECT q.id, q.phone, u.full_name as recipient, 'teacher' as role,
                           'pattern' as send_type, 'cyedV1fZDy' as pattern_code,
                           CONCAT('کنترل کیفیت: ', q.item_key, ' (کلاس ', IFNULL(c.title, q.class_id), ')') as message_text,
                           'sent' as status, q.sent_at,
                           'هشدار هوشمند QC' as source_label
                    FROM qc_sms_alerts_log q
                    LEFT JOIN users u ON q.teacher_id = u.id
                    LEFT JOIN classes c ON q.class_id = c.id
                    ORDER BY q.id DESC
                    LIMIT ?
                ");
                $st2->bindValue(1, $fetch_limit, PDO::PARAM_INT);
                $st2->execute();
                $r2 = $st2->fetchAll(PDO::FETCH_ASSOC);

                $st3 = $pdo->prepare("
                    SELECT 0 as id, IFNULL(u.phone, u.username) as phone, u.full_name as recipient, 'parent' as role,
                           'pattern' as send_type, 'aX3d0ttYHc' as pattern_code,
                           CONCAT('اعلام غیبت تاریخ ', a.class_date, ' (کلاس ', IFNULL(c.title, a.class_id), ')') as message_text,
                           'sent' as status, a.sent_at,
                           'غیبت دانش‌آموز' as source_label
                    FROM attendance_sms_log a
                    LEFT JOIN users u ON a.user_id = u.id
                    LEFT JOIN classes c ON a.class_id = c.id
                    ORDER BY a.sent_at DESC
                    LIMIT ?
                ");
                $st3->bindValue(1, $fetch_limit, PDO::PARAM_INT);
                $st3->execute();
                $r3 = $st3->fetchAll(PDO::FETCH_ASSOC);

                $st4 = $pdo->prepare("
                    SELECT 0 as id, IFNULL(u.phone, u.username) as phone, u.full_name as recipient, 'parent' as role,
                           'pattern' as send_type, '188246' as pattern_code,
                           CONCAT('اعلام تأخیر تاریخ ', l.class_date, ' (کلاس ', IFNULL(c.title, l.class_id), ')') as message_text,
                           'sent' as status, l.sent_at,
                           'تأخیر دانش‌آموز' as source_label
                    FROM attendance_late_sms_log l
                    LEFT JOIN users u ON l.user_id = u.id
                    LEFT JOIN classes c ON l.class_id = c.id
                    ORDER BY l.sent_at DESC
                    LIMIT ?
                ");
                $st4->bindValue(1, $fetch_limit, PDO::PARAM_INT);
                $st4->execute();
                $r4 = $st4->fetchAll(PDO::FETCH_ASSOC);

                $all_merged = array_merge($r1, $r2, $r3, $r4);
                usort($all_merged, function($a, $b) {
                    return strcmp((string)$b['sent_at'], (string)$a['sent_at']);
                });

                $items = array_slice($all_merged, $offset, $limit);

                // Total count
                $c1 = (int)$pdo->query("SELECT COUNT(*) FROM sms_manual_logs")->fetchColumn();
                $c2 = (int)$pdo->query("SELECT COUNT(*) FROM qc_sms_alerts_log")->fetchColumn();
                $c3 = (int)$pdo->query("SELECT COUNT(*) FROM attendance_sms_log")->fetchColumn();
                $c4 = (int)$pdo->query("SELECT COUNT(*) FROM attendance_late_sms_log")->fetchColumn();
                $total_count = $c1 + $c2 + $c3 + $c4;
            }

            echo json_encode([
                'ok' => true,
                'items' => $items,
                'page' => $page,
                'limit' => $limit,
                'total' => $total_count,
                'total_pages' => max(1, (int)ceil($total_count / $limit)),
            ], JSON_UNESCAPED_UNICODE);
            exit;

        default:
            throw new Exception('اکشن معتبر نیست');
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
    exit;
}
