<?php
/**
 * Quiz reports → Mattermost «کوییز چک», and a bank of good questions for few-shot.
 */

function quiz_feedback_config() {
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }
    $all = [];
    $path = __DIR__ . '/rollcall-config.php';
    if (is_file($path)) {
        $loaded = require $path;
        if (is_array($loaded)) {
            $all = $loaded;
        }
    }
    $q = is_array($all['quiz_check'] ?? null) ? $all['quiz_check'] : [];
    $extra = __DIR__ . '/quiz-check-config.php';
    if (is_file($extra)) {
        $more = require $extra;
        if (is_array($more)) {
            $q = array_merge($q, $more);
        }
    }
    $ai = is_array($all['ai_training'] ?? null) ? $all['ai_training'] : [];
    $cfg = [
        'url' => rtrim((string)($q['url'] ?? $all['mattermost_url'] ?? 'https://co.ghazalify.com'), '/'),
        'bot_token' => trim((string)($q['bot_token'] ?? $ai['bot_token'] ?? $all['mattermost_bot_token'] ?? '')),
        'channel_id' => trim((string)($q['channel_id'] ?? '')),
        'hook_secret' => trim((string)($q['hook_secret'] ?? '')),
        'slash_token' => trim((string)($q['slash_token'] ?? '')),
        'hook_url' => trim((string)($q['hook_url'] ?? 'https://panel.ghazalify.com/quiz-check-hook.php')),
    ];
    return $cfg;
}

function quiz_feedback_clip($text, $max = 3500) {
    $text = trim((string)$text);
    if ($text === '') {
        return '';
    }
    if (function_exists('mb_substr')) {
        if (mb_strlen($text, 'UTF-8') > $max) {
            return mb_substr($text, 0, $max, 'UTF-8') . '…';
        }
        return $text;
    }
    if (strlen($text) > $max) {
        return substr($text, 0, $max) . '…';
    }
    return $text;
}

function quiz_feedback_ensure(PDO $pdo) {
    static $done = false;
    if ($done) {
        return;
    }
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quiz_check_reports (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            exam_id INT NOT NULL DEFAULT 0,
            class_id INT NOT NULL DEFAULT 0,
            book_id INT NOT NULL DEFAULT 0,
            template_no TINYINT NOT NULL DEFAULT 0,
            level_key VARCHAR(24) NOT NULL DEFAULT '',
            scope VARCHAR(16) NOT NULL,
            question_id VARCHAR(32) NOT NULL DEFAULT '',
            payload_json MEDIUMTEXT NOT NULL,
            username VARCHAR(64) NOT NULL DEFAULT '',
            full_name VARCHAR(190) NOT NULL DEFAULT '',
            mm_ok TINYINT(1) NOT NULL DEFAULT 0,
            mm_post_id VARCHAR(32) NOT NULL DEFAULT '',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            KEY user_idx (user_id, created_at),
            KEY exam_idx (exam_id),
            KEY mm_post_idx (mm_post_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS quiz_good_bank (
            id INT AUTO_INCREMENT PRIMARY KEY,
            report_id INT NOT NULL DEFAULT 0,
            exam_id INT NOT NULL DEFAULT 0,
            scope VARCHAR(16) NOT NULL,
            level_key VARCHAR(24) NOT NULL DEFAULT '',
            template_no TINYINT NOT NULL DEFAULT 0,
            skill VARCHAR(24) NOT NULL DEFAULT '',
            qtype VARCHAR(16) NOT NULL DEFAULT '',
            question_hash CHAR(40) NOT NULL,
            question_json MEDIUMTEXT NOT NULL,
            added_by VARCHAR(190) NOT NULL DEFAULT '',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY uq_qgb_hash (question_hash),
            KEY lvl_tpl (level_key, template_no),
            KEY skill_idx (skill)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    $done = true;
}

function quiz_feedback_actor() {
    return [
        'user_id' => (int)($_SESSION['user_id'] ?? 0),
        'username' => trim((string)($_SESSION['username'] ?? '')),
        'full_name' => trim((string)($_SESSION['full_name'] ?? '')),
    ];
}

function quiz_feedback_sig($report_id, $action) {
    $secret = quiz_feedback_config()['hook_secret'];
    if ($secret === '') {
        return '';
    }
    return hash_hmac('sha256', (int)$report_id . '|' . (string)$action, $secret);
}

function quiz_feedback_sig_ok($report_id, $action, $sig) {
    $want = quiz_feedback_sig($report_id, $action);
    $sig = (string)$sig;
    return $want !== '' && $sig !== '' && hash_equals($want, $sig);
}

function quiz_feedback_format_question(array $q, $index = 0) {
    $n = $index + 1;
    $skill = trim((string)($q['skill'] ?? ''));
    $type = trim((string)($q['type'] ?? ''));
    $id = trim((string)($q['id'] ?? ('q-' . $n)));
    $prompt = trim((string)($q['prompt'] ?? ''));
    $lines = ['**Q' . $n . '** `' . $id . '` · ' . $skill . ' · ' . $type, $prompt];
    $opts = [];
    if (!empty($q['options']) && is_array($q['options'])) {
        foreach ($q['options'] as $opt) {
            $opt = trim((string)$opt);
            if ($opt !== '') {
                $opts[] = $opt;
            }
        }
    }
    if ($opts) {
        $lines[] = 'گزینه‌ها: ' . implode(' | ', $opts);
    }
    $ans = trim((string)($q['answer'] ?? ''));
    if ($ans !== '') {
        $lines[] = 'پاسخ: ' . $ans;
    }
    return implode("\n", $lines);
}

function quiz_feedback_question_hash($level_key, $template_no, array $q) {
    $key = strtoupper(trim((string)$level_key))
        . '|' . (int)$template_no
        . '|' . trim((string)($q['skill'] ?? ''))
        . '|' . trim((string)($q['type'] ?? ''))
        . '|' . trim((string)($q['prompt'] ?? ''))
        . '|' . trim((string)($q['answer'] ?? ''));
    return sha1($key);
}

function quiz_feedback_post_mattermost($message, array $attachments = []) {
    $cfg = quiz_feedback_config();
    $token = $cfg['bot_token'];
    $channel = $cfg['channel_id'];
    $base = $cfg['url'];
    if ($token === '' || $channel === '' || $base === '' || trim((string)$message) === '') {
        return '';
    }
    $body = [
        'channel_id' => $channel,
        'message' => $message,
        'props' => [
            'from_webhook' => 'true',
            'override_username' => 'کوییز',
            'webhook_display_name' => 'کوییز',
        ],
    ];
    if ($attachments) {
        $body['props']['attachments'] = $attachments;
    }
    $payload = json_encode($body, JSON_UNESCAPED_UNICODE);
    $ch = curl_init($base . '/api/v4/posts');
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ],
        CURLOPT_TIMEOUT => 12,
        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
    ]);
    $resp = curl_exec($ch);
    $errno = curl_errno($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $ok = $errno === 0 && $http >= 200 && $http < 300;
    if (!$ok) {
        error_log('quiz check mm fail errno=' . $errno . ' http=' . $http . ' resp=' . substr((string)$resp, 0, 180));
        return '';
    }
    $decoded = json_decode((string)$resp, true);
    if (is_array($decoded) && !empty($decoded['id'])) {
        return (string)$decoded['id'];
    }
    return 'ok';
}

function quiz_feedback_actions($report_id, $scope, $question_id) {
    $cfg = quiz_feedback_config();
    $url = $cfg['hook_url'];
    if ($url === '' || $cfg['hook_secret'] === '') {
        return [];
    }
    $actions = [];
    if ($scope === 'question' || $question_id !== '') {
        $actions[] = [
            'id' => 'add_question',
            'name' => 'افزودن این سؤال',
            'type' => 'button',
            'style' => 'good',
            'integration' => [
                'url' => $url,
                'context' => [
                    'action' => 'add_question',
                    'report_id' => (int)$report_id,
                    'sig' => quiz_feedback_sig($report_id, 'add_question'),
                ],
            ],
        ];
    }
    $actions[] = [
        'id' => 'add_quiz',
        'name' => 'افزودن کل کوییز',
        'type' => 'button',
        'style' => 'primary',
        'integration' => [
            'url' => $url,
            'context' => [
                'action' => 'add_quiz',
                'report_id' => (int)$report_id,
                'sig' => quiz_feedback_sig($report_id, 'add_quiz'),
            ],
        ],
    ];
    return $actions;
}

function quiz_feedback_report(PDO $pdo, array $exam, $scope, $question_id, array $overlay = []) {
    $actor = quiz_feedback_actor();
    if ($actor['user_id'] <= 0) {
        throw new Exception('نشست نامعتبر است.');
    }
    $scope = $scope === 'quiz' ? 'quiz' : 'question';
    $question_id = trim((string)$question_id);

    $now = time();
    $bucket = $_SESSION['quiz_check_report_rate'] ?? ['start' => $now, 'n' => 0];
    if ($now - (int)$bucket['start'] > 900) {
        $bucket = ['start' => $now, 'n' => 0];
    }
    $bucket['n'] = (int)$bucket['n'] + 1;
    $_SESSION['quiz_check_report_rate'] = $bucket;
    if ($bucket['n'] > 16) {
        throw new Exception('تعداد گزارش در این چند دقیقه زیاد شد. کمی بعد دوباره امتحان کنید.');
    }

    $payload = quiz_builder_decode_payload($exam['questions_json'] ?? '');
    unset($payload['_facts']);
    if ($overlay) {
        if (isset($overlay['questions']) && is_array($overlay['questions'])) {
            $payload['questions'] = $overlay['questions'];
        }
        foreach (['passage', 'listening_script', 'listening_repetition'] as $k) {
            if (array_key_exists($k, $overlay)) {
                $payload[$k] = $overlay[$k];
            }
        }
    }
    $questions = [];
    foreach ((array)($payload['questions'] ?? []) as $q) {
        if (is_array($q)) {
            $questions[] = $q;
        }
    }
    if (!$questions) {
        throw new Exception('سؤالی برای گزارش نیست.');
    }
    $picked = null;
    $picked_i = 0;
    if ($scope === 'question') {
        foreach ($questions as $i => $q) {
            if ((string)($q['id'] ?? '') === $question_id) {
                $picked = $q;
                $picked_i = $i;
                break;
            }
        }
        if (!$picked && $questions && $question_id === '') {
            $picked = $questions[0];
            $question_id = (string)($picked['id'] ?? 'q-1');
        }
        if (!$picked) {
            throw new Exception('سؤال پیدا نشد.');
        }
    }

    $level_key = (string)($exam['level_key'] ?? '');
    $tpl_no = (int)($exam['template_no'] ?? 0);
    $tpl_label = (string)($exam['template_label'] ?? ('Quiz ' . $tpl_no));
    $name = $actor['full_name'] !== '' ? $actor['full_name'] : 'بدون نام';
    $uname = $actor['username'] !== '' ? $actor['username'] : '—';

    $store = [
        'exam_id' => (int)($exam['id'] ?? 0),
        'class_id' => (int)($exam['class_id'] ?? 0),
        'book_id' => (int)($exam['book_id'] ?? 0),
        'book_name' => (string)($exam['book_name'] ?? ''),
        'template_no' => $tpl_no,
        'template_label' => $tpl_label,
        'level_key' => $level_key,
        'difficulty' => (string)($exam['difficulty'] ?? ''),
        'page_from' => (int)($exam['page_from'] ?? 0),
        'page_to' => (int)($exam['page_to'] ?? 0),
        'passage' => (string)($payload['passage'] ?? ''),
        'listening_script' => (string)($payload['listening_script'] ?? ''),
        'questions' => $questions,
        'focus_id' => $question_id,
    ];

    quiz_feedback_ensure($pdo);
    $st = $pdo->prepare("
        INSERT INTO quiz_check_reports
            (user_id, exam_id, class_id, book_id, template_no, level_key, scope, question_id,
             payload_json, username, full_name, mm_ok, mm_post_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, '')
    ");
    $st->execute([
        $actor['user_id'],
        (int)$store['exam_id'],
        (int)$store['class_id'],
        (int)$store['book_id'],
        $tpl_no,
        $level_key,
        $scope,
        $question_id,
        json_encode($store, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        $uname,
        $name,
    ]);
    $report_id = (int)$pdo->lastInsertId();

    $head = "**گزارش کوییز** · " . ($scope === 'quiz' ? 'کل آزمون' : 'سؤال تکی') . "\n";
    $head .= "معلم: **" . $name . "** (`" . $uname . "`)\n";
    $head .= "سطح **" . ($level_key !== '' ? $level_key : '—') . "** · " . $tpl_label;
    $head .= " · صفحه " . (int)$store['page_from'] . "–" . (int)$store['page_to'] . "\n";
    $head .= "شناسه گزارش `" . $report_id . "`";
    if (!empty($store['book_name'])) {
        $head .= "\nکتاب: " . $store['book_name'];
    }

    $body = '';
    if ($scope === 'question' && $picked) {
        $body = quiz_feedback_format_question($picked, $picked_i);
    } else {
        $parts = [];
        foreach ($questions as $i => $q) {
            $parts[] = quiz_feedback_format_question($q, $i);
        }
        $body = implode("\n\n", $parts);
    }
    $msg = quiz_feedback_clip($head . "\n\n" . $body, 3800);
    $msg .= "\n\nاز دکمه‌ها یا `/quiz-bank سوال` و `/quiz-bank کوییز` به بانک خوب اضافه کنید.";

    $actions = quiz_feedback_actions($report_id, $scope, $question_id);
    $attachments = [];
    if ($actions) {
        $attachments[] = [
            'fallback' => 'افزودن به بانک کوییزهای خوب',
            'color' => '#1830a8',
            'title' => $scope === 'quiz' ? 'کل کوییز' : 'این سؤال',
            'text' => 'اگر سؤال خوب است، به بانک کوییزهای خوب اضافه کنید تا ساخت بعدی شبیه همین شود.',
            'actions' => $actions,
        ];
    }
    $mm_post = quiz_feedback_post_mattermost($msg, $attachments);
    $pdo->prepare("UPDATE quiz_check_reports SET mm_ok = ?, mm_post_id = ? WHERE id = ?")
        ->execute([$mm_post !== '' ? 1 : 0, $mm_post === 'ok' ? '' : $mm_post, $report_id]);
    if ($mm_post === '') {
        throw new Exception('گزارش ذخیره شد، ولی ارسال به کانال کوییز چک برقرار نشد.');
    }
    return [
        'ok' => true,
        'report_id' => $report_id,
        'scope' => $scope,
    ];
}

function quiz_feedback_load_report(PDO $pdo, $report_id) {
    quiz_feedback_ensure($pdo);
    $st = $pdo->prepare("SELECT * FROM quiz_check_reports WHERE id = ? LIMIT 1");
    $st->execute([(int)$report_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function quiz_feedback_latest_report(PDO $pdo) {
    quiz_feedback_ensure($pdo);
    $row = $pdo->query("SELECT * FROM quiz_check_reports ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function quiz_feedback_insert_question(PDO $pdo, array $report, array $q, $scope, $added_by) {
    $payload = json_decode((string)($report['payload_json'] ?? ''), true);
    if (!is_array($payload)) {
        $payload = [];
    }
    $level_key = (string)($payload['level_key'] ?? $report['level_key'] ?? '');
    $template_no = (int)($payload['template_no'] ?? $report['template_no'] ?? 0);
    if (!function_exists('quiz_builder_normalize_question')) {
        require_once __DIR__ . '/quiz-builder.php';
    }
    $item = quiz_builder_normalize_question($q, 0);
    if ($item['prompt'] === '') {
        return 'skip';
    }
    $hash = quiz_feedback_question_hash($level_key, $template_no, $item);
    $st = $pdo->prepare("
        INSERT INTO quiz_good_bank
            (report_id, exam_id, scope, level_key, template_no, skill, qtype, question_hash, question_json, added_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            added_by = VALUES(added_by),
            question_json = VALUES(question_json)
    ");
    $st->execute([
        (int)($report['id'] ?? 0),
        (int)($report['exam_id'] ?? 0),
        $scope,
        $level_key,
        $template_no,
        $item['skill'],
        $item['type'],
        $hash,
        json_encode($item, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        quiz_feedback_clip($added_by, 180),
    ]);
    return $st->rowCount() === 1 ? 'added' : 'exists';
}

function quiz_feedback_add_from_report(PDO $pdo, $report_id, $action, $added_by, $question_index = 0) {
    $report = quiz_feedback_load_report($pdo, $report_id);
    if (!$report) {
        throw new Exception('گزارش پیدا نشد.');
    }
    $payload = json_decode((string)($report['payload_json'] ?? ''), true);
    $questions = is_array($payload['questions'] ?? null) ? $payload['questions'] : [];
    if (!$questions) {
        throw new Exception('در این گزارش سؤالی نیست.');
    }
    $added = 0;
    $exists = 0;
    if ($action === 'add_question') {
        $focus = (string)($report['question_id'] ?? $payload['focus_id'] ?? '');
        $picked = null;
        foreach ($questions as $i => $q) {
            if (!is_array($q)) {
                continue;
            }
            if ($question_index > 0 && ($i + 1) === (int)$question_index) {
                $picked = $q;
                break;
            }
            if ($focus !== '' && (string)($q['id'] ?? '') === $focus) {
                $picked = $q;
                break;
            }
        }
        if (!$picked) {
            if ($question_index > 0) {
                throw new Exception('شماره سؤال در این گزارش نیست.');
            }
            $picked = $questions[0];
        }
        $r = quiz_feedback_insert_question($pdo, $report, $picked, 'question', $added_by);
        if ($r === 'added') {
            $added = 1;
        } elseif ($r === 'exists') {
            $exists = 1;
        }
    } else {
        foreach ($questions as $q) {
            if (!is_array($q)) {
                continue;
            }
            $r = quiz_feedback_insert_question($pdo, $report, $q, 'quiz', $added_by);
            if ($r === 'added') {
                $added++;
            } elseif ($r === 'exists') {
                $exists++;
            }
        }
    }
    return [
        'ok' => true,
        'added' => $added,
        'exists' => $exists,
        'report_id' => (int)$report['id'],
        'action' => $action,
    ];
}

function quiz_feedback_fewshot(PDO $pdo, $level_key, $template_no) {
    quiz_feedback_ensure($pdo);
    $level_key = strtoupper(trim((string)$level_key));
    $template_no = (int)$template_no;
    $out = [];
    $seen = [];
    $try = [
        [$level_key, $template_no],
        [$level_key, 0],
        ['', 0],
    ];
    foreach ($try as $pair) {
        $sql = "SELECT question_json FROM quiz_good_bank WHERE 1=1";
        $params = [];
        if ($pair[0] !== '') {
            $sql .= " AND level_key = ?";
            $params[] = $pair[0];
        }
        if ((int)$pair[1] > 0) {
            $sql .= " AND template_no = ?";
            $params[] = (int)$pair[1];
        }
        $sql .= " ORDER BY id DESC LIMIT 8";
        $st = $pdo->prepare($sql);
        $st->execute($params);
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $q = json_decode((string)$row['question_json'], true);
            if (!is_array($q) || trim((string)($q['prompt'] ?? '')) === '') {
                continue;
            }
            $h = sha1((string)$q['prompt']);
            if (isset($seen[$h])) {
                continue;
            }
            $seen[$h] = true;
            $out[] = $q;
            if (count($out) >= 5) {
                return $out;
            }
        }
        if (count($out) >= 3) {
            break;
        }
    }
    return $out;
}

function quiz_feedback_parse_slash_text($text) {
    $text = trim((string)$text);
    $text = str_replace(
        ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'],
        ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'],
        $text
    );
    $low = function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8') : strtolower($text);
    $n = 0;
    if (preg_match('/(\d+)/u', $text, $m)) {
        $n = (int)$m[1];
    }
    if ($low === '' || preg_match('/سؤال|سوال|question|q\b/u', $low)) {
        return ['add_question', $n];
    }
    if (preg_match('/کوییز|کوئیز|quiz|کل|همه/u', $low)) {
        return ['add_quiz', 0];
    }
    return ['add_question', $n];
}

function quiz_feedback_hook_result($text, $ok = true) {
    return [
        'response_type' => 'ephemeral',
        'text' => $text,
        'username' => 'کوییز',
        'ephemeral_text' => $text,
        'ok' => $ok,
    ];
}

function quiz_feedback_human_result(array $out) {
    $added = (int)($out['added'] ?? 0);
    $exists = (int)($out['exists'] ?? 0);
    $act = (string)($out['action'] ?? '');
    if ($added < 1 && $exists > 0) {
        return $act === 'add_quiz'
            ? 'این سؤال‌ها از قبل در بانک کوییزهای خوب بودند.'
            : 'این سؤال از قبل در بانک کوییزهای خوب بود.';
    }
    if ($act === 'add_quiz') {
        return $added . ' سؤال به بانک کوییزهای خوب اضافه شد'
            . ($exists ? ' (' . $exists . ' تکراری بود).' : '.');
    }
    return $added ? 'این سؤال به بانک کوییزهای خوب اضافه شد.' : 'چیزی ذخیره نشد.';
}
