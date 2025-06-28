<?php
require_once '../../includes/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $description = $_POST['description'];

  $stmt = $pdo->prepare("UPDATE businesses SET name = ?, description = ? WHERE id = ?");
  $stmt->execute([$name, $description, $id]);
}

header("Location: ../user/my_listings.php");
exit;
?>
