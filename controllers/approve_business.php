<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
  die("Unauthorized.");
}

$id = $_POST['id'];

$stmt = $pdo->prepare("UPDATE businesses SET approved = 1 WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../views/admin/business_approval.php");
exit;
?>
