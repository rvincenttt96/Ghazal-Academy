<?php
require_once '../../includes/auth.php';
require_role('supervisor');
header('Content-Type: application/json; charset=utf-8');

$cfg = is_file(__DIR__ . '/../../includes/liara-ai-config.php')
    ? (require __DIR__ . '/../../includes/liara-ai-config.php')
    : [];
$workspace = trim((string)($cfg['workspace_id'] ?? ''));
$key = trim((string)($cfg['api_key'] ?? ''));
$base = rtrim((string)($cfg['base_url'] ?? 'https://ai.liara.ir'), '/');
$prefix = $base . '/api/' . rawurlencode($workspace);

function sai($url, $key, $method, $body, $http_ver, $timeout = 8) {
    $ch = curl_init($url);
    $headers = ['Accept: application/json', 'Authorization: Bearer ' . $key, 'Expect:'];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
    }
    $opts = [
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => $timeout,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
        CURLOPT_HTTP_VERSION => $http_ver,
        CURLOPT_HTTPHEADER => $headers,
    ];
    if ($body !== null) {
        $opts[CURLOPT_POSTFIELDS] = $body;
    }
    curl_setopt_array($ch, $opts);
    $raw = curl_exec($ch);
    $info = [
        'method' => $method,
        'ver' => $http_ver === CURL_HTTP_VERSION_2_0 ? 'h2' : '1.1',
        'errno' => curl_errno($ch),
        'error' => curl_error($ch),
        'http' => (int)curl_getinfo($ch, CURLINFO_HTTP_CODE),
        'total' => round((float)curl_getinfo($ch, CURLINFO_TOTAL_TIME), 3),
        'snippet' => is_string($raw) ? mb_substr($raw, 0, 180, 'UTF-8') : '',
    ];
    curl_close($ch);
    return $info;
}

$out = [
    'post_empty_completions' => sai($prefix . '/v1/chat/completions', $key, 'POST', '{}', CURL_HTTP_VERSION_1_1, 8),
    'post_models' => sai($prefix . '/v1/models', $key, 'POST', '{}', CURL_HTTP_VERSION_1_1, 8),
    'post_empty_h2' => sai($prefix . '/v1/chat/completions', $key, 'POST', '{}', CURL_HTTP_VERSION_2_0, 8),
];
echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
