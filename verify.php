<?php
require_once 'includes/db.php';
require_once 'includes/flash.php';
session_start();

$token = $_GET['token'] ?? '';

if (!$token) {
  set_flash('login', 'Invalid verification link.', 'error');
  header('Location: views/auth/login.php');
  exit;
}

$stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if ($user) {
  $update = $pdo->prepare("UPDATE users SET email_verified = 1, verify_token = NULL WHERE id = ?");
  $update->execute([$user['id']]);
  set_flash('login', '✅ Email confirmed! You can now log in.', 'success');
} else {
  set_flash('login', '❌ Invalid or expired token.', 'error');
}

header('Location: views/auth/login.php');
exit;
