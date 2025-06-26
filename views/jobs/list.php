<?php
include '../../includes/db.php';
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY created_at DESC");
$jobs = $stmt->fetchAll();
?>

<h2>Job Listings</h2>
<?php foreach ($jobs as $job): ?>
  <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
    <h3><?= htmlspecialchars($job['title']) ?> <small>(<?= htmlspecialchars($job['type']) ?>)</small></h3>
    <p><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
    <p><strong>Location:</strong> <?= htmlspecialchars($job['location']) ?></p>
    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
    <p><strong>Contact:</strong> <a href="mailto:<?= $job['contact_email'] ?>"><?= $job['contact_email'] ?></a></p>
  </div>
<?php endforeach; ?>
