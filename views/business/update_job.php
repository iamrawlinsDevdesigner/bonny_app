// update_job.php
<?php
require_once '../../includes/db.php';
require_once '../../includes/flash.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $type = $_POST['type'];
  $description = $_POST['description'];

  $stmt = $pdo->prepare("UPDATE jobs SET title = ?, type = ?, description = ? WHERE id = ?");
  if ($stmt->execute([$title, $type, $description, $id])) {
    set_flash('success', 'Job updated successfully.', 'success');
  } else {
    set_flash('error', 'Failed to update job.', 'error');
  }
}

header("Location: my_listings.php");
exit;
?>