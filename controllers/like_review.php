<?php
session_start();
include '../includes/db.php';

if (!isset($_POST['review_id']) || !is_numeric($_POST['review_id'])) {
    echo "Invalid review ID.";
    exit;
}

$review_id = (int) $_POST['review_id'];

// Increment likes
$stmt = $pdo->prepare("UPDATE reviews SET likes = likes + 1 WHERE id = ?");
if ($stmt->execute([$review_id])) {
    // Return updated count
    $count = $pdo->prepare("SELECT likes FROM reviews WHERE id = ?");
    $count->execute([$review_id]);
    $likes = $count->fetchColumn();
    echo $likes;
} else {
    echo "Failed to like.";
}
