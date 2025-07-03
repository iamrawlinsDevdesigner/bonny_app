<?php
include '../../includes/db.php';
include '../../includes/flash.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid business ID.";
    exit;
}

$biz_id = (int)$_GET['id'];

// Increment view count
$pdo->prepare("UPDATE businesses SET views = views + 1 WHERE id = ?")->execute([$biz_id]);

// Fetch business details
$stmt = $pdo->prepare("SELECT * FROM businesses WHERE id = ?");
$stmt->execute([$biz_id]);
$biz = $stmt->fetch();

if (!$biz) {
    echo "Business not found.";
    exit;
}

// Check if user has favourited
$is_favourited = false;
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favourites WHERE user_id = ? AND business_id = ?");
    $stmt->execute([$_SESSION['user']['id'], $biz_id]);
    $is_favourited = $stmt->fetchColumn() > 0;
}

// Reviews pagination
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 5;
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT r.*, u.name FROM reviews r JOIN users u ON r.user_id = u.id WHERE business_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->bindParam(1, $biz_id, PDO::PARAM_INT);
$stmt->bindParam(2, $perPage, PDO::PARAM_INT);
$stmt->bindParam(3, $offset, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll();

// Total reviews for pagination
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE business_id = ?");
$count_stmt->execute([$biz_id]);
$total_reviews = $count_stmt->fetchColumn();
$total_pages = ceil($total_reviews / $perPage);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($biz['name']) ?> - Business</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .map-container iframe { width: 100%; height: 300px; border: 0; }
        .review-box { background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-left: 3px solid #333; }
        .stars { color: #f5a623; }
        .review-actions a { margin-right: 10px; font-size: 14px; }
        .favourite-btn { background: <?= $is_favourited ? '#e74c3c' : '#3498db' ?>; color: #fff; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; }
        .pagination a { margin: 0 5px; text-decoration: none; }
    </style>
</head>
<body>

<div class="container">
    <h1><?= htmlspecialchars($biz['name']) ?></h1>

    <?php if (!empty($biz['image'])): ?>
        <img src="../../assets/images/<?= htmlspecialchars($biz['image']) ?>" alt="Business Image" width="100%">
    <?php endif; ?>

    <p><strong>Category:</strong> <?= htmlspecialchars($biz['category'] ?? 'N/A') ?></p>
    <p><strong>Phone:</strong> <a href="tel:<?= $biz['phone'] ?>"><?= $biz['phone'] ?></a></p>
    <p><strong>Views:</strong> <?= $biz['views'] ?? 0 ?></p>
    <p><strong>Rating:</strong> <span id="avg-rating">Loading...</span></p>

    <?php if (isset($_SESSION['user'])): ?>
        <button id="favBtn" class="favourite-btn" onclick="toggleFavourite(<?= $biz_id ?>)">
            <?= $is_favourited ? '❤️ Favourited' : '🤍 Favourite' ?>
        </button>
    <?php endif; ?>

    <p><?= nl2br(htmlspecialchars($biz['description'])) ?></p>

    <?php if (!empty($biz['location'])): ?>
        <div class="map-container">
            <iframe src="https://maps.google.com/maps?q=<?= urlencode($biz['location']) ?>&output=embed"></iframe>
        </div>
    <?php endif; ?>

    <hr>

    <h2>Reviews</h2>

    <?php if (isset($_SESSION['user'])): ?>
        <form id="reviewForm">
            <input type="hidden" name="biz_id" value="<?= $biz_id ?>">
            <textarea name="content" placeholder="Write your review..." required></textarea>
            <label>Rating:</label>
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
            <div class="review-box">
                <p><strong><?= htmlspecialchars($rev['name']) ?>:</strong></p>
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?= $i <= $rev['rating'] ? '⭐' : '☆' ?>
                    <?php endfor; ?>
                </div>
                <p><?= nl2br(htmlspecialchars($rev['content'])) ?></p>
                <small>Posted on <?= date('F j, Y', strtotime($rev['created_at'])) ?></small>
                <div class="review-actions">
                    <a href="report_review.php?id=<?= $rev['id'] ?>" onclick="return confirm('Report this review as abusive?')">🚩 Report</a>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['id'] == $rev['user_id']): ?>
                        <a href="edit_review.php?id=<?= $rev['id'] ?>">✏️ Edit</a>
                        <a href="delete_review.php?id=<?= $rev['id'] ?>" onclick="return confirm('Delete this review?')">🗑️ Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <a href="?id=<?= $biz_id ?>&page=<?= $p ?>" <?= $p == $page ? 'style="font-weight:bold;"' : '' ?>><?= $p ?></a>
        <?php endfor; ?>
    </div>

    <hr>

    <h3>Related Businesses</h3>
    <div class="listing-grid">
        <?php
        $cat = $biz['category'] ?? 'General';
        $stmt = $pdo->prepare("SELECT * FROM businesses WHERE category = ? AND id != ? LIMIT 4");
        $stmt->execute([$cat, $biz_id]);
        $related = $stmt->fetchAll();
        foreach ($related as $r): ?>
            <div class="listing-card">
                <h4><a href="show.php?id=<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></a></h4>
                <p><?= htmlspecialchars(substr($r['description'], 0, 60)) ?>...</p>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<script>
function toggleFavourite(bizId) {
    fetch('../../controllers/toggle_favourite.php?biz_id=' + bizId)
    .then(res => res.text())
    .then(data => {
        document.getElementById('favBtn').innerText = data.includes('added') ? '❤️ Favourited' : '🤍 Favourite';
        document.getElementById('favBtn').style.background = data.includes('added') ? '#e74c3c' : '#3498db';
    });
}

document.getElementById("reviewForm")?.addEventListener("submit", function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("../../controllers/submit_review.php", {
        method: "POST",
        body: formData
    }).then(res => res.text()).then(msg => {
        alert(msg);
        location.reload();
    });
});

document.addEventListener("DOMContentLoaded", () => {
    fetch("../../controllers/get_average_rating.php?biz_id=<?= $biz_id ?>")
    .then(res => res.text())
    .then(text => {
        document.getElementById("avg-rating").innerText = text;
    });
});
</script>
</body>
</html>
