<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    echo "Please login to send a message.";
    exit;
}

$sender_id = $_SESSION['user']['id'];
$receiver_id = (int)($_POST['receiver_id'] ?? 0);
$message = trim($_POST['message']);

if ($receiver_id <= 0 || empty($message)) {
    echo "Invalid receiver or empty message.";
    exit;
}

// Insert into messages table
$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, content, created_at) VALUES (?, ?, ?, NOW())");
if ($stmt->execute([$sender_id, $receiver_id, $message])) {
    echo "Message sent successfully!";
} else {
    echo "Failed to send message.";
}
?>
