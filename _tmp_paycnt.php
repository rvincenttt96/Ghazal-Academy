<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/accounting.php';
$acc = accounting_pdo();
$term = $acc->query("SELECT id, name FROM terms ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$st = $pdo->prepare("SELECT id, title FROM terms WHERE accounting_term_id = ? AND IFNULL(is_deleted,0)=0 ORDER BY id DESC LIMIT 1");
$st->execute([$term['id']]);
$pt = $st->fetch(PDO::FETCH_ASSOC);
$out = ['accTerm'=>$term,'panelTerm'=>$pt,'active'=>0,'archived'=>0,'deleted'=>0,'stats'=>0];
if ($pt) {
  $q = $pdo->prepare("SELECT IFNULL(is_deleted,0) d, IFNULL(is_archived,0) a FROM classes WHERE term_id = ?");
  $q->execute([$pt['id']]);
  foreach ($q as $row) {
    if ((int)$row['d']===1) $out['deleted']++;
    elseif ((int)$row['a']===1) $out['archived']++;
    else $out['active']++;
  }
  $q2 = $pdo->prepare("SELECT COUNT(*) FROM classes c JOIN users u ON u.id=c.teacher_id WHERE c.term_id=? AND IFNULL(c.is_deleted,0)=0");
  $q2->execute([$pt['id']]);
  $out['stats'] = (int)$q2->fetchColumn();
  $out['included'] = $out['active']+$out['archived'];
  $out['matches'] = $out['stats']===$out['included'];
}
echo json_encode($out, JSON_UNESCAPED_UNICODE);
