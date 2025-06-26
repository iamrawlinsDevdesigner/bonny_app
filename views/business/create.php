<?php
include '../../includes/db.php';
include '../../includes/flash.php';

if (!isset($_SESSION['user'])) {
  set_flash('login', 'You must be logged in to add a business.', 'error');
  header('Location: ../auth/login.php');
  exit;
}
?>

<h2>Add Your Business</h2>
<form id="businessForm" enctype="multipart/form-data">
  <input type="text" name="name" placeholder="Business Name" required><br>
  <input type="text" name="category" placeholder="Category (e.g. Food, Laundry)" required><br>
  <textarea name="description" placeholder="Description" required></textarea><br>
  <input type="text" name="phone" placeholder="Phone" required><br>
  <input type="text" name="address" placeholder="Address" required><br>
  <input type="file" name="image" accept="image/*" required><br>
  <button type="submit">Submit</button>
</form>

<div id="response"></div>

<!-- ✅ jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ AJAX logic -->
<script>
  $('#businessForm').on('submit', function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    $.ajax({
      url: '../../controllers/business_handler.php',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function(res) {
        $('#response').html(res);
      },
      error: function(xhr, status, error) {
        console.log("AJAX Error:", error);
        $('#response').html("<div class='flash error'>Something went wrong.</div>");
      }
    });
  });
</script>
