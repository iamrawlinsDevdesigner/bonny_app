<?php
include __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request.";
    exit;
}

if (!isset($_POST['review_id']) || !is_numeric($_POST['review_id'])) {
    echo "Invalid review ID.";
    exit;
}

$review_id = (int)$_POST['review_id'];

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    echo "You must be logged in to delete a review.";
    exit;
}

$user_id = $_SESSION['user']['id'];

// Check if the review exists and belongs to the user
$stmt = $pdo->prepare("SELECT * FROM reviews WHERE id = ? AND user_id = ?");
$stmt->execute([$review_id, $user_id]);
$review = $stmt->fetch();

if (!$review) {
    echo "Review not found or you don’t have permission to delete it.";
    exit;
}

// Delete the review
$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ?");
if ($stmt->execute([$review_id])) {
    echo "Review deleted successfully.";
} else {
    echo "Failed to delete review. Please try again.";
}
