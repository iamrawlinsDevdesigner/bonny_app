<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to send messages.']);
    exit;
}

if (!isset($_POST['receiver_id'], $_POST['business_id'], $_POST['message'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incomplete form data.']);
    exit;
}

$sender_id = $_SESSION['user']['id'];
$receiver_id = (int)$_POST['receiver_id'];
$business_id = (int)$_POST['business_id'];
$message = trim($_POST['message']);

if (empty($message)) {
    echo json_encode(['status' => 'error', 'message' => 'Message cannot be empty.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, business_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$sender_id, $receiver_id, $business_id, $message]);

    echo json_encode([
    'status' => 'success',
    'message' => 'Your message was sent successfully.',
    'user_message' => 'Thanks! The business owner has been notified and will respond soon.'
]);
exit;
} catch (PDOException $e) {
   echo json_encode([
    'status' => 'error',
    'message' => 'Failed to send message.',
    'user_message' => 'Sorry, something went wrong while sending your message. Please try again later.'
]);
exit;
}

