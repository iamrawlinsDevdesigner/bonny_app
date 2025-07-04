<?php
session_start();
include '../includes/db.php';

$biz_id = (int) ($_GET['biz_id'] ?? 0);
$page = (int) ($_GET['page'] ?? 1);
$limit = 5;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT r.*, u.name, u.id as user_id FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.business_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute([$biz_id, $limit, $offset]);
$reviews = $stmt->fetchAll();

foreach ($reviews as $rev): ?>
  <div class="review-box">
    <p><strong><?= htmlspecialchars($rev['name']) ?>:</strong></p>
    <div class="stars">
      <?php for ($i = 1; $i <= 5; $i++): ?>
        <?= $i <= $rev['rating'] ? '⭐' : '☆' ?>
      <?php endfor; ?>
    </div>
    <p><?= nl2br(htmlspecialchars($rev['content'])) ?></p>
    <small>Posted on <?= date('F j, Y', strtotime($rev['created_at'])) ?>
      <?php if (!empty($rev['updated_at'])): ?>
        | Edited on <?= date('F j, Y', strtotime($rev['updated_at'])) ?>
      <?php endif; ?>
    </small>
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $rev['user_id']): ?>
      <div class="review-actions">
        <button onclick="openEditModal(<?= $rev['id'] ?>, '<?= htmlspecialchars(addslashes($rev['content'])) ?>', <?= $rev['rating'] ?>)">✏️</button>
        <button onclick="deleteReview(<?= $rev['id'] ?>)">🗑️</button>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach;
