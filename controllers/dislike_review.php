<?php
session_start();
include '../includes/db.php';

if (!isset($_POST['review_id']) || !is_numeric($_POST['review_id'])) {
    echo "Invalid review ID.";
    exit;
}

$review_id = (int) $_POST['review_id'];

// Increment dislikes
$stmt = $pdo->prepare("UPDATE reviews SET dislikes = dislikes + 1 WHERE id = ?");
if ($stmt->execute([$review_id])) {
    // Return updated count
    $count = $pdo->prepare("SELECT dislikes FROM reviews WHERE id = ?");
    $count->execute([$review_id]);
    $dislikes = $count->fetchColumn();
    echo $dislikes;
} else {
    echo "Failed to dislike.";
}
