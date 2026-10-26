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
