<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo "🤍 Favourite"; // Default icon for logged out users
    exit;
}

$user_id = $_SESSION['user']['id'];
$biz_id = (int)($_POST['biz_id'] ?? 0);

if ($biz_id <= 0) {
    http_response_code(400);
    echo "🤍 Favourite"; // Safe fallback
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
    echo "🤍 Favourite"; // Return empty heart
} else {
    // Add favourite
    $add = $pdo->prepare("INSERT INTO favourites (user_id, business_id) VALUES (?, ?)");
    if ($add->execute([$user_id, $biz_id])) {
        echo "❤️ Favourited"; // Return filled heart
    } else {
        http_response_code(500);
        echo "🤍 Favourite"; // On error fallback
    }
}
?>
