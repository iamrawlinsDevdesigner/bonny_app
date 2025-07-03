<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    echo "Login required";
    exit;
}

$user_id = $_SESSION['user']['id'];
$biz_id = (int)($_GET['biz_id'] ?? 0);

if ($biz_id <= 0) {
    echo "Invalid business ID";
    exit;
}

// Check if already favourited
$stmt = $pdo->prepare("SELECT COUNT(*) FROM favourites WHERE user_id = ? AND business_id = ?");
$stmt->execute([$user_id, $biz_id]);
$is_fav = $stmt->fetchColumn();

if ($is_fav) {
    // Remove favourite
    $del = $pdo->prepare("DELETE FROM favourites WHERE user_id = ? AND business_id = ?");
    $del->execute([$user_id, $biz_id]);
    echo "🤍 Favourite";
} else {
    // Add favourite
    $add = $pdo->prepare("INSERT INTO favourites (user_id, business_id) VALUES (?, ?)");
    $add->execute([$user_id, $biz_id]);
    echo "❤️ Favourited";
}
?>
