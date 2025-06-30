<?php
include '../../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  echo "Unauthorized";
  exit;
}

$user_id = $_SESSION['user']['id'];
$biz_id = $_POST['biz_id'];
$content = trim($_POST['content']);
$rating = (int) $_POST['rating'];

if (!$biz_id || !$content || !$rating) {
  echo "Missing data";
  exit;
}

$stmt = $pdo->prepare("INSERT INTO reviews (business_id, user_id, content, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
if ($stmt->execute([$biz_id, $user_id, $content, $rating])) {
  echo "Review submitted successfully";
} else {
  echo "Failed to submit review";
}
?>