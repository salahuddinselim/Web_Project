<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$trainer_name = $_SESSION['full_name'];

// Get assigned members
$members = getTrainerMembers($trainer_id);

// Handle Form Submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? '';
    $plan_date = $_POST['plan_date'] ?? date('Y-m-d');
    $meals = [
        'breakfast' => $_POST['breakfast'] ?? '',
        'lunch' => $_POST['lunch'] ?? '',
        'dinner' => $_POST['dinner'] ?? ''
    ];
    
    if (empty($member_id)) {
        $error_message = "Please select a member.";
    } else {
        try {
            $pdo->beginTransaction();
            
            foreach ($meals as $time => $items) {
                if (!empty($items)) {
                    $stmt = $pdo->prepare("INSERT INTO diet_plans (member_id, trainer_id, meal_name, meal_time, food_items, created_by, plan_date) VALUES (?, ?, ?, ?, ?, 'trainer', ?)");
                    $stmt->execute([
                        $member_id,
                        $trainer_id,
                        ucfirst($time), // Meal name
                        $time,          // Meal time (enum)
                        $items,
                        $plan_date
                    ]);
                }
            }
            
            $pdo->commit();
            $success_message = "Diet plan saved successfully!";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_message = "Error saving plan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Member Details - Diet Plan</title>
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
      margin-bottom: 5px;
    }

    .page-header p {
      margin: 0;
      color: #6a7a70;
      font-size: 14px;
    }

    .section-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 25px;
      margin-top: 10px;
    }

    /* Form Styling */
    .form-group {
      margin-bottom: 25px;
      max-width: 600px;
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      color: #e5e5e5;
      font-size: 14px;
      font-weight: 500;
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
      opacity: 0.7;
    }

    .form-control:focus {
      background-color: #25462d;
    }

    textarea.form-control {
      resize: none;
    }

    /* Day Selector */
    .day-selector-group {
      margin-top: 40px;
      margin-bottom: 20px;
      width: 600px;
    }

    .day-label {
      font-weight: bold;
      color: #ffffff;
      margin-bottom: 15px;
      display: block;
    }

    /* Save Button */
    .btn-save-container {
      margin-top: 40px;
      max-width: 600px;
      display: flex;
      justify-content: flex-end;
    }

    .btn-save {
      background-color: #22c55e;
      color: #000000;
      border: none;
      padding: 10px 30px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      font-size: 14px;
    }

    .btn-save:hover {
      background-color: #4ade80;
    }
  </style>
</head>

<body>
  <div class="mainContainer">
<?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="mainContent">
      <div class="page-header">
        <h2>Member Details</h2>
        <p>View and manage member information, health progress, and plans.</p>
      </div>

      <div class="section-title">Create Custom Diet Plan</div>

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

      <form method="POST" action="">
        <div class="form-group">
          <label class="form-label">Member Name</label>
          <select name="member_id" class="form-control" required>
            <option value="">Select Member</option>
            <?php foreach ($members as $member): ?>
                <option value="<?php echo $member['member_id']; ?>"><?php echo htmlspecialchars($member['full_name']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
            <label class="form-label">Plan Date</label>
            <input type="date" name="plan_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="form-group">
          <label class="form-label">Description</label>
          <textarea class="form-control" rows="4"></textarea>
        </div>

        <div class="day-selector-group">
          <label class="day-label" style="font-size: 16px">Select Plan Duration</label>
          <select class="form-control" id="durationSelect" onchange="loadDurationData()">
            <option value="daily">Daily</option>
            <option value="weekly">Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Breakfast</label>
          <textarea name="breakfast" class="form-control" rows="3" id="breakfastInput" placeholder="Enter breakfast plan"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Lunch</label>
          <textarea name="lunch" class="form-control" rows="3" id="lunchInput" placeholder="Enter lunch plan"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Dinner</label>
          <textarea name="dinner" class="form-control" rows="3" id="dinnerInput" placeholder="Enter dinner plan"></textarea>
        </div>

        <div class="btn-save-container">
          <button type="submit" class="btn-save">
            Save Plan
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Simple data object to simulate storing plans for different durations
    const dietData = {
      daily: { breakfast: "", lunch: "", dinner: "" },
      weekly: { breakfast: "", lunch: "", dinner: "" },
      monthly: { breakfast: "", lunch: "", dinner: "" },
    };

    let currentDuration = "daily";

    function loadDurationData() {
      // Save current inputs to memory before switching
      saveCurrentInputsToMemory();

      // Switch current duration
      const select = document.getElementById("durationSelect");
      currentDuration = select.value;

      // Load data for new duration
      document.getElementById("breakfastInput").value =
        dietData[currentDuration].breakfast;
      document.getElementById("lunchInput").value =
        dietData[currentDuration].lunch;
      document.getElementById("dinnerInput").value =
        dietData[currentDuration].dinner;
    }

    function saveCurrentInputsToMemory() {
      dietData[currentDuration].breakfast =
        document.getElementById("breakfastInput").value;
      dietData[currentDuration].lunch =
        document.getElementById("lunchInput").value;
      dietData[currentDuration].dinner =
        document.getElementById("dinnerInput").value;
    }

    function savePlan() {
      saveCurrentInputsToMemory();
      console.log("Final Plan Data:", dietData);
      alert("Diet Plan Saved for " + currentDuration + "!");
    }
  </script>
</body>

</html>
