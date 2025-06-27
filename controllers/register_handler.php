// register_handler.php
<?php
require_once '../includes/db.php';
require_once '../includes/mailer.php';
require_once '../includes/flash.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'user';

  if (!$name || !$email || !$password) {
    set_flash('register', 'All fields are required.', 'error');
    header('Location: ../views/auth/register.php');
    exit;
  }

  $check = $pdo->prepare("SELECT id, email_verified FROM users WHERE email = ?");
  $check->execute([$email]);
  $existing = $check->fetch();

  if ($existing) {
    if ($existing['email_verified']) {
      set_flash('register', 'Email already registered and verified. Please log in.', 'error');
      header('Location: ../views/auth/register.php');
      exit;
    } else {
      // Resend confirmation email
      $token = bin2hex(random_bytes(16));
      $update = $pdo->prepare("UPDATE users SET verify_token = ? WHERE id = ?");
      $update->execute([$token, $existing['id']]);

      $link = "http://localhost/bonny_app/verify.php?token=" . $token;
      $message = '
      <!DOCTYPE html>
      <html>
      <head><style>
        .email { font-family: Arial; background:#fff; padding:20px; border:1px solid #eee; }
        .btn { display:inline-block; background:#007bff; color:#fff; padding:10px 15px; border-radius:5px; text-decoration:none; }
      </style></head>
      <body>
        <div class="email">
          <h2>🔁 Confirm Your Email Again</h2>
          <p>Hi,</p>
          <p>You recently tried to register again. Click below to confirm your email address and activate your BonnyHub account:</p>
          <a href="' . $link . '" class="btn">Confirm Email</a>
        </div>
      </body>
      </html>';

      send_mail($email, "Resend Email Confirmation", $message);

      set_flash('login', 'Your email is already registered but not yet verified. A new confirmation email has been sent.', 'success');
      header('Location: ../views/auth/login.php');
      exit;
    }
  }

  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $token = bin2hex(random_bytes(16));

  $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, verify_token) VALUES (?, ?, ?, ?, ?)");
  $stmt->execute([$name, $email, $hashedPassword, $role, $token]);

  $link = "http://localhost/bonny_app/verify.php?token=" . $token;
  $message = '
  <!DOCTYPE html>
  <html>
  <head><style>
    .email { font-family: Arial; background:#fff; padding:20px; border:1px solid #eee; }
    .btn { display:inline-block; background:#007bff; color:#fff; padding:10px 15px; border-radius:5px; text-decoration:none; }
  </style></head>
  <body>
    <div class="email">
      <h2>🛡 Confirm Your Email</h2>
      <p>Hi <strong>' . htmlspecialchars($name) . '</strong>,</p>
      <p>Please verify your email to activate your BonnyHub account.</p>
      <a href="' . $link . '" class="btn">Confirm Email</a>
    </div>
  </body>
  </html>';

  send_mail($email, "Confirm Your Email", $message);

  set_flash('login', 'Registration successful! Please check your email to confirm your account.', 'success');
  header('Location: ../views/auth/login.php');
  exit;
} else {
  header('Location: ../views/auth/register.php');
  exit;
}