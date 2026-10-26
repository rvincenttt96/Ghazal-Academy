<?php
require __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/inja-prices.php';
require_once __DIR__ . '/includes/sell-prices.php';
require_once __DIR__ . '/includes/packages.php';
require_auth();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'save_postage') {
        $fee = (int)preg_replace('/\D+/', '', normalize_digits((string)($_POST['postage_fee'] ?? '0')));
        set_setting('postage_fee', (string)max(0, $fee));
        set_setting('payment_card', trim((string)($_POST['payment_card'] ?? '')));
        set_setting('payment_bank', trim((string)($_POST['payment_bank'] ?? '')));
        set_setting('payment_holder', trim((string)($_POST['payment_holder'] ?? '')));
        set_setting('payment_note', trim((string)($_POST['payment_note'] ?? '')));
        flash('success', 'هزینه پست و اطلاعات واریز ذخیره شد.');
        redirect('prices.php');
    }
    if ($action === 'refresh') {
        inja_get_catalog_prices(true);
        flash('success', 'قیمت‌ها تازه شد.');
        redirect('prices.php');
    }
    if ($action === 'save_prices') {
        $result = ghazal_save_overrides($pdo, ghazal_collapse_sell_post($_POST['sell'] ?? []));
        if ($result['invalid'] > 0) {
            flash('danger', 'بعضی عددها ذخیره نشد. فقط رقم وارد کنید.');
        } else {
            $bits = [];
            if ($result['saved'] > 0) {
                $bits[] = $result['saved'] . ' قیمت دستی ذخیره شد';
            }
            if ($result['cleared'] > 0) {
                $bits[] = $result['cleared'] . ' عنوان به ضریب برگشت';
            }
            flash('success', $bits ? implode('؛ ', $bits) . '.' : 'تغییری نبود.');
        }
        redirect('prices.php');
    }
    if ($action === 'add_book') {
        $code = trim((string)($_POST['package'] ?? ''));
        $hit = ghazal_add_package_book($pdo, $code, (string)($_POST['title'] ?? ''));
        flash($hit['ok'] ? 'success' : 'danger', $hit['ok'] ? 'کتاب به سطح اضافه شد.' : (string)$hit['error']);
        redirect('prices.php' . ($code !== '' ? ('#pkg-' . rawurlencode($code)) : ''));
    }
    if ($action === 'remove_book') {
        $code = trim((string)($_POST['package'] ?? ''));
        $hit = ghazal_remove_package_book($pdo, $code, (string)($_POST['title'] ?? ''));
        flash($hit['ok'] ? 'success' : 'danger', $hit['ok'] ? 'کتاب از این سطح برداشته شد.' : (string)$hit['error']);
        redirect('prices.php' . ($code !== '' ? ('#pkg-' . rawurlencode($code)) : ''));
    }
}

$catalog = inja_attach_sell_prices(inja_get_catalog_prices(false));

render_header('قیمت‌ها', 'prices');
?>
<p class="page-lead">عدد فروش همان است که دانش‌آموز می‌بیند. هزینه پست برای ارسال پستی جداگانه روی فرم دانش‌آموز نشان داده می‌شود.</p>

<?php $pay = payment_info(); ?>
<form method="post" class="card glass postage-card">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_postage">
  <div class="card-head">
    <div>
      <h2>پست و واریز</h2>
      <p>این عدد وقتی دانش‌آموز «ارسال با پست» را بزند به جمع سفارش اضافه می‌شود. شماره کارت روی صفحه کتاب‌فروشی پنل بچه‌ها دیده می‌شود.</p>
    </div>
  </div>
  <div class="postage-grid">
    <div class="field">
      <label for="postageFee">هزینه پست (تومان)</label>
      <input class="input" id="postageFee" name="postage_fee" dir="ltr" inputmode="numeric" value="<?= e((string)postage_fee_amount()) ?>">
    </div>
    <div class="field">
      <label for="paymentCard">شماره کارت</label>
      <input class="input" id="paymentCard" name="payment_card" dir="ltr" value="<?= e($pay['card']) ?>" placeholder="6037…">
    </div>
    <div class="field">
      <label for="paymentBank">بانک</label>
      <input class="input" id="paymentBank" name="payment_bank" value="<?= e($pay['bank']) ?>">
    </div>
    <div class="field">
      <label for="paymentHolder">به نام</label>
      <input class="input" id="paymentHolder" name="payment_holder" value="<?= e($pay['holder']) ?>">
    </div>
  </div>
  <div class="field" style="margin-top:12px">
    <label for="paymentNote">توضیح واریز برای دانش‌آموز</label>
    <textarea class="input" id="paymentNote" name="payment_note" rows="2" placeholder="مثلاً مبلغ را به کارت آموزشگاه واریز کنید و رسید را بارگذاری کنید."><?= e($pay['note']) ?></textarea>
  </div>
  <div class="actions" style="margin-top:14px">
    <button class="btn primary" type="submit">ذخیره پست و واریز</button>
  </div>
</form>

<div class="grid stats">
  <div class="stat glass">
    <div class="label">عنوان با قیمت فروش</div>
    <div class="value is-ltr"><?= (int)($catalog['sell_found'] ?? 0) ?></div>
    <div class="hint">از پکیج سطح‌های غزال</div>
  </div>
  <div class="stat glass">
    <div class="label">قیمت دستی</div>
    <div class="value is-ltr"><?= (int)($catalog['manual_count'] ?? 0) ?></div>
    <div class="hint">ضریب برای بقیه برقرار است</div>
  </div>
  <div class="stat glass">
    <div class="label">وضعیت قیمت</div>
    <div class="value"><?= !empty($catalog['from_cache']) ? 'کش' : 'زنده' ?></div>
    <div class="hint"><?= !empty($catalog['fetched_at']) ? e($catalog['fetched_at']) : '—' ?></div>
  </div>
  <div class="stat glass">
    <div class="label">اتصال</div>
    <div class="value"><?= ($catalog['live_ok'] ?? true) ? 'وصل' : 'قطع' ?></div>
    <div class="hint">اگر قطع باشد قیمت ذخیره‌شده می‌ماند</div>
  </div>
</div>

<?php if (!empty($catalog['error'])): ?>
  <div class="alert danger glass"><?= e($catalog['error']) ?></div>
<?php elseif (!empty($catalog['from_cache']) && empty($catalog['live_ok'])): ?>
  <div class="alert info glass">الان اینجا بوک جواب نداد؛ قیمت ذخیره‌شده نشان داده می‌شود.</div>
<?php endif; ?>

<form id="save-prices" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="save_prices">
</form>

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
    <div class="actions">
      <button class="btn primary" type="submit" form="save-prices">ذخیره قیمت‌های دستی</button>
    </div>
  </div>
</div>

<?php foreach (($catalog['packages'] ?? []) as $package): ?>
<div class="card glass price-package" id="pkg-<?= e($package['code']) ?>" data-package="<?= e($package['code']) ?>">
  <div class="card-head">
    <div>
      <h2 dir="ltr"><?= e($package['code']) ?></h2>
      <p>فروش غزال <?= (int)$package['sell_count'] ?> از <?= (int)$package['book_count'] ?> عنوان</p>
    </div>
    <div class="price-head-sum">
      <span class="muted">جمع فروش</span>
      <div class="price-sum"><?= e(inja_format_toman($package['sell_sum'] ?? null)) ?></div>
    </div>
  </div>
  <div class="table-wrap">
    <table class="data-table prices-table">
      <colgroup>
        <col class="col-book">
        <col class="col-inja">
        <col class="col-sell">
        <col class="col-act">
      </colgroup>
      <thead>
        <tr>
          <th>کتاب</th>
          <th>قیمت اینجا بوک</th>
          <th>قیمت فروش غزال</th>
          <th>حذف</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($package['rows'] as $row): ?>
        <?php
          $title = (string)($row['title'] ?? '');
          $isManual = !empty($row['is_manual']);
          $computed = $row['computed_price'] ?? null;
          $inputVal = $isManual ? (string)(int)$row['override_price'] : '';
        ?>
        <tr class="price-book" data-book="<?= e(mb_strtolower($title . ' ' . (($row['product']['name'] ?? '')), 'UTF-8')) ?>">
          <td class="cell-book"><strong dir="ltr"><?= e($title) ?></strong></td>
          <td class="cell-amount <?= empty($row['inja_price']) ? 'is-missing' : 'is-inja' ?>"><?= e(inja_format_toman($row['inja_price'] ?? null)) ?></td>
          <td class="cell-sell">
            <label class="price-box <?= $isManual ? 'is-manual' : 'is-formula' ?>">
            <input
              class="input price-input <?= $isManual ? 'is-manual' : 'is-formula' ?>"
              form="save-prices"
              data-title="<?= e($title) ?>"
              name="sell[<?= e($title) ?>][]"
              value="<?= e($inputVal) ?>"
              inputmode="numeric"
              autocomplete="off"
              title="<?= $computed ? e('ضریب: ' . number_format((int)$computed, 0, '.', ',')) : 'قیمت دستی' ?>"
              placeholder="<?= $computed ? e(number_format((int)$computed, 0, '.', ',')) : 'دستی' ?>"
            >
            </label>
          </td>
          <td class="cell-act">
            <form method="post">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="remove_book">
              <input type="hidden" name="package" value="<?= e($package['code']) ?>">
              <input type="hidden" name="title" value="<?= e($title) ?>">
              <button class="btn sm danger" type="submit" data-confirm="این کتاب از سطح <?= e($package['code']) ?> حذف شود؟">حذف</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <form class="pkg-add" method="post">
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="add_book">
    <input type="hidden" name="package" value="<?= e($package['code']) ?>">
    <label class="sr-only" for="add-<?= e($package['code']) ?>">کتاب جدید</label>
    <input class="input" id="add-<?= e($package['code']) ?>" name="title" dir="ltr" placeholder="نام انگلیسی کتاب، مثلاً Lost" required>
    <button class="btn primary" type="submit">افزودن به <?= e($package['code']) ?></button>
  </form>
</div>
<?php endforeach; ?>

<div class="actions" style="margin-bottom:18px">
  <button class="btn primary" type="submit" form="save-prices">ذخیره قیمت‌های دستی</button>
</div>

<form method="post" class="actions" style="margin-bottom:24px">
  <?= csrf_field() ?>
  <input type="hidden" name="action" value="refresh">
  <button class="btn" type="submit">تازه‌سازی قیمت اینجا بوک</button>
</form>

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
  var lastEdit = {};
  document.addEventListener('input', function (e) {
    var el = e.target;
    if (!el || !el.classList || !el.classList.contains('price-input')) return;
    var title = el.getAttribute('data-title');
    if (!title) return;
    lastEdit[title] = el.value;
    var filled = el.value.trim() !== '';
    el.classList.toggle('is-manual', filled);
    el.classList.toggle('is-formula', !filled);
    var box = el.closest('.price-box');
    if (box) {
      box.classList.toggle('is-manual', filled);
      box.classList.toggle('is-formula', !filled);
    }
    document.querySelectorAll('.price-input').forEach(function (other) {
      if (other !== el && other.getAttribute('data-title') === title) {
        other.value = el.value;
        other.classList.toggle('is-manual', filled);
        other.classList.toggle('is-formula', !filled);
        var otherBox = other.closest('.price-box');
        if (otherBox) {
          otherBox.classList.toggle('is-manual', filled);
          otherBox.classList.toggle('is-formula', !filled);
        }
      }
    });
  });
  var save = document.getElementById('save-prices');
  if (save) {
    save.addEventListener('submit', function () {
      document.querySelectorAll('.price-input').forEach(function (el) {
        var title = el.getAttribute('data-title');
        if (title && Object.prototype.hasOwnProperty.call(lastEdit, title)) {
          el.value = lastEdit[title];
        }
      });
    });
  }
})();
</script>
<?php render_footer(); ?>
