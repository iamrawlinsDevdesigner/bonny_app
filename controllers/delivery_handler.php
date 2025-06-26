<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  echo "<div class='flash error'>Unauthorized.</div>";
  exit;
}

$title = $_POST['title'];
$desc = $_POST['description'];
$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("INSERT INTO deliveries (user_id, title, description) VALUES (?, ?, ?)");
$success = $stmt->execute([$user_id, $title, $desc]);

if ($success) {
  echo "<div class='flash success'>Delivery request posted.</div>";
} else {
  echo "<div class='flash error'>Failed to post request.</div>";
}
?>
