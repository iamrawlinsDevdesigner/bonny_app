<?php
include '../../includes/db.php';

$biz_id = $_GET['biz_id'] ?? 0;

$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE business_id = ?");
$stmt->execute([$biz_id]);
$avg = $stmt->fetchColumn();

echo $avg ? number_format($avg, 1) . " / 5" : "No reviews yet";
?>