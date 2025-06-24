<?php
require '../includes/db.php';
require '../includes/flash.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $email = $_POST['email'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    set_flash('login', 'Login successful!', 'success');
    header("Location: ../index.php");
  } else {
    set_flash('login', 'Invalid credentials!', 'error');
    header("Location: ../views/auth/login.php");
  }
}
