// login_handler.php
<?php
require_once '../includes/db.php';
require_once '../includes/flash.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if (!$email || !$password) {
    set_flash('login', 'Both fields are required.', 'error');
    header('Location: ../views/auth/login.php');
    exit;
  }

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch();

  if ($user && password_verify($password, $user['password'])) {
    if (!$user['email_verified']) {
      set_flash('login', 'Please confirm your email before logging in.', 'error');
      header('Location: ../views/auth/login.php');
      exit;
    }

    $_SESSION['user'] = $user;
    set_flash('login', 'Welcome back, ' . $user['name'] . '!', 'success');
    header('Location: ../index.php');
    exit;
  }

  set_flash('login', 'Invalid email or password.', 'error');
  header('Location: ../views/auth/login.php');
  exit;
} else {
  header('Location: ../views/auth/login.php');
  exit;
}
