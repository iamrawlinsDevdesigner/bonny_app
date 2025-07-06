<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    echo "You must be logged in to post a review.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $biz_id = (int)($_POST['biz_id'] ?? 0);
    $content = trim($_POST['content'] ?? '');
    $rating = (int)($_POST['rating'] ?? 0);
    $user_id = $_SESSION['user']['id'];

    if (!$biz_id || !$content || $rating < 1 || $rating > 5) {
        echo "Invalid data.";
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO reviews (business_id, user_id, content, rating, status, created_at)
        VALUES (?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([$biz_id, $user_id, $content, $rating]);

    echo "Your review has been submitted and is awaiting moderation.";
}
?>
