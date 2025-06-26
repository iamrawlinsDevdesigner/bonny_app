<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  set_flash('login', 'Access denied. Admins only.', 'error');
  header('Location: ../../index.php');
  exit;
}

$stmt = $pdo->query("SELECT * FROM businesses WHERE approved = 0 ORDER BY created_at DESC");
$pending = $stmt->fetchAll();
?>

<h2>Pending Business Approvals</h2>
<?php if (empty($pending)): ?>
  <p>No pending businesses for approval.</p>
<?php else: ?>
  <?php foreach ($pending as $biz): ?>
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
      <h3><?= htmlspecialchars($biz['name']) ?> (<?= htmlspecialchars($biz['category']) ?>)</h3>
      <p><?= htmlspecialchars($biz['description']) ?></p>
      <p><strong>Phone:</strong> <?= $biz['phone'] ?> | <strong>Address:</strong> <?= $biz['address'] ?></p>
      <img src="../../assets/images/<?= $biz['image'] ?>" width="250">
      <form method="post" action="../../controllers/approve_business.php">
        <input type="hidden" name="id" value="<?= $biz['id'] ?>">
        <button type="submit">✅ Approve</button>
      </form>
    </div>
  <?php endforeach; ?>
<?php endif; ?>
