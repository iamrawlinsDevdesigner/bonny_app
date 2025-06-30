<?php
include '../../includes/db.php';
session_start();

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM reviews WHERE id = ? AND user_id = ?");
$stmt->execute([$id, $_SESSION['user']['id']]);

header("Location: " . $_SERVER['HTTP_REFERER']);
