<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['user'])) {
  set_flash('login', 'Please log in to view your listings.', 'error');
  header('Location: ../auth/login.php');
  exit;
}

$user_id = $_SESSION['user']['id'];
$role = $_SESSION['user']['role'];
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Listings</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

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

  <div class="admin-container">
    <!-- Businesses -->
    <?php if ($role === 'vendor' || $role === 'user'): ?>
      <h2>My Businesses</h2>
      <?php
      $stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ?");
      $stmt->execute([$user_id]);
      $biz_list = $stmt->fetchAll();

      if ($biz_list):
        foreach ($biz_list as $biz): ?>
          <div class="admin-business-card">
            <h3><?= htmlspecialchars($biz['name']) ?></h3>
            <p>Status: <?= $biz['approved'] ? '✅ Approved' : '⏳ Pending' ?></p>
            <p><?= htmlspecialchars($biz['description']) ?></p>
          </div>
        <?php endforeach;
      else:
        echo "<p>No businesses listed.</p>";
      endif;
      ?>
    <?php endif; ?>

    <!-- Jobs -->
    <h2>My Job Posts</h2>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE posted_by = ?");
    $stmt->execute([$user_id]);
    $jobs = $stmt->fetchAll();

    if ($jobs):
      foreach ($jobs as $job): ?>
        <div class="admin-business-card">
          <h3><?= htmlspecialchars($job['title']) ?> (<?= $job['type'] ?>)</h3>
          <p><?= htmlspecialchars($job['description']) ?></p>
        </div>
      <?php endforeach;
    else:
      echo "<p>No job posts found.</p>";
    endif;
    ?>

    <!-- Deliveries -->
    <h2>My Delivery Requests</h2>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM deliveries WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $deliveries = $stmt->fetchAll();

    if ($deliveries):
      foreach ($deliveries as $d): ?>
        <div class="admin-business-card">
          <h3><?= htmlspecialchars($d['title']) ?></h3>
          <p>Status: <?= $d['status'] ?></p>
          <p><?= htmlspecialchars($d['description']) ?></p>
        </div>
      <?php endforeach;
    else:
      echo "<p>No delivery requests.</p>";
    endif;
    ?>
  </div>

</body>
</html>
