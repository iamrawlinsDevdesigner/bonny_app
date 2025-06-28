// delete_job.php
<?php
require_once '../../includes/db.php';
require_once '../../includes/flash.php';
session_start();

$id = $_GET['id'] ?? 0;
$stmt = $pdo->prepare("DELETE FROM jobs WHERE id = ?");
if ($stmt->execute([$id])) {
  set_flash('success', 'Job deleted successfully.', 'success');
} else {
  set_flash('error', 'Failed to delete job.', 'error');
}

header("Location: my_listings.php");
exit;
?>
