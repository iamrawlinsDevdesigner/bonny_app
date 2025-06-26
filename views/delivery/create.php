<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['user'])) {
  set_flash('login', 'Please log in to request delivery.', 'error');
  header('Location: ../auth/login.php');
  exit;
}
?>

<h2>Request a Delivery</h2>
<form id="deliveryForm">
  <input type="text" name="title" placeholder="e.g. Deliver food from market" required><br>
  <textarea name="description" placeholder="Delivery details..." required></textarea><br>
  <button type="submit">Submit</button>
</form>

<div id="delivery-response"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#deliveryForm').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();

    $.post('../../controllers/delivery_handler.php', formData, function(res) {
      $('#delivery-response').html(res);
      $('#deliveryForm')[0].reset();
    });
  });
</script>
