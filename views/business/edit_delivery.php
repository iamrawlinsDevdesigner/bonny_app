// edit_delivery.php
<?php
require_once '../../includes/db.php';
require_once '../../includes/flash.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("SELECT * FROM deliveries WHERE id = ?");
$stmt->execute([$id]);
$delivery = $stmt->fetch();

if (!$delivery) {
  echo "Delivery not found.";
  exit;
}
?>
<h2>Edit Delivery Request</h2>
<form method="POST" action="update_delivery.php">
  <input type="hidden" name="id" value="<?= $delivery['id'] ?>">
  <input type="text" name="title" value="<?= htmlspecialchars($delivery['title']) ?>" required>
  <input type="text" name="status" value="<?= htmlspecialchars($delivery['status']) ?>" required>
  <textarea name="description"><?= htmlspecialchars($delivery['description']) ?></textarea>
  <button type="submit">Update</button>
</form>