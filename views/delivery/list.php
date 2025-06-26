<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();

$user = $_SESSION['user'] ?? null;
$is_rider = $user && $user['role'] === 'rider';

$stmt = $pdo->query("SELECT * FROM deliveries WHERE status = 'Pending' ORDER BY created_at DESC");
$requests = $stmt->fetchAll();
?>

<h2>Open Delivery Requests</h2>
<?php foreach ($requests as $req): ?>
  <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
    <h3><?= htmlspecialchars($req['title']) ?></h3>
    <p><?= nl2br(htmlspecialchars($req['description'])) ?></p>
    <p><strong>Status:</strong> <?= $req['status'] ?></p>

    <?php if ($is_rider): ?>
      <form method="post" action="../../controllers/delivery_accept.php">
        <input type="hidden" name="delivery_id" value="<?= $req['id'] ?>">
        <button type="submit">Accept</button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
