<?php
include '../../includes/db.php';
include '../../includes/flash.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "Invalid business ID.";
  exit;
}

$biz_id = (int) $_GET['id'];
$pdo->prepare("UPDATE businesses SET views = views + 1 WHERE id = ?")->execute([$biz_id]);

$stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->execute([$biz_id]);
$biz = $stmt->fetch();

if (!$biz) {
  echo "Business not found.";
  exit;
}

$stmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE business_id = ? ORDER BY created_at DESC");
$stmt->execute([$biz_id]);
$reviews = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
  <title><?= htmlspecialchars($biz['name']) ?> - Business</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
<div class="container">
  <h1><?= htmlspecialchars($biz['name']) ?></h1>
  <?php if (!empty($biz['image'])): ?>
    <img src="../../assets/images/<?= $biz['image'] ?>" alt="Business Image" width="100%">
  <?php endif; ?>
  <p><strong>Category:</strong> <?= htmlspecialchars($biz['category'] ?? 'N/A') ?></p>
  <p><strong>Phone:</strong> <a href="tel:<?= $biz['phone'] ?>"><?= $biz['phone'] ?></a></p>
  <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $biz['phone']) ?>" target="_blank">Chat on WhatsApp</a>

  <p><strong>Views:</strong> <?= $biz['views'] ?? 0 ?></p>
  <p><strong>Rating:</strong> <span id="avg-rating">Loading...</span></p>
  <p><?= nl2br(htmlspecialchars($biz['description'])) ?></p>
  <div class="map-container">
   <iframe src="https://maps.google.com/maps?q=<?= urlencode($biz['location']) ?>&output=embed" width="100%" height="300" style="border:0;" allowfullscreen></iframe>

  </div>
  <hr>
  <h2>Reviews</h2>
  <?php if (isset($_SESSION['user'])): ?>
    <form id="review-form">
      <input type="hidden" name="biz_id" value="<?= $biz_id ?>">
      <textarea name="content" required></textarea>
      <select name="rating" required>
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
      </select>
      <button type="submit">Post Review</button>
    </form>
  <?php else: ?>
    <p><a href="../auth/login.php">Login</a> to leave a review.</p>
  <?php endif; ?>
  <div id="reviews">
    <?php foreach ($reviews as $rev): ?>
      <div>
        <strong><?= htmlspecialchars($rev['name']) ?>:</strong>
        <?php for ($i = 1; $i <= 5; $i++): ?>
          <?= $i <= $rev['rating'] ? '⭐' : '☆' ?>
        <?php endfor; ?>
        <p><?= nl2br(htmlspecialchars($rev['content'])) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", () => {
  fetch("get_average_rating.php?biz_id=<?= $biz_id ?>")
    .then(res => res.text()).then(text => {
      document.getElementById("avg-rating").innerText = text;
    });

  document.getElementById("review-form")?.addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("submit_review.php", {
      method: "POST",
      body: formData
    }).then(res => res.text()).then(msg => {
      alert(msg);
      location.reload();
    });
  });
});
</script>
</body>
</html>
