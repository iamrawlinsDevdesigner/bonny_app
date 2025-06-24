<?php
require '../includes/db.php';
require '../includes/flash.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  if ($stmt->rowCount() > 0) {
    set_flash('register', 'Email already exists!', 'error');
    header("Location: ../views/auth/register.php");
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
  if ($stmt->execute([$name, $email, $password])) {
    set_flash('register', 'Registration successful!', 'success');
    header("Location: ../views/auth/login.php");
  } else {
    set_flash('register', 'Registration failed!', 'error');
    header("Location: ../views/auth/register.php");
  }
}
