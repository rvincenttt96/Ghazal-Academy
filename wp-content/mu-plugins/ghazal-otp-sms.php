<?php
/**
 * Plugin Name: Ghazal OTP SMS
 * Description: Site login OTP uses the academy Faraz SMS pattern API (same account as panel roll-call).
 * Version: 1.1.0
 */
defined('ABSPATH') || exit;

add_action('init', 'ghazal_otp_sync_digits_faraz_options', 20);

function ghazal_otp_sms_config() {
    $file = WP_CONTENT_DIR . '/ghazal-sms-config.php';
    $cfg = is_file($file) ? include $file : [];
    if (!is_array($cfg)) {
        $cfg = [];
    }
    return [
        'api_key' => $cfg['api_key'] ?? 'PpgL4mm0OiqyOoDuh0GT6C9pTNI2elZzzrHxXwHrMxQMbNsrD1',
        'sender' => $cfg['sender'] ?? '90008361',
        'pattern_code' => $cfg['pattern_code'] ?? '8G3FF2Yo7M',
        'username' => $cfg['username'] ?? 'u09362798093',
        'url' => $cfg['url'] ?? 'https://api.iranpayamak.com/ws/v1/sms/pattern',
    ];
}

function ghazal_otp_sync_digits_faraz_options() {
    if (!function_exists('get_option') || !function_exists('update_option')) {
        return;
    }
    $cfg = ghazal_otp_sms_config();
    $active = ghazal_otp_discover_active_pattern($cfg);
    if ($active !== '') {
        $cfg['pattern_code'] = $active;
    }

    $current = get_option('digit_farazsms');
    if (!is_array($current)) {
        $current = [];
    }
    $next = $current;
    $next['username'] = $cfg['username'];
    $next['sender'] = $cfg['sender'];
    $next['sample'] = '1';
    $next['patterncode'] = $cfg['pattern_code'];
    $next['patternvars'] = 'code:{OTP}';
    $next['domain'] = 'api.iranpayamak.com';
    if (isset($cfg['password']) && $cfg['password'] !== '') {
        $next['password'] = $cfg['password'];
    }
    if ($next !== $current) {
        update_option('digit_farazsms', $next);
    }
    if ((string) get_option('digit_tapp') !== '322') {
        update_option('digit_tapp', '322');
    }
}

function ghazal_otp_discover_active_pattern($cfg) {
    $cached = get_transient('ghazal_otp_active_pattern');
    if ($cached !== false) {
        return (string) $cached;
    }
    $key = (string) ($cfg['api_key'] ?? '');
    if ($key === '') {
        return '';
    }
    $res = wp_remote_get('https://api.iranpayamak.com/ws/v1/patterns?per_page=50', [
        'timeout' => 12,
        'headers' => [
            'Api-Key' => $key,
            'Accept' => 'application/json',
        ],
        'sslverify' => false,
    ]);
    if (is_wp_error($res)) {
        set_transient('ghazal_otp_active_pattern', '', 45);
        return '';
    }
    $decoded = json_decode((string) wp_remote_retrieve_body($res), true);
    $rows = $decoded['data']['data'] ?? [];
    $preferred = (string) ($cfg['pattern_code'] ?? '');
    $blocked = ['aX3d0ttYHc', 'yCT20au05o'];
    $active = [];
    foreach ((array) $rows as $row) {
        if (!is_array($row) || ($row['status'] ?? '') !== 'active') {
            continue;
        }
        $code = (string) ($row['code'] ?? '');
        if ($code === '' || in_array($code, $blocked, true)) {
            continue;
        }
        $text = (string) ($row['text'] ?? '');
        $vars = [];
        foreach ((array) ($row['attributes'] ?? []) as $attr) {
            if (is_array($attr) && !empty($attr['var'])) {
                $vars[] = (string) $attr['var'];
            }
        }
        if (strpos($text, '%code%') === false && !in_array('code', $vars, true)) {
            continue;
        }
        $active[] = $code;
    }
    $chosen = '';
    if ($preferred !== '' && in_array($preferred, $active, true)) {
        $chosen = $preferred;
    } elseif ($active) {
        $chosen = $active[0];
    }
    set_transient('ghazal_otp_active_pattern', $chosen, $chosen === '' ? 45 : 10 * MINUTE_IN_SECONDS);
    return $chosen;
}
