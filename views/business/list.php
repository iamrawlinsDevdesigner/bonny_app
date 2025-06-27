<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();

// Get filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Query with filters
$query = "SELECT * FROM businesses WHERE approved = 1";
$params = [];

if ($search) {
  $query .= " AND (name LIKE ? OR description LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

if ($category) {
  $query .= " AND category = ?";
  $params[] = $category;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Business Listings</title>
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
    <h1>Approved Businesses</h1>
  </div>

  <form method="get" class="filter-form">
    <input type="text" name="search" placeholder="Search businesses..." value="<?= htmlspecialchars($search) ?>">
    <select name="category">
      <option value="">All Categories</option>
      <option value="Food" <?= $category == 'Food' ? 'selected' : '' ?>>Food</option>
      <option value="Laundry" <?= $category == 'Laundry' ? 'selected' : '' ?>>Laundry</option>
      <option value="Health" <?= $category == 'Health' ? 'selected' : '' ?>>Health</option>
      <!-- Add more categories as needed -->
    </select>
    <button type="submit">Filter</button>
  </form>

  <div class="admin-container">
    <?php if ($results): ?>
      <?php foreach ($results as $biz): ?>
        <div class="admin-business-card">
          <h3><?= htmlspecialchars($biz['name']) ?> (<?= htmlspecialchars($biz['category']) ?>)</h3>
          <p><?= htmlspecialchars($biz['description']) ?></p>
          <img src="../../assets/images/<?= htmlspecialchars($biz['image']) ?>" alt="Business image">
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No businesses found.</p>
    <?php endif; ?>
  </div>

</body>
</html>
