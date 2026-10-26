<?php
require __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/inja-prices.php';
require_auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    if (($_POST['action'] ?? '') === 'refresh') {
        inja_get_catalog_prices(true);
        flash('success', 'قیمت اینجا بوک تازه شد.');
        redirect('inja-book.php');
    }
}

$catalog = inja_get_catalog_prices(false);

render_header('قیمت اینجا بوک', 'inja');
?>
<p class="page-lead">این قیمت خرید از اینجا بوک است، نه قیمت فروش غزال. قیمت فروش را از منوی «قیمت‌ها» تنظیم کنید.</p>

<div class="grid stats">
  <div class="stat glass">
    <div class="label">عنوان پیدا شده</div>
    <div class="value is-ltr"><?= (int)($catalog['found'] ?? 0) ?></div>
    <div class="hint">روی اینجا بوک</div>
  </div>
  <div class="stat glass">
    <div class="label">وضعیت</div>
    <div class="value"><?= !empty($catalog['from_cache']) ? 'کش' : 'زنده' ?></div>
    <div class="hint"><?= !empty($catalog['fetched_at']) ? e($catalog['fetched_at']) : '—' ?></div>
  </div>
  <div class="stat glass">
    <div class="label">اتصال</div>
    <div class="value"><?= ($catalog['live_ok'] ?? true) ? 'وصل' : 'قطع' ?></div>
    <div class="hint">اگر قطع باشد لیست ذخیره‌شده می‌ماند</div>
  </div>
</div>

<?php if (!empty($catalog['error'])): ?>
  <div class="alert danger glass"><?= e($catalog['error']) ?></div>
<?php elseif (!empty($catalog['from_cache']) && empty($catalog['live_ok'])): ?>
  <div class="alert info glass">الان اینجا بوک جواب نداد؛ قیمت ذخیره‌شده نشان داده می‌شود.</div>
<?php endif; ?>

<div class="card glass">
  <div class="price-toolbar">
    <div class="field">
      <label for="pricePackageFilter">سطح</label>
      <select class="select" id="pricePackageFilter">
        <option value="">همه سطوح</option>
        <?php foreach (($catalog['packages'] ?? []) as $package): ?>
          <option value="<?= e($package['code']) ?>"><?= e($package['code']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="field">
      <label for="priceBookFilter">جستجوی کتاب</label>
      <input class="input" id="priceBookFilter" placeholder="مثلاً Let's Go">
    </div>
    <form method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="refresh">
      <button class="btn primary" type="submit">تازه‌سازی</button>
    </form>
  </div>
</div>

<?php foreach (($catalog['packages'] ?? []) as $package): ?>
<div class="card glass price-package" data-package="<?= e($package['code']) ?>">
  <div class="card-head">
    <div>
      <h2 dir="ltr"><?= e($package['code']) ?></h2>
      <p><?= (int)$package['priced_count'] ?> از <?= (int)$package['book_count'] ?> عنوان روی اینجا بوک قیمت دارد</p>
    </div>
    <div class="price-head-sum">
      <span class="muted">جمع خرید</span>
      <div class="price-sum"><?= e(inja_format_toman($package['sum'] ?? null)) ?></div>
    </div>
  </div>
  <div class="table-wrap">
    <table class="data-table inja-table">
      <colgroup>
        <col class="col-book">
        <col class="col-inja-name">
        <col class="col-inja">
        <col class="col-status">
      </colgroup>
      <thead>
        <tr>
          <th>کتاب غزال</th>
          <th>عنوان اینجا بوک</th>
          <th>قیمت</th>
          <th>وضعیت</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($package['rows'] as $row):
          $product = is_array($row['product'] ?? null) ? $row['product'] : null;
          $found = !empty($row['found']) && $product;
          ?>
        <tr class="price-book" data-book="<?= e(mb_strtolower(($row['title'] ?? '') . ' ' . ($product['name'] ?? ''), 'UTF-8')) ?>">
          <td class="cell-book"><strong dir="ltr"><?= e($row['title'] ?? '') ?></strong></td>
          <td class="cell-book" dir="ltr"><?= $found ? e($product['name']) : '—' ?></td>
          <td class="cell-amount"><?= $found ? e(inja_format_toman($product['price'] ?? null)) : '—' ?></td>
          <td>
            <?php if ($found): ?>
              <span class="badge <?= !empty($product['in_stock']) ? 'success' : 'warning' ?>"><?= !empty($product['in_stock']) ? 'موجود' : 'ناموجود' ?></span>
            <?php else: ?>
              <span class="badge muted"><?= e($row['reason'] ?? 'نیست') ?></span>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endforeach; ?>

<script>
(function () {
  var pack = document.getElementById('pricePackageFilter');
  var book = document.getElementById('priceBookFilter');
  function apply() {
    var code = pack ? pack.value : '';
    var q = book ? book.value.trim().toLowerCase() : '';
    document.querySelectorAll('.price-package').forEach(function (card) {
      var matchPack = !code || card.getAttribute('data-package') === code;
      var rows = card.querySelectorAll('.price-book');
      var any = false;
      rows.forEach(function (row) {
        var hay = (row.getAttribute('data-book') || '').toLowerCase();
        var matchBook = !q || hay.indexOf(q) !== -1;
        row.hidden = !matchBook;
        if (matchBook) any = true;
      });
      card.hidden = !(matchPack && (any || !q));
    });
  }
  if (pack) pack.addEventListener('change', apply);
  if (book) book.addEventListener('input', apply);
})();
</script>
<?php render_footer(); ?>
