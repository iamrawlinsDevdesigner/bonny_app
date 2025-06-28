// update_delivery.php
<?php
require_once '../../includes/db.php';
require_once '../../includes/flash.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $status = $_POST['status'];
  $description = $_POST['description'];

  $stmt = $pdo->prepare("UPDATE deliveries SET title = ?, status = ?, description = ? WHERE id = ?");
  if ($stmt->execute([$title, $status, $description, $id])) {
    set_flash('success', 'Delivery updated successfully.', 'success');
  } else {
    set_flash('error', 'Failed to update delivery.', 'error');
  }
}

header("Location: my_listings.php");
exit;
?>