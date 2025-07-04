<?php
include '../includes/db.php';

$biz_id = (int)($_GET['biz_id'] ?? 0);
if ($biz_id <= 0) {
    echo "N/A";
    exit;
}

$stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM reviews WHERE business_id = ?");
$stmt->execute([$biz_id]);
$row = $stmt->fetch();

$avg_rating = $row['avg_rating'];
echo $avg_rating ? round($avg_rating, 1) . " / 5" : "No ratings yet";
?>
