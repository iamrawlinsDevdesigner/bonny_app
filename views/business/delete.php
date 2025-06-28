<?php
require_once '../../includes/db.php';
session_start();

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM businesses WHERE id = ?");
$stmt->execute([$id]);

header("Location: ../user/my_listings.php");
exit;
?>
