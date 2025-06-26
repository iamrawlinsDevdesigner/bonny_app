<?php
require '../includes/db.php';
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'rider') {
  echo "<div class='flash error'>Unauthorized.</div>";
  exit;
}

$delivery_id = $_POST['delivery_id'];
$rider_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("UPDATE deliveries SET status = 'Accepted', accepted_by = ? WHERE id = ?");
$success = $stmt->execute([$rider_id, $delivery_id]);

if ($success) {
  header("Location: ../views/delivery/list.php");
} else {
  echo "<div class='flash error'>Could not accept delivery.</div>";
}
?>
