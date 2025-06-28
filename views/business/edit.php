<?php


require_once '../../includes/db.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->execute([$id]);
$biz = $stmt->fetch();

if (!$biz) {
  echo "Business not found.";
  exit;
}
?>

<h2>Edit Business</h2>
<form method="POST" action="update.php">
  <input type="hidden" name="id" value="<?= $biz['id'] ?>">
  <input type="text" name="name" value="<?= htmlspecialchars($biz['name']) ?>" required>
  <textarea name="description"><?= htmlspecialchars($biz['description']) ?></textarea>
  <button type="submit">Update</button>
</form>
