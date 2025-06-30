<?php
include '../../includes/db.php';
session_start();

$id = $_POST['id'];
$content = $_POST['content'];
$rating = (int) $_POST['rating'];
$user_id = $_SESSION['user']['id'];

$stmt = $pdo->prepare("UPDATE reviews SET content = ?, rating = ? WHERE id = ? AND user_id = ?");
$stmt->execute([$content, $rating, $id, $user_id]);

header("Location: show.php?id=" . $_GET['biz_id']);
