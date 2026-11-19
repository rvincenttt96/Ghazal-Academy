<?php
/**
 * پیامک فراز — همان تابع پنل اعلان‌ها (announcement.ghazalify.com/services.php)
 */

function sms_faraz_config() {
    $config = function_exists('rollcall_config') ? rollcall_config() : [];
    $sms = is_array($config['sms'] ?? null) ? $config['sms'] : [];
    $patternUrl = (string)($sms['url'] ?? 'https://api.iranpayamak.com/ws/v1/sms/pattern');
    $base = preg_replace('#/sms/pattern/?$#', '', $patternUrl);
    if ($base === $patternUrl || $base === '') {
        $base = 'https://api.iranpayamak.com/ws/v1';
    }
    return [
        'api_key' => (string)($sms['api_key'] ?? 'PpgL4mm0OiqyOoDuh0GT6C9pTNI2elZzzrHxXwHrMxQMbNsrD1'),
        'sender' => (string)($sms['sender'] ?? '90008361'),
        'pattern_url' => $patternUrl,
        'base' => rtrim($base, '/'),
    ];
}

function sms_faraz_post($url, array $payload) {
    $cfg = sms_faraz_config();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Api-Key: ' . $cfg['api_key'],
        'Content-Type: application/json',
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 20);

    $response = curl_exec($ch);
    $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    $curlErrno = curl_errno($ch);
    curl_close($ch);

    $decoded = json_decode((string)$response, true);
    $apiStatus = is_array($decoded) ? (string)($decoded['status'] ?? '') : '';
    $ok = $curlErrno === 0 && $httpCode >= 200 && $httpCode < 300 && ($apiStatus === '' || $apiStatus === 'success');
    return [
        'ok' => $ok,
        'http_code' => $httpCode,
        'response' => $decoded,
        'curl_error' => $curlErr,
        'curl_errno' => $curlErrno,
        'raw' => is_array($decoded) ? '' : substr((string)$response, 0, 180),
    ];
}

function sendFarazSMS($phone, $patternCode, $variables) {
    $cfg = sms_faraz_config();
    $result = sms_faraz_post($cfg['pattern_url'], [
        'code' => $patternCode,
        'recipient' => $phone,
        'attributes' => $variables,
        'line_number' => $cfg['sender'],
        'number_format' => 'english',
    ]);
    if (empty($result['ok']) && function_exists('ghazal_error_sms_fail')) {
        ghazal_error_sms_fail(
            (string)$phone,
            (string)$patternCode,
            !empty($result['curl_errno']) ? 0 : (int)$result['http_code'],
            is_array($result['response']) ? $result['response'] : ['message' => $result['curl_error'] !== '' ? $result['curl_error'] : (string)($result['raw'] ?? '')],
            (string)$result['curl_error']
        );
    }
    return [
        'http_code' => $result['http_code'],
        'response' => $result['response'],
        'curl_error' => $result['curl_error'],
    ];
}

/**
 * One-shot / plaintext Faraz send (پیام یک‌باره) — not a pattern.
 * POST /ws/v1/sms/simple with unique text per recipient.
 */
function sms_send_once($phone, $text) {
    $cfg = sms_faraz_config();
    $phone = trim((string)$phone);
    $text = (string)$text;
    if ($phone === '' || $text === '') {
        return ['ok' => false, 'http_code' => 0, 'response' => ['status' => 'error', 'message' => 'empty'], 'curl_error' => ''];
    }
    $result = sms_faraz_post($cfg['base'] . '/sms/simple', [
        'text' => $text,
        'line_number' => $cfg['sender'],
        'recipients' => [$phone],
        'number_format' => 'english',
    ]);
    if (empty($result['ok']) && function_exists('ghazal_error_sms_fail')) {
        ghazal_error_sms_fail(
            (string)$phone,
            'simple',
            !empty($result['curl_errno']) ? 0 : (int)$result['http_code'],
            is_array($result['response']) ? $result['response'] : ['message' => $result['curl_error'] !== '' ? $result['curl_error'] : (string)($result['raw'] ?? '')],
            (string)$result['curl_error']
        );
    }
    return $result;
}

/**
 * Personalized one-shot bulk (still not a Magfa/Payamak pattern).
 * POST /ws/v1/sms/keywords — variables inside %...%.
 * $recipients: list of ['mobile' => '09...', 'username' => ..., 'password' => ..., ...]
 */
function sms_send_once_keywords($text, array $recipients) {
    $cfg = sms_faraz_config();
    $text = (string)$text;
    if ($text === '' || !$recipients) {
        return ['ok' => false, 'http_code' => 0, 'response' => ['status' => 'error', 'message' => 'empty'], 'curl_error' => ''];
    }
    $result = sms_faraz_post($cfg['base'] . '/sms/keywords', [
        'text' => $text,
        'line_number' => $cfg['sender'],
        'recipients' => array_values($recipients),
        'number_format' => 'english',
    ]);
    if (empty($result['ok']) && function_exists('ghazal_error_sms_fail')) {
        ghazal_error_sms_fail(
            '',
            'keywords',
            !empty($result['curl_errno']) ? 0 : (int)$result['http_code'],
            is_array($result['response']) ? $result['response'] : ['message' => $result['curl_error'] !== '' ? $result['curl_error'] : (string)($result['raw'] ?? '')],
            (string)$result['curl_error']
        );
    }
    return $result;
}

/**
 * Generic GET request to Faraz SMS (IranPayamak) API
 */
function sms_faraz_get($url) {
    $cfg = sms_faraz_config();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Api-Key: ' . $cfg['api_key'],
        'Accept: application/json',
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $response = curl_exec($ch);
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr = curl_error($ch);
    $curlErrno = curl_errno($ch);
    curl_close($ch);

    $decoded = json_decode((string)$response, true);
    $ok = $curlErrno === 0 && $httpCode >= 200 && $httpCode < 300;
    return [
        'ok' => $ok,
        'http_code' => $httpCode,
        'response' => $decoded,
        'curl_error' => $curlErr,
    ];
}

/**
 * Fetch wallet balance and remaining SMS count
 */
function sms_get_balance() {
    $cfg = sms_faraz_config();
    $url = $cfg['base'] . '/account/balance';
    $res = sms_faraz_get($url);
    if (!empty($res['ok']) && !empty($res['response']['data'])) {
        $d = $res['response']['data'];
        return [
            'ok' => true,
            'amount' => (int)($d['balance_amount'] ?? 0),
            'count' => (int)floor((float)($d['balance_count'] ?? 0)),
            'raw_count' => (float)($d['balance_count'] ?? 0),
        ];
    }
    return [
        'ok' => false,
        'amount' => 0,
        'count' => 0,
        'error' => $res['curl_error'] ?: 'API connection failed',
    ];
}

/**
 * Fetch patterns list from Faraz SMS
 */
function sms_get_patterns($page = 1) {
    $cfg = sms_faraz_config();
    $url = $cfg['base'] . '/patterns?page=' . max(1, (int)$page);
    $res = sms_faraz_get($url);
    if (!empty($res['ok']) && !empty($res['response']['data'])) {
        return [
            'ok' => true,
            'patterns' => $res['response']['data']['data'] ?? [],
            'meta' => [
                'current_page' => $res['response']['data']['current_page'] ?? 1,
                'last_page' => $res['response']['data']['last_page'] ?? 1,
                'total' => $res['response']['data']['total'] ?? 0,
            ]
        ];
    }
    return [
        'ok' => false,
        'patterns' => [],
        'meta' => ['total' => 0],
        'error' => $res['curl_error'] ?: 'API connection failed',
    ];
}

/**
 * Create a new pattern via Faraz SMS API
 */
function sms_create_pattern($description, $text, $website = 'https://ghazalify.com') {
    $cfg = sms_faraz_config();
    $url = $cfg['base'] . '/patterns';
    $payload = [
        'text' => trim((string)$text),
        'description' => trim((string)$description),
        'share' => 0,
        'website' => trim((string)$website),
    ];
    $res = sms_faraz_post($url, $payload);
    return [
        'ok' => !empty($res['ok']),
        'http_code' => $res['http_code'],
        'response' => $res['response'],
        'message' => $res['response']['message'] ?? ($res['curl_error'] ?: 'Unknown error'),
    ];
}

/**
 * Ensure database tables for SMS center exist
 */
function sms_ensure_schema(PDO $pdo) {
    static $schema_checked = false;
    if ($schema_checked) return;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sms_manual_logs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            send_type VARCHAR(20) NOT NULL,
            pattern_code VARCHAR(30) NULL,
            recipient_phone VARCHAR(20) NOT NULL,
            recipient_name VARCHAR(100) NULL,
            recipient_role VARCHAR(30) NULL,
            message_text TEXT NULL,
            variables_json TEXT NULL,
            status VARCHAR(20) NOT NULL DEFAULT 'sent',
            response_info TEXT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_created (created_at),
            INDEX idx_phone (recipient_phone)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sms_automations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(150) NOT NULL,
            trigger_event VARCHAR(60) NOT NULL,
            target_audience VARCHAR(40) NOT NULL,
            send_type VARCHAR(20) NOT NULL DEFAULT 'pattern',
            pattern_code VARCHAR(30) NULL,
            simple_text TEXT NULL,
            variables_mapping TEXT NULL,
            time_offset_min INT DEFAULT 0,
            is_active TINYINT(1) DEFAULT 1,
            total_sent INT DEFAULT 0,
            last_run_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_active (is_active),
            INDEX idx_trigger (trigger_event)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // Seed default core automations if table is empty
    $chk = (int)$pdo->query("SELECT COUNT(*) FROM sms_automations")->fetchColumn();
    if ($chk === 0) {
        $defaults = [
            [
                'title' => 'اعلام غیبت دانش‌آموز به اولیا',
                'trigger_event' => 'student_absent',
                'target_audience' => 'parent',
                'send_type' => 'pattern',
                'pattern_code' => 'aX3d0ttYHc',
                'variables_mapping' => json_encode(['name' => '{student_name}', 'date' => '{jalali_date}'], JSON_UNESCAPED_UNICODE),
                'time_offset_min' => 0,
                'is_active' => 1,
            ],
            [
                'title' => 'اعلام تأخیر دانش‌آموز به اولیا',
                'trigger_event' => 'student_late',
                'target_audience' => 'parent',
                'send_type' => 'pattern',
                'pattern_code' => '188246',
                'variables_mapping' => json_encode(['name' => '{student_name}', 'date' => '{jalali_date}'], JSON_UNESCAPED_UNICODE),
                'time_offset_min' => 0,
                'is_active' => 1,
            ],
            [
                'title' => 'یادآوری رول‌کال به استاد (جلسه جاری)',
                'trigger_event' => 'teacher_qc_rollcall',
                'target_audience' => 'teacher',
                'send_type' => 'pattern',
                'pattern_code' => 'cyedV1fZDy',
                'variables_mapping' => json_encode(['name' => '{teacher_name}', 'item' => 'رول‌کال جلسه {session_no}', 'class' => '{class_title}'], JSON_UNESCAPED_UNICODE),
                'time_offset_min' => 30,
                'is_active' => 1,
            ],
            [
                'title' => 'یادآوری نمرات نهایی ترم به استاد (جلسه آخر)',
                'trigger_event' => 'teacher_qc_finalmarks',
                'target_audience' => 'teacher',
                'send_type' => 'pattern',
                'pattern_code' => 'cyedV1fZDy',
                'variables_mapping' => json_encode(['name' => '{teacher_name}', 'item' => 'نمرات پایانی ترم', 'class' => '{class_title}'], JSON_UNESCAPED_UNICODE),
                'time_offset_min' => 15,
                'is_active' => 1,
            ],
            [
                'title' => 'تبریک سالروز تولد زبان‌آموزان',
                'trigger_event' => 'birthday',
                'target_audience' => 'student',
                'send_type' => 'simple',
                'pattern_code' => null,
                'simple_text' => "{student_name} عزیز، زادروزتان خجسته باد! آرزومند پیروزی و درخشش همیشگی شما در آموزشگاه غزال هستیم.",
                'variables_mapping' => json_encode(['student_name' => '{student_name}'], JSON_UNESCAPED_UNICODE),
                'time_offset_min' => 0,
                'is_active' => 0,
            ],
        ];

        $ins = $pdo->prepare("
            INSERT INTO sms_automations (title, trigger_event, target_audience, send_type, pattern_code, simple_text, variables_mapping, time_offset_min, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        foreach ($defaults as $d) {
            $ins->execute([
                $d['title'],
                $d['trigger_event'],
                $d['target_audience'],
                $d['send_type'],
                $d['pattern_code'],
                $d['simple_text'] ?? null,
                $d['variables_mapping'],
                $d['time_offset_min'],
                $d['is_active'],
            ]);
        }
    }

    $schema_checked = true;
}

/**
 * Log manual send action
 */
function sms_log_manual(PDO $pdo, array $data) {
    sms_ensure_schema($pdo);
    $st = $pdo->prepare("
        INSERT INTO sms_manual_logs 
        (sender_id, send_type, pattern_code, recipient_phone, recipient_name, recipient_role, message_text, variables_json, status, response_info, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");
    $st->execute([
        (int)($data['sender_id'] ?? 0),
        (string)($data['send_type'] ?? 'simple'),
        !empty($data['pattern_code']) ? (string)$data['pattern_code'] : null,
        (string)($data['recipient_phone'] ?? ''),
        (string)($data['recipient_name'] ?? ''),
        (string)($data['recipient_role'] ?? 'manual'),
        !empty($data['message_text']) ? (string)$data['message_text'] : null,
        !empty($data['variables_json']) ? (is_array($data['variables_json']) ? json_encode($data['variables_json'], JSON_UNESCAPED_UNICODE) : (string)$data['variables_json']) : null,
        (string)($data['status'] ?? 'sent'),
        !empty($data['response_info']) ? (is_array($data['response_info']) ? json_encode($data['response_info'], JSON_UNESCAPED_UNICODE) : (string)$data['response_info']) : null,
    ]);
    return (int)$pdo->lastInsertId();
}
