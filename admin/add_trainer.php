<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('admin');
$admin_name = $_SESSION['full_name'];

// Handle Form Submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $specialization = $_POST['specialization'] ?? '';
    $experience_years = $_POST['experience_years'] ?? 0;
    $certification = $_POST['certification'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    $username = strtolower(str_replace(' ', '_', $full_name)) . rand(10,99); // Generate username
    
    if (empty($full_name) || empty($email) || empty($password)) {
        $error_message = "Name, Email and Password are required.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // 1. Create User
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, 'trainer')");
            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $email
            ]);
            $user_id = $pdo->lastInsertId();
            
            // 2. Create Trainer (matching actual schema: no gender, certification not certifications)
            $stmt = $pdo->prepare("INSERT INTO trainers (user_id, full_name, phone, specialization, experience_years, certification, bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id,
                $full_name,
                $phone,
                $specialization,
                $experience_years,
                $certification,
                $bio
            ]);
            
            $pdo->commit();
            $success_message = "Trainer added successfully! Username: " . $username;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Error adding trainer: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Trainer Management - Pranayom</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: sans-serif;
    }

    body {
      background-color: #121712;
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
      margin-bottom: 40px;
    }

    h2 {
      font-size: 18px;
      margin-bottom: 30px;
      color: #ddd;
    }

    /* Form Styling */
    .form-container {
      max-width: 500px;
    }

    .input-group {
      margin-bottom: 25px;
    }

    .input-group label {
      display: block;
      font-size: 14px;
      margin-bottom: 8px;
      color: #ddd;
    }

    .input-field {
      width: 100%;
      background-color: #1f261f;
      border: 1px solid #333;
      padding: 15px;
      border-radius: 5px;
      color: white;
      font-size: 14px;
    }

    .input-field::placeholder {
      color: #666;
    }

    .input-field:focus {
      outline: none;
      border-color: #00d26a;
    }

    select.input-field {
      cursor: pointer;
    }

    /* Button */
    .btn-submit {
      background-color: #00d26a;
      color: black;
      font-weight: bold;
      border: none;
      padding: 15px 40px;
      border-radius: 5px;
      font-size: 14px;
      cursor: pointer;
      margin-top: 20px;
    }

    .btn-submit:hover {
      background-color: #00b85a;
    }

    textarea.input-field {
      min-height: 80px;
      resize: vertical;
      font-family: sans-serif;
    }
  </style>
</head>

<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Trainer Management</h1>
    <h2>Add New Trainer</h2>

    <?php if ($success_message): ?>
      <div style="background-color: #22c55e; color: black; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
          <?php echo $success_message; ?>
      </div>
    <?php endif; ?>
    <?php if ($error_message): ?>
      <div style="background-color: #ef4444; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
          <?php echo $error_message; ?>
      </div>
    <?php endif; ?>

    <div class="form-container">
      <form method="POST" action="">
      <div class="input-group">
        <label>Full Name</label>
        <input type="text" name="full_name" class="input-field" placeholder="Enter trainer's full name" required />
      </div>

      <div class="input-group">
        <label>Contact Number</label>
        <input type="text" name="phone" class="input-field" placeholder="Enter contact number" />
      </div>

      <div class="input-group">
        <label>Email Address</label>
        <input type="email" name="email" class="input-field" placeholder="Enter email address" required />
      </div>

      <div class="input-group">
        <label>Password</label>
        <input type="password" name="password" class="input-field" placeholder="Enter password" required />
      </div>

      <div class="input-group">
        <label>Specialization</label>
        <input type="text" name="specialization" class="input-field" placeholder="e.g., Yoga, Pilates, Strength Training" />
      </div>

      <div class="input-group">
        <label>Experience (Years)</label>
        <input type="number" name="experience_years" class="input-field" placeholder="Enter years of experience" min="0" />
      </div>

      <div class="input-group">
        <label>Certification</label>
        <input type="text" name="certification" class="input-field" placeholder="Enter certification details" />
      </div>

      <div class="input-group">
        <label>Bio</label>
        <textarea name="bio" class="input-field" placeholder="Enter trainer bio" rows="3"></textarea>
      </div>

      <button type="submit" class="btn-submit">Add Trainer</button>
      </form>
    </div>
  </div>
</body>

</html>
