<?php
require __DIR__ . '/includes/layout.php';
require_auth();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $statusKeep = (string)($_POST['status'] ?? ($_GET['status'] ?? ''));
    $qKeep = trim((string)($_POST['q'] ?? ($_GET['q'] ?? '')));
    $qs = [];
    if (in_array($statusKeep, ['awaiting_receipt', 'pending', 'approved', 'ordered', 'cancelled'], true)) {
        $qs['status'] = $statusKeep;
    }
    if ($qKeep !== '') {
        $qs['q'] = $qKeep;
    }
    $back = 'orders.php' . ($qs ? ('?' . http_build_query($qs)) : '');

    if ($action === 'approve') {
        $stmt = $pdo->prepare("UPDATE orders SET status='approved', approved_at=NOW(), updated_at=NOW() WHERE id=? AND status='pending'");
        $stmt->execute([$id]);
        flash($stmt->rowCount() ? 'success' : 'danger', $stmt->rowCount() ? 'سفارش آماده خرید شد.' : 'این سفارش را نمی‌شود آماده خرید کرد.');
    } elseif ($action === 'cancel') {
        $stmt = $pdo->prepare("UPDATE orders SET status='cancelled', cancelled_at=NOW(), updated_at=NOW() WHERE id=? AND status IN ('awaiting_receipt','pending','approved')");
        $stmt->execute([$id]);
        flash($stmt->rowCount() ? 'success' : 'danger', $stmt->rowCount() ? 'سفارش لغو شد.' : 'این سفارش قابل لغو نیست.');
    }
    redirect($back);
}

$status = $_GET['status'] ?? 'pending';
$allowed = ['awaiting_receipt', 'pending', 'approved', 'ordered', 'cancelled', 'all'];
if (!in_array($status, $allowed, true)) {
    $status = 'pending';
}
$q = trim((string)($_GET['q'] ?? ''));

$where = [];
$params = [];
if ($status !== 'all') {
    $where[] = 'o.status = ?';
    $params[] = $status;
}
if ($q !== '') {
    $where[] = '(o.first_name LIKE ? OR o.last_name LIKE ? OR o.mobile LIKE ? OR o.tracking_code LIKE ? OR o.student_code LIKE ? OR o.term_title_snapshot LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like, $like, $like, $like);
}
$sql = 'SELECT o.* FROM orders o';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY o.id DESC LIMIT 300';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
$itemsByOrder = order_items_grouped($pdo, array_column($orders, 'id'));

$tabCounts = [];
foreach (['awaiting_receipt', 'pending', 'approved', 'ordered', 'cancelled'] as $st) {
    $tabCounts[$st] = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status=" . $pdo->quote($st))->fetchColumn();
}

function orders_tab_url($status, $q): string
{
    $qs = ['status' => $status];
    if ($q !== '') {
        $qs['q'] = $q;
    }
    return 'orders.php?' . http_build_query($qs);
}

render_header('سفارش‌ها', 'orders');
?>
<p class="page-lead">دانش‌آموز فرم را می‌زند و سفارش «در انتظار رسید» می‌ماند. بعد از بارگذاری رسید واریز، سفارش ثبت می‌شود و اینجا برای بررسی می‌آید.</p>

<form class="search-bar glass" method="get">
  <input type="hidden" name="status" value="<?= e($status) ?>">
  <label class="sr-only" for="orderSearch">جستجو</label>
  <input class="input" id="orderSearch" name="q" value="<?= e($q) ?>" placeholder="نام، شماره پرونده، موبایل یا کد رهگیری">
  <button class="btn primary" type="submit">جستجو</button>
  <?php if ($q !== ''): ?><a class="btn sm" href="orders.php?status=<?= e($status) ?>">پاک کردن</a><?php endif; ?>
</form>

<div class="filters">
  <a class="btn sm <?= $status === 'awaiting_receipt' ? 'primary' : '' ?>" href="<?= e(orders_tab_url('awaiting_receipt', $q)) ?>">در انتظار رسید (<?= (int)$tabCounts['awaiting_receipt'] ?>)</a>
  <a class="btn sm <?= $status === 'pending' ? 'primary' : '' ?>" href="<?= e(orders_tab_url('pending', $q)) ?>">جدید (<?= (int)$tabCounts['pending'] ?>)</a>
  <a class="btn sm <?= $status === 'approved' ? 'primary' : '' ?>" href="<?= e(orders_tab_url('approved', $q)) ?>">آماده خرید (<?= (int)$tabCounts['approved'] ?>)</a>
  <a class="btn sm <?= $status === 'ordered' ? 'primary' : '' ?>" href="<?= e(orders_tab_url('ordered', $q)) ?>">خرید شد</a>
  <a class="btn sm <?= $status === 'cancelled' ? 'primary' : '' ?>" href="<?= e(orders_tab_url('cancelled', $q)) ?>">لغو شده</a>
  <a class="btn sm <?= $status === 'all' ? 'primary' : '' ?>" href="<?= e(orders_tab_url('all', $q)) ?>">همه</a>
</div>

<?php if (!$orders): ?>
  <div class="empty glass">در این فهرست سفارشی نیست.</div>
<?php else: ?>
  <div class="table-wrap">
    <table class="data-table orders-table">
      <colgroup>
        <col class="col-who">
        <col class="col-code">
        <col class="col-mobile">
        <col class="col-pkg">
        <col class="col-deliv">
        <col class="col-money">
        <col class="col-status">
        <col class="col-ops">
      </colgroup>
      <thead>
        <tr>
          <th>دانش‌آموز</th>
          <th>شماره پرونده</th>
          <th>موبایل</th>
          <th>سطح / کتاب</th>
          <th>تحویل</th>
          <th>مبلغ</th>
          <th>وضعیت</th>
          <th>عملیات</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($orders as $o):
          $dtype = normalize_order_delivery_type($o['delivery_type'] ?? 'pickup');
          $items = $itemsByOrder[(int)$o['id']] ?? [];
          $postage = order_postage_fee($o);
          $grand = order_grand_total($o);
          $hasReceipt = order_has_receipt($o);
          ?>
        <tr>
          <td>
            <div class="cell-stack">
              <strong><?= e(trim($o['first_name'] . ' ' . $o['last_name'])) ?></strong>
              <span class="muted"><?= e(source_label($o['source'] ?? '')) ?></span>
            </div>
          </td>
          <td class="cell-code"><?= e(student_file_number($o['student_code'] ?? '')) ?></td>
          <td class="cell-code"><?= e($o['mobile']) ?></td>
          <td><?= e(display_package_title($o['term_title_snapshot'] ?? '')) ?></td>
          <td>
            <?= e(order_delivery_label($dtype)) ?>
            <?php if (order_is_delivered($o)): ?>
              <div class="muted">تحویل شد<?= !empty($o['delivered_at']) ? ' · ' . e($o['delivered_at']) : '' ?></div>
            <?php endif; ?>
          </td>
          <td class="cell-amount"><?= e(format_toman_amount($grand)) ?></td>
          <td><span class="badge <?= e(status_class($o['status'])) ?>"><?= e(status_label($o['status'])) ?></span></td>
          <td>
            <div class="cell-actions">
              <button type="button" class="btn sm" data-toggle-order>جزئیات</button>
              <?php if ($o['status'] === 'pending'): ?>
                <form method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="approve">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <input type="hidden" name="status" value="<?= e($status) ?>">
                  <input type="hidden" name="q" value="<?= e($q) ?>">
                  <button class="btn sm success" type="submit">آماده خرید</button>
                </form>
              <?php endif; ?>
              <?php if (in_array($o['status'], ['awaiting_receipt', 'pending', 'approved'], true)): ?>
                <form method="post">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="cancel">
                  <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                  <input type="hidden" name="status" value="<?= e($status) ?>">
                  <input type="hidden" name="q" value="<?= e($q) ?>">
                  <button class="btn sm danger" type="submit" data-confirm="این سفارش لغو شود؟">لغو</button>
                </form>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <tr class="order-detail-row" hidden>
          <td colspan="8">
            <div class="order-detail">
              <div class="order-detail-grid">
                <div>
                  <h3>کتاب‌ها</h3>
                  <?php if (!$items): ?>
                    <p class="muted">عنوان جدا ثبت نشده است.</p>
                  <?php else: ?>
                    <ul class="book-lines">
                      <?php foreach ($items as $it): ?>
                        <li>
                          <span dir="ltr"><?= e($it['book_title_snapshot']) ?></span>
                          <span class="mono"><?= (int)$it['quantity'] > 1 ? (int)$it['quantity'] . '× ' : '' ?><?= e(format_toman_amount($it['unit_price'] ?? 0)) ?></span>
                        </li>
                      <?php endforeach; ?>
                    </ul>
                  <?php endif; ?>
                </div>
                <div>
                  <h3>تحویل و مبلغ</h3>
                  <p><?= e(order_delivery_label($dtype)) ?></p>
                  <?php if (order_is_delivered($o)): ?>
                    <p>تحویل کتاب ثبت شده است<?= !empty($o['delivered_at']) ? ' · ' . e($o['delivered_at']) : '' ?></p>
                  <?php endif; ?>
                  <?php if ($dtype === 'post'): ?>
                    <p class="mono"><?= e($o['postal_code'] ?? '') ?></p>
                    <p><?= e($o['address'] ?? '') ?></p>
                    <p>هزینه پست: <?= e(format_postage_label($postage, $dtype)) ?></p>
                  <?php else: ?>
                    <p class="muted">دانش‌آموز کتاب را از آموزشگاه می‌گیرد.</p>
                  <?php endif; ?>
                  <p>کتاب‌ها: <?= e(format_toman_amount($o['sell_total'] ?? 0)) ?></p>
                  <p>جمع قابل واریز: <?= e(format_toman_amount($grand)) ?></p>
                  <p class="muted">کد رهگیری: <span class="mono"><?= e($o['tracking_code']) ?></span></p>
                  <p class="muted"><?= e($o['created_at']) ?></p>
                </div>
                <div>
                  <h3>رسید واریز</h3>
                  <?php if ($hasReceipt): ?>
                    <a class="receipt-link" href="receipt.php?id=<?= (int)$o['id'] ?>" target="_blank" rel="noopener">
                      <?php
                        $ext = strtolower(pathinfo((string)$o['receipt_path'], PATHINFO_EXTENSION));
                        if ($ext === 'pdf'): ?>
                        <span>دانلود / مشاهده PDF رسید</span>
                      <?php else: ?>
                        <img class="receipt-thumb" src="receipt.php?id=<?= (int)$o['id'] ?>" alt="رسید واریز">
                      <?php endif; ?>
                    </a>
                    <?php if (!empty($o['receipt_uploaded_at'])): ?>
                      <p class="muted"><?= e($o['receipt_uploaded_at']) ?></p>
                    <?php endif; ?>
                  <?php else: ?>
                    <p class="muted">هنوز رسیدی بارگذاری نشده است.</p>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>
<?php render_footer(); ?>
