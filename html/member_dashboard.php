<?php
/**
 * Member Dashboard
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';

// Require member login
requireLogin('member');

$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get today's routine
$routines = getMemberRoutines($member_id, true);
$todayRoutine = !empty($routines) ? $routines[0] : null;

// Get current diet plan
$dietPlans = getMemberDietPlans($member_id, date('Y-m-d'));

// Get recent progress
$progressData = getMemberProgress($member_id, 1);
$latestProgress = !empty($progressData) ? $progressData[0] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Member Dashboard - Pranayom</title>
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
      margin-bottom: 20px;
      color: #ddd;
    }

    /* Cards Layout */
    .content-section {
      margin-bottom: 50px;
    }

    .card-row {
      display: flex;
      gap: 40px;
      margin-bottom: 40px;
    }

    .info-card {
      background-color: #1a201a;
      border-radius: 12px;
      padding: 0;
      display: flex;
      width: 100%;
      overflow: hidden;
      justify-content: space-between;
    }

    .card-text {
      padding: 30px;
      flex: 1;
    }

    .card-text h3 {
      font-size: 18px;
      margin-bottom: 10px;
    }

    .card-text p {
      font-size: 13px;
      color: #aaa;
      margin-bottom: 30px;
      line-height: 1.5;
    }

    .buttons {
      display: flex;
      gap: 10px;
    }

    .btn {
      padding: 8px 20px;
      border-radius: 5px;
      font-size: 12px;
      font-weight: bold;
      cursor: pointer;
      border: none;
      text-decoration: none;
      display: inline-block;
    }

    .btn-view {
      background-color: #2a352a;
      color: white;
    }

    .btn-download {
      background-color: #00d26a;
      color: #003300;
    }

    .card-image {
      width: 250px;
      background-color: #ffe0b2;
      position: relative;
    }

    .card-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    /* Forms */
    .progress-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      max-width: 800px;
    }

    .input-group {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .input-group label {
      font-size: 13px;
      color: #eee;
    }

    .custom-input {
      background-color: #1f261f;
      border: 1px solid #333;
      padding: 15px;
      border-radius: 5px;
      color: white;
      font-size: 14px;
    }

    .custom-input::placeholder {
      color: #666;
    }

    .progress-actions {
      grid-column: span 2;
      display: flex;
      justify-content: flex-end;
      margin-top: 20px;
    }

    .btn-large {
      padding: 12px 30px;
      font-size: 14px;
    }

    .account-settings {
      margin-top: 50px;
    }

    /* Rating Styles */
    .rating-stars {
      display: flex;
      gap: 5px;
      font-size: 30px;
      color: #555;
      cursor: pointer;
      margin: 15px 0;
    }

    .rating-stars span.active {
      color: #ffd700;
    }

    .success-msg {
      color: #00d26a;
      font-size: 12px;
      margin-top: 10px;
      display: none;
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Welcome back, <?php echo htmlspecialchars($member_name); ?></h1>

    <!-- Today's Routine -->
    <div class="content-section">
      <h2>Today's Routine</h2>
      <div class="info-card">
        <div class="card-text">
          <?php if ($todayRoutine): ?>
            <h3><?php echo htmlspecialchars($todayRoutine['title']); ?></h3>
            <p><?php echo htmlspecialchars($todayRoutine['description']); ?></p>
            <div class="buttons">
              <a href="member_routines.php" class="btn btn-view">View</a>
              <button class="btn btn-download" onclick="alert('Download feature coming soon!')">Download</button>
            </div>
          <?php else: ?>
            <h3>No Routine Assigned</h3>
            <p>Your trainer hasn't assigned a routine yet. Check back later!</p>
          <?php endif; ?>
        </div>
        <div class="card-image" style="background-color: #ffe0b2">
          <img src="../images/pregnant-woman-holding-fitness-mat.jpg" alt="Yoga" />
        </div>
      </div>
    </div>

    <!-- Diet Plan -->
    <div class="content-section">
      <h2>Diet Plan</h2>
      <div class="info-card">
        <div class="card-text">
          <?php if (!empty($dietPlans)): ?>
            <h3>Today's Diet Plan</h3>
            <p>You have <?php echo count($dietPlans); ?> meal(s) planned for today. View details and track your intake.</p>
            <div class="buttons">
              <a href="member_diet.php" class="btn btn-view">View</a>
              <button class="btn btn-download" onclick="alert('Download feature coming soon!')">Download</button>
            </div>
          <?php else: ?>
            <h3>No Diet Plan for Today</h3>
            <p>Add your own meals or wait for your trainer to create a plan.</p>
            <div class="buttons">
              <a href="member_diet.php" class="btn btn-download">Add Meals</a>
            </div>
          <?php endif; ?>
        </div>
        <div class="card-image" style="background-color: #cfe8fc">
          <img src="../images/interior-design-yoga-space.jpg" alt="Diet" />
        </div>
      </div>
    </div>

    <!-- Track Progress -->
    <div class="content-section">
      <h2>Track Your Progress</h2>
      <form id="progressForm" action="/Web_Project/handlers/member/log_progress.php" method="POST">
        <div class="progress-grid">
          <div class="input-group">
            <label>Weight (kg)</label>
            <input type="number" step="0.1" name="weight" class="custom-input" placeholder="Enter weight" 
                   value="<?php echo $latestProgress['weight_kg'] ?? ''; ?>" />
          </div>
          <div class="input-group">
            <label>Heart Rate (bpm)</label>
            <input type="number" name="heart_rate" class="custom-input" placeholder="Enter heart rate" 
                   value="<?php echo $latestProgress['heart_rate'] ?? ''; ?>" />
          </div>
          <div class="input-group">
            <label>Sleep (hours)</label>
            <input type="number" step="0.5" name="sleep_hours" class="custom-input" placeholder="Enter sleep hours" 
                   value="<?php echo $latestProgress['sleep_hours'] ?? ''; ?>" />
          </div>
          <div class="input-group">
            <label>Mood</label>
            <select name="mood" class="custom-input">
              <option value="">Select mood</option>
              <option value="excellent">Excellent</option>
              <option value="good">Good</option>
              <option value="neutral">Neutral</option>
              <option value="poor">Poor</option>
              <option value="bad">Bad</option>
            </select>
          </div>
          <div class="progress-actions">
            <button type="submit" class="btn btn-download btn-large">Log Progress</button>
          </div>
        </div>
        <p class="success-msg" id="progress-success">Progress logged successfully!</p>
      </form>
    </div>

    <!-- Rate Experience -->
    <div class="content-section">
      <h2>Rate Your Experience</h2>
      <div class="card-row">
        <!-- Rate App -->
        <div class="info-card" style="flex-direction: column; padding: 30px; align-items: center; text-align: center;">
          <h3>Rate the App</h3>
          <p style="margin-bottom: 10px;">How is your experience with the app?</p>
          <div class="rating-stars" id="app_stars">
            <span onclick="submitRating('app', 1)">★</span>
            <span onclick="submitRating('app', 2)">★</span>
            <span onclick="submitRating('app', 3)">★</span>
            <span onclick="submitRating('app', 4)">★</span>
            <span onclick="submitRating('app', 5)">★</span>
          </div>
          <p id="app_msg" style="color: #00d26a; font-size: 12px; height: 15px;"></p>
        </div>

        <!-- Rate Trainer -->
        <div class="info-card" style="flex-direction: column; padding: 30px; align-items: center; text-align: center;">
          <h3>Rate Trainer</h3>
          <p style="margin-bottom: 10px;">How is your trainer performing?</p>
          <div class="rating-stars" id="trainer_stars">
            <span onclick="submitRating('trainer', 1)">★</span>
            <span onclick="submitRating('trainer', 2)">★</span>
            <span onclick="submitRating('trainer', 3)">★</span>
            <span onclick="submitRating('trainer', 4)">★</span>
            <span onclick="submitRating('trainer', 5)">★</span>
          </div>
          <p id="trainer_msg" style="color: #00d26a; font-size: 12px; height: 15px;"></p>
        </div>
      </div>
    </div>

    <!-- Account Settings -->
    <div class="account-settings">
      <h2>Account Settings</h2>
      <a href="member_profile.php" class="btn btn-view btn-large">Edit Account Info</a>
    </div>
  </div>

  <script>
    // Progress form submission
    document.getElementById('progressForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      
      fetch('/Web_Project/handlers/member/log_progress.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById('progress-success').style.display = 'block';
          setTimeout(() => {
            document.getElementById('progress-success').style.display = 'none';
          }, 3000);
        }
      })
      .catch(error => console.error('Error:', error));
    });

    // Rating function
    function submitRating(type, value) {
      const container = document.getElementById(type + '_stars');
      const stars = container.getElementsByTagName('span');
      const msg = document.getElementById(type + '_msg');

      // Update visual stars
      for (let i = 0; i < stars.length; i++) {
        if (i < value) {
          stars[i].classList.add('active');
        } else {
          stars[i].classList.remove('active');
        }
      }

      // Submit rating to server
      fetch('/Web_Project/handlers/member/submit_rating.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `rating_type=${type}&rating_value=${value}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          msg.innerText = "Thanks for rating! (" + value + "/5)";
        }
      })
      .catch(error => console.error('Error:', error));
    }
  </script>
</body>

</html>
