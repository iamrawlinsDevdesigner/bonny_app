// delete_delivery.php
<?php
require_once '../../includes/db.php';
require_once '../../includes/flash.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM deliveries WHERE id = ?");
if ($stmt->execute([$id])) {
  set_flash('success', 'Delivery request deleted.', 'success');
} else {
  set_flash('error', 'Failed to delete delivery.', 'error');
}

header("Location: my_listings.php");
exit;
?>
