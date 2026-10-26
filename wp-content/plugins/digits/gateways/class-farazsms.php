<?php
/**
 * Digits FarazSMS drop-in — site OTP via Faraz pattern send
 * (https://docs.farazsms.com POST /ws/v1/sms/pattern).
 *
 * OTP must use an approved pattern. Simple SMS is queued for human review
 * and is not used for login codes.
 */
class digits_farazsms {
    public $username;
    public $Password;
    public $sender;
    public $domain;

    private static $last_error = '';

    public function __construct($arg, $options = null) {
        $arg = is_array($arg) ? $arg : [];
        $this->username = (string) ($arg['username'] ?? '');
        $this->Password = (string) ($arg['password'] ?? ($arg['Password'] ?? ''));
        $this->sender = (string) ($arg['sender'] ?? '');
        $this->domain = (string) ($arg['domain'] ?? '');
    }

    public function send($sms) {
        $to = $this->normalize_mobile($this->sms_get($sms, 'to'));
        $message = (string) $this->sms_get($sms, 'message');
        $cfg = $this->config();

        if ($to === '' || !preg_match('/^09\d{9}$/', $to)) {
            $this->report_panel_error('SMS-WRONG', 'ورود سایت — شماره نامعتبر است.');
            return $this->fail('شماره موبایل نامعتبر است');
        }

        $otp = $this->extract_otp($message);
        if ($otp === '' || !ctype_digit($otp)) {
            return $this->fail('کد تایید در متن پیامک پیدا نشد');
        }

        $pattern = $this->resolve_otp_pattern($cfg);
        if ($pattern === '') {
            $this->report_panel_error('SMS-PATTERN', 'ورود سایت — پترن OTP تأیید نشده.');
            return $this->fail('پترن OTP فراز هنوز تأیید نشده است');
        }

        // Official OTP send (docs.farazsms.com POST /ws/v1/sms/pattern):
        // approved pattern UID, recipient ^09\d{9}$, attributes.code, english digits.
        $result = $this->send_pattern($cfg, [
            'code' => $pattern,
            'recipient' => $to,
            'attributes' => ['code' => $otp],
            'line_number' => $cfg['sender'],
            'number_format' => 'english',
        ]);

        if ($result['ok']) {
            self::$last_error = '';
            return wp_json_encode([0, 'ok']);
        }
        if ($this->is_unapproved_pattern_error($result['message'])) {
            delete_transient('ghazal_otp_active_pattern');
            $this->report_panel_error('SMS-PATTERN', 'ورود سایت — پترن OTP تأیید نشده.', $result['message']);
            return $this->fail('پترن OTP فراز هنوز تأیید نشده است');
        }
        $code = 'SMS-FAIL';
        $blob = strtolower((string) $result['message']);
        if (preg_match('/credit|balance|شارژ|موجودی|اعتبار/', $blob)) {
            $code = 'SMS-CREDIT';
        } elseif (preg_match('/invalid|wrong number|شماره/', $blob)) {
            $code = 'SMS-WRONG';
        } elseif (preg_match('/timeout|timed out/', $blob)) {
            $code = 'SMS-TIMEOUT';
        } elseif (preg_match('/reject|denied|forbidden/', $blob)) {
            $code = 'SMS-REJECT';
        }
        $this->report_panel_error($code, 'ورود سایت — پیامک ارسال نشد.', $result['message']);
        return $this->fail($result['message']);
    }

    public function sendPattern($sms, $url) {
        return $this->send($sms);
    }

    public function errors_describe($error) {
        if (self::$last_error !== '') {
            return self::$last_error;
        }
        return is_string($error) ? $error : wp_json_encode($error);
    }

    private function send_pattern($cfg, $payload) {
        $response = $this->post_json($cfg['url'], $cfg['api_key'], $payload);
        $decoded = json_decode($response['body'], true);
        $ok = $response['http'] >= 200 && $response['http'] < 300 && is_array($decoded) && ($decoded['status'] ?? '') === 'success';
        $msg = '';
        if (is_array($decoded)) {
            $msg = (string) ($decoded['message'] ?? '');
        }
        if ($msg === '') {
            $msg = $response['body'] !== '' ? $response['body'] : ('HTTP ' . $response['http']);
        }
        return ['ok' => $ok, 'message' => $msg];
    }

    private function resolve_otp_pattern($cfg) {
        $cached = get_transient('ghazal_otp_active_pattern');
        if ($cached !== false) {
            return (string) $cached;
        }

        $preferred = (string) $cfg['pattern_code'];
        if ($preferred !== '' && $this->pattern_is_active_otp($cfg, $preferred)) {
            set_transient('ghazal_otp_active_pattern', $preferred, 10 * MINUTE_IN_SECONDS);
            return $preferred;
        }

        $candidates = $this->list_otp_patterns($cfg);
        $active = [];
        foreach ($candidates as $row) {
            if (($row['status'] ?? '') === 'active') {
                $active[] = $row['code'];
            }
        }
        if ($active) {
            set_transient('ghazal_otp_active_pattern', $active[0], 10 * MINUTE_IN_SECONDS);
            return $active[0];
        }
        set_transient('ghazal_otp_active_pattern', '', 45);
        return '';
    }

    private function pattern_is_active_otp($cfg, $code) {
        $response = $this->get_json(
            'https://api.iranpayamak.com/ws/v1/patterns/' . rawurlencode($code),
            $cfg['api_key']
        );
        $decoded = json_decode($response['body'], true);
        $row = is_array($decoded) ? ($decoded['data'] ?? []) : [];
        return $this->row_is_active_otp($row);
    }

    private function row_is_active_otp($row) {
        return $this->row_looks_like_otp($row) && (string) ($row['status'] ?? '') === 'active';
    }

    private function list_otp_patterns($cfg) {
        $response = $this->get_json(
            'https://api.iranpayamak.com/ws/v1/patterns?per_page=50',
            $cfg['api_key']
        );
        $decoded = json_decode($response['body'], true);
        $rows = [];
        if (is_array($decoded)) {
            $data = $decoded['data']['data'] ?? $decoded['data'] ?? [];
            if (is_array($data)) {
                $rows = $data;
            }
            if (isset($rows['code'])) {
                $rows = [$rows];
            }
        }
        $out = [];
        foreach ($rows as $row) {
            if (!$this->row_looks_like_otp($row)) {
                continue;
            }
            $out[] = [
                'code' => (string) $row['code'],
                'status' => (string) ($row['status'] ?? ''),
            ];
        }
        return $out;
    }

    private function row_looks_like_otp($row) {
        if (!is_array($row)) {
            return false;
        }
        $code = (string) ($row['code'] ?? '');
        $blocked = ['aX3d0ttYHc', 'yCT20au05o'];
        if ($code === '' || in_array($code, $blocked, true)) {
            return false;
        }
        $text = (string) ($row['text'] ?? '');
        $vars = [];
        foreach ((array) ($row['attributes'] ?? []) as $attr) {
            if (is_array($attr) && !empty($attr['var'])) {
                $vars[] = (string) $attr['var'];
            }
        }
        return (strpos($text, '%code%') !== false) || in_array('code', $vars, true);
    }

    private function is_unapproved_pattern_error($message) {
        return strpos((string) $message, 'یافت نشد') !== false;
    }

    private function sms_get($sms, $key, $default = '') {
        if (is_array($sms) && array_key_exists($key, $sms)) {
            return $sms[$key];
        }
        if (is_object($sms) && isset($sms->{$key})) {
            return $sms->{$key};
        }
        return $default;
    }

    private function config() {
        $file = defined('WP_CONTENT_DIR')
            ? WP_CONTENT_DIR . '/ghazal-sms-config.php'
            : __DIR__ . '/../../../../ghazal-sms-config.php';
        $cfg = is_file($file) ? include $file : [];
        if (!is_array($cfg)) {
            $cfg = [];
        }
        $opt = get_option('digit_farazsms');
        $opt = is_array($opt) ? $opt : [];

        return [
            'api_key' => $cfg['api_key'] ?? 'PpgL4mm0OiqyOoDuh0GT6C9pTNI2elZzzrHxXwHrMxQMbNsrD1',
            'sender' => $cfg['sender'] ?? ((string) ($opt['sender'] ?? '') ?: '90008361'),
            'pattern_code' => $cfg['pattern_code'] ?? ((string) ($opt['patterncode'] ?? '') ?: '8G3FF2Yo7M'),
            'url' => $cfg['url'] ?? 'https://api.iranpayamak.com/ws/v1/sms/pattern',
        ];
    }

    private function normalize_mobile($to) {
        $d = preg_replace('/\D+/', '', (string) $to);
        if ($d === '') {
            return '';
        }
        if (strpos($d, '0098') === 0) {
            $d = substr($d, 4);
        } elseif (strpos($d, '98') === 0 && strlen($d) >= 12) {
            $d = substr($d, 2);
        }
        if ($d !== '' && $d[0] !== '0') {
            $d = '0' . $d;
        }
        return $d;
    }

    private function extract_otp($message) {
        if (preg_match('/(?:^|;|\s)code:([0-9]{3,8})/i', $message, $m)) {
            return $m[1];
        }
        if (preg_match('/patterncode:[^;]+;([A-Za-z0-9_]+):([0-9]{3,8})/i', $message, $m)) {
            return $m[2];
        }
        if (preg_match('/\b([0-9]{4,8})\b/', $message, $m)) {
            return $m[1];
        }
        return '';
    }

    private function post_json($url, $api_key, $payload) {
        $body = wp_json_encode($payload, JSON_UNESCAPED_UNICODE);
        return $this->http($url, $api_key, $body);
    }

    private function get_json($url, $api_key) {
        return $this->http($url, $api_key, null);
    }

    private function http($url, $api_key, $body = null) {
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            $headers = [
                'Api-Key: ' . $api_key,
                'Accept: application/json',
            ];
            if ($body !== null) {
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            $raw = curl_exec($ch);
            $http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err = curl_error($ch);
            curl_close($ch);
            if ($raw === false) {
                return ['http' => 0, 'body' => $err];
            }
            return ['http' => $http, 'body' => (string) $raw];
        }

        $args = [
            'timeout' => 15,
            'headers' => [
                'Api-Key' => $api_key,
                'Accept' => 'application/json',
            ],
            'sslverify' => false,
        ];
        if ($body !== null) {
            $args['headers']['Content-Type'] = 'application/json';
            $args['body'] = $body;
            $res = wp_remote_post($url, $args);
        } else {
            $res = wp_remote_get($url, $args);
        }
        if (is_wp_error($res)) {
            return ['http' => 0, 'body' => $res->get_error_message()];
        }
        return [
            'http' => (int) wp_remote_retrieve_response_code($res),
            'body' => (string) wp_remote_retrieve_body($res),
        ];
    }

    private function fail($message) {
        self::$last_error = (string) $message;
        return wp_json_encode([1, $message], JSON_UNESCAPED_UNICODE);
    }

    private function report_panel_error($code, $reason, $extra = '') {
        $secret = '';
        if (defined('GHAZAL_IDENTITY_SECRET')) {
            $secret = (string) GHAZAL_IDENTITY_SECRET;
        } elseif (defined('WP_CONTENT_DIR') && is_file(WP_CONTENT_DIR . '/ghazal-identity-secret.php')) {
            require_once WP_CONTENT_DIR . '/ghazal-identity-secret.php';
            if (defined('GHAZAL_IDENTITY_SECRET')) {
                $secret = (string) GHAZAL_IDENTITY_SECRET;
            }
        }
        if ($secret === '') {
            return;
        }
        $body = wp_json_encode([
            'code' => (string) $code,
            'reason' => (string) $reason,
            'extra' => (string) $extra,
        ], JSON_UNESCAPED_UNICODE);
        $host = 'panel.ghazalify.com';
        $path = '/error-ping.php';
        $ctx = stream_context_create([
            'ssl' => [
                'peer_name' => $host,
                'verify_peer' => false,
                'verify_peer_name' => false,
                'SNI_enabled' => true,
            ],
        ]);
        $fp = @stream_socket_client('ssl://' . $host . ':443', $errno, $errstr, 0.3, STREAM_CLIENT_CONNECT, $ctx);
        if (!$fp) {
            return;
        }
        stream_set_timeout($fp, 0, 350000);
        $req = "POST {$path} HTTP/1.1\r\n";
        $req .= "Host: {$host}\r\n";
        $req .= "Content-Type: application/json\r\n";
        $req .= 'X-Ghazal-Identity: ' . $secret . "\r\n";
        $req .= 'Content-Length: ' . strlen($body) . "\r\n";
        $req .= "Connection: close\r\n\r\n" . $body;
        @fwrite($fp, $req);
        @fclose($fp);
    }
}
