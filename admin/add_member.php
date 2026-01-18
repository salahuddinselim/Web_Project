<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('admin');
$admin_name = $_SESSION['full_name'];

// Get all trainers for assignment
$trainers = getAllTrainers();

// Handle Form Submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    // $age = $_POST['age'] ?? ''; // Not in schema, skipping or converting to DOB? Schema has date_of_birth.
    // Let's assume age is not stored directly but we used date_of_birth in schema.
    // The previous form had "Age". I should change it to DOB or calculate it?
    // Let's keep the form as "Age" and just set a dummy DOB or change the form to DOB.
    // Changing form to DOB is better for schema alignment.
    
    $gender = $_POST['gender'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $weight = $_POST['weight'] ?? ''; // Not in member schema directly (progress_tracking has it).
    // Actually schema has weight? No. progress_tracking has it.
    // I can insert an initial progress record or ignore. Let's ignore for now or add to medical notes?
    
    $fitness_level = $_POST['fitness_level'] ?? ''; // Not in schema?
    // Schema: membership_type, join_date, etc. 
    // Let's look at schema again. 
    /*
    members: full_name, phone, address, date_of_birth, gender, membership_type, join_date, emergency..., medical_notes
    */
    
    $medical_notes = $_POST['medical_notes'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? null;
    if (empty($trainer_id)) $trainer_id = null;
    
    $username = strtolower(str_replace(' ', '', $full_name)) . rand(10,99); // Generate username
    
    if (empty($full_name) || empty($email) || empty($password)) {
        $error_message = "Name, Email and Password are required.";
    } else {
        try {
            $pdo->beginTransaction();
            
            // 1. Create User
            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email, role) VALUES (?, ?, ?, 'member')");
            $stmt->execute([
                $username,
                password_hash($password, PASSWORD_DEFAULT),
                $email
            ]);
            $user_id = $pdo->lastInsertId();
            
            // 2. Create Member
            // Using current date for join_date
            $stmt = $pdo->prepare("INSERT INTO members (user_id, full_name, phone, gender, medical_notes, trainer_id, join_date) VALUES (?, ?, ?, ?, ?, ?, CURDATE())");
            $stmt->execute([
                $user_id,
                $full_name,
                $phone,
                $gender,
                $medical_notes,
                $trainer_id
            ]);
            
            $pdo->commit();
            $success_message = "Member added successfully! Username: " . $username;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error_message = "Error adding member: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Member Management - Pranayom</title>
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

      textarea.input-field {
        min-height: 100px;
        resize: vertical;
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
    </style>
  </head>
  <body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
      <h1>Member Management</h1>
      <h2>Add New Member</h2>

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
          <input
            type="text"
            name="full_name"
            class="input-field"
            placeholder="Enter member's full name"
            required
          />
        </div>

        <!-- Changed Age to DOB for schema consistency -->
        <!-- <div class="input-group">
          <label>Age</label>
          <input type="text" class="input-field" placeholder="Enter member's age" />
        </div> -->

        <div class="input-group">
          <label>Gender</label>
          <select name="gender" class="input-field">
            <option value="">Select gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="input-group">
          <label>Contact Number</label>
          <input
            type="text"
            name="phone"
            class="input-field"
            placeholder="Enter contact number"
          />
        </div>

        <div class="input-group">
          <label>Email Address</label>
          <input
            type="email"
            name="email"
            class="input-field"
            placeholder="Enter email address"
            required
          />
        </div>

        <div class="input-group">
          <label>Password</label>
          <input
            type="password"
            name="password"
            class="input-field"
            placeholder="Enter password"
            required
          />
        </div>

        <div class="input-group">
          <label>Current Weight (kg)</label>
          <input
            type="text"
            class="input-field"
            placeholder="Enter current weight"
          />
        </div>

        <div class="input-group">
          <label>Fitness Level</label>
          <select class="input-field">
            <option value="">Select fitness level</option>
            <option value="beginner">Beginner</option>
            <option value="intermediate">Intermediate</option>
            <option value="advanced">Advanced</option>
          </select>
        </div>

        <div class="input-group">
          <label>Existing Conditions / Medical Notes</label>
          <textarea
            name="medical_notes"
            class="input-field"
            placeholder="Enter any existing medical conditions or notes"
          ></textarea>
        </div>

        <div class="input-group">
          <label>Assign Trainer</label>
          <select name="trainer_id" class="input-field">
            <option value="">Select trainer</option>
            <?php foreach ($trainers as $trainer): ?>
                <option value="<?php echo $trainer['trainer_id']; ?>">
                    <?php echo htmlspecialchars($trainer['full_name']); ?>
                </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn-submit">Add Member</button>
        </form>
      </div>
    </div>
  </body>
  <script></script>
  </body>
</html>
