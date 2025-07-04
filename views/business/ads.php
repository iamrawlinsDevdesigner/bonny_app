<?php
include __DIR__ . '/../../includes/db.php';
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo "Invalid user ID.";
    exit;
}

$user_id = (int)$_GET['user_id'];

// Get user info
$stmt = $pdo->prepare("SELECT name, image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit;
}

// Get user’s businesses
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$businesses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Ads by <?= htmlspecialchars($user['name']) ?></title>
</head>
<body>
<h1>All Ads by <?= htmlspecialchars($user['name']) ?></h1>
<?php foreach ($businesses as $biz): ?>
    <div style="border: 1px solid #ccc; margin: 10px 0; padding: 10px;">
        <h2><a href="../business/show.php?id=<?= $biz['id'] ?>"><?= htmlspecialchars($biz['name']) ?></a></h2>
        <p><?= nl2br(htmlspecialchars($biz['description'])) ?></p>
    </div>
<?php endforeach; ?>
</body>
</html>
