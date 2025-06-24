<?php include '../../includes/db.php'; include '../../includes/flash.php'; ?>
<form method="POST" action="../../controllers/register_handler.php">
  <input type="text" name="name" placeholder="Full Name" required><br>
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Register</button>
</form>
<?php display_flash('register'); ?>
