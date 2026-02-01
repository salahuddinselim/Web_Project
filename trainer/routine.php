<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$trainer_name = $_SESSION['full_name'];


$members = getTrainerMembers($trainer_id);


$success_message = '';
$error_message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $member_id = $_POST['member_id'] ?? '';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $exercises_json = $_POST['exercises_json'] ?? '[]';
        
        $selected_day = intval($_POST['target_day'] ?? 1);
        
        $monday = date('Y-m-d', strtotime('monday this week'));
        $scheduled_date = date('Y-m-d', strtotime("+" . ($selected_day - 1) . " days", strtotime($monday)));
    
    if (empty($member_id) || empty($title)) {
        $error_message = "Please select a member and provide a routine title.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO routines (member_id, trainer_id, title, description, exercises, is_active, scheduled_date) VALUES (?, ?, ?, ?, ?, 1, ?)");
            $stmt->execute([
                $member_id,
                $trainer_id,
                $title,
                $description,
                $exercises_json,
                $scheduled_date
            ]);
            $success_message = "Routine created successfully!";
        } catch (PDOException $e) {
            $error_message = "Error creating routine: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Routine</title>
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

      .section-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
        margin-top: 30px;
        color: #e5e5e5;
      }

      /* Form Styling */
      .form-group {
        margin-bottom: 20px;
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

      /* Exercise Block Styling */
      .exercise-block {
        margin-bottom: 30px;
        padding-bottom: 20px;
      }

      .exercise-count {
        color: #4ade80;
        font-size: 12px;
        margin-bottom: 15px;
        display: none;
        /* Hidden for refined look, or can be shown */
      }

      /* Buttons */
      .btn-container {
        margin-top: 40px;
        max-width: 600px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-items: flex-end;
      }

      .add-exercise-btn {
        background-color: #2d4a36;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        font-size: 14px;
      }

      .add-exercise-btn:hover {
        background-color: #3d5a46;
      }

      .save-btn {
        background-color: #22c55e;
        color: #000000;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        font-size: 14px;
        width: 200px;
      }

      .save-btn:hover {
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
          <h2>Create Custom Yoga Routine</h2>
        </div>

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

        <form method="POST" action="" id="routineForm">
        <input type="hidden" name="exercises_json" id="exercises_json">

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
          <label class="form-label">Routine Title</label>
          <input
            type="text"
            name="title"
            class="form-control"
            placeholder="e.g., Morning Flow"
            required
          />
        </div>

        <div class="form-group">
          <label class="form-label">Routine Description</label>
          <textarea name="description" class="form-control" rows="4"></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Target Day (7-Day Cycle)</label>
          <select name="target_day" class="form-control" required>
            <?php 
              $days_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
              for($i = 1; $i <= 7; $i++): ?>
                <option value="<?php echo $i; ?>">Day <?php echo $i; ?> - <?php echo $days_labels[$i-1]; ?></option>
            <?php endfor; ?>
          </select>
          <small style="color: #888; font-size: 11px;">Mapped to the current week (<?php echo date('M d', strtotime('monday this week')); ?> to <?php echo date('M d', strtotime('sunday this week')); ?>)</small>
        </div>

        <div class="section-title">Meditations</div>

        <div id="exercises-container">
          <!-- Initial Exercise Block -->
          <div class="exercise-block">
            <div class="form-group">
              <label class="form-label">Meditation Name</label>
              <input
                type="text"
                class="form-control"
                placeholder="e.g., Sun Salutation"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Meditation Description</label>
              <textarea class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Duration (minutes)</label>
              <input type="text" class="form-control" placeholder="e.g., 5" />
            </div>
            <div class="form-group">
              <label class="form-label">Sets/Reps</label>
              <input
                type="text"
                class="form-control"
                placeholder="e.g., 3 sets of 10 reps"
              />
            </div>
            <div class="form-group">
              <label class="form-label">Notes/Instructions</label>
              <textarea class="form-control" rows="3"></textarea>
            </div>
          </div>
        </div>

        <div class="btn-container">
          <button
            type="button"
            class="add-exercise-btn"
            onclick="addExercise()"
          >
            Add Exercise
          </button>
          <button type="button" class="save-btn" onclick="saveRoutine()">
            Save Yoga Routine
          </button>
        </div>
        </form>
      </div>
    </div>

    <script>
      function addExercise() {
        const container = document.getElementById("exercises-container");
        const exerciseBlock = document.createElement("div");
        exerciseBlock.className = "exercise-block";

        // Add a visual separator or margin
        exerciseBlock.style.marginTop = "30px";
        exerciseBlock.innerHTML = `
                <div style="border-top: 1px solid #1f2b23; margin-bottom: 20px;"></div>
                <div class="form-group">
                    <label class="form-label">Exercise Name</label>
                    <input type="text" class="form-control ex-name" placeholder="e.g., Sun Salutation">
                </div>
                <div class="form-group">
                    <label class="form-label">Exercise Description</label>
                    <textarea class="form-control ex-desc" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Duration (minutes)</label>
                    <input type="text" class="form-control ex-duration" placeholder="e.g., 5">
                </div>
                <div class="form-group">
                    <label class="form-label">Sets/Reps</label>
                    <input type="text" class="form-control ex-reps" placeholder="e.g., 3 sets of 10 reps">
                </div>
                <div class="form-group">
                    <label class="form-label">Notes/Instructions</label>
                    <textarea class="form-control ex-notes" rows="3"></textarea>
                </div>
            `;

        container.appendChild(exerciseBlock);
        // Optionally scroll to the new block
        exerciseBlock.scrollIntoView({ behavior: "smooth" });
      }

      function saveRoutine() {
        const exerciseBlocks = document.querySelectorAll('.exercise-block');
        const exercises = [];

        exerciseBlocks.forEach(block => {
            const name = block.querySelector('input[type="text"]:nth-of-type(1), .ex-name')?.value;
            // The initial block doesn't have classes like ex-name, so we use selectors robustly
            // Wait, I didn't add classes to the initial block in HTML replacement.
            // I should rely on querySelector order or add classes to initial block too.
            // Let's use form-groups order.
            const inputs = block.querySelectorAll('.form-control');
            // Index 0: Name, 1: Desc, 2: Duration, 3: Reps, 4: Notes
            if (inputs.length >= 5) {
                exercises.push({
                    name: inputs[0].value,
                    description: inputs[1].value,
                    duration: inputs[2].value,
                    reps: inputs[3].value,
                    notes: inputs[4].value
                });
            }
        });

        document.getElementById('exercises_json').value = JSON.stringify(exercises);
        document.getElementById('routineForm').submit();
      }
    </script>
  </body>
</html>
