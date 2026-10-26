<?php
/**
 * One student code (e.g. 1102) across panel, WordPress, and accounting.
 * Username = code. Default password for new accounts = Ghazal{code}.
 */

require_once __DIR__ . '/rollcall.php';
require_once __DIR__ . '/accounting.php';
if (!function_exists('alocom_name_tokens')) {
    require_once __DIR__ . '/alocom.php';
}

function identity_log($message) {
    $line = date('[Y-m-d H:i:s] ') . $message . "\n";
    @error_log($line, 3, __DIR__ . '/../dashboard/supervisor/identity_log.txt');
}

function identity_valid_secret($given) {
    $config = rollcall_config();
    $secret = (string)($config['identity']['secret'] ?? '');
    if ($secret === '' || $given === '') {
        return false;
    }
    return hash_equals($secret, (string)$given);
}

function identity_default_password($code) {
    return 'Ghazal' . identity_normalize_code($code);
}

function identity_normalize_code($code) {
    $code = trim((string)$code);
    $map = ['۰'=>'0','۱'=>'1','۲'=>'2','۳'=>'3','۴'=>'4','۵'=>'5','۶'=>'6','۷'=>'7','۸'=>'8','۹'=>'9',
            '٠'=>'0','١'=>'1','٢'=>'2','٣'=>'3','٤'=>'4','٥'=>'5','٦'=>'6','٧'=>'7','٨'=>'8','٩'=>'9'];
    $code = strtr($code, $map);
    $code = preg_replace('/\s+/', '', $code);
    return $code;
}

function identity_is_code($code) {
    $code = identity_normalize_code($code);
    return $code !== '' && preg_match('/^[0-9]{3,8}$/', $code);
}

function identity_split_name($full_name) {
    $full_name = trim(preg_replace('/\s+/u', ' ', (string)$full_name));
    if ($full_name === '') {
        return ['', ''];
    }
    $parts = preg_split('/\s+/u', $full_name);
    $first = array_shift($parts);
    $last = implode(' ', $parts);
    return [$first, $last];
}

function identity_ensure_acc_schema() {
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $pdo = accounting_pdo();
    $col = $pdo->query("SHOW COLUMNS FROM students LIKE 'studentCode'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE students ADD COLUMN studentCode VARCHAR(20) NULL DEFAULT NULL");
        try {
            $pdo->exec("ALTER TABLE students ADD INDEX idx_studentCode (studentCode)");
        } catch (Exception $e) {
            identity_log('index studentCode: ' . $e->getMessage());
        }
        identity_log('Added students.studentCode column');
    }
}

function identity_used_codes(PDO $panel_pdo) {
    $used = [];
    $stmt = $panel_pdo->query("SELECT username FROM users WHERE role = 'student' AND username REGEXP '^[0-9]{3,8}$'");
    foreach ($stmt as $row) {
        $used[identity_normalize_code($row['username'])] = true;
    }
    identity_ensure_acc_schema();
    $acc = accounting_pdo();
    $stmt = $acc->query("SELECT studentCode FROM students WHERE studentCode IS NOT NULL AND studentCode <> ''");
    foreach ($stmt as $row) {
        $code = identity_normalize_code($row['studentCode']);
        if (identity_is_code($code)) {
            $used[$code] = true;
        }
    }
    return $used;
}

function identity_next_code(PDO $panel_pdo) {
    $used = identity_used_codes($panel_pdo);
    $four = [];
    foreach (array_keys($used) as $code) {
        if (strlen($code) === 4) {
            $four[] = (int)$code;
        }
    }
    $max = $four ? max($four) : 1101;
    if ($max < 1101) {
        $max = 1101;
    }
    $next = $max + 1;
    while (isset($used[(string)$next])) {
        $next++;
    }
    return (string)$next;
}

function identity_code_from_person(PDO $panel_pdo, $full_name, $phone) {
    $code = identity_code_from_phone($phone, $panel_pdo);
    if ($code !== '') {
        return $code;
    }
    $full_name = trim((string)$full_name);
    if ($full_name === '') {
        return '';
    }
    $acc_one = [[
        'id' => '0',
        'full' => $full_name,
        'phone_key' => preg_replace('/^0+/', '', rollcall_normalize_phone($phone)),
        'studentCode' => '',
    ]];
    $stmt = $panel_pdo->query("SELECT username, full_name, phone FROM users WHERE role = 'student' AND is_deleted = 0");
    foreach ($stmt as $panel) {
        $pool = $acc_one;
        $hit = accounting_match_student($panel, $pool);
        if ($hit && identity_is_code($panel['username'])) {
            return identity_normalize_code($panel['username']);
        }
    }
    return '';
}

function identity_code_from_phone($phone, PDO $panel_pdo) {
    $phone_key = preg_replace('/^0+/', '', rollcall_normalize_phone($phone));
    if ($phone_key === '') {
        return '';
    }
    $stmt = $panel_pdo->prepare("
        SELECT username FROM users
        WHERE role = 'student' AND is_deleted = 0 AND phone IS NOT NULL AND phone <> ''
    ");
    $stmt->execute();
    foreach ($stmt as $row) {
        $row_key = preg_replace('/^0+/', '', rollcall_normalize_phone($row['phone'] ?? ''));
        if ($row_key !== '' && $row_key === $phone_key && identity_is_code($row['username'])) {
            return identity_normalize_code($row['username']);
        }
    }
    identity_ensure_acc_schema();
    $acc = accounting_pdo();
    $stmt = $acc->query("SELECT phone, studentCode FROM students WHERE studentCode IS NOT NULL AND studentCode <> ''");
    foreach ($stmt as $row) {
        $row_key = preg_replace('/^0+/', '', rollcall_normalize_phone($row['phone'] ?? ''));
        if ($row_key !== '' && $row_key === $phone_key && identity_is_code($row['studentCode'])) {
            return identity_normalize_code($row['studentCode']);
        }
    }
    return '';
}

function identity_ensure_panel_student(PDO $panel_pdo, $code, $full_name, $phone, $plain_password, $reset_password = false) {
    $code = identity_normalize_code($code);
    $full_name = trim((string)$full_name);
    $phone = trim((string)$phone);
    if ($plain_password === '' || $plain_password === null) {
        $plain_password = identity_default_password($code);
    }
    $stmt = $panel_pdo->prepare("SELECT id, is_deleted, phone, full_name FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$code]);
    $existing = $stmt->fetch();
    if ($existing) {
        $sets = ['is_deleted = 0'];
        $params = [];
        if ($full_name !== '') {
            $sets[] = 'full_name = ?';
            $params[] = $full_name;
        }
        if ($phone !== '') {
            $sets[] = 'phone = ?';
            $params[] = $phone;
        }
        $sets[] = "role = 'student'";
        if ($reset_password) {
            $sets[] = 'password = ?';
            $params[] = password_hash($plain_password, PASSWORD_DEFAULT);
        }
        $params[] = $existing['id'];
        $panel_pdo->prepare('UPDATE users SET ' . implode(', ', $sets) . ' WHERE id = ?')->execute($params);
        return ['id' => (int)$existing['id'], 'created' => false];
    }
    $hash = password_hash($plain_password, PASSWORD_DEFAULT);
    $stmt = $panel_pdo->prepare("
        INSERT INTO users (username, password, full_name, phone, role, is_deleted, created_at)
        VALUES (?, ?, ?, ?, 'student', 0, NOW())
    ");
    $stmt->execute([$code, $hash, $full_name !== '' ? $full_name : $code, $phone,]);
    return ['id' => (int)$panel_pdo->lastInsertId(), 'created' => true];
}

function identity_wp_request(array $payload) {
    $config = rollcall_config();
    $url = $config['wordpress']['ensure_url'] ?? 'https://ghazalify.com/wp-content/ghazal-identity-ensure.php';
    $secret = $config['identity']['secret'] ?? '';
    if ($url === '' || $secret === '') {
        return ['ok' => false, 'error' => 'wordpress not configured'];
    }
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-Ghazal-Identity: ' . $secret,
        ],
    ]);
    $raw = curl_exec($ch);
    $err = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($raw === false) {
        return ['ok' => false, 'error' => $err, 'http' => $code];
    }
    $json = json_decode($raw, true);
    if (!is_array($json)) {
        return ['ok' => false, 'error' => 'invalid json', 'http' => $code, 'raw' => substr((string)$raw, 0, 300)];
    }
    $json['http'] = $code;
    return $json;
}

function identity_set_acc_code($acc_student_id, $code, $phone = '') {
    identity_ensure_acc_schema();
    $acc = accounting_pdo();
    $code = identity_normalize_code($code);
    if ($acc_student_id) {
        $stmt = $acc->prepare('UPDATE students SET studentCode = ? WHERE id = ?');
        $stmt->execute([$code, $acc_student_id]);
    }
    $phone_key = preg_replace('/^0+/', '', rollcall_normalize_phone($phone));
    if ($phone_key !== '') {
        $stmt = $acc->query('SELECT id, phone, studentCode FROM students');
        foreach ($stmt as $row) {
            if ((string)$row['id'] === (string)$acc_student_id) {
                continue;
            }
            $existing = identity_normalize_code($row['studentCode'] ?? '');
            if ($existing !== '') {
                continue;
            }
            $row_key = preg_replace('/^0+/', '', rollcall_normalize_phone($row['phone'] ?? ''));
            if ($row_key !== '' && $row_key === $phone_key) {
                $acc->prepare('UPDATE students SET studentCode = ? WHERE id = ?')->execute([$code, $row['id']]);
            }
        }
    }
}

/**
 * @return array{ok:bool,code:string,panel:array,wordpress:array,accounting:bool}
 */
function identity_sync_student(PDO $panel_pdo, array $opts) {
    identity_ensure_acc_schema();
    $full_name = trim((string)($opts['full_name'] ?? ''));
    $first = trim((string)($opts['first_name'] ?? ''));
    $last = trim((string)($opts['last_name'] ?? ''));
    if ($full_name === '') {
        $full_name = trim($first . ' ' . $last);
    }
    if ($first === '' && $full_name !== '') {
        list($first, $last) = identity_split_name($full_name);
    }
    $phone = trim((string)($opts['phone'] ?? ''));
    $code = identity_normalize_code($opts['code'] ?? '');
    $explicit_password = isset($opts['password']) && $opts['password'] !== '' && $opts['password'] !== null;
    $password = $explicit_password ? (string)$opts['password'] : '';
    $reset = !empty($opts['reset_password']);
    $acc_id = $opts['acc_student_id'] ?? null;
    $previous = identity_normalize_code($opts['previous_code'] ?? '');

    if (!identity_is_code($code)) {
        $code = identity_code_from_person($panel_pdo, $full_name, $phone);
    }
    if (!identity_is_code($code)) {
        $code = identity_next_code($panel_pdo);
    }
    if ($password === '') {
        $password = identity_default_password($code);
    }

    $panel = identity_ensure_panel_student(
        $panel_pdo,
        $code,
        $full_name !== '' ? $full_name : $code,
        $phone,
        $password,
        $reset
    );

    $wp_reset = $reset || !empty($opts['wp_reset_password']) || !empty($panel['created']);
    $wp = identity_wp_request([
        'code' => $code,
        'previous_code' => $previous,
        'full_name' => $full_name !== '' ? $full_name : $code,
        'first_name' => $first,
        'last_name' => $last,
        'phone' => $phone,
        'password' => $password,
        'reset_password' => $wp_reset ? 1 : 0,
    ]);

    identity_set_acc_code($acc_id, $code, $phone);

    identity_log('sync source=' . ($opts['source'] ?? '?') . ' code=' . $code . ' panel=' . (empty($panel['created']) ? 'existing' : 'created') . ' wp=' . (!empty($wp['ok']) ? 'ok' : 'fail'));

    return [
        'ok' => true,
        'code' => $code,
        'panel' => $panel,
        'wordpress' => $wp,
        'accounting' => true,
    ];
}

function identity_backfill(PDO $panel_pdo, $dry_run = false, $limit = 0, $offset = 0) {
    identity_ensure_acc_schema();
    $acc = accounting_pdo();
    $all_panel = $panel_pdo->query("
        SELECT id, username, full_name, phone
        FROM users
        WHERE role = 'student' AND is_deleted = 0
        ORDER BY id
    ")->fetchAll();
    $total_panel = count($all_panel);
    $limit = (int)$limit;
    $offset = (int)$offset;
    if ($limit > 0) {
        $panel_students = array_slice($all_panel, $offset, $limit);
    } else {
        $panel_students = $all_panel;
    }
    $chunk_end = $offset + count($panel_students);
    $last_chunk = ($limit <= 0) || ($chunk_end >= $total_panel);

    $acc_students = $acc->query("SELECT id, firstName, lastName, phone, studentCode, classType FROM students")->fetchAll();

    $results = [
        'panel_to_wp' => 0,
        'acc_linked' => 0,
        'acc_created' => 0,
        'codes' => [],
        'offset' => $offset,
        'limit' => $limit,
        'total_panel' => $total_panel,
        'next_offset' => $last_chunk ? null : $chunk_end,
        'done' => $last_chunk,
    ];
    $pool = [];
    foreach ($acc_students as $row) {
        $row['full'] = trim((string)$row['firstName'] . ' ' . (string)$row['lastName']);
        $row['phone_key'] = preg_replace('/^0+/', '', rollcall_normalize_phone($row['phone'] ?? ''));
        $pool[] = $row;
    }

    $acc_code_by_id = [];
    foreach ($panel_students as $student) {
        $code = identity_normalize_code($student['username']);
        if (!identity_is_code($code)) {
            continue;
        }
        $match = accounting_match_student($student, $pool);
        if ($match) {
            $acc_code_by_id[$match['id']] = $code;
            $phone_key = $match['phone_key'] ?? '';
            if ($phone_key !== '') {
                foreach ($acc_students as $row) {
                    $row_key = preg_replace('/^0+/', '', rollcall_normalize_phone($row['phone'] ?? ''));
                    if ($row_key !== '' && $row_key === $phone_key) {
                        $acc_code_by_id[$row['id']] = $code;
                    }
                }
            }
        }
        if (!$dry_run) {
            identity_sync_student($panel_pdo, [
                'source' => 'backfill-panel',
                'code' => $code,
                'full_name' => $student['full_name'],
                'phone' => $student['phone'],
                'password' => identity_default_password($code),
                'reset_password' => false,
                'wp_reset_password' => true,
            ]);
        }
        $results['panel_to_wp']++;
        $results['codes'][$code] = $student['full_name'];
    }

    if (!$last_chunk) {
        if (!$dry_run && $acc_code_by_id) {
            foreach ($acc_code_by_id as $id => $code) {
                $acc->prepare('UPDATE students SET studentCode = ? WHERE id = ?')->execute([$code, $id]);
                $results['acc_linked']++;
            }
        } else {
            $results['acc_linked'] = count($acc_code_by_id);
        }
        return $results;
    }

    foreach ($acc_students as $row) {
        if (!empty($acc_code_by_id[$row['id']])) {
            continue;
        }
        $existing = identity_normalize_code($row['studentCode'] ?? '');
        if (identity_is_code($existing)) {
            $acc_code_by_id[$row['id']] = $existing;
            continue;
        }
        $from_phone = identity_code_from_phone($row['phone'] ?? '', $panel_pdo);
        if ($from_phone !== '') {
            $acc_code_by_id[$row['id']] = $from_phone;
            continue;
        }
        if (!$dry_run) {
            $code = identity_next_code($panel_pdo);
            $full = trim((string)$row['firstName'] . ' ' . (string)$row['lastName']);
            identity_sync_student($panel_pdo, [
                'source' => 'backfill-acc',
                'code' => $code,
                'first_name' => $row['firstName'],
                'last_name' => $row['lastName'],
                'full_name' => $full,
                'phone' => $row['phone'],
                'acc_student_id' => $row['id'],
                'password' => identity_default_password($code),
                'reset_password' => true,
                'create_if_missing' => true,
            ]);
            $acc_code_by_id[$row['id']] = $code;
            $results['acc_created']++;
            $results['codes'][$code] = $full;
        } else {
            $results['acc_created']++;
        }
    }

    if (!$dry_run) {
        foreach ($acc_code_by_id as $id => $code) {
            $acc->prepare('UPDATE students SET studentCode = ? WHERE id = ?')->execute([$code, $id]);
            $results['acc_linked']++;
        }
    } else {
        $results['acc_linked'] = count($acc_code_by_id);
    }

    return $results;
}

function identity_repair_acc_codes(PDO $panel_pdo) {
    identity_ensure_acc_schema();
    $acc = accounting_pdo();
    $rows = $acc->query("SELECT id, firstName, lastName, phone, studentCode FROM students")->fetchAll();
    $repaired = [];
    $kept = [];
    foreach ($rows as $row) {
        $current = identity_normalize_code($row['studentCode'] ?? '');
        $full = trim((string)$row['firstName'] . ' ' . (string)$row['lastName']);
        $matched = identity_code_from_person($panel_pdo, $full, $row['phone'] ?? '');
        if ($matched !== '' && $matched !== $current) {
            $acc->prepare('UPDATE students SET studentCode = ? WHERE id = ?')->execute([$matched, $row['id']]);
            $repaired[] = [
                'id' => $row['id'],
                'name' => $full,
                'from' => $current,
                'to' => $matched,
            ];
        } else {
            $kept[] = ['id' => $row['id'], 'code' => $current ?: $matched, 'name' => $full];
        }
    }
    return ['repaired' => $repaired, 'kept' => count($kept)];
}
