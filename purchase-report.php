<?php
require __DIR__ . '/includes/layout.php';
require_auth();
$pdo = db();
$tab = ($_GET['tab'] ?? '') === 'history' ? 'history' : 'buy';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create_batch') {
    verify_csrf();
    $pdo->beginTransaction();
    try {
        $orders = $pdo->query("SELECT id FROM orders WHERE status='approved' ORDER BY id FOR UPDATE")->fetchAll(PDO::FETCH_COLUMN);
        if (!$orders) {
            throw new RuntimeException('سفارش آماده‌ای برای خرید نیست.');
        }
        $ph = implode(',', array_fill(0, count($orders), '?'));
        $stmt = $pdo->prepare("SELECT oi.book_id, oi.book_title_snapshot, SUM(oi.quantity) AS qty FROM order_items oi WHERE oi.order_id IN ($ph) GROUP BY oi.book_id, oi.book_title_snapshot ORDER BY oi.book_title_snapshot");
        $stmt->execute($orders);
        $items = $stmt->fetchAll();
        $booksCount = array_sum(array_map(fn($x) => (int)$x['qty'], $items));
        $code = make_batch_code();
        $pdo->prepare('INSERT INTO purchase_batches (batch_code, orders_count, books_count, created_at) VALUES (?,?,?,NOW())')->execute([$code, count($orders), $booksCount]);
        $batchId = (int)$pdo->lastInsertId();
        $insOrder = $pdo->prepare('INSERT INTO purchase_batch_orders (batch_id, order_id) VALUES (?,?)');
        foreach ($orders as $oid) {
            $insOrder->execute([$batchId, $oid]);
        }
        $insItem = $pdo->prepare('INSERT INTO purchase_batch_items (batch_id, book_id, book_title_snapshot, quantity) VALUES (?,?,?,?)');
        foreach ($items as $it) {
            $insItem->execute([$batchId, $it['book_id'] ?: null, $it['book_title_snapshot'], (int)$it['qty']]);
        }
        $upd = $pdo->prepare("UPDATE orders SET status='ordered', ordered_at=NOW(), updated_at=NOW() WHERE id IN ($ph) AND status='approved'");
        $upd->execute($orders);
        $pdo->commit();
        flash('success', 'خرید ثبت شد. این سفارش‌ها از لیست جاری خارج شدند.');
        redirect('purchase-report.php?tab=history');
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        flash('danger', $e->getMessage());
        redirect('purchase-report.php');
    }
}

$items = $pdo->query("SELECT oi.book_title_snapshot, SUM(oi.quantity) AS qty FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status='approved' GROUP BY oi.book_title_snapshot ORDER BY oi.book_title_snapshot")->fetchAll();
$ordersCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='approved'")->fetchColumn();
$booksCount = array_sum(array_map(fn($x) => (int)$x['qty'], $items));
$postCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='approved' AND delivery_type='post'")->fetchColumn();
$pickupCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE status='approved' AND IFNULL(delivery_type,'pickup') <> 'post'")->fetchColumn();

$batches = $pdo->query('SELECT * FROM purchase_batches ORDER BY id DESC LIMIT 80')->fetchAll();
$detailId = (int)($_GET['id'] ?? 0);
$detail = null;
$detailItems = [];
if ($detailId) {
    $stmt = $pdo->prepare('SELECT * FROM purchase_batches WHERE id=?');
    $stmt->execute([$detailId]);
    $detail = $stmt->fetch();
    if ($detail) {
        $tab = 'history';
        $stmt = $pdo->prepare('SELECT * FROM purchase_batch_items WHERE batch_id=? ORDER BY book_title_snapshot');
        $stmt->execute([$detailId]);
        $detailItems = $stmt->fetchAll();
    }
}

render_header('خرید کتاب', 'report');
?>
<p class="page-lead">اینجا جمع کتاب‌هایی است که دفتر تأیید کرده و باید از اینجا بوک بخرید. وقتی خرید انجام شد، همان را علامت بزنید.</p>

<div class="filters">
  <a class="btn sm <?= $tab === 'buy' ? 'primary' : '' ?>" href="purchase-report.php">الان باید بخریم</a>
  <a class="btn sm <?= $tab === 'history' ? 'primary' : '' ?>" href="purchase-report.php?tab=history">خریدهای قبلی</a>
</div>

<?php if ($tab === 'buy'): ?>
<div class="card glass">
  <div class="card-head">
    <div>
      <h2>لیست خرید از اینجا بوک</h2>
      <p>فقط سفارش‌های «آماده خرید». دانش‌آموز این کتاب‌ها را از پنل خواسته است.</p>
    </div>
  </div>
  <?php if (!$items): ?>
    <div class="empty">الان چیزی برای خرید نیست. اول سفارش‌های جدید را آماده خرید کنید.</div>
  <?php else: ?>
    <div class="total-row">
      <div class="total-pill"><span>سفارش دانش‌آموز</span><strong><?= $ordersCount ?></strong></div>
      <div class="total-pill"><span>نسخه کتاب</span><strong><?= $booksCount ?></strong></div>
      <div class="total-pill"><span>حضوری</span><strong><?= $pickupCount ?></strong></div>
      <div class="total-pill"><span>پست</span><strong><?= $postCount ?></strong></div>
    </div>
    <div class="table-wrap" style="margin-top:16px">
      <table class="data-table">
        <colgroup>
          <col style="width:78%">
          <col style="width:22%">
        </colgroup>
        <thead><tr><th>کتاب</th><th>تعداد</th></tr></thead>
        <tbody>
        <?php foreach ($items as $it): ?>
          <tr>
            <td dir="ltr"><?= e($it['book_title_snapshot']) ?></td>
            <td><strong><?= (int)$it['qty'] ?></strong></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <form method="post" style="margin-top:18px">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="create_batch">
      <button class="btn success" type="submit" data-confirm="فقط وقتی بزنید که این کتاب‌ها را از اینجا بوک خریده باشید. بعد از آن از این لیست خارج می‌شوند.">خرید این لیست انجام شد</button>
    </form>
  <?php endif; ?>
</div>
<?php else: ?>

<?php if ($detail): ?>
<div class="card glass">
  <div class="card-head">
    <div>
      <h2>جزئیات خرید <?= e($detail['batch_code']) ?></h2>
      <p><?= e($detail['created_at']) ?></p>
    </div>
    <a class="btn sm" href="purchase-report.php?tab=history">بستن</a>
  </div>
  <div class="kpi-mini">
    <div><strong><?= (int)$detail['orders_count'] ?></strong><span>سفارش دانش‌آموز</span></div>
    <div><strong><?= (int)$detail['books_count'] ?></strong><span>نسخه کتاب</span></div>
  </div>
  <div class="table-wrap" style="margin-top:16px">
    <table class="data-table">
      <colgroup>
        <col style="width:78%">
        <col style="width:22%">
      </colgroup>
      <thead><tr><th>کتاب</th><th>تعداد</th></tr></thead>
      <tbody>
      <?php foreach ($detailItems as $it): ?>
        <tr><td dir="ltr"><?= e($it['book_title_snapshot']) ?></td><td><?= (int)$it['quantity'] ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>

<div class="card glass">
  <div class="card-head">
    <div>
      <h2>خریدهای قبلی</h2>
      <p>هر بار که «خرید انجام شد» زده شده، یک ردیف اینجا مانده است.</p>
    </div>
  </div>
  <?php if (!$batches): ?>
    <div class="empty">هنوز خریدی ثبت نشده است.</div>
  <?php else: ?>
    <div class="table-wrap">
      <table class="data-table">
        <colgroup>
          <col style="width:28%">
          <col style="width:14%">
          <col style="width:14%">
          <col style="width:28%">
          <col style="width:16%">
        </colgroup>
        <thead><tr><th>کد</th><th>سفارش</th><th>کتاب</th><th>تاریخ</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($batches as $b): ?>
          <tr>
            <td class="cell-code"><?= e($b['batch_code']) ?></td>
            <td><?= (int)$b['orders_count'] ?></td>
            <td><?= (int)$b['books_count'] ?></td>
            <td><?= e($b['created_at']) ?></td>
            <td><a class="btn sm" href="purchase-report.php?tab=history&id=<?= (int)$b['id'] ?>">جزئیات</a></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php render_footer(); ?>
