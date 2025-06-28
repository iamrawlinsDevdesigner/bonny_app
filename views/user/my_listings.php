<?php
include '../../includes/db.php';
include '../../includes/flash.php';


if (!isset($_SESSION['user'])) {
  set_flash('login', 'Please log in to view your listings.', 'error');
  header('Location: ../auth/login.php');
  exit;
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];
$q = $_GET['q'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Listings</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<?php if (has_flash('success')): ?>
  <div class="flash success"><?= get_flash('success') ?></div>
<?php endif; ?>
<?php if (has_flash('error')): ?>
  <div class="flash error"><?= get_flash('error') ?></div>
<?php endif; ?>

  <nav class="navbar">
    <div class="navbar-brand">BonnyHub</div>
    <div class="navbar-links">
      <a href="../../index.php">Home</a>
      <a href="../auth/logout.php">Logout</a>
    </div>
  </nav>

  <div class="welcome">
    <h1>My Listings</h1>
  </div>

  <form method="GET" class="search-bar" style="margin-bottom: 30px; text-align: center;">
    <input type="text" name="q" placeholder="Search my listings..." value="<?= htmlspecialchars($q) ?>" />
    <button type="submit">Search</button>
  </form>

  <div class="admin-container">
    <!-- Businesses -->
    <?php if ($role === 'vendor' || $role === 'user'): ?>
      <h2>My Businesses</h2>
      <div class="listing-grid">
      <?php
      $stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ? AND name LIKE ?");
      $stmt->execute([$user_id, "%$q%"]);
      $biz_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($biz_list):
        foreach ($biz_list as $biz): ?>
          <div class="listing-card">
            <div class="listing-left">
              <span class="status-badge <?= $biz['approved'] ? 'approved' : 'pending' ?>">
                <?= $biz['approved'] ? 'Approved' : 'Pending' ?>
              </span>
              <?php if (!empty($biz['image'])): ?>
                <img src="../../assets/images/<?= $biz['image'] ?>" class="business-photo" alt="Business Photo">
              <?php else: ?>
                <div class="photo-box">No Photo</div>
              <?php endif; ?>
            </div>

            <div class="listing-center">
              <h3><?= htmlspecialchars($biz['name']) ?></h3>
              <div class="tags">
                <span class="tag"><?= htmlspecialchars($biz['category'] ?? 'General') ?></span>
                <span class="tag">Free Stuff</span>
              </div>
              <p><strong>Added:</strong> <?= date('F j, Y', strtotime($biz['created_at'] ?? 'now')) ?></p>
              <div class="metrics">
                👁️ 0 &nbsp;&nbsp; 📞 0 &nbsp;&nbsp; ❤️ 0
              </div>
            </div>

            <div class="listing-right">
              <a href="../business/edit.php?id=<?= $biz['id'] ?>" class="edit-link">✏️ Edit</a>
              <a href="../business/delete.php?id=<?= $biz['id'] ?>" onclick="return confirm('Delete this business?')" class="delete-link">🗑️ Delete</a>
            </div>
          </div>
        <?php endforeach;
      else:
        echo "<p>No businesses listed.</p>";
      endif;
      ?>
      </div>
    <?php endif; ?>

    <!-- Jobs -->
    <h2>My Job Posts</h2>
    <div class="listing-grid">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE posted_by = ? AND title LIKE ?");
    $stmt->execute([$user_id, "%$q%"]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($jobs):
      foreach ($jobs as $job): ?>
        <div class="listing-card">
          <div class="listing-left">
            <span class="status-badge approved">Active</span>
            <div class="photo-box">💼</div>
          </div>

          <div class="listing-center">
            <h3><?= htmlspecialchars($job['title']) ?> (<?= $job['type'] ?>)</h3>
            <p><strong>Posted:</strong> <?= date('F j, Y', strtotime($job['created_at'] ?? 'now')) ?></p>
            <p><?= htmlspecialchars($job['description']) ?></p>
          </div>

          <div class="listing-right">
            <a href="edit_job.php?id=<?= $job['id'] ?>" class="edit-link">✏️ Edit</a>
            <a href="delete_job.php?id=<?= $job['id'] ?>" onclick="return confirm('Delete this job?')" class="delete-link">🗑️ Delete</a>
          </div>
        </div>
    <?php endforeach;
    else:
      echo "<p>No job posts found.</p>";
    endif;
    ?>
    </div>

    <!-- Deliveries -->
    <h2>My Delivery Requests</h2>
    <div class="listing-grid">
    <?php
    $stmt = $pdo->prepare("SELECT * FROM deliveries WHERE user_id = ? AND title LIKE ?");
    $stmt->execute([$user_id, "%$q%"]);
    $deliveries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($deliveries):
      foreach ($deliveries as $d): ?>
        <div class="listing-card">
          <div class="listing-left">
            <span class="status-badge <?= $d['status'] === 'Delivered' ? 'approved' : 'pending' ?>">
              <?= htmlspecialchars($d['status']) ?>
            </span>
            <div class="photo-box">🚚</div>
          </div>

          <div class="listing-center">
            <h3><?= htmlspecialchars($d['title']) ?></h3>
            <p><strong>Requested:</strong> <?= date('F j, Y', strtotime($d['created_at'] ?? 'now')) ?></p>
            <p><?= htmlspecialchars($d['description']) ?></p>
          </div>

          <div class="listing-right">
            <a href="edit_delivery.php?id=<?= $d['id'] ?>" class="edit-link">✏️ Edit</a>
            <a href="delete_delivery.php?id=<?= $d['id'] ?>" onclick="return confirm('Delete this delivery request?')" class="delete-link">🗑️ Delete</a>
          </div>
        </div>
    <?php endforeach;
    else:
      echo "<p>No delivery requests found.</p>";
    endif;
    ?>
    </div>
  </div>

</body>
</html>
