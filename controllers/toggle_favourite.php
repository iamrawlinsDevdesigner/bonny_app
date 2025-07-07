<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "Login required";
    exit;
}

$user_id = $_SESSION['user']['id'];
$biz_id = (int)($_POST['biz_id'] ?? 0);

if ($biz_id <= 0) {
    http_response_code(400);
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
    echo "🤍 Removed from favourites";
} else {
    // Add favourite
    $add = $pdo->prepare("INSERT INTO favourites (user_id, business_id) VALUES (?, ?)");
    if ($add->execute([$user_id, $biz_id])) {
        echo "❤️ Added to favourites";
    } else {
        http_response_code(500);
        echo "Failed to add favourite";
    }
}
?>
