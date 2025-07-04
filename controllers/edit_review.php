<?php
include __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_POST['review_id'], $_POST['content'], $_POST['rating']) || !is_numeric($_POST['review_id'])) {
    echo "Invalid input.";
    exit;
}

$review_id = (int)$_POST['review_id'];
$content = trim($_POST['content']);
$rating = (int)$_POST['rating'];

// Check if review exists and belongs to the logged-in user
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ?");
$stmt->execute([$review_id]);
$review = $stmt->fetch();

if (!$review) {
    echo "Review not found.";
    exit;
}

if (!isset($_SESSION['user']) || $_SESSION['user']['id'] !== $review['user_id']) {
    echo "Unauthorized action.";
    exit;
}

// Update review
$stmt = $pdo->prepare("UPDATE reviews SET content = ?, rating = ?, updated_at = NOW() WHERE id = ?");
if ($stmt->execute([$content, $rating, $review_id])) {
    echo "Review updated successfully.";
} else {
    echo "Failed to update review.";
}
