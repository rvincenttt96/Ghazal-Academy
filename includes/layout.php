<?php
require_once __DIR__ . '/bootstrap.php';

function render_header(string $title, string $active = ''): void
{
    global $appConfig;
    $flashes = pull_flashes();
    ?>
<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title><?= e(document_title($title)) ?></title>
    <link rel="stylesheet" href="assets/css/app.css?v=0.8.0">
    <?php render_tab_head(); ?>
</head>
<body class="lg-app">
<div class="lg-orbs" aria-hidden="true">
    <span class="lg-orb lg-orb-a"></span>
    <span class="lg-orb lg-orb-b"></span>
    <span class="lg-orb lg-orb-c"></span>
    <span class="lg-orb lg-orb-d"></span>
    <span class="lg-orb lg-orb-e"></span>
    <div class="lg-grid-floor"></div>
</div>
<div class="shell">
    <aside class="sidebar glass">
        <div class="brand">
            <img class="brand-logo" src="assets/img/ghazal-logo.png" alt="غزال" width="46" height="46">
            <div>
                <strong>کتاب غزال</strong>
                <small>سفارش دانش‌آموز</small>
            </div>
        </div>

        <nav class="nav">
            <?php
            $items = [
                'dashboard' => ['dashboard.php', 'خانه', '۱'],
                'orders' => ['orders.php', 'سفارش‌ها', '۲'],
                'report' => ['purchase-report.php', 'خرید کتاب', '۳'],
                'prices' => ['prices.php', 'قیمت‌ها', '۴'],
                'inja' => ['inja-book.php', 'قیمت اینجا بوک', '۵'],
            ];
            foreach ($items as $key => [$url, $label, $icon]): ?>
                <a class="nav-link <?= $active === $key ? 'active' : '' ?>" href="<?= e($url) ?>">
                    <span class="nav-icon"><?= e($icon) ?></span>
                    <span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-foot">
            <a class="nav-link" href="logout.php"><span class="nav-icon">↪</span><span>خروج</span></a>
        </div>
    </aside>

    <main class="main">
        <header class="topbar glass">
            <div>
                <div class="eyebrow">پنل کتابفروشی آموزشگاه</div>
                <h1><?= e($title) ?></h1>
            </div>
        </header>

        <?php foreach ($flashes as $flash): ?>
            <div class="alert <?= e($flash['type']) ?> glass"><?= e($flash['message']) ?></div>
        <?php endforeach; ?>

        <section class="content">
    <?php
}

function render_footer(): void
{
    ?>
        </section>
        <footer class="footer">دانش‌آموز فرم را می‌زند، رسید واریز را می‌گذارد، بعد اینجا سفارش بررسی و خرید می‌شود.</footer>
    </main>
</div>
<script src="assets/js/app.js?v=0.7.0"></script>
</body>
</html>
    <?php
}
