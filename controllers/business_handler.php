<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require '../includes/db.php';
session_start();

if (!isset($_SESSION['user'])) {
  echo "<div class='flash error'>Unauthorized.</div>";
  exit;
}

$user_id = $_SESSION['user']['id'];
$name = $_POST['name'];
$category = $_POST['category'];
$desc = $_POST['description'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Upload image
if ($_FILES['image']['error'] === 0) {
  $image_name = uniqid() . '_' . $_FILES['image']['name'];
  $image_tmp = $_FILES['image']['tmp_name'];
  $upload_dir = '../assets/images/' . $image_name;

  if (move_uploaded_file($image_tmp, $upload_dir)) {
    var_dump($user_id, $name, $category, $desc, $phone, $address, $image_name);

    $stmt = $pdo->prepare("INSERT INTO businesses (user_id, name, category, description, phone, address, image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $success = $stmt->execute([$user_id, $name, $category, $desc, $phone, $address, $image_name]);

    if ($success) {
      echo "<div class='flash success'>Business submitted for approval.</div>";
    } else {
      echo "<div class='flash error'>Error saving business.</div>";
    }
  } else {
    echo "<div class='flash error'>Image upload failed.</div>";
  }
} else {
  echo "<div class='flash error'>Invalid image.</div>";
} 


?>
