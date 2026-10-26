<?php

function ghazal_story_titles(): array
{
    return [
        'A Day with a Baby',
        'Little Helpers',
        'Where Is It',
        'Doctor Doctor',
        'My Family',
        'Monkeying Around',
        'Silly Squirrel',
        'Just Like Mine',
        'Things That Fly',
        'What Did You Do Yesterday',
        'New Girl in School',
        'Up and Down',
        'Lost',
        'Numbers Numbers',
        'Wonderful Wild Animals',
        'Students in Space',
        'In the Ocean',
        'Tough Task',
        'City Girl',
    ];
}

function ghazal_book_kind(string $title): string
{
    $title = trim($title);
    if ($title === 'Jingle Bells') {
        return 'jingle';
    }
    if ($title === 'Phonics Book' || preg_match('/pamphlet/i', $title)) {
        return 'fixed';
    }
    return in_array($title, ghazal_story_titles(), true) ? 'story' : 'course';
}

function ghazal_fixed_sell_price(string $title): ?int
{
    $kind = ghazal_book_kind($title);
    if ($kind === 'jingle') {
        return 230000;
    }
    if ($kind === 'fixed') {
        return 750000;
    }
    return null;
}

function ghazal_ceil_toman(int $amount): int
{
    if ($amount <= 0) {
        return $amount;
    }
    return (int)(ceil($amount / 10000) * 10000);
}

function ghazal_formula_sell_price(string $title, $inja_price): ?int
{
    $fixed = ghazal_fixed_sell_price($title);
    if ($fixed !== null) {
        return ghazal_ceil_toman($fixed);
    }
    $inja = (int)$inja_price;
    if ($inja <= 0) {
        return null;
    }
    $mult = ghazal_book_kind($title) === 'story' ? 3.0 : 1.35;
    return ghazal_ceil_toman((int)round($inja * $mult));
}

function ghazal_sell_price(string $title, $inja_price, $override = null): ?int
{
    if ($override !== null && (int)$override > 0) {
        return ghazal_ceil_toman((int)$override);
    }
    return ghazal_formula_sell_price($title, $inja_price);
}

function ghazal_delivery_label($type): string
{
    return $type === 'post' ? 'ارسال با پست' : 'تحویل حضوری';
}

function ghazal_collapse_sell_post($submitted): array
{
    $out = [];
    if (!is_array($submitted)) {
        return $out;
    }
    foreach ($submitted as $title => $raw) {
        $title = trim((string)$title);
        if ($title === '') {
            continue;
        }
        if (is_array($raw)) {
            $picked = '';
            foreach ($raw as $value) {
                $value = trim((string)$value);
                if ($value !== '') {
                    $picked = $value;
                }
            }
            $out[$title] = $picked;
        } else {
            $out[$title] = trim((string)$raw);
        }
    }
    return $out;
}

function ghazal_parse_toman($raw): ?int
{
    $value = trim((string)$raw);
    if ($value === '') {
        return null;
    }
    if (function_exists('normalize_digits')) {
        $value = normalize_digits($value);
    } else {
        $value = strtr($value, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);
    }
    $value = str_replace([',', '٬', '،', ' ', "\u{00A0}"], '', $value);
    if ($value === '' || !preg_match('/^\d+$/', $value)) {
        return null;
    }
    $n = (int)$value;
    return $n > 0 ? $n : null;
}

function ensure_sell_overrides_table(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $pdo->exec("CREATE TABLE IF NOT EXISTS book_sell_overrides (
        book_title VARCHAR(190) NOT NULL,
        sell_price INT NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (book_title)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function ghazal_override_map(?PDO $pdo = null): array
{
    static $map = null;
    if ($map !== null) {
        return $map;
    }
    $map = [];
    try {
        $pdo = $pdo ?: db();
        ensure_sell_overrides_table($pdo);
        $rows = $pdo->query('SELECT book_title, sell_price FROM book_sell_overrides')->fetchAll();
        foreach ($rows as $row) {
            $title = trim((string)($row['book_title'] ?? ''));
            $price = (int)($row['sell_price'] ?? 0);
            if ($title !== '' && $price > 0) {
                $map[$title] = $price;
            }
        }
    } catch (Throwable $e) {
        $map = [];
    }
    return $map;
}

function ghazal_save_overrides(PDO $pdo, array $submitted): array
{
    ensure_sell_overrides_table($pdo);
    $saved = 0;
    $cleared = 0;
    $invalid = 0;
    $upsert = $pdo->prepare('INSERT INTO book_sell_overrides (book_title, sell_price, updated_at) VALUES (?,?,NOW()) ON DUPLICATE KEY UPDATE sell_price=VALUES(sell_price), updated_at=NOW()');
    $delete = $pdo->prepare('DELETE FROM book_sell_overrides WHERE book_title=?');
    foreach ($submitted as $title => $raw) {
        $title = trim((string)$title);
        if ($title === '') {
            continue;
        }
        $raw = trim((string)$raw);
        if ($raw === '') {
            $delete->execute([$title]);
            if ($delete->rowCount()) {
                $cleared++;
            }
            continue;
        }
        $parsed = ghazal_parse_toman($raw);
        if ($parsed === null) {
            $invalid++;
            continue;
        }
        $upsert->execute([$title, ghazal_ceil_toman($parsed)]);
        $saved++;
    }
    return ['saved' => $saved, 'cleared' => $cleared, 'invalid' => $invalid];
}
