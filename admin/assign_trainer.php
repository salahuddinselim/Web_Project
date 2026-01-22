<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('admin');
$admin_name = $_SESSION['full_name'];

// Get all trainers and members for dropdowns
$trainers = getAllTrainers();
$members = getAllMembers();

// Handle Form Submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? '';
    
    if (empty($member_id) || empty($trainer_id)) {
        $error_message = "Please select both member and trainer.";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE members SET trainer_id = ? WHERE member_id = ?");
            $stmt->execute([$trainer_id, $member_id]);
            $success_message = "Trainer assigned successfully!";
            
            // Refresh members list
            $members = getAllMembers();
        } catch (PDOException $e) {
            $error_message = "Error assigning trainer: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Assign Trainer - Pranayom</title>
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
        margin-bottom: 50px;
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

      /* Table Styling */
      .table-section {
        margin-top: 50px;
      }

      .table-container {
        background-color: #1a201a;
        border-radius: 8px;
        padding: 20px;
      }

      .trainer-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
      }

      .trainer-table th {
        text-align: left;
        border-bottom: 1px solid #333;
        padding: 10px 0;
        color: #aaa;
        font-weight: normal;
      }

      .trainer-table td {
        padding: 15px 0;
        border-bottom: 1px solid #2a2a2a;
      }

      .trainer-table tr:last-child td {
        border-bottom: none;
      }

      .btn-edit {
        background-color: transparent;
        color: #00d26a;
        border: 1px solid #00d26a;
        padding: 5px 15px;
        border-radius: 3px;
        font-size: 12px;
        cursor: pointer;
      }

      .btn-edit:hover {
        background-color: #00d26a;
        color: black;
      }
    </style>
  </head>
  <body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
    <h1>Assign Trainers</h1>
    <h2>Assign Trainer to Member</h2>

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
          <label>Member Name</label>
          <select name="member_id" class="input-field" required>
            <option value="">Select member</option>
            <?php foreach ($members as $member): ?>
              <option value="<?php echo $member['member_id']; ?>">
                <?php echo htmlspecialchars($member['full_name']); ?>
                <?php if ($member['trainer_name']): ?>
                  (Currently: <?php echo htmlspecialchars($member['trainer_name']); ?>)
                <?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label>Trainer Name</label>
          <select name="trainer_id" class="input-field" required>
            <option value="">Select trainer</option>
            <?php foreach ($trainers as $trainer): ?>
              <option value="<?php echo $trainer['trainer_id']; ?>">
                <?php echo htmlspecialchars($trainer['full_name']); ?>
                <?php if ($trainer['specialization']): ?>
                  - <?php echo htmlspecialchars($trainer['specialization']); ?>
                <?php endif; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="btn-submit">Assign Trainer</button>
        </form>
      </div>

      <div class="table-section">
        <h2>Current Trainer Assignments</h2>
        <div class="table-container">
          <table class="trainer-table">
            <thead>
              <tr>
                <th width="30%">Member Name</th>
                <th width="30%">Assigned Trainer</th>
                <th width="40%">Specialization</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($members)): ?>
                <tr>
                  <td colspan="3" style="text-align: center; color: #666;">No members found</td>
                </tr>
              <?php else: ?>
                <?php foreach ($members as $member): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($member['full_name']); ?></td>
                    <td><?php echo $member['trainer_name'] ? htmlspecialchars($member['trainer_name']) : '<span style="color: #666;">Not assigned</span>'; ?></td>
                    <td><?php 
                      if ($member['trainer_name']) {
                        // Find trainer specialization
                        $trainer_spec = '';
                        foreach ($trainers as $t) {
                          if ($t['full_name'] === $member['trainer_name']) {
                            $trainer_spec = $t['specialization'] ?? 'General Fitness';
                            break;
                          }
                        }
                        echo htmlspecialchars($trainer_spec);
                      } else {
                        echo '<span style="color: #666;">-</span>';
                      }
                    ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </body>

</html>
