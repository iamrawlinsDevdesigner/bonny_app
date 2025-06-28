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
$phone = $_POST['phone'] ?? '';
$description = $_POST['description'] ?? '';
$address = $_POST['address'] ?? '';
$new_email = trim($_POST['new_email'] ?? '');
$old_password = $_POST['old_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';

// Fetch user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
  set_flash('error', 'User not found.', 'error');
  header('Location: ../views/user/profile.php');
  exit;
}

// Handle image upload
$image_name = $user['profile_image'] ?? '';
if (!empty($_FILES['profile_image']['name'])) {
  $file_name = $_FILES['profile_image']['name'];
  $tmp_name = $_FILES['profile_image']['tmp_name'];
  $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
  $allowed = ['jpg', 'jpeg', 'png', 'gif'];

  if (in_array($file_ext, $allowed)) {
    $new_name = 'profile_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
    $target = '../assets/images/' . $new_name;
    if (move_uploaded_file($tmp_name, $target)) {
      // Delete old image
      if (!empty($image_name) && file_exists('../assets/images/' . $image_name)) {
        unlink('../assets/images/' . $image_name);
      }
      $image_name = $new_name;
    }
  }
}

// Handle password
$password = $user['password'];
if (!empty($old_password) && !empty($new_password)) {
  if (!password_verify($old_password, $password)) {
    set_flash('error', 'Old password is incorrect.', 'error');
    header('Location: ../views/user/profile.php');
    exit;
  }
  $password = password_hash($new_password, PASSWORD_DEFAULT);
}

// Handle email update
$email = $user['email'];
if (!empty($new_email) && $new_email !== $email) {
  $email = $new_email;
}

// Update user
$stmt = $pdo->prepare("UPDATE users SET phone = ?, description = ?, address = ?, profile_image = ?, password = ?, email = ?, updated_at = NOW() WHERE id = ?");
$success = $stmt->execute([$phone, $description, $address, $image_name, $password, $email, $user_id]);

if ($success) {
  $_SESSION['user']['email'] = $email;
  set_flash('success', 'Profile updated successfully.', 'success');
} else {
  set_flash('error', 'Update failed.', 'error');
}

header('Location: ../views/user/profile.php');
exit;
