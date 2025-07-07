<?php
include __DIR__ . '/../../includes/db.php';
include __DIR__ . '/../../includes/flash.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid business ID.";
    exit;
}

$biz_id = (int)$_GET['id'];

// Increment view count
$pdo->prepare("UPDATE businesses SET views = views + 1 WHERE id = ?")->execute([$biz_id]);

// Fetch business details + owner details
$stmt = $pdo->prepare("
    SELECT b.*, u.name AS owner_name, u.profile_image, u.created_at AS member_since, u.id AS user_id
    FROM businesses b
    JOIN users u ON b.user_id = u.id
    WHERE b.id = ?
");
$stmt->execute([$biz_id]);
$biz = $stmt->fetch();

if (!$biz) {
    echo "Business not found.";
    exit;
}

$is_fav = false;
if (isset($_SESSION['user'])) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM favourites WHERE user_id = ? AND business_id = ?");
    $stmt->execute([$_SESSION['user']['id'], $biz_id]);
    $is_fav = $stmt->fetchColumn() > 0;
}


?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($biz['name']) ?> - Business</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .map-container iframe { width: 100%; height: 300px; border: 0; }
        .review-box { background: #f9f9f9; padding: 10px; margin-bottom: 10px; border-left: 3px solid #333; border-radius: 5px; }
        .stars { color: #f5a623; }
        .review-actions button { margin-right: 8px; border: none; background: none; cursor: pointer; font-size: 16px; }
        .favorite-btn { color: red; cursor: pointer; }
        #loadMoreBtn { background: #007bff; color: #fff; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        #loadMoreBtn:hover { background: #0056b3; }
        .owner-box { display: flex; align-items: center; background: #f1f1f1; padding: 10px; border-radius: 5px; margin: 15px 0; }
        .owner-box img { width: 60px; height: 60px; border-radius: 50%; margin-right: 15px; object-fit: cover; border: 2px solid #007bff; }
        .owner-box .info { line-height: 1.4; }
        .owner-box .info strong { display: block; font-size: 16px; color: #333; }
        .owner-box .info small { color: #666; }
        @media (min-width: 768px) {
            .business-details { display: flex; gap: 20px; }
            .business-main { flex: 2; }
            .business-sidebar { flex: 1; }
        }
        .modal {
  display: none;
  position: fixed;
  top: 0; left: 0; right: 0; bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  justify-content: center;
  align-items: center;
}
.modal-content {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  text-align: center;
}

    </style>
</head>
<body>

<div class="container">
    <div class="business-details">
        <div class="business-main">
            <h1><?= htmlspecialchars($biz['name']) ?></h1>

            <?php if (!empty($biz['image'])): ?>
                <img src="../../assets/images/<?= htmlspecialchars($biz['image']) ?>" alt="Business Image" width="100%">
            <?php endif; ?>

            <p><strong>Category:</strong> <?= htmlspecialchars($biz['category'] ?? 'N/A') ?></p>
            <p><strong>Phone:</strong> <a href="tel:<?= htmlspecialchars($biz['phone']) ?>"><?= htmlspecialchars($biz['phone']) ?></a></p>
            <p><strong>Views:</strong> <?= $biz['views'] ?? 0 ?></p>
            <p><strong>Rating:</strong> <span id="avg-rating">Loading...</span></p>
            <p><?= nl2br(htmlspecialchars($biz['description'])) ?></p>

            <span class="favorite-btn" onclick="toggleFavorite(<?= $biz_id ?>)">
                <?= $is_fav ? "❤️ Favourited" : "🤍 Favourite" ?>
            </span>



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

            <div id="reviews-container"></div>
            <button id="loadMoreBtn">Load More</button>
        </div>

        <div class="business-sidebar">
            <h3>Owner</h3>
            <div class="owner-box">
                <img src="../../assets/profile/<?= htmlspecialchars($biz['profile_image'] ?? 'default.png') ?>" alt="Owner Profile">
                <div class="info">
                    <strong><?= htmlspecialchars($biz['owner_name']) ?></strong>
                    <small>Member since <?= date('F Y', strtotime($biz['member_since'])) ?></small>
                </div>
            </div>
            <p><a href="../user/profile.php?id=<?= $biz['user_id'] ?>">View Owner Profile</a></p>
        </div>
    </div>
</div>

<!-- Edit Review Modal -->
<div id="editModal" class="modal">
  <div id="moderationModal" class="modal">
  <div class="modal-content">
    <h3>Thank you!</h3>
    <p>Your comment is awaiting moderation. It will appear once approved.</p>
    <button onclick="closeModerationModal()">OK</button>
  </div>
</div>

  <div class="modal-content">
    <h3>Edit Review</h3>
    <form id="editReviewForm">
      <input type="hidden" name="review_id" id="edit_review_id">
      <textarea name="content" id="edit_content" rows="4" required></textarea><br>
      <label>Rating:</label>
      <select name="rating" id="edit_rating" required>
        <option value="5">⭐⭐⭐⭐⭐</option>
        <option value="4">⭐⭐⭐⭐</option>
        <option value="3">⭐⭐⭐</option>
        <option value="2">⭐⭐</option>
        <option value="1">⭐</option>
      </select><br>
      <button type="submit">Update</button>
      <button type="button" onclick="closeModal()">Cancel</button>
    </form>
  </div>
</div>

<script>
let currentPage = 1;

function loadReviews(page = 1) {
    fetch("../../controllers/fetch_reviews.php?biz_id=<?= $biz_id ?>&page=" + page)
    .then(res => res.text())
    .then(html => {
        if (page === 1) {
            document.getElementById('reviews-container').innerHTML = html;
        } else {
            document.getElementById('reviews-container').insertAdjacentHTML('beforeend', html);
        }
    });
}

document.getElementById('loadMoreBtn').addEventListener('click', () => {
    currentPage++;
    loadReviews(currentPage);
});

document.getElementById('reviewForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("../../controllers/submit_review.php", {
        method: "POST",
        body: formData
    }).then(res => res.text()).then(msg => {
        showModerationModal(msg);
        this.reset();
    });
});

function showModerationModal(message) {
    const modal = document.getElementById('moderationModal');
    modal.querySelector('p').innerText = message;
    modal.style.display = 'flex';
}

function closeModerationModal() {
    document.getElementById('moderationModal').style.display = 'none';
}


document.addEventListener('click', function(e) {
    if (e.target.classList.contains('edit-btn')) {
        const content = e.target.dataset.content.replace(/&quot;/g, '"').replace(/&#039;/g, "'").replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
        document.getElementById('edit_review_id').value = e.target.dataset.reviewId;
        document.getElementById('edit_content').value = content;
        document.getElementById('edit_rating').value = e.target.dataset.rating;
        document.getElementById('editModal').style.display = 'flex';
    }
});

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

document.getElementById('editReviewForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch("../../controllers/edit_review.php", {
        method: "POST",
        body: formData
    }).then(res => res.text()).then(msg => {
        alert(msg);
        closeModal();
        currentPage = 1;
        loadReviews(currentPage);
    });
});

function deleteReview(id) {
    if (confirm("Are you sure you want to delete this review?")) {
        fetch("../../controllers/delete_review.php", {
            method: "POST",
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: "review_id=" + id
        }).then(res => res.text()).then(msg => {
            alert(msg);
            currentPage = 1;
            loadReviews(currentPage);
        });
    }
}

function toggleFavorite(biz_id) {
    const favBtn = document.querySelector('.favorite-btn');

    fetch("../../controllers/toggle_favourite.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: "biz_id=" + encodeURIComponent(biz_id)
    })
    .then(res => res.text())
    .then(response => {
        // Update button text instantly
        favBtn.textContent = response.trim();
    })
    .catch(err => console.error("Favourite toggle failed:", err));
}




document.addEventListener("DOMContentLoaded", () => {
    fetch("../../controllers/get_average_rating.php?biz_id=<?= $biz_id ?>")
    .then(res => res.text())
    .then(rating => {
        document.getElementById("avg-rating").innerText = rating;
    });
    loadReviews(currentPage);
});

function likeReview(review_id) {
    fetch("../../controllers/like_review.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: "review_id=" + review_id
    })
    .then(res => res.text())
    .then(newCount => {
        document.getElementById('like-count-' + review_id).innerText = newCount;
    });
}

function dislikeReview(review_id) {
    fetch("../../controllers/dislike_review.php", {
        method: "POST",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: "review_id=" + review_id
    })
    .then(res => res.text())
    .then(newCount => {
        document.getElementById('dislike-count-' + review_id).innerText = newCount;
    });
}
</script>
</body>
</html>
