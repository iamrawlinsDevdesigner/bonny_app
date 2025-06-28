<?php
session_start();
include '../includes/db.php';
include '../includes/flash.php';

if (!isset($_SESSION['user'])) {
  set_flash('login', 'Unauthorized access.', 'error');
  header('Location: ../auth/login.php');
  exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if ($user && !empty($user['profile_image'])) {
  $file = '../../assets/images/' . $user['profile_image'];
  if (file_exists($file)) {
    unlink($file);
  }

  $update = $pdo->prepare("UPDATE users SET profile_image = NULL WHERE id = ?");
  $update->execute([$user_id]);

  set_flash('success', 'Profile image removed.', 'success');
} else {
  set_flash('error', 'No profile image to remove.', 'error');
}

header('Location: ../views/user/profile.php');
exit;
