<?php
session_start();
include '../../includes/db.php';
include '../../includes/flash.php';

if (!isset($_SESSION['user'])) {
  set_flash('login', 'Please log in to manage your profile.', 'error');
  header('Location: ../auth/login.php');
  exit;
}

$user_id = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Profile Management</title>
  <link rel="stylesheet" href="../../assets/css/style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/css/intlTelInput.min.css" />
  <style>
    .profile-section {
      max-width: 700px;
      margin: 30px auto;
      background: #fff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px #ccc;
    }
    .form-group {
      margin-bottom: 15px;
    }
    .form-group label {
      display: block;
      margin-bottom: 5px;
    }
    .form-group input, .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
    }
    .image-preview-container {
      position: relative;
      display: inline-block;
      margin-bottom: 15px;
    }
    .preview-img {
      width: 120px;
      height: 120px;
      object-fit: cover;
      border-radius: 8px;
      border: 1px solid #ddd;
    }
    .remove-btn {
      position: absolute;
      top: -8px;
      right: -8px;
      background: #ff4d4d;
      color: white;
      border: none;
      border-radius: 50%;
      width: 24px;
      height: 24px;
      text-align: center;
      line-height: 24px;
      font-weight: bold;
      text-decoration: none;
    }
  </style>
</head>
<body>

<?php if (has_flash('success')): ?>
  <div class="flash success"> <?= get_flash('success') ?> </div>
<?php endif; ?>
<?php if (has_flash('error')): ?>
  <div class="flash error"> <?= get_flash('error') ?> </div>
<?php endif; ?>

<div class="profile-section">
  <h2>Manage Your Profile</h2>
  <form action="../../controllers/update_profile.php" method="POST" enctype="multipart/form-data">

    <div class="form-group">
      <label>Phone Number</label>
      <input id="phone" type="tel" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Profile Description</label>
      <textarea name="description"><?= htmlspecialchars($user['description'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
      <label>Address</label>
      <input type="text" name="address" value="<?= htmlspecialchars($user['address'] ?? '') ?>">
    </div>

    <div class="form-group">
      <label>Profile Image</label><br>
      <?php if (!empty($user['profile_image'])): ?>
        <div class="image-preview-container">
          <img src="../../assets/images/<?= htmlspecialchars($user['profile_image']) ?>" class="preview-img">
          <a href="../../controllers/delete_profile_image.php" class="remove-btn" onclick="return confirm('Remove this profile image?')">×</a>
        </div>
      <?php endif; ?>
      <input type="file" name="profile_image" accept="image/*">
    </div>

    <hr>

    <div class="form-group">
      <label>Old Password</label>
      <input type="password" name="old_password">
    </div>

    <div class="form-group">
      <label>New Password</label>
      <input type="password" name="new_password">
    </div>

    <hr>

    <div class="form-group">
      <label>Current Email</label>
      <input type="email" name="current_email" value="<?= htmlspecialchars($user['email']) ?>" readonly>
    </div>

    <div class="form-group">
      <label>New Email</label>
      <input type="email" name="new_email">
    </div>

    <button type="submit">Update Profile</button>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/intlTelInput.min.js"></script>
<script>
  const input = document.querySelector("#phone");
  window.intlTelInput(input, {
    initialCountry: "auto",
    geoIpLookup: callback => {
      fetch('https://ipinfo.io?token=YOUR_TOKEN')
        .then(resp => resp.json())
        .then(resp => callback(resp.country))
        .catch(() => callback("ng"));
    },
    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@17/build/js/utils.js",
  });
</script>

</body>
</html>
