<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Tehran');

$cfgPath = __DIR__ . '/includes/rollcall-config.php';
$cfg = require $cfgPath;
$base = rtrim((string)($cfg['mattermost_url'] ?? 'https://co.ghazalify.com'), '/');
$token = trim((string)($cfg['mattermost_bot_token'] ?? ''));
$oldCh = trim((string)($cfg['mattermost_channel_id'] ?? ''));
if ($token === '') {
    echo json_encode(['ok' => false, 'error' => 'no token']);
    exit;
}

function mm_req($base, $token, $method, $path, $body = null) {
    $ch = curl_init($base . $path);
    $headers = ['Authorization: Bearer ' . $token, 'Accept: application/json'];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_UNESCAPED_UNICODE));
    }
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => strtoupper($method),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HTTPHEADER => $headers,
    ]);
    $raw = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $json = json_decode((string)$raw, true);
    return ['http' => $http, 'json' => is_array($json) ? $json : null, 'raw' => substr((string)$raw, 0, 280)];
}

function mm_id($res) {
    if ((int)($res['http'] ?? 0) < 200 || (int)($res['http'] ?? 0) >= 300) {
        return '';
    }
    $id = (string)($res['json']['id'] ?? '');
    return preg_match('/^[a-z0-9]{26}$/', $id) ? $id : '';
}

$out = ['ok' => false, 'notes' => []];
$me = mm_req($base, $token, 'GET', '/api/v4/users/me');
$meId = mm_id($me);
$out['me'] = [
    'username' => (string)($me['json']['username'] ?? ''),
    'is_bot' => !empty($me['json']['is_bot']),
    'roles' => (string)($me['json']['roles'] ?? ''),
    'http' => $me['http'],
];
if ($meId === '') {
    $out['error'] = 'me failed';
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

$teamId = '';
if (preg_match('/^[a-z0-9]{26}$/', $oldCh)) {
    $chInfo = mm_req($base, $token, 'GET', '/api/v4/channels/' . $oldCh);
    if (!empty($chInfo['json']['team_id']) && preg_match('/^[a-z0-9]{26}$/', (string)$chInfo['json']['team_id'])) {
        $teamId = (string)$chInfo['json']['team_id'];
    }
}
if ($teamId === '') {
    $teams = mm_req($base, $token, 'GET', '/api/v4/users/me/teams');
    foreach (($teams['json'] ?? []) as $t) {
        if (!empty($t['id']) && preg_match('/^[a-z0-9]{26}$/', $t['id'])) {
            $teamId = $t['id'];
            break;
        }
    }
}
$out['team_ok'] = $teamId !== '';
if ($teamId === '') {
    $out['error'] = 'no team';
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}

$botCreate = mm_req($base, $token, 'POST', '/api/v4/bots', [
    'username' => 'error-logs',
    'display_name' => 'Error Logs',
    'description' => 'SMS, database and AloCom errors',
]);
$out['bot_create_http'] = $botCreate['http'];
$out['bot_create_msg'] = (string)($botCreate['json']['message'] ?? $botCreate['raw']);

$postToken = $token;
$botUserId = $meId;
$botUsername = (string)($me['json']['username'] ?? 'rollcall');

if ($botCreate['http'] === 201 || $botCreate['http'] === 200) {
    $newId = (string)($botCreate['json']['user_id'] ?? '');
    if (preg_match('/^[a-z0-9]{26}$/', $newId)) {
        $tok = mm_req($base, $token, 'POST', '/api/v4/users/' . $newId . '/tokens', ['description' => 'error-logs-post']);
        if (!empty($tok['json']['token']) && strlen($tok['json']['token']) >= 20) {
            $postToken = (string)$tok['json']['token'];
            $botUserId = $newId;
            $botUsername = (string)($botCreate['json']['username'] ?? 'error-logs');
            $out['notes'][] = 'created bot';
        }
    }
}

$channelId = '';
$byName = mm_req($base, $token, 'GET', '/api/v4/teams/' . $teamId . '/channels/name/error-logs');
$channelId = mm_id($byName);
if ($channelId === '') {
    $created = mm_req($base, $token, 'POST', '/api/v4/channels', [
        'team_id' => $teamId,
        'name' => 'error-logs',
        'display_name' => 'Error Logs',
        'type' => 'P',
        'header' => 'خطاهای پیامک، دیتابیس و الوکام',
    ]);
    $out['channel_create_http'] = $created['http'];
    $out['channel_create_msg'] = (string)($created['json']['message'] ?? $created['raw']);
    $channelId = mm_id($created);
    if ($channelId !== '') {
        $out['notes'][] = 'created channel';
    }
} else {
    $out['notes'][] = 'channel exists';
    mm_req($base, $token, 'PUT', '/api/v4/channels/' . $channelId, [
        'id' => $channelId,
        'display_name' => 'Error Logs',
        'header' => 'خطاهای پیامک، دیتابیس و الوکام',
    ]);
}

if ($channelId === '') {
    $out['error'] = 'channel failed';
    echo json_encode($out, JSON_UNESCAPED_UNICODE);
    exit;
}
$out['channel_id_len'] = strlen($channelId);

$wanted = [
    'e.fallah' => 'emad',
    'mohammad_akhavan' => 'akhavan',
    'mahtamortezapour' => 'matin',
];
$keep = [$botUserId => 'bot'];
$out['members_added'] = [];
foreach ($wanted as $username => $key) {
    $u = mm_req($base, $token, 'GET', '/api/v4/users/username/' . rawurlencode($username));
    $uid = mm_id($u);
    if ($uid === '') {
        $out['members_added'][] = ['key' => $key, 'username' => $username, 'http' => $u['http'], 'err' => $u['raw']];
        continue;
    }
    $keep[$uid] = $key;
    $add = mm_req($base, $token, 'POST', '/api/v4/channels/' . $channelId . '/members', [
        'user_id' => $uid,
        'channel_id' => $channelId,
    ]);
    if ($add['http'] === 404 || ($add['http'] >= 400 && $add['http'] !== 409)) {
        $add2 = mm_req($base, $token, 'POST', '/api/v4/channels/' . $channelId . '/members/' . $uid, [
            'user_id' => $uid,
            'channel_id' => $channelId,
        ]);
        $add = $add2;
    }
    $out['members_added'][] = [
        'key' => $key,
        'username' => $username,
        'http' => $add['http'],
        'msg' => (string)($add['json']['message'] ?? ''),
    ];
}

$mem = mm_req($base, $token, 'GET', '/api/v4/channels/' . $channelId . '/members?per_page=200');
$out['member_list_http'] = $mem['http'];
$out['member_count_before_prune'] = is_array($mem['json'] ?? null) ? count($mem['json']) : 0;
$removed = 0;
if (is_array($mem['json'] ?? null)) {
    foreach ($mem['json'] as $m) {
        $uid = (string)($m['user_id'] ?? '');
        if ($uid === '' || isset($keep[$uid])) {
            continue;
        }
        $del = mm_req($base, $token, 'DELETE', '/api/v4/channels/' . $channelId . '/members/' . $uid);
        if ($del['http'] >= 200 && $del['http'] < 300) {
            $removed++;
        }
    }
}
$out['members_removed'] = $removed;

$legend = <<<'TXT'
**کدهای Error Logs**
پیامک: `SMS-NO-PHONE` شماره نیست · `SMS-WRONG` شماره اشتباه/نامعتبر · `SMS-CREDIT` شارژ تمام · `SMS-REJECT` رد فراز · `SMS-PATTERN` پترن · `SMS-TIMEOUT` تایم‌اوت · `SMS-FAIL` سایر
دیتابیس: `DB-CONNECT` اتصال · `DB-QUERY` کوئری · `DB-SAVE` ثبت · `DB-CRASH` کرش
الوکام: `ALO-LOGIN` ورود · `ALO-API` API · `ALO-USER` کاربر · `ALO-CLASS` کلاس/رویداد · `ALO-ENROLL` ثبت در رویداد · `ALO-CRASH` کرش
TXT;

$legendPosted = false;
$posts = mm_req($base, $token, 'GET', '/api/v4/channels/' . $channelId . '/posts?per_page=30');
if (is_array($posts['json']['posts'] ?? null)) {
    foreach ($posts['json']['posts'] as $p) {
        if (strpos((string)($p['message'] ?? ''), 'کدهای Error Logs') !== false && preg_match('/^[a-z0-9]{26}$/', (string)($p['id'] ?? ''))) {
            $legendPosted = true;
            mm_req($base, $token, 'POST', '/api/v4/posts/' . $p['id'] . '/pin');
            $out['notes'][] = 'legend exists';
            break;
        }
    }
}
if (!$legendPosted) {
    $posted = mm_req($base, $token, 'POST', '/api/v4/posts', [
        'channel_id' => $channelId,
        'message' => $legend,
    ]);
    $out['legend_http'] = $posted['http'];
    $pid = mm_id($posted);
    if ($pid !== '') {
        mm_req($base, $token, 'POST', '/api/v4/posts/' . $pid . '/pin');
        $legendPosted = true;
        $out['notes'][] = 'legend posted';
    } else {
        $out['legend_msg'] = (string)($posted['json']['message'] ?? $posted['raw']);
    }
}
$out['legend_posted'] = $legendPosted;

$hookUrl = '';
$hook = mm_req($base, $token, 'POST', '/api/v4/hooks/incoming', [
    'channel_id' => $channelId,
    'display_name' => 'Error Logs',
    'username' => 'Error Logs',
]);
$out['hook_http'] = $hook['http'];
$hookId = '';
if (($hook['http'] === 201 || $hook['http'] === 200) && !empty($hook['json']['id']) && preg_match('/^[a-z0-9]{26}$/', (string)$hook['json']['id'])) {
    $hookId = (string)$hook['json']['id'];
    $hookUrl = $base . '/hooks/' . $hookId;
    $out['notes'][] = 'webhook created';
} else {
    $out['hook_msg'] = (string)($hook['json']['message'] ?? $hook['raw']);
}

$cfg['error_logs'] = [
    'bot_token' => $postToken,
    'channel_id' => $channelId,
    'webhook' => $hookUrl,
    'url' => $base,
];
@copy($cfgPath, dirname(__DIR__) . '/rollcall-config.bak-errorlogs.php');
$wrote = file_put_contents($cfgPath, "<?php\nreturn " . var_export($cfg, true) . ";\n");
$verify = require $cfgPath;
$el = $verify['error_logs'] ?? [];
$out['config_written'] = $wrote !== false;
$out['config_channel_len'] = strlen((string)($el['channel_id'] ?? ''));
$out['config_token_len'] = strlen((string)($el['bot_token'] ?? ''));
$out['config_webhook'] = !empty($el['webhook']);
$out['bot_username'] = $botUsername;
$mem2 = mm_req($base, $token, 'GET', '/api/v4/channels/' . $channelId . '/members?per_page=200');
$names = [];
if (is_array($mem2['json'] ?? null)) {
    foreach ($mem2['json'] as $m) {
        $uid = (string)($m['user_id'] ?? '');
        $u = mm_req($base, $token, 'GET', '/api/v4/users/' . $uid);
        $names[] = (string)($u['json']['username'] ?? $uid);
    }
}
$out['members_now'] = $names;
$out['ok'] = $legendPosted && strlen((string)($el['channel_id'] ?? '')) === 26 && count(array_intersect(['e.fallah', 'mohammad_akhavan', 'mahtamortezapour'], $names)) === 3;

echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
