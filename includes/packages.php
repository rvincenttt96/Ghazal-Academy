<?php

function ghazal_seed_packages(): array
{
    static $seed = null;
    if ($seed === null) {
        $loaded = require __DIR__ . '/ghazal-packages.php';
        $seed = is_array($loaded) ? $loaded : [];
    }
    return $seed;
}

function ensure_package_books_table(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;
    $pdo->exec("CREATE TABLE IF NOT EXISTS package_books (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        package_code VARCHAR(40) NOT NULL,
        book_title VARCHAR(190) NOT NULL,
        sort_order INT NOT NULL DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY uq_pkg_book (package_code, book_title),
        KEY idx_pkg (package_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $count = (int)$pdo->query('SELECT COUNT(*) FROM package_books')->fetchColumn();
    if ($count > 0) {
        return;
    }
    $ins = $pdo->prepare('INSERT INTO package_books (package_code, book_title, sort_order) VALUES (?,?,?)');
    foreach (ghazal_seed_packages() as $code => $books) {
        $i = 0;
        foreach ((array)$books as $title) {
            $title = trim((string)$title);
            if ($title === '') {
                continue;
            }
            $ins->execute([(string)$code, $title, $i++]);
        }
    }
}

function ghazal_live_packages(?PDO $pdo = null, bool $refresh = false): array
{
    static $map = null;
    if ($refresh) {
        $map = null;
    }
    if ($map !== null) {
        return $map;
    }
    $map = [];
    try {
        $pdo = $pdo ?: db();
        ensure_package_books_table($pdo);
        $rows = $pdo->query('SELECT package_code, book_title FROM package_books ORDER BY package_code, sort_order, id')->fetchAll();
        foreach ($rows as $row) {
            $code = trim((string)($row['package_code'] ?? ''));
            $title = trim((string)($row['book_title'] ?? ''));
            if ($code === '') {
                continue;
            }
            if (!isset($map[$code])) {
                $map[$code] = [];
            }
            if ($title !== '' && !in_array($title, $map[$code], true)) {
                $map[$code][] = $title;
            }
        }
        foreach (ghazal_seed_packages() as $code => $_books) {
            $code = (string)$code;
            if ($code !== '' && !isset($map[$code])) {
                $map[$code] = [];
            }
        }
    } catch (Throwable $e) {
        $map = [];
    }
    if ($map === []) {
        $map = ghazal_seed_packages();
    }
    return $map;
}

function ghazal_package_exists(string $code): bool
{
    $code = trim($code);
    if ($code === '') {
        return false;
    }
    return array_key_exists($code, ghazal_live_packages());
}

function ghazal_add_package_book(PDO $pdo, string $code, string $title): array
{
    ensure_package_books_table($pdo);
    $code = trim($code);
    $title = trim($title);
    if ($code === '' || !ghazal_package_exists($code)) {
        return ['ok' => false, 'error' => 'سطح انتخاب‌شده معتبر نیست.'];
    }
    if ($title === '') {
        return ['ok' => false, 'error' => 'نام کتاب را بنویسید.'];
    }
    if (function_exists('mb_strlen') && mb_strlen($title, 'UTF-8') > 180) {
        return ['ok' => false, 'error' => 'نام کتاب خیلی بلند است.'];
    }
    $stmt = $pdo->prepare('SELECT COALESCE(MAX(sort_order), -1) FROM package_books WHERE package_code=?');
    $stmt->execute([$code]);
    $next = (int)$stmt->fetchColumn() + 1;
    try {
        $pdo->prepare('INSERT INTO package_books (package_code, book_title, sort_order) VALUES (?,?,?)')
            ->execute([$code, $title, $next]);
    } catch (Throwable $e) {
        return ['ok' => false, 'error' => 'این کتاب در این سطح هست.'];
    }
    ghazal_live_packages($pdo, true);
    ghazal_bust_inja_cache();
    return ['ok' => true];
}

function ghazal_remove_package_book(PDO $pdo, string $code, string $title): array
{
    ensure_package_books_table($pdo);
    $code = trim($code);
    $title = trim($title);
    if ($code === '' || $title === '') {
        return ['ok' => false, 'error' => 'حذف انجام نشد.'];
    }
    $stmt = $pdo->prepare('DELETE FROM package_books WHERE package_code=? AND book_title=?');
    $stmt->execute([$code, $title]);
    if ($stmt->rowCount() < 1) {
        return ['ok' => false, 'error' => 'این کتاب در این سطح نبود.'];
    }
    ghazal_live_packages($pdo, true);
    ghazal_bust_inja_cache();
    return ['ok' => true];
}

function ghazal_bust_inja_cache(): void
{
    require_once __DIR__ . '/inja-prices.php';
    try {
        inja_rebuild_catalog_cache();
    } catch (Throwable $e) {
        try {
            set_setting('inja_prices_cache', '');
            set_setting('inja_prices_cached_at', '0');
        } catch (Throwable $e2) {
            // ignore
        }
    }
}

function paginate_offset(int $total, int $page, int $per = 10): array
{
    $pages = max(1, (int)ceil($total / max(1, $per)));
    $page = max(1, min($page, $pages));
    return [
        'page' => $page,
        'pages' => $pages,
        'per' => $per,
        'offset' => ($page - 1) * $per,
        'total' => $total,
    ];
}

function render_pagination(string $base, int $page, int $pages): void
{
    if ($pages <= 1) {
        return;
    }
    $join = str_contains($base, '?') ? '&' : '?';
    echo '<nav class="pager" aria-label="صفحه‌ها">';
    if ($page > 1) {
        echo '<a class="btn sm" href="' . e($base . $join . 'p=' . ($page - 1)) . '">قبلی</a>';
    }
    $start = max(1, $page - 3);
    $end = min($pages, $page + 3);
    if ($start > 1) {
        echo '<a class="btn sm" href="' . e($base . $join . 'p=1') . '">1</a>';
        if ($start > 2) {
            echo '<span class="pager-gap">…</span>';
        }
    }
    for ($i = $start; $i <= $end; $i++) {
        $cls = $i === $page ? 'btn sm primary' : 'btn sm';
        echo '<a class="' . $cls . '" href="' . e($base . $join . 'p=' . $i) . '">' . $i . '</a>';
    }
    if ($end < $pages) {
        if ($end < $pages - 1) {
            echo '<span class="pager-gap">…</span>';
        }
        echo '<a class="btn sm" href="' . e($base . $join . 'p=' . $pages) . '">' . $pages . '</a>';
    }
    if ($page < $pages) {
        echo '<a class="btn sm" href="' . e($base . $join . 'p=' . ($page + 1)) . '">بعدی</a>';
    }
    echo '</nav>';
}
