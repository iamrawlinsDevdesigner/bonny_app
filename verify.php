<?php
require_once 'includes/db.php';
require_once 'includes/flash.php';


$token = $_GET['token'] ?? '';
$status = 'error';
$message = 'Invalid or expired confirmation link.';

if ($token) {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE verify_token = ?");
  $stmt->execute([$token]);
  $user = $stmt->fetch();

  if ($user) {
    $update = $pdo->prepare("UPDATE users SET email_verified = 1, verify_token = NULL WHERE id = ?");
    $update->execute([$user['id']]);
    $status = 'success';
    $message = '✅ Your email has been verified. Redirecting you to login...';
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Email verified. You may now log in.'];
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Email Confirmation</title>
  <style>
    body {
      background-color: #f0f4f8;
      font-family: Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
    }
    .card {
      background: #fff;
      padding: 30px 40px;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
    }
    .card h2 {
      color: <?= $status === 'success' ? '#28a745' : '#dc3545' ?>;
      margin-bottom: 10px;
    }
    .loader {
      border: 4px solid #f3f3f3;
      border-top: 4px solid #007bff;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
  <script>
    setTimeout(() => {
      window.location.href = "views/auth/login.php";
    }, 4000);
  </script>
</head>
<body>
  <div class="card">
    <h2><?= htmlspecialchars($message) ?></h2>
    <div class="loader"></div>
    <p>You’ll be redirected shortly...</p>
  </div>
</body>
</html>
