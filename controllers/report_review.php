<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    echo "Login required";
    exit;
}

$user_id = $_SESSION['user']['id'];
$review_id = (int)($_POST['review_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');

if ($review_id <= 0 || empty($reason)) {
    echo "Invalid request";
    exit;
}

// Save report
$stmt = $pdo->prepare("INSERT INTO review_reports (review_id, user_id, reason, reported_at) VALUES (?, ?, ?, NOW())");
$success = $stmt->execute([$review_id, $user_id, $reason]);

echo $success ? "Review reported." : "Failed to report review.";
?>
