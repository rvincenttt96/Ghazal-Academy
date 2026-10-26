<?php
require __DIR__ . '/includes/layout.php';
require_auth();

$pdo = db();
$counts = [];
foreach (['awaiting_receipt', 'pending', 'approved', 'ordered', 'cancelled'] as $status) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE status = ?');
    $stmt->execute([$status]);
    $counts[$status] = (int)$stmt->fetchColumn();
}
$postPending = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','approved') AND delivery_type='post'")->fetchColumn();
$pickupPending = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','approved') AND delivery_type='pickup'")->fetchColumn();

require_once __DIR__ . '/includes/packages.php';
$totalRecent = (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn();
$pg = paginate_offset($totalRecent, (int)($_GET['p'] ?? 1), 10);
$stmt = $pdo->prepare('SELECT id, tracking_code, first_name, last_name, student_code, term_title_snapshot, status, source, delivery_type, sell_total, postage_fee, created_at FROM orders ORDER BY id DESC LIMIT ' . (int)$pg['per'] . ' OFFSET ' . (int)$pg['offset']);
$stmt->execute();
$recent = $stmt->fetchAll();

render_header('خانه', 'dashboard');
?>
<p class="page-lead">دانش‌آموز فرم را پر می‌کند، واریز می‌کند و رسید را می‌گذارد. بعد از رسید، سفارش اینجا برای بررسی می‌آید.</p>

<div class="flow-steps">
  <div class="flow-step glass <?= $counts['awaiting_receipt'] ? 'is-hot' : '' ?>">
    <b>۰</b>
    <div>
      <strong>در انتظار رسید</strong>
      <span><?= $counts['awaiting_receipt'] ?> سفارش فرم خورده و هنوز رسید ندارد</span>
      <?php if ($counts['awaiting_receipt']): ?><a href="orders.php?status=awaiting_receipt">دیدن فهرست</a><?php endif; ?>
    </div>
  </div>
  <div class="flow-step glass <?= $counts['pending'] ? 'is-hot' : '' ?>">
    <b>۱</b>
    <div>
      <strong>سفارش جدید</strong>
      <span><?= $counts['pending'] ?> تا رسید گذاشته و هنوز دیده نشده</span>
      <?php if ($counts['pending']): ?><a href="orders.php?status=pending">رفتن به سفارش‌ها</a><?php endif; ?>
    </div>
  </div>
  <div class="flow-step glass <?= $counts['approved'] ? 'is-hot' : '' ?>">
    <b>۲</b>
    <div>
      <strong>آماده خرید</strong>
      <span><?= $counts['approved'] ?> سفارش را باید از اینجا بوک بخرید</span>
      <?php if ($counts['approved']): ?><a href="purchase-report.php">لیست خرید</a><?php endif; ?>
    </div>
  </div>
  <div class="flow-step glass">
    <b>۳</b>
    <div>
      <strong>خرید شد</strong>
      <span><?= $counts['ordered'] ?> سفارش قبلاً خرید شده است</span>
    </div>
  </div>
</div>

<div class="grid stats">
  <div class="stat glass"><div class="label">در انتظار رسید</div><div class="value"><?= $counts['awaiting_receipt'] ?></div><div class="hint">فرم خورده، رسید نیامده</div></div>
  <div class="stat glass"><div class="label">جدید</div><div class="value"><?= $counts['pending'] ?></div><div class="hint">رسید آمده، منتظر بررسی</div></div>
  <div class="stat glass"><div class="label">آماده خرید</div><div class="value"><?= $counts['approved'] ?></div><div class="hint">تأیید شده، هنوز نخریده‌اید</div></div>
  <div class="stat glass"><div class="label">ارسال پستی باز</div><div class="value"><?= $postPending ?></div><div class="hint">جدید یا آماده خرید</div></div>
  <div class="stat glass"><div class="label">تحویل حضوری باز</div><div class="value"><?= $pickupPending ?></div><div class="hint">جدید یا آماده خرید</div></div>
</div>

<div class="card glass">
  <div class="card-head">
    <div>
      <h2>آخرین سفارش‌ها</h2>
      <p>در هر صفحه ۱۰ سفارش. جزئیات کامل در سفارش‌هاست.</p>
    </div>
    <a class="btn sm" href="orders.php">همه سفارش‌ها</a>
  </div>
  <?php if (!$recent): ?>
    <div class="empty">هنوز سفارشی از پنل نیامده است.</div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table recent-table">
        <colgroup>
          <col class="col-who">
          <col class="col-pkg">
          <col class="col-deliv">
          <col class="col-money">
          <col class="col-status">
          <col class="col-date">
        </colgroup>
        <thead>
          <tr>
            <th>دانش‌آموز</th>
            <th>پکیج / کتاب</th>
            <th>تحویل</th>
            <th>مبلغ</th>
            <th>وضعیت</th>
            <th>تاریخ</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($recent as $row): ?>
          <tr>
            <td>
              <div class="cell-stack">
                <strong><?= e(trim($row['first_name'] . ' ' . $row['last_name'])) ?></strong>
                <span class="muted"><?= e(student_file_number($row['student_code'] ?? '')) ?></span>
              </div>
            </td>
            <td><?= e(display_package_title($row['term_title_snapshot'] ?? '')) ?></td>
            <td><?= e(order_delivery_label($row['delivery_type'] ?? 'pickup')) ?></td>
            <td class="cell-amount"><?= e(format_toman_amount(order_grand_total($row))) ?></td>
            <td><span class="badge <?= e(status_class($row['status'])) ?>"><?= e(status_label($row['status'])) ?></span></td>
            <td class="cell-date"><?= e($row['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php render_pagination('dashboard.php', (int)$pg['page'], (int)$pg['pages']); ?>
  <?php endif; ?>
</div>
<?php render_footer(); ?>
