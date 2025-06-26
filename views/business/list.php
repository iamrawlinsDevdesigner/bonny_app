<?php
include '../../includes/db.php';
include '../../includes/flash.php';

$stmt = $pdo->query("SELECT * FROM businesses WHERE approved = 1 ORDER BY created_at DESC");
$businesses = $stmt->fetchAll();
?>

<h2>Approved Businesses</h2>
<div style="display: flex; flex-wrap: wrap; gap: 20px;">
  <?php foreach ($businesses as $biz): ?>
    <div style="border:1px solid #ccc; padding:10px; width:250px;">
      <img src="../../assets/images/<?= $biz['image'] ?>" style="width:100%; height:150px; object-fit:cover;">
      <h3><?= htmlspecialchars($biz['name']) ?></h3>
      <p><strong>Category:</strong> <?= htmlspecialchars($biz['category']) ?></p>
      <p><?= htmlspecialchars($biz['description']) ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($biz['phone']) ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($biz['address']) ?></p>
    </div>
  <?php endforeach; ?>
</div>
