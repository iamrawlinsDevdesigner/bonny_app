<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();

$search = $_GET['search'] ?? '';
$type = $_GET['type'] ?? '';

// Build query
$query = "SELECT * FROM jobs WHERE 1";
$params = [];

if ($search) {
  $query .= " AND (title LIKE ? OR description LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

if ($type) {
  $query .= " AND type = ?";
  $params[] = $type;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Job Listings</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

  <nav class="navbar">
    <div class="navbar-brand">BonnyHub</div>
    <div class="navbar-links">
      <a href="../../index.php">Home</a>
    </div>
  </nav>

  <div class="welcome">
    <h1>Job Listings</h1>
  </div>

  <form method="get" class="filter-form">
    <input type="text" name="search" placeholder="Search jobs..." value="<?= htmlspecialchars($search) ?>">
    <select name="type">
      <option value="">All Types</option>
      <option value="Full-time" <?= $type == 'Full-time' ? 'selected' : '' ?>>Full-time</option>
      <option value="Part-time" <?= $type == 'Part-time' ? 'selected' : '' ?>>Part-time</option>
      <option value="Contract" <?= $type == 'Contract' ? 'selected' : '' ?>>Contract</option>
    </select>
    <button type="submit">Filter</button>
  </form>

  <div class="admin-container">
    <?php if ($jobs): ?>
      <?php foreach ($jobs as $job): ?>
        <div class="admin-business-card">
          <h3><?= htmlspecialchars($job['title']) ?> <small>(<?= htmlspecialchars($job['type']) ?>)</small></h3>
          <p><strong>Company:</strong> <?= htmlspecialchars($job['company']) ?></p>
          <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
          <p><strong>Contact:</strong> <a href="mailto:<?= $job['contact_email'] ?>"><?= $job['contact_email'] ?></a></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No jobs found.</p>
    <?php endif; ?>
  </div>

</body>
</html>
