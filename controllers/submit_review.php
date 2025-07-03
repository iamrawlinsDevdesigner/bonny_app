<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    echo "Login required";
    exit;
}

$user_id = $_SESSION['user']['id'];
$biz_id = (int)($_POST['biz_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$rating = (int)($_POST['rating'] ?? 0);

if ($biz_id <= 0 || empty($content) || $rating < 1 || $rating > 5) {
    echo "Invalid input";
    exit;
}

// Save review
$stmt = $pdo->prepare("INSERT INTO reviews (business_id, user_id, content, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
$success = $stmt->execute([$biz_id, $user_id, $content, $rating]);

if ($success) {
    echo "Review submitted successfully.";
} else {
    echo "Failed to submit review.";
}
?>
