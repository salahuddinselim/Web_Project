<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];

// Get fresh trainer data
$trainer = getTrainerProfile($trainer_id);
$trainer_name = $trainer['full_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trainer Profile</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      background-color: #0e0e0e;
      color: #ffffff;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .mainContainer {
      display: flex;
      width: 95%;
      height: 90vh;
      background-color: #121212;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.5);
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #0d110d;
      padding: 30px 20px;
      border-right: 1px solid #222;
    }

    .user-profile {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 50px;
    }

    .avatar {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background-color: #333;
      overflow: hidden;
    }

    .avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .user-name {
      font-weight: bold;
      font-size: 16px;
    }

    .menu {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .menu-item {
      display: flex;
      align-items: center;
      gap: 15px;
      color: #aaa;
      text-decoration: none;
      font-size: 14px;
      padding: 10px;
      border-radius: 5px;
      transition: 0.3s;
    }

    .menu-item:hover,
    .menu-item.active {
      background-color: #1f261f;
      color: white;
    }

    .icon {
      width: 20px;
      text-align: center;
    }

    /* Main Content Styling */
    .mainContent {
      flex: 1;
      padding: 40px;
      overflow-y: auto;
      background-color: #0e0e0e;
    }

    .page-header {
      margin-bottom: 30px;
    }

    .page-header h2 {
      margin: 0;
      font-size: 24px;
      font-weight: bold;
    }

    .page-header p {
      margin: 5px 0 0 0;
      color: #a1a1aa;
      font-size: 14px;
    }

    /* Profile Top Layout */
    .profile-header-large {
      display: flex;
      align-items: center;
      gap: 25px;
      margin-bottom: 40px;
    }

    .profile-pic-large {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      /* Gradient representation from image */
      background: linear-gradient(135deg, #e0e0e0 0%, #333333 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden;
    }

    .profile-pic-large img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-details-large h3 {
      margin: 0;
      font-size: 22px;
      color: #ffffff;
    }

    .profile-details-large span {
      display: block;
      font-size: 14px;
      color: #a1a1aa;
      margin-top: 5px;
    }

    .profile-details-large .id-text {
      font-size: 12px;
      color: #4ade80;
      margin-top: 2px;
    }

    /* Forms */
    .section-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 20px;
      color: #ffffff;
      margin-top: 10px;
    }

    .form-section {
      margin-bottom: 40px;
      max-width: 600px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      color: #e5e5e5;
      font-size: 14px;
      font-weight: 600;
    }

    .form-control {
      width: 100%;
      padding: 12px 15px;
      background-color: #1f3b26;
      /* Dark green background */
      border: none;
      border-radius: 6px;
      color: #a0bba5;
      font-size: 14px;
      outline: none;
      box-sizing: border-box;
      font-family: inherit;
    }

    .form-control::placeholder {
      color: #a0bba5;
      opacity: 0.6;
    }

    .form-control:focus {
      background-color: #25462d;
    }

    textarea.form-control {
      resize: none;
      height: 100px;
    }

    .action-btn {
      background-color: #22c55e;
      color: #000000;
      border: none;
      padding: 12px 25px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      font-size: 14px;
      float: right;
      margin-top: 10px;
    }

    .action-btn:hover {
      background-color: #4ade80;
    }

    .clearfix::after {
      content: "";
      clear: both;
      display: table;
    }
  </style>
</head>

<body>
  <div class="mainContainer">
    <?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="mainContent">
      <div class="page-header">
        <h2>Trainer Profile & Settings</h2>
        <p>Manage your profile information and settings.</p>
      </div>

      <div class="profile-header-large">
        <div class="profile-pic-large">
          <img
            src="<?php echo !empty($trainer['profile_picture']) && file_exists(__DIR__ . '/../uploads/profile_pics/' . $trainer['profile_picture'])
                    ? '../uploads/profile_pics/' . htmlspecialchars($trainer['profile_picture'])
                    : '../images/default_avatar.jpg'; ?>"
            alt=""
            style="width: 100%; height: 100%; object-fit: cover;" />
        </div>
        <div class="profile-details-large">
          <h3><?php echo htmlspecialchars($trainer_name); ?></h3>
          <span><?php echo htmlspecialchars($trainer['specialization'] ?? 'Fitness Trainer'); ?></span>
          <span class="id-text">ID: <?php echo str_pad($trainer_id, 6, '0', STR_PAD_LEFT); ?></span>
        </div>
      </div>

      <!-- Edit Profile Information -->
      <div class="section-title">Edit Profile Information</div>

      <?php if (isset($_SESSION['success_message'])): ?>
        <div style="color: #4ade80; margin-bottom: 15px;"><?php echo $_SESSION['success_message'];
                                                          unset($_SESSION['success_message']); ?></div>
      <?php endif; ?>
      <?php if (isset($_SESSION['error_message'])): ?>
        <div style="color: #ff4d4d; margin-bottom: 15px;"><?php echo $_SESSION['error_message'];
                                                          unset($_SESSION['error_message']); ?></div>
      <?php endif; ?>

      <!-- Profile Picture Upload Form -->
      <form action="../handlers/upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="form-section clearfix" style="border: 1px solid #333; padding: 20px; border-radius: 6px;">
        <div class="form-group">
          <label class="form-label">Change Profile Picture</label>
          <input type="file" name="profile_picture" class="form-control" style="background: transparent;" required accept="image/*">
        </div>
        <button type="submit" class="action-btn">Upload Photo</button>
      </form>
      <div class="form-section clearfix">
        <form action="../handlers/trainer/update_profile.php" method="POST">
          <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($trainer['full_name']); ?>" required />
          </div>
          <div class="form-group">
            <label class="form-label">Contact Number</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($trainer['phone'] ?? ''); ?>" />
          </div>
          <div class="form-group">
            <label class="form-label">Specialization</label>
            <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($trainer['specialization'] ?? ''); ?>" />
          </div>
          <div class="form-group">
            <label class="form-label">Experience (Years)</label>
            <input type="number" name="experience_years" class="form-control" value="<?php echo htmlspecialchars($trainer['experience_years'] ?? 0); ?>" min="0" />
          </div>
          <div class="form-group">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-control"><?php echo htmlspecialchars($trainer['bio'] ?? ''); ?></textarea>
          </div>
          <button type="submit" class="action-btn">Update Profile</button>
        </form>

        <!-- Change Password -->
        <div class="section-title">Change Password</div>
        <div class="form-section clearfix">
          <form action="../handlers/trainer/change_password.php" method="POST">
            <div class="form-group">
              <label class="form-label">Current Password</label>
              <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required />
            </div>
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input type="password" name="new_password" class="form-control" placeholder="Enter new password" required />
            </div>
            <div class="form-group">
              <label class="form-label">Confirm New Password</label>
              <input type="password" name="confirm_password" class="form-control" placeholder="Confirm new password" required />
            </div>
            <button type="submit" class="action-btn">Change Password</button>
          </form>
        </div>
      </div>
    </div>
</body>

</html>