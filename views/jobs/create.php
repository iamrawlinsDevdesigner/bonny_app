<?php
include '../../includes/db.php';
include '../../includes/flash.php';
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['user'])) {
  set_flash('login', 'You must be logged in to post a job.', 'error');
  header('Location: ../auth/login.php');
  exit;
}
?>

<h2>Post a Job</h2>
<form id="jobForm">
  <input type="text" name="title" placeholder="Job Title" required><br>
  <input type="text" name="company" placeholder="Company Name" required><br>
  <input type="text" name="location" placeholder="Location (e.g. Bonny Island)" required><br>
  <select name="type" required>
    <option value="">Select Job Type</option>
    <option value="Full-time">Full-time</option>
    <option value="Part-time">Part-time</option>
    <option value="Contract">Contract</option>
  </select><br>
  <textarea name="description" placeholder="Job Description" required></textarea><br>
  <input type="email" name="contact_email" placeholder="Contact Email" required><br>
  <button type="submit">Submit</button>
</form>

<div id="job-response"></div>

<!-- ✅ jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- ✅ AJAX -->
<script>
  $('#jobForm').on('submit', function(e) {
    e.preventDefault();
    let formData = $(this).serialize();

    $.post('../../controllers/job_handler.php', formData, function(res) {
      $('#job-response').html(res);
      $('#jobForm')[0].reset();
    });
  });
</script>
