<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Define week dates (Monday to Sunday)
$current_monday = date('Y-m-d', strtotime('monday this week'));
$week_dates = [];
for ($i = 0; $i < 7; $i++) {
    $week_dates[$i + 1] = date('Y-m-d', strtotime("+$i days", strtotime($current_monday)));
}

// Get selected day (1-7), fallback to today's day of week
$today_dow = date('N'); // 1 (Mon) to 7 (Sun)
$selected_day_num = isset($_GET['day']) ? intval($_GET['day']) : $today_dow;
if ($selected_day_num < 1 || $selected_day_num > 7) $selected_day_num = $today_dow;

$requested_date = $week_dates[$selected_day_num];

// Fetch routines for selected date
$stmt = $pdo->prepare("SELECT * FROM routines WHERE member_id = ? AND (scheduled_date = ? OR scheduled_date IS NULL) AND is_active = 1");
$stmt->execute([$member_id, $requested_date]);
$routines = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch all yoga sessions for summary (current week)
$all_sessions = getMemberYogaSessions($member_id, 100);
$totalMinutes = 0;
foreach($all_sessions as $s) {
    if ($s['session_date'] >= $week_dates[1] && $s['session_date'] <= $week_dates[7]) {
        $totalMinutes += $s['duration_minutes'];
    }
}

// Comprehensive Week Analysis
$week_statuses = [];
$daysCompleted = 0;
$totalMinutes = 0;

// Fetch all routines for this week
$stmt_week = $pdo->prepare("SELECT * FROM routines WHERE member_id = ? AND scheduled_date BETWEEN ? AND ? AND is_active = 1");
$stmt_week->execute([$member_id, $week_dates[1], $week_dates[7]]);
$all_week_routines = $stmt_week->fetchAll(PDO::FETCH_ASSOC);

// Map routine completion to days
$routine_completion_map = [];
foreach ($all_week_routines as $r) {
    $exercises = json_decode($r['exercises'], true);
    $completed = json_decode($r['completed_exercises'] ?? '[]', true);
    $is_all_done = (count($exercises) > 0 && count($completed) >= count($exercises));
    $routine_completion_map[$r['scheduled_date']] = [
        'is_done' => $is_all_done,
        'has_progress' => count($completed) > 0
    ];
}

// Fetch attendance and yoga sessions for the week
$attendance = getMemberAttendance($member_id, 100);
$yoga_sessions = getMemberYogaSessions($member_id, 100);

$week_activity = [];
foreach ($attendance as $a) {
    if ($a['booking_date'] >= $week_dates[1] && $a['booking_date'] <= $week_dates[7]) {
        $week_activity[$a['booking_date']] = true;
    }
}
foreach ($yoga_sessions as $s) {
    if ($s['session_date'] >= $week_dates[1] && $s['session_date'] <= $week_dates[7]) {
        $week_activity[$s['session_date']] = true;
        $totalMinutes += $s['duration_minutes'];
    }
}

// Calculate days completed and statuses for the selector
for ($i = 1; $i <= 7; $i++) {
    $d = $week_dates[$i];
    $status = 'upcoming';
    
    // Check if any activity (attendance or session) or routine progress
    $has_routine = isset($routine_completion_map[$d]);
    $is_routine_done = $has_routine && $routine_completion_map[$d]['is_done'];
    $is_routine_ongoing = $has_routine && $routine_completion_map[$d]['has_progress'] && !$is_routine_done;
    
    if (isset($week_activity[$d]) || $is_routine_done) {
        $daysCompleted++;
        $status = 'completed';
    } else if ($is_routine_ongoing) {
        $status = 'ongoing';
    }
    
    $week_statuses[$i] = $status;
}

// Calculate streak
$streak = 0;
$today = date('Y-m-d');
for ($i = 0; $i < 30; $i++) {
    $check_date = date('Y-m-d', strtotime("-$i days"));
    // check attendance, sessions, or completed routines for streak
    // (simplified check for recent activity)
    $stmt_streak = $pdo->prepare("SELECT count(*) FROM yoga_sessions WHERE member_id = ? AND session_date = ?");
    $stmt_streak->execute([$member_id, $check_date]);
    if ($stmt_streak->fetchColumn() > 0) {
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
        <?php 
          $days_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          for($i = 1; $i <= 7; $i++) {
            $date_str = $week_dates[$i];
            $label = "Day $i<br><small>" . $days_labels[$i-1] . "</small>";
            $active = ($i == $selected_day_num) ? 'active' : '';
            $status_class = $week_statuses[$i]; // completed, ongoing, upcoming
            
            echo "<button class='day-btn $active $status_class' onclick='window.location.href=\"?day=$i\"'>$label</button>";
          }
        ?>
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

              <?php 
                $exercises = json_decode($routine['exercises'], true);
                $completed = json_decode($routine['completed_exercises'] ?? '[]', true);
                $totalEx = is_array($exercises) ? count($exercises) : 0;
                $doneEx = is_array($completed) ? count($completed) : 0;
                $percent = $totalEx > 0 ? ($doneEx / $totalEx) * 100 : 0;
              ?>
              
              <div class="progress-container" style="margin-bottom: 25px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:5px; font-size:12px;">
                    <span style="color:#888;">Routine Progress</span>
                    <span style="color:#00d26a; font-weight:bold;" class="progressPercent"><?php echo round($percent); ?>%</span>
                </div>
                <div style="height:8px; background:#2a352a; border-radius:10px; overflow:hidden;">
                    <div class="progressFill" style="width:<?php echo $percent; ?>%; height:100%; background:linear-gradient(90deg, #00d26a, #00a854); transition: width 0.3s ease;"></div>
                </div>
              </div>

              <div class="routine-list">
                <?php
                $exercises = json_decode($routine['exercises'], true);
                $completed = json_decode($routine['completed_exercises'] ?? '[]', true);
                if ($exercises):
                  foreach ($exercises as $ex_idx => $ex):
                    $isDone = in_array($ex_idx, $completed);
                ?>
                    <div class="routine-item">
                      <div class="routine-icon yoga">🧘</div> <!-- Fallback icon -->
                      <div class="routine-info">
                        <div class="routine-name"><?php echo htmlspecialchars($ex['name'] ?? 'Exercise'); ?></div>
                        <div class="routine-duration"><?php echo htmlspecialchars($ex['duration'] ?? '0') . ' ' . htmlspecialchars($ex['reps'] ?? 'mins'); ?></div>
                      </div>
                      <div class="routine-check <?php echo $isDone ? 'done' : ''; ?>" 
                           onclick="toggleExercise(this, <?php echo $routine['routine_id']; ?>, <?php echo $ex_idx; ?>)">✓</div>
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
        <button class="btn btn-primary" onclick="openSessionLog()">Log Completed Session</button>
        <button class="btn btn-secondary" onclick="window.location.href='../handlers/member/download_routine_report.php'">
          📄 Download Report
        </button>
      </div>

      <!-- Session Log Modal (Simple implementation) -->
      <div id="sessionLog" style="display:none; margin-top: 20px; background: #1a201a; padding: 25px; border-radius: 14px; border: 1px solid #2a352a;">
         <h3 style="color: #00d26a; margin-bottom: 15px;">Log Yoga Session</h3>
         <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label class="form-label">Duration (minutes)</label>
                <input type="number" id="logDuration" class="form-input" value="30">
            </div>
            <div>
                <label class="form-label">Date</label>
                <input type="date" id="logDate" class="form-input" value="<?php echo $requested_date; ?>">
            </div>
         </div>
         <label class="form-label">Session Notes</label>
         <textarea id="logNotes" class="form-input" style="margin-bottom: 15px;" placeholder="Optional notes..."></textarea>
         <div style="display: flex; gap: 10px;">
            <button class="btn btn-primary" onclick="submitSessionLog()">Submit Log</button>
            <button class="btn btn-secondary" onclick="document.getElementById('sessionLog').style.display='none'">Cancel</button>
         </div>
      </div>

      <script>
        function openSessionLog() {
            document.getElementById('sessionLog').style.display = 'block';
            document.getElementById('sessionLog').scrollIntoView({behavior: 'smooth'});
        }

        function submitSessionLog() {
            const duration = document.getElementById('logDuration').value;
            const date = document.getElementById('logDate').value;
            const notes = document.getElementById('logNotes').value;

            if (duration <= 0) { alert('Enter valid duration'); return; }

            const formData = new FormData();
            formData.append('duration', duration);
            formData.append('session_date', date);
            formData.append('notes', notes);

            fetch('../handlers/member/log_yoga_session.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('Session logged!');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.error);
                }
            });
        }

        function toggleExercise(el, routineId, exIndex) {
            el.classList.toggle('done');
            
            // Get all done items for this routine
            const routineContent = el.closest('.routine-content');
            const doneItems = [];
            routineContent.querySelectorAll('.routine-check.done').forEach(check => {
                // We need the index. Let's find it from the onclick.
                const onclickStr = check.getAttribute('onclick');
                const match = onclickStr.match(/,\s*(\d+)\)/);
                if (match) doneItems.push(parseInt(match[1]));
            });

            const formData = new FormData();
            formData.append('routine_id', routineId);
            formData.append('completed_exercises', JSON.stringify(doneItems));

            fetch('../handlers/member/save_exercise_status.php', {
                method: 'POST',
                body: formData
            });
        }
      </script>

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
                      // Fetch all routines for the week to populate summary
                      $all_week_routines = $pdo->prepare("SELECT * FROM routines WHERE member_id = ? AND scheduled_date BETWEEN ? AND ?");
                      $all_week_routines->execute([$member_id, $week_dates[1], $week_dates[7]]);
                      $week_routines_rows = $all_week_routines->fetchAll(PDO::FETCH_ASSOC);

                      foreach ($week_routines_rows as $r) {
                        $r_date = $r['scheduled_date'];
                        $day_idx = 0;
                        foreach($week_dates as $idx => $d) { if($d == $r_date) $day_idx = $idx; }
                        if ($day_idx == 0) continue;

                        $exercises = json_decode($r['exercises'], true);
                        $completed = json_decode($r['completed_exercises'] ?? '[]', true);
                        $ui_exercises = [];
                        if (is_array($exercises)) {
                          foreach ($exercises as $ex_idx => $ex) {
                            $ui_exercises[] = [
                              'name' => $ex['name'] ?? 'Exercise',
                              'duration' => ($ex['duration'] ?? '0') . ' min',
                              'icon' => '🧘',
                              'type' => 'yoga',
                              'done' => in_array($ex_idx, $completed)
                            ];
                          }
                        }

                        $phpRoutineData[$day_idx]['title'] = $r['title'];
                        $phpRoutineData[$day_idx]['routines'] = $ui_exercises;

                        if (count($completed) > 0) {
                            $phpRoutineData[$day_idx]['status'] = (count($completed) == count($exercises)) ? 'completed' : 'ongoing';
                        }
                      }


                      echo json_encode($phpRoutineData);
                      ?>;

    let selectedDay = <?php echo $selected_day_num; ?>; 

    // Generate Day Buttons (removed duplicate rendering as PHP handles it now)
    function renderDaySelector() {
       // Using PHP rendered selector for better state sync
    }

    function toggleExercise(el, routineId, exIndex) {
        el.classList.toggle('done');
        
        // Get all done items for this routine
        const routineContent = el.closest('.routine-content');
        const doneItems = [];
        const allItems = routineContent.querySelectorAll('.routine-check');
        
        routineContent.querySelectorAll('.routine-check.done').forEach(check => {
            const onclickStr = check.getAttribute('onclick');
            const match = onclickStr.match(/,\s*\d+,\s*(\d+)\)/); // Adjusted regex for 3 args
            if (match) doneItems.push(parseInt(match[1]));
        });

        // Update progress bar
        const percent = (doneItems.length / allItems.length) * 100;
        const progressFill = routineContent.querySelector('.progressFill');
        const progressPercent = routineContent.querySelector('.progressPercent');
        if (progressFill) progressFill.style.width = percent + '%';
        if (progressPercent) progressPercent.textContent = Math.round(percent) + '%';

        // Update Day selector status visually
        const dayBtn = document.querySelector(`.day-btn[onclick*="day=${selectedDay}"]`);
        if (dayBtn) {
            dayBtn.classList.remove('completed', 'ongoing', 'upcoming');
            if (doneItems.length === allItems.length) dayBtn.classList.add('completed');
            else if (doneItems.length > 0) dayBtn.classList.add('ongoing');
            else dayBtn.classList.add('upcoming');
        }

        const formData = new FormData();
        formData.append('routine_id', routineId);
        formData.append('completed_exercises', JSON.stringify(doneItems));

        fetch('../handlers/member/save_exercise_status.php', {
            method: 'POST',
            body: formData
        });
    }
    
    // Remove legacy JS functions to prevent UI interference
    function renderDaySelector() {}
    function renderDayDetails() {}
    

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