<?php include '../../includes/db.php'; include '../../includes/flash.php'; ?>
<form method="POST" action="../../controllers/login_handler.php">
  <input type="email" name="email" placeholder="Email" required><br>
  <input type="password" name="password" placeholder="Password" required><br>
  <button type="submit">Login</button>
</form>
<?php display_flash('login'); ?>
