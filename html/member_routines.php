<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Fetch routines
$routines = getMemberRoutines($member_id);

// Fetch attendance for this week (Sun-Sat)
$today = date('Y-m-d');
$startOfWeek = date('Y-m-d', strtotime('last sunday', strtotime($today)));
$endOfWeek = date('Y-m-d', strtotime('next saturday', strtotime($today)));
$attendance = getMemberAttendance($member_id, 100);
$daysCompleted = 0;
$totalMinutes = 0;
$streak = 0;
$weekAttendance = [];
foreach ($attendance as $a) {
  $date = $a['booking_date'];
  if ($date >= $startOfWeek && $date <= $endOfWeek) {
    $daysCompleted++;
    $totalMinutes += $a['duration_minutes'];
    $weekAttendance[$date] = true;
  }
}
// Calculate streak (consecutive days up to today)
$streak = 0;
for ($i = 0; $i < 7; $i++) {
  $d = date('Y-m-d', strtotime("-$i day", strtotime($today)));
  if (isset($weekAttendance[$d])) {
    $streak++;
  } else {
    break;
  }
}
$weekProgress = round(($daysCompleted / 7) * 100);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Yoga Routines - Pranayom</title>
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
      flex-shrink: 0;
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

    /* Main Content Layout */
    .main-content {
      flex: 1;
      padding: 40px 30px;
      display: flex;
      gap: 30px;
      overflow-y: auto;
    }

    /* Left Panel - Days & Details */
    .left-panel {
      flex: 1;
      max-width: 700px;
    }

    h1 {
      font-size: 28px;
      margin-bottom: 25px;
      background: linear-gradient(135deg, #00d26a, #00a854);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    h2 {
      font-size: 16px;
      margin-bottom: 15px;
      color: #aaa;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    /* Day Selector */
    .day-selector {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-bottom: 30px;
    }

    .day-btn {
      padding: 12px 20px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-size: 13px;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
      min-width: 90px;
      text-align: center;
    }

    .day-btn.completed {
      background: linear-gradient(135deg, #1a3d2a, #0d2818);
      color: #00d26a;
      border: 1px solid #00d26a;
    }

    .day-btn.completed::after {
      content: "✓";
      position: absolute;
      top: -5px;
      right: -5px;
      background: #00d26a;
      color: #000;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .day-btn.ongoing {
      background: linear-gradient(135deg, #3d3a1a, #282518);
      color: #f0c040;
      border: 1px solid #f0c040;
    }

    .day-btn.ongoing::after {
      content: "⏳";
      position: absolute;
      top: -5px;
      right: -5px;
      background: #f0c040;
      color: #000;
      width: 18px;
      height: 18px;
      border-radius: 50%;
      font-size: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .day-btn.upcoming {
      background: linear-gradient(135deg, #1f261f, #151a15);
      color: #888;
      border: 1px solid #333;
    }

    .day-btn.active {
      transform: scale(1.05);
      box-shadow: 0 4px 20px rgba(0, 210, 106, 0.3);
    }

    .day-btn:hover {
      transform: translateY(-2px);
    }

    /* Day Details Card */
    .day-details {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 16px;
      padding: 25px;
      border: 1px solid #2a352a;
      margin-bottom: 25px;
    }

    .day-details-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
      padding-bottom: 15px;
      border-bottom: 1px solid #2a352a;
    }

    .day-title {
      font-size: 22px;
      font-weight: bold;
    }

    .day-status {
      padding: 6px 16px;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 600;
    }

    .day-status.completed {
      background: rgba(0, 210, 106, 0.15);
      color: #00d26a;
    }

    .day-status.ongoing {
      background: rgba(240, 192, 64, 0.15);
      color: #f0c040;
    }

    .day-status.upcoming {
      background: rgba(136, 136, 136, 0.15);
      color: #888;
    }

    /* Routine Items */
    .routine-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .routine-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px;
      background: #1f261f;
      border-radius: 10px;
      transition: all 0.3s ease;
    }

    .routine-item:hover {
      background: #252d25;
      transform: translateX(5px);
    }

    .routine-icon {
      width: 45px;
      height: 45px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      flex-shrink: 0;
    }

    .routine-icon.yoga {
      background: linear-gradient(135deg, #00d26a33, #00a85433);
    }

    .routine-icon.meditation {
      background: linear-gradient(135deg, #6b5bff33, #4f42cc33);
    }

    .routine-icon.breathing {
      background: linear-gradient(135deg, #00bcd433, #00838f33);
    }

    .routine-info {
      flex: 1;
    }

    .routine-name {
      font-size: 15px;
      font-weight: 600;
      margin-bottom: 4px;
    }

    .routine-duration {
      font-size: 12px;
      color: #888;
    }

    .routine-check {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      border: 2px solid #333;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .routine-check.done {
      background: #00d26a;
      border-color: #00d26a;
      color: #000;
    }

    /* Action Buttons */
    .action-buttons {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    .btn {
      padding: 12px 25px;
      border-radius: 8px;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(135deg, #00d26a, #00a854);
      color: #000;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(0, 210, 106, 0.4);
    }

    .btn-secondary {
      background: #2a352a;
      color: #fff;
    }

    .btn-secondary:hover {
      background: #3a453a;
    }

    /* Right Panel - Mini Calendar */
    .right-panel {
      width: 280px;
      flex-shrink: 0;
    }

    .mini-calendar {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 16px;
      padding: 20px;
      border: 1px solid #2a352a;
    }

    .calendar-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 15px;
    }

    .calendar-title {
      font-size: 14px;
      font-weight: 600;
    }

    .calendar-nav {
      display: flex;
      gap: 5px;
    }

    .calendar-nav-btn {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      background: #2a352a;
      border: none;
      color: #aaa;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      transition: all 0.2s;
    }

    .calendar-nav-btn:hover {
      background: #3a453a;
      color: #fff;
    }

    .calendar-weekdays {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      text-align: center;
      margin-bottom: 10px;
    }

    .weekday {
      font-size: 10px;
      color: #666;
      padding: 5px;
    }

    .calendar-days {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 2px;
    }

    .cal-day {
      aspect-ratio: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 11px;
      border-radius: 50%;
      cursor: pointer;
      transition: all 0.2s;
      color: #aaa;
    }

    .cal-day:hover:not(.empty) {
      background: #2a352a;
    }

    .cal-day.today {
      background: #00d26a;
      color: #000;
      font-weight: bold;
    }

    .cal-day.has-routine {
      position: relative;
    }

    .cal-day.has-routine::after {
      content: "";
      position: absolute;
      bottom: 2px;
      width: 4px;
      height: 4px;
      border-radius: 50%;
      background: #00d26a;
    }

    .cal-day.empty {
      cursor: default;
    }

    /* Progress Summary */
    .progress-summary {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 16px;
      padding: 20px;
      border: 1px solid #2a352a;
      margin-top: 20px;
    }

    .progress-title {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 15px;
    }

    .progress-stats {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .stat-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .stat-label {
      font-size: 12px;
      color: #888;
    }

    .stat-value {
      font-size: 14px;
      font-weight: 600;
      color: #00d26a;
    }

    .progress-bar-container {
      margin-top: 15px;
    }

    .progress-bar-label {
      display: flex;
      justify-content: space-between;
      font-size: 11px;
      margin-bottom: 5px;
    }

    .progress-bar-label span:first-child {
      color: #888;
    }

    .progress-bar-label span:last-child {
      color: #00d26a;
    }

    .progress-bar {
      height: 6px;
      background: #2a352a;
      border-radius: 3px;
      overflow: hidden;
    }

    .progress-bar-fill {
      height: 100%;
      background: linear-gradient(90deg, #00d26a, #00a854);
      border-radius: 3px;
      transition: width 0.5s ease;
    }

    /* Quick Actions */
    .quick-actions {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 16px;
      padding: 20px;
      border: 1px solid #2a352a;
      margin-top: 20px;
    }

    .quick-action-btn {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 8px;
      background: #2a352a;
      color: #fff;
      font-size: 13px;
      cursor: pointer;
      margin-bottom: 10px;
      transition: all 0.2s;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .quick-action-btn:last-child {
      margin-bottom: 0;
    }

    .quick-action-btn:hover {
      background: #3a453a;
      transform: translateX(3px);
    }

    .quick-action-btn span {
      font-size: 16px;
    }

    /* Post-workout Feedback Styles */
    .post-workout {
      margin-top: 20px;
      background: #1a201a;
      padding: 20px;
      border-radius: 12px;
      border: 1px solid #2a352a;
    }

    .section-subtitle {
      margin: 0 0 15px 0;
      font-size: 14px;
      color: #aaa;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .feedback-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
    }

    .form-label {
      font-size: 12px;
      color: #ccc;
      margin-bottom: -5px;
    }

    .form-input {
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #333;
      background: #0d110d;
      color: #fff;
      font-size: 13px;
      width: 100%;
    }

    .form-input:focus {
      outline: none;
      border-color: #00d26a;
    }

    .divider {
      border: 0;
      border-top: 1px solid #2a352a;
      margin: 10px 0;
    }

    .subsection-title {
      font-size: 13px;
      color: #00d26a;
      margin-bottom: 5px;
    }

    .input-row {
      display: flex;
      gap: 10px;
    }

    .btn-group {
      display: flex;
      gap: 10px;
      margin-top: 10px;
    }
  </style>
</head>

<body>
  <?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <!-- Left Panel -->
    <div class="left-panel">
      <h1>Your Yoga Routines</h1>

      <h2>Select Day</h2>
      <div class="day-selector" id="daySelector">
        <?php if (empty($routines)): ?>
          <p>No routines assigned yet.</p>
        <?php else: ?>
          <?php foreach ($routines as $index => $routine): ?>
            <button class="day-btn <?php echo $index === 0 ? 'active' : ''; ?>"
              onclick="showRoutine(<?php echo $index; ?>)"
              id="btn-routine-<?php echo $index; ?>">
              <?php echo htmlspecialchars($routine['title']); ?>
            </button>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <!-- Day Details -->
      <div class="day-details" id="dayDetails">
        <?php if (!empty($routines)): ?>
          <?php foreach ($routines as $index => $routine): ?>
            <div id="routine-content-<?php echo $index; ?>" class="routine-content" style="display: <?php echo $index === 0 ? 'block' : 'none'; ?>;">
              <div class="day-details-header">
                <span class="day-title"><?php echo htmlspecialchars($routine['title']); ?></span>
                <span class="day-status <?php echo ($routine['difficulty_level'] ?? 'beginner'); ?>">
                  <?php echo ucfirst($routine['difficulty_level'] ?? 'Beginner'); ?>
                </span>
              </div>
              <p style="color: #aaa; margin-bottom: 20px;"><?php echo htmlspecialchars($routine['description']); ?></p>

              <div class="routine-list">
                <?php
                $exercises = json_decode($routine['exercises'], true);
                if ($exercises):
                  foreach ($exercises as $ex):
                ?>
                    <div class="routine-item">
                      <div class="routine-icon yoga">🧘</div> <!-- Fallback icon -->
                      <div class="routine-info">
                        <div class="routine-name"><?php echo htmlspecialchars($ex['name'] ?? 'Exercise'); ?></div>
                        <div class="routine-duration"><?php echo htmlspecialchars($ex['duration'] ?? '0') . ' ' . htmlspecialchars($ex['sets'] ?? 'mins'); ?></div>
                      </div>
                      <div class="routine-check" onclick="this.classList.toggle('done')">✓</div>
                    </div>
                  <?php
                  endforeach;
                else:
                  ?>
                  <p>No exercises listed.</p>
                <?php endif; ?>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>Please contact your trainer to get a routine assigned.</p>
        <?php endif; ?>
      </div>

      <div class="action-buttons">
        <button class="btn btn-primary" id="startBtn">Start Routine</button>
        <button class="btn btn-secondary" onclick="markDayComplete()">
          Mark Complete
        </button>
        <button class="btn btn-secondary" onclick="window.location.href='../handlers/member/download_routine_report.php'">
          📄 Download Report
        </button>
      </div>

      <!-- Post-workout feedback inputs -->
      <div class="post-workout">
        <h2 class="section-subtitle">
          Post-workout Feedback
        </h2>
        <div class="feedback-form">
          <label class="form-label">Rating (1-5)</label>
          <input
            id="postWorkoutRating"
            type="number"
            min="1"
            max="5"
            class="form-input"
            placeholder="Enter rating 1-5" />

          <label class="form-label">Energy level (1-5)</label>
          <input
            id="postWorkoutEnergy"
            type="number"
            min="1"
            max="5"
            class="form-input"
            placeholder="1 (low) - 5 (high)" />

          <label class="form-label">Notes</label>
          <textarea
            id="postWorkoutNotes"
            rows="3"
            class="form-input"
            placeholder="How did it go?"></textarea>

          <hr class="divider" />

          <h3 class="subsection-title">
            Improvement Measurements (before / after)
          </h3>

          <label class="form-label">Flexibility (reach in cm)</label>
          <div class="input-row">
            <input
              id="flexBefore"
              type="number"
              class="form-input"
              placeholder="before" />
            <input
              id="flexAfter"
              type="number"
              class="form-input"
              placeholder="after" />
          </div>

          <label class="form-label">Balance (seconds)</label>
          <div class="input-row">
            <input
              id="balanceBefore"
              type="number"
              class="form-input"
              placeholder="before (s)" />
            <input
              id="balanceAfter"
              type="number"
              class="form-input"
              placeholder="after (s)" />
          </div>

          <label class="form-label">Pain level (0-10)</label>
          <div class="input-row">
            <input
              id="painBefore"
              type="number"
              min="0"
              max="10"
              class="form-input"
              placeholder="before" />
            <input
              id="painAfter"
              type="number"
              min="0"
              max="10"
              class="form-input"
              placeholder="after" />
          </div>

          <label class="form-label">Sleep quality (1-5)</label>
          <input
            id="sleepQuality"
            type="number"
            min="1"
            max="5"
            class="form-input"
            placeholder="1 poor - 5 excellent" />

          <label class="form-label">Mood (1-5)</label>
          <input
            id="moodLevel"
            type="number"
            min="1"
            max="5"
            class="form-input"
            placeholder="1 low - 5 great" />

          <div class="btn-group">
            <button
              id="postWorkoutBtn"
              class="btn btn-primary"
              type="button">
              Submit Feedback
            </button>
            <button
              id="calcEffectBtn"
              class="btn btn-secondary"
              type="button">
              Calculate Effectiveness
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Right Panel - Mini Calendar & Stats -->
  <div class="right-panel">
    <!-- Mini Calendar -->
    <div class="mini-calendar">
      <div class="calendar-header">
        <span class="calendar-title" id="calendarTitle">January 2026</span>
        <div class="calendar-nav">
          <button class="calendar-nav-btn" id="prevMonth">‹</button>
          <button class="calendar-nav-btn" id="nextMonth">›</button>
        </div>
      </div>
      <div class="calendar-weekdays">
        <span class="weekday">S</span>
        <span class="weekday">M</span>
        <span class="weekday">T</span>
        <span class="weekday">W</span>
        <span class="weekday">T</span>
        <span class="weekday">F</span>
        <span class="weekday">S</span>
      </div>
      <div class="calendar-days" id="calendarDays">
        <!-- Calendar days will be generated by JS -->
      </div>
    </div>

    <!-- Progress Summary -->
    <div class="progress-summary">
      <div class="progress-title">Weekly Progress</div>
      <div class="progress-stats">
        <div class="stat-item">
          <span class="stat-label">Days Completed</span>
          <span class="stat-value" id="completedCount"><?php echo $daysCompleted; ?>/7</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Current Streak</span>
          <span class="stat-value">🔥 <?php echo $streak; ?> days</span>
        </div>
        <div class="stat-item">
          <span class="stat-label">Total Minutes</span>
          <span class="stat-value"><?php echo $totalMinutes; ?> min</span>
        </div>
      </div>
      <div class="progress-bar-container">
        <div class="progress-bar-label">
          <span>Week Progress</span>
          <span id="weekProgress"><?php echo $weekProgress; ?>%</span>
        </div>
        <div class="progress-bar">
          <div
            class="progress-bar-fill"
            id="progressFill"
            style="width: <?php echo $weekProgress; ?>%"></div>
        </div>
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
      <div class="post-workout-actions">
        <button
          class="btn btn-secondary"
          onclick="window.location.href='member_diet.php'">
          🍎 Log Meals
        </button>
        <button
          class="btn btn-secondary"
          onclick="window.location.href='member_progress.php'">
          📊 View Progress
        </button>
        <button
          class="btn btn-secondary"
          onclick="window.location.href='member_chat.php'">
          💬 Ask Trainer
        </button>
      </div>
    </div>
  </div>

  <script>
    // Routine Data for each day - loaded from database
    let routineData = <?php
                      $db_routines = getMemberRoutines($member_id, true);
                      $phpRoutineData = [];
                      // Initialize 7 days structure
                      for ($i = 1; $i <= 7; $i++) {
                        $phpRoutineData[$i] = [
                          'title' => "Day $i - Rest / Free Practice",
                          'status' => 'upcoming',
                          'routines' => []
                        ];
                      }

                      // Map DB routines to days
                      foreach ($db_routines as $k => $r) {
                        $day = $k + 1;
                        if ($day > 7) break;

                        $exercises = json_decode($r['exercises'], true);
                        $ui_exercises = [];
                        if (is_array($exercises)) {
                          foreach ($exercises as $ex) {
                            $ui_exercises[] = [
                              'name' => $ex['name'] ?? 'Exercise',
                              'duration' => ($ex['duration'] ?? '0') . ' min',
                              'icon' => '🧘',
                              'type' => 'yoga',
                              'done' => false
                            ];
                          }
                        }

                        $phpRoutineData[$day]['title'] = $r['title'];
                        $phpRoutineData[$day]['routines'] = $ui_exercises;

                        // Set status logic (simple approximation for demo)
                        if ($day == 1) $phpRoutineData[$day]['status'] = 'ongoing';
                      }

                      echo json_encode($phpRoutineData);
                      ?>;

    let selectedDay = 1; // Current day (ongoing)

    // Generate Day Buttons
    function renderDaySelector() {
      const container = document.getElementById("daySelector");
      container.innerHTML = "";

      for (let i = 1; i <= 7; i++) {
        const data = routineData[i];
        const btn = document.createElement("button");
        btn.className = `day-btn ${data.status}`;
        if (i === selectedDay) btn.classList.add("active");
        btn.innerHTML = `Day ${i}`;
        btn.onclick = () => selectDay(i);
        container.appendChild(btn);
      }
    }

    // Select a day and show details
    function selectDay(day) {
      selectedDay = day;
      renderDaySelector();
      renderDayDetails();
    }

    // Render Day Details
    function renderDayDetails() {
      const data = routineData[selectedDay];

      document.getElementById("dayTitle").textContent = data.title;

      const statusEl = document.getElementById("dayStatus");
      statusEl.textContent =
        data.status.charAt(0).toUpperCase() + data.status.slice(1);
      statusEl.className = `day-status ${data.status}`;

      const listEl = document.getElementById("routineList");
      listEl.innerHTML = "";

      data.routines.forEach((routine, index) => {
        const item = document.createElement("div");
        item.className = "routine-item";
        item.innerHTML = `
            <div class="routine-icon ${routine.type}">${routine.icon}</div>
            <div class="routine-info">
              <div class="routine-name">${routine.name}</div>
              <div class="routine-duration">${routine.duration}</div>
            </div>
            <div class="routine-check ${
              routine.done ? "done" : ""
            }" onclick="toggleRoutine(${index})">
              ${routine.done ? "✓" : ""}
            </div>
          `;
        listEl.appendChild(item);
      });

      // Update button text based on status
      const startBtn = document.getElementById("startBtn");
      if (data.status === "completed") {
        startBtn.textContent = "Review Routine";
      } else if (data.status === "ongoing") {
        startBtn.textContent = "Continue Routine";
      } else {
        startBtn.textContent = "Start Routine";
      }

      updateProgress();
    }

    // Toggle routine completion
    function toggleRoutine(index) {
      routineData[selectedDay].routines[index].done = !routineData[selectedDay].routines[index].done;

      // Update day status based on routines
      const routines = routineData[selectedDay].routines;
      const allDone = routines.every((r) => r.done);
      const someDone = routines.some((r) => r.done);

      if (allDone) {
        routineData[selectedDay].status = "completed";
      } else if (someDone) {
        routineData[selectedDay].status = "ongoing";
      } else {
        routineData[selectedDay].status = "upcoming";
      }

      renderDaySelector();
      renderDayDetails();
    }

    // Mark entire day as complete
    function markDayComplete() {
      routineData[selectedDay].routines.forEach((r) => (r.done = true));
      routineData[selectedDay].status = "completed";
      renderDaySelector();
      renderDayDetails();
    }

    // Update progress stats
    function updateProgress() {
      const completed = Object.values(routineData).filter(
        (d) => d.status === "completed"
      ).length;
      const total = Object.keys(routineData).length;
      const percentage = Math.round((completed / total) * 100);

      document.getElementById(
        "completedCount"
      ).textContent = `${completed}/${total}`;
      document.getElementById("weekProgress").textContent = `${percentage}%`;
      document.getElementById("progressFill").style.width = `${percentage}%`;
    }

    // Mini Calendar Logic
    const date = new Date();
    let currYear = date.getFullYear();
    let currMonth = date.getMonth();

    const months = [
      "January",
      "February",
      "March",
      "April",
      "May",
      "June",
      "July",
      "August",
      "September",
      "October",
      "November",
      "December",
    ];

    function renderCalendar() {
      document.getElementById(
        "calendarTitle"
      ).textContent = `${months[currMonth]} ${currYear}`;

      const container = document.getElementById("calendarDays");
      container.innerHTML = "";

      const firstDay = new Date(currYear, currMonth, 1).getDay();
      const lastDate = new Date(currYear, currMonth + 1, 0).getDate();
      const today = new Date();

      // Empty days before first day (Sunday = 0)
      for (let i = 0; i < firstDay; i++) {
        const day = document.createElement("div");
        day.className = "cal-day empty";
        container.appendChild(day);
      }

      // Actual days
      for (let i = 1; i <= lastDate; i++) {
        const day = document.createElement("div");
        day.className = "cal-day";
        day.textContent = i;

        // Highlight today
        if (
          i === today.getDate() &&
          currMonth === today.getMonth() &&
          currYear === today.getFullYear()
        ) {
          day.classList.add("today");
        }

        // Mark yoga/class days from day 1 (not day 4)
        if (i <= 7) {
          day.classList.add("has-routine");
        }

        day.onclick = () => {
          if (i <= 7) selectDay(i);
        };

        container.appendChild(day);
      }
    }

    document.getElementById("prevMonth").onclick = () => {
      currMonth--;
      if (currMonth < 0) {
        currMonth = 11;
        currYear--;
      }
      renderCalendar();
    };

    document.getElementById("nextMonth").onclick = () => {
      currMonth++;
      if (currMonth > 11) {
        currMonth = 0;
        currYear++;
      }
      renderCalendar();
    };

    // Initialize
    renderDaySelector();
    renderDayDetails();
    renderCalendar();

    // Show alert when the Start/Review button is used for reviewing
    const startBtnEl = document.getElementById("startBtn");
    if (startBtnEl) {
      startBtnEl.addEventListener("click", function() {
        if (
          this.textContent &&
          this.textContent.toLowerCase().includes("review")
        ) {
          alert("Routine reviewing");
        }
      });
    }

    // Post-workout feedback submit -> show entered data in alert
    const postBtn = document.getElementById("postWorkoutBtn");
    if (postBtn) {
      postBtn.addEventListener("click", function() {
        const rating =
          document.getElementById("postWorkoutRating").value || "N/A";
        const energy =
          document.getElementById("postWorkoutEnergy").value || "N/A";
        const notes = document.getElementById("postWorkoutNotes").value || "";
        alert(
          "Post-workout saved.\\nRating: " +
          rating +
          "\\nEnergy: " +
          energy +
          "\\nNotes: " +
          notes
        );
        // clear inputs
        document.getElementById("postWorkoutRating").value = "";
        document.getElementById("postWorkoutEnergy").value = "";
        document.getElementById("postWorkoutNotes").value = "";
      });
    }

    // Calculate effectiveness based on before/after and subjective inputs
    const calcBtn = document.getElementById("calcEffectBtn");
    if (calcBtn) {
      calcBtn.addEventListener("click", function() {
        const fb =
          parseFloat(document.getElementById("flexBefore").value) || 0;
        const fa =
          parseFloat(document.getElementById("flexAfter").value) || 0;
        const bb =
          parseFloat(document.getElementById("balanceBefore").value) || 0;
        const ba =
          parseFloat(document.getElementById("balanceAfter").value) || 0;
        const pb = parseFloat(document.getElementById("painBefore").value);
        const pa = parseFloat(document.getElementById("painAfter").value);
        const sleep =
          parseFloat(document.getElementById("sleepQuality").value) || 0;
        const mood =
          parseFloat(document.getElementById("moodLevel").value) || 0;

        // compute percent improvements (handle zero baseline)
        const flexImp = fb > 0 ? ((fa - fb) / fb) * 100 : fa > 0 ? 100 : 0;
        const balImp = bb > 0 ? ((ba - bb) / bb) * 100 : ba > 0 ? 100 : 0;
        const painImp =
          typeof pb === "number" && !isNaN(pb) ?
          ((pb - (isNaN(pa) ? pb : pa)) / (pb || 1)) * 100 :
          0;

        // subjective score 0-100 from 1-5 scale
        const subj =
          (((Math.min(Math.max(sleep, 1), 5) - 1) / 4) * 100 +
            ((Math.min(Math.max(mood, 1), 5) - 1) / 4) * 100) /
          2;

        // weighted overall effectiveness
        const overall =
          clamp(flexImp, -100, 200) * 0.3 +
          clamp(balImp, -100, 200) * 0.2 +
          clamp(painImp, -100, 100) * 0.3 +
          subj * 0.2;
        const score = Math.round(overall);

        alert(
          "Effectiveness calculation:\\n" +
          "Flexibility change: " +
          Math.round(flexImp) +
          "%\\n" +
          "Balance change: " +
          Math.round(balImp) +
          "%\\n" +
          "Pain improvement: " +
          Math.round(painImp) +
          "%\\n" +
          "Subjective (sleep+mood): " +
          Math.round(subj) +
          "%\\n\\n" +
          "Overall effectiveness: " +
          score +
          "%"
        );

        // helper clamp
        function clamp(v, a, b) {
          return Math.max(a, Math.min(b, v));
        }
      });
    }
  </script>
</body>

</html>