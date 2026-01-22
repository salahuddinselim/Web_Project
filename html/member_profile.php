<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];

// Get fresh member data
$member = getMemberProfile($member_id);
$member_name = $member['full_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Profile & Settings - Pranayom</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: sans-serif;
    }

    body {
      background-color: #121712;
      /* Very dark green/black */
      color: white;
      display: flex;
      min-height: 100vh;
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

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 40px 60px;
    }

    h1 {
      font-size: 32px;
      margin-bottom: 10px;
    }

    .page-subtitle {
      color: #888;
      font-size: 14px;
      margin-bottom: 40px;
    }

    /* Profile Header */
    .profile-header {
      display: flex;
      align-items: center;
      gap: 20px;
      margin-bottom: 40px;
    }

    .large-avatar {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      background: linear-gradient(145deg, #333, #111);
      overflow: hidden;
    }

    .large-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .profile-info h2 {
      font-size: 20px;
      margin-bottom: 5px;
    }

    .profile-details {
      font-size: 12px;
      color: #aaa;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    /* Forms */
    .settings-section {
      margin-bottom: 50px;
      max-width: 600px;
    }

    .settings-section h3 {
      font-size: 18px;
      margin-bottom: 20px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-size: 13px;
      color: #eee;
      margin-bottom: 8px;
      font-weight: bold;
    }

    .form-input,
    .form-textarea {
      width: 100%;
      background-color: #1f261f;
      /* Darker green bg */
      border: 1px solid #333;
      /* Dark green accent */
      padding: 12px 15px;
      border-radius: 5px;
      color: white;
      font-size: 14px;
      outline: none;
    }

    /* Greenish tint to inputs as per screenshot slightly */
    .form-input {
      background-color: #1a3a1a;
      border-color: #2a4a2a;
    }

    .form-textarea {
      background-color: #1a3a1a;
      border-color: #2a4a2a;
      resize: vertical;
      min-height: 100px;
    }

    .btn-update {
      background-color: #00d26a;
      /* Bright Green */
      color: black;
      border: none;
      padding: 10px 25px;
      border-radius: 5px;
      font-weight: bold;
      cursor: pointer;
      float: right;
    }

    .btn-update:hover {
      background-color: #00b05a;
    }

    .clearfix::after {
      content: "";
      clear: both;
      display: table;
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Your Profile & Settings</h1>
    <p class="page-subtitle">Manage your profile information and settings.</p>

    <div class="profile-header">
      <div class="large-avatar">
        <div style="width: 100%; height: 100%;">
          <img
            src="<?php echo !empty($member['profile_picture']) && file_exists(__DIR__ . '/../uploads/profile_pics/' . $member['profile_picture'])
                    ? '../uploads/profile_pics/' . htmlspecialchars($member['profile_picture'])
                    : '../images/default_avatar.jpg'; ?>"
            alt="Profile Picture"
            style="width: 100%; height: 100%; object-fit: cover;" />
        </div>
      </div>
      <div class="profile-info">
        <h2><?php echo htmlspecialchars($member_name); ?></h2>
        <div class="profile-details">
          <span>Joined <?php echo date('Y', strtotime($member['join_date'])); ?></span>
          <span>ID: #<?php echo str_pad($member_id, 5, '0', STR_PAD_LEFT); ?></span>
        </div>
      </div>
    </div>

    <div class="settings-section clearfix">
      <div class="settings-section clearfix">
        <h3>Edit Profile Information</h3>

        <?php if (isset($_SESSION['success_message'])): ?>
          <div style="color: #4ade80; margin-bottom: 15px;"><?php echo $_SESSION['success_message'];
                                                            unset($_SESSION['success_message']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
          <div style="color: #ff4d4d; margin-bottom: 15px;"><?php echo $_SESSION['error_message'];
                                                            unset($_SESSION['error_message']); ?></div>
        <?php endif; ?>

        <!-- Profile Picture Upload Form -->
        <form action="../handlers/upload_profile_picture.php" method="POST" enctype="multipart/form-data" class="form-group" style="background: #1a201a; padding: 15px; border-radius: 5px; border: 1px solid #333;">
          <label style="margin-bottom: 10px; display: block;">Change Profile Picture</label>
          <input type="file" name="profile_picture" class="form-input" style="padding: 10px;" required accept="image/*">
          <button type="submit" class="btn-update" style="margin-top: 10px;">Upload Photo</button>
          <div style="clear: both;"></div>
        </form>

        <form action="../handlers/member/update_profile.php" method="POST">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="full_name" class="form-input" value="<?php echo htmlspecialchars($member['full_name']); ?>" required />
          </div>

          <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="phone" class="form-input" value="<?php echo htmlspecialchars($member['phone'] ?? ''); ?>" />
          </div>

          <div class="form-group">
            <label>Address</label>
            <textarea name="address" class="form-textarea"><?php echo htmlspecialchars($member['address'] ?? ''); ?></textarea>
          </div>

          <button type="submit" class="btn-update">Update Profile</button>
        </form>
      </div>

      <div class="settings-section clearfix">
        <h3>Change Password</h3>

        <form action="../handlers/member/change_password.php" method="POST">
          <div class="form-group">
            <label>Current Password</label>
            <input
              type="password"
              name="current_password"
              class="form-input"
              placeholder="Enter current password"
              required />
          </div>

          <div class="form-group">
            <label>New Password</label>
            <input
              type="password"
              name="new_password"
              class="form-input"
              placeholder="Enter new password"
              required />
          </div>

          <div class="form-group">
            <label>Confirm New Password</label>
            <input
              type="password"
              name="confirm_password"
              class="form-input"
              placeholder="Confirm new password"
              required />
          </div>

          <button type="submit" class="btn-update">
            Change Password
          </button>
        </form>
      </div>
    </div>
</body>

</html>