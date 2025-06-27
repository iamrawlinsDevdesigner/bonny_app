<?php
require '../includes/db.php';
require '../includes/mailer.php';



send_mail('rawcoaster@gmail.com', 'Test Subject', 'This is the body of the message.');

session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  die("Unauthorized.");
}

$id = $_POST['id'];

// Fetch business and user email
$stmt = $pdo->prepare("SELECT b.name, u.email FROM businesses b JOIN users u ON b.user_id = u.id WHERE b.id = ?");
$stmt->execute([$id]);
$biz = $stmt->fetch();

if ($biz) {
  $update = $pdo->prepare("UPDATE businesses SET approved = 1 WHERE id = ?");
  $update->execute([$id]);

  // Email notification
  $to = $biz['email'];
  $subject = "Business Approved!";
  $message = "Hi,\n\nYour business '" . $biz['name'] . "' has been approved and is now live on BonnyHub.\n\nCheers,\nBonnyHub Team";

  send_mail($to, $subject, $message);
}

header("Location: ../views/admin/business_approval.php");
exit;
