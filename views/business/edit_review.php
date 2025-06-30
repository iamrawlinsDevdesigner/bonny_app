<?php
include '../../includes/db.php';
session_start();

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$id]);
$review = $stmt->fetch();

if (!$review || $review['user_id'] != $_SESSION['user']['id']) {
  echo "Unauthorized.";
  exit;
}
?>

<form method="POST" action="update_review.php">
  <input type="hidden" name="id" value="<?= $review['id'] ?>">
  <textarea name="content"><?= htmlspecialchars($review['content']) ?></textarea>
  <label>Rating:</label>
  <select name="rating">
    <?php for ($i = 1; $i <= 5; $i++): ?>
      <option value="<?= $i ?>" <?= $review['rating'] == $i ? 'selected' : '' ?>><?= str_repeat('⭐', $i) ?></option>
    <?php endfor; ?>
  </select>
  <button type="submit">Update</button>
</form>
