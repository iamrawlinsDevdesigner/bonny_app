// edit_job.php
<?php
require_once '../../includes/db.php';
require_once '../../includes/flash.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->execute([$id]);
$job = $stmt->fetch();

if (!$job) {
  echo "Job not found.";
  exit;
}
?>
<h2>Edit Job</h2>
<form method="POST" action="update_job.php">
  <input type="hidden" name="id" value="<?= $job['id'] ?>">
  <input type="text" name="title" value="<?= htmlspecialchars($job['title']) ?>" required>
  <input type="text" name="type" value="<?= htmlspecialchars($job['type']) ?>" required>
  <textarea name="description"><?= htmlspecialchars($job['description']) ?></textarea>
  <button type="submit">Update</button>
</form>
