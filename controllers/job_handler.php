<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  echo "<div class='flash error'>Unauthorized.</div>";
  exit;
}

$title = $_POST['title'];
$company = $_POST['company'];
$location = $_POST['location'];
$type = $_POST['type'];
$desc = $_POST['description'];
$email = $_POST['contact_email'];
$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("INSERT INTO jobs (title, company, location, type, description, contact_email, posted_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
$success = $stmt->execute([$title, $company, $location, $type, $desc, $email, $user_id]);

if ($success) {
  echo "<div class='flash success'>Job posted successfully.</div>";
} else {
  echo "<div class='flash error'>Failed to post job.</div>";
}
?>
