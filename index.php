<?php
include 'includes/db.php';
include 'includes/flash.php';
if (!isset($_SESSION)) session_start();

$user = $_SESSION['user'] ?? null;
?>

<!DOCTYPE html>
<html>
<head>
  <title>Bonny Community App</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Flash Message -->
  <div class="flash-container">
    <?php display_flash('login'); ?>
  </div>

  <!-- Navbar -->
  <nav class="navbar">
    <div class="navbar-brand">BonnyHub</div>
    <div class="navbar-links">
      <a href="index.php">Home</a>
      <a href="views/business/list.php">Businesses</a>
      <a href="views/jobs/list.php">Jobs</a>
      <a href="views/delivery/list.php">Deliveries</a>
      <?php if ($user): ?>
        <?php if ($user['role'] === 'admin'): ?>
          <a href="views/admin/business_approval.php">Admin Panel</a>
        <?php endif; ?>
        <a href="views/auth/logout.php">Logout</a>
      <?php else: ?>
        <a href="views/auth/login.php">Login</a>
        <a href="views/auth/register.php">Register</a>
      <?php endif; ?>
    </div>
  </nav>

  <!-- Welcome -->
  <div class="welcome">
    <h1>Welcome to BonnyHub 👋</h1>
    <?php if ($user): ?>
      <p>Hello <strong><?= htmlspecialchars($user['name']) ?></strong> (<?= $user['role'] ?>)</p>
    <?php else: ?>
      <p>Join the Bonny Island community by logging in or registering.</p>
    <?php endif; ?>
  </div>

  <!-- Dashboard Links -->
  <div class="dashboard">
    <a href="views/business/create.php" class="dashboard-link">➕ Add Business</a>
    <a href="views/jobs/create.php" class="dashboard-link">📌 Post Job</a>
    <a href="views/delivery/create.php" class="dashboard-link">🚚 Request Delivery</a>
  </div>

  <?php if ($user && $user['role'] === 'admin'): ?>
    <div class="admin-container">
      <h2>Pending Business Approvals</h2>
      <?php
      $stmt = $pdo->query("SELECT * FROM businesses WHERE approved = 0 ORDER BY created_at DESC");
      $pending = $stmt->fetchAll();
      if (empty($pending)) {
        echo '<p>No pending businesses for approval.</p>';
      } else {
        foreach ($pending as $biz) {
          echo '<div class="admin-business-card">';
          echo '<h3>' . htmlspecialchars($biz['name']) . ' (' . htmlspecialchars($biz['category']) . ')</h3>';
          echo '<p>' . htmlspecialchars($biz['description']) . '</p>';
          echo '<p><strong>Phone:</strong> ' . htmlspecialchars($biz['phone']) . ' | <strong>Address:</strong> ' . htmlspecialchars($biz['address']) . '</p>';
          echo '<img src="assets/images/' . htmlspecialchars($biz['image']) . '" alt="Business Image">';
          echo '<form method="post" action="controllers/approve_business.php">';
          echo '<input type="hidden" name="id" value="' . $biz['id'] . '">';
          echo '<button type="submit">✅ Approve</button>';
          echo '</form>';
          echo '</div>';
        }
      }
      ?>
    </div>
  <?php endif; ?>

</body>
</html>
