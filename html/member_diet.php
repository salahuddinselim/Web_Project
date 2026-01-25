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
$today = date('Y-m-d');

$calSummary = getMemberCalorieSummary($member_id, $requested_date);
$total_calories = $calSummary['planned_calories'] ?? 0;
$taken_calories = $calSummary['taken_calories'] ?? 0;

$plans = getMemberDietPlans($member_id, $requested_date);
$todays_meals = [];
if (is_array($plans)) {
  foreach ($plans as $p) {
    if (isset($p['meal_time'])) {
      $todays_meals[$p['meal_time']] = $p;
    }
  }
}

// Daily targets (hardcoded for now, can be made configurable)
$calorie_target = 1850;
$protein_target = 120;
$carbs_target = 180;
$fat_target = 65;

// Comprehensive Week Analysis for Diet
$diet_week_statuses = [];
foreach ($week_dates as $idx => $date) {
    if ($date > $today) {
        $diet_week_statuses[$idx] = 'upcoming';
        continue;
    }
    
    $summary = getMemberCalorieSummary($member_id, $date);
    $planned = $summary['planned_calories'] ?? 0;
    $taken = $summary['taken_calories'] ?? 0;
    
    if ($planned > 0) {
        if ($taken >= $planned) {
            $diet_week_statuses[$idx] = 'completed';
        } else if ($taken > 0) {
            $diet_week_statuses[$idx] = 'ongoing';
        } else {
            $diet_week_statuses[$idx] = 'upcoming';
        }
    } else {
        $diet_week_statuses[$idx] = 'upcoming';
    }
}

// Monthly data
$all_plans = getMemberDietPlans($member_id); // Define $all_plans before use
$month_start = date('Y-m-01');
$month_end = date('Y-m-t');
$monthly_total_calories = 0;
$days_with_plans = [];

if (is_array($all_plans)) {
    foreach ($all_plans as $p) {
        if ($p['plan_date'] >= $month_start && $p['plan_date'] <= $month_end) {
            $monthly_total_calories += intval($p['calories'] ?? 0);
            $days_with_plans[$p['plan_date']] = true;
        }
    }
}

$days_tracked = count($days_with_plans);
$total_days_in_month = date('t');
$adherence = $total_days_in_month > 0 ? round(($days_tracked / $total_days_in_month) * 100) : 0;

// Weekly adherence
$week_start_date = $week_dates[1];
$week_end_date = $week_dates[7];

$weekly_plans = is_array($all_plans) ? array_filter($all_plans, function ($p) use ($week_start_date, $week_end_date) {
    return $p['plan_date'] >= $week_start_date && $p['plan_date'] <= $week_end_date;
}) : [];

$weekly_plans_count = count($weekly_plans);
$weekly_days_tracked = count(array_unique(array_column($weekly_plans, 'plan_date')));
$weekly_adherence = round(($weekly_days_tracked / 7) * 100);

$progress_data = getMemberProgress($member_id);
if (!is_array($progress_data)) $progress_data = [];
$monthly_progress = array_filter($progress_data, function ($p) use ($month_start, $month_end) {
  return $p['tracking_date'] >= $month_start && $p['tracking_date'] <= $month_end;
});
usort($monthly_progress, function ($a, $b) {
  return strcmp($a['tracking_date'], $b['tracking_date']);
});
$weight_change = 0;
if (count($monthly_progress) >= 2) {
  $first_weight = $monthly_progress[0]['weight_kg'];
  $last_weight = $monthly_progress[count($monthly_progress) - 1]['weight_kg'];
  $weight_change = $last_weight - $first_weight;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Your Diet Plan - Pranayom</title>
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

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 40px 50px;
      overflow-y: auto;
    }

    .page-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    h1 {
      font-size: 28px;
      background: linear-gradient(135deg, #00d26a, #00a854);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    /* View Toggle */
    .view-toggle {
      display: flex;
      background: #1a201a;
      border-radius: 12px;
      padding: 5px;
      gap: 5px;
    }

    .view-btn {
      padding: 10px 24px;
      border: none;
      border-radius: 8px;
      background: transparent;
      color: #888;
      font-size: 13px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .view-btn.active {
      background: linear-gradient(135deg, #00d26a, #00a854);
      color: #000;
    }

    .view-btn:hover:not(.active) {
      color: #fff;
      background: #2a352a;
    }

    /* Stats Overview */
    .stats-overview {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-card {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 14px;
      padding: 20px;
      border: 1px solid #2a352a;
    }

    .stat-label {
      font-size: 12px;
      color: #888;
      margin-bottom: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .stat-value {
      font-size: 24px;
      font-weight: 700;
      color: #00d26a;
    }

    .stat-unit {
      font-size: 14px;
      color: #666;
      margin-left: 4px;
    }

    /* Day/Week Selector */
    .selector-row {
      display: flex;
      gap: 10px;
      margin-bottom: 25px;
      flex-wrap: wrap;
    }

    .selector-btn {
      padding: 10px 18px;
      border: 1px solid #333;
      border-radius: 10px;
      background: #1a201a;
      color: #aaa;
      font-size: 13px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .selector-btn.active {
      background-color: #1f3b26;
      border-color: #00d26a;
      color: #fff;
      transform: scale(1.05);
      box-shadow: 0 4px 15px rgba(0, 210, 106, 0.2);
    }
    
    .selector-btn.completed { border-bottom: 3px solid #00d26a; }
    .selector-btn.ongoing { border-bottom: 3px solid #f0c040; }
    .selector-btn.upcoming { border-bottom: 1px solid #2a352a; }

    .selector-btn:hover:not(.active) {
      border-color: #555;
      color: #fff;
    }

    /* Content Container */
    .diet-content {
      display: flex;
      gap: 30px;
    }

    /* Meals Panel */
    .meals-panel {
      flex: 1;
    }

    .meals-panel h2 {
      font-size: 16px;
      color: #aaa;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 20px;
    }

    /* Meal Card */
    .meal-card {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 14px;
      padding: 20px;
      border: 1px solid #2a352a;
      margin-bottom: 15px;
      transition: all 0.3s ease;
    }

    .meal-card:hover {
      transform: translateY(-2px);
      border-color: #3a453a;
    }

    .meal-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 15px;
    }

    .meal-title-row {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .meal-icon {
      width: 45px;
      height: 45px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
    }

    .meal-icon.breakfast {
      background: linear-gradient(135deg, #ff980033, #ff570033);
    }

    .meal-icon.lunch {
      background: linear-gradient(135deg, #4caf5033, #2e7d3233);
    }

    .meal-icon.dinner {
      background: linear-gradient(135deg, #9c27b033, #6a1b9a33);
    }

    .meal-icon.snack {
      background: linear-gradient(135deg, #03a9f433, #01579b33);
    }

    .meal-type {
      font-size: 11px;
      color: #888;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .meal-name {
      font-size: 16px;
      font-weight: 600;
      margin-top: 3px;
    }

    .meal-time {
      font-size: 12px;
      color: #666;
      background: #2a352a;
      padding: 5px 12px;
      border-radius: 15px;
    }

    .meal-details {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 15px;
      padding-top: 15px;
      border-top: 1px solid #2a352a;
    }

    .meal-detail-item {
      text-align: center;
    }

    .detail-value {
      font-size: 16px;
      font-weight: 600;
      color: #fff;
    }

    .detail-label {
      font-size: 10px;
      color: #666;
      text-transform: uppercase;
      margin-top: 3px;
    }

    .meal-description {
      padding: 12px 15px;
      background: #1f261f;
      border-radius: 8px;
      margin-bottom: 15px;
    }

    .meal-description p {
      font-size: 13px;
      color: #aaa;
      line-height: 1.5;
    }

    .ingredients-list {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 15px;
    }

    .ingredient-tag {
      font-size: 11px;
      padding: 5px 12px;
      background: #2a352a;
      border-radius: 15px;
      color: #aaa;
    }

    /* Side Panel - Nutrition Summary */
    .side-panel {
      width: 300px;
      flex-shrink: 0;
    }

    .nutrition-card {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 14px;
      padding: 20px;
      border: 1px solid #2a352a;
      margin-bottom: 20px;
    }

    .nutrition-title {
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 20px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    /* Circular Progress */
    .calorie-circle {
      position: relative;
      width: 150px;
      height: 150px;
      margin: 0 auto 20px;
    }

    .calorie-circle svg {
      transform: rotate(-90deg);
    }

    .calorie-circle circle {
      fill: none;
      stroke-width: 10;
    }

    .calorie-bg {
      stroke: #2a352a;
    }

    .calorie-progress {
      stroke: #00d26a;
      stroke-linecap: round;
      transition: stroke-dashoffset 0.5s ease;
    }

    .calorie-text {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      text-align: center;
    }

    .calorie-value {
      font-size: 28px;
      font-weight: 700;
      color: #00d26a;
    }

    .calorie-label {
      font-size: 11px;
      color: #888;
    }

    /* Macro Bars */
    .macro-list {
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    .macro-item {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .macro-header {
      display: flex;
      justify-content: space-between;
      font-size: 12px;
    }

    .macro-name {
      color: #aaa;
    }

    .macro-value {
      font-weight: 600;
    }

    .macro-bar {
      height: 6px;
      background: #2a352a;
      border-radius: 3px;
      overflow: hidden;
    }

    .macro-fill {
      height: 100%;
      border-radius: 3px;
      transition: width 0.5s ease;
    }

    .macro-fill.protein {
      background: linear-gradient(90deg, #ff6b6b, #ee5a5a);
    }

    .macro-fill.carbs {
      background: linear-gradient(90deg, #ffd93d, #f0c040);
    }

    .macro-fill.fat {
      background: linear-gradient(90deg, #6bcfff, #4fb8e8);
    }

    .macro-fill.fiber {
      background: linear-gradient(90deg, #00d26a, #00a854);
    }

    /* Water Tracker */
    .water-tracker {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 14px;
      padding: 20px;
      border: 1px solid #2a352a;
    }

    .water-glasses {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 10px;
      margin-top: 15px;
    }

    .water-glass {
      width: 100%;
      aspect-ratio: 1;
      border-radius: 10px;
      background: #2a352a;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 20px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .water-glass.filled {
      background: linear-gradient(135deg, #00bcd433, #00838f33);
      border-color: #00bcd4;
    }

    .water-glass:hover:not(.filled) {
      background: #3a453a;
    }

    /* Weekly/Monthly View Specific */
    .week-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 10px;
      margin-bottom: 25px;
    }

    .week-day-card {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 12px;
      padding: 15px 10px;
      text-align: center;
      border: 1px solid #2a352a;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .week-day-card.active {
      border-color: #00d26a;
      background: linear-gradient(135deg, #1a3d2a, #0d2818);
    }

    .week-day-card:hover:not(.active) {
      border-color: #555;
    }

    .week-day-name {
      font-size: 11px;
      color: #888;
      margin-bottom: 5px;
    }

    .week-day-date {
      font-size: 18px;
      font-weight: 600;
    }

    .week-day-calories {
      font-size: 10px;
      color: #00d26a;
      margin-top: 5px;
    }

    /* Month Grid */
    .month-summary {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px;
      margin-bottom: 25px;
    }

    .month-stat-card {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 14px;
      padding: 25px;
      border: 1px solid #2a352a;
      text-align: center;
    }

    .month-stat-icon {
      font-size: 30px;
      margin-bottom: 10px;
    }

    .month-stat-value {
      font-size: 28px;
      font-weight: 700;
      color: #00d26a;
      margin-bottom: 5px;
    }

    .month-stat-label {
      font-size: 12px;
      color: #888;
    }

    .weekly-breakdown {
      background: linear-gradient(145deg, #1a201a, #151915);
      border-radius: 14px;
      padding: 20px;
      border: 1px solid #2a352a;
    }

    .week-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 0;
      border-bottom: 1px solid #2a352a;
    }

    .week-row:last-child {
      border-bottom: none;
    }

    .week-label {
      font-size: 14px;
      font-weight: 500;
    }

    .week-stats {
      display: flex;
      gap: 20px;
    }

    .week-stat {
      text-align: right;
    }

    .week-stat-value {
      font-size: 14px;
      font-weight: 600;
      color: #00d26a;
    }

    .week-stat-label {
      font-size: 10px;
      color: #666;
    }

    /* Hidden class */
    .hidden {
      display: none !important;
    }

    /* Action Buttons */
    .action-row {
      display: flex;
      gap: 10px;
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

    /* Ensure interaction on critical buttons */
    .toggle-taken-btn {
      position: relative !important;
      z-index: 9999 !important;
      pointer-events: all !important;
      cursor: pointer !important;
    }
  </style>
</head>

<body>
    <script>
    // DEFINE TOGGLE EARLY
    window.handleConsumptionToggle = function(btn) {
        if (!btn || btn.disabled) return;
        
        const dietId = btn.getAttribute('data-diet-id');
        const currentStatus = btn.getAttribute('data-status');
        const newStatus = (currentStatus == '1') ? 0 : 1;
        
        btn.disabled = true;
        const originalText = btn.innerText;
        btn.innerText = 'Syncing...';
        btn.style.opacity = '0.5';

        const formData = new FormData();
        formData.append('diet_plan_id', dietId);
        formData.append('is_consumed', newStatus);

        fetch('../handlers/member/mark_consumed.php', {
            method: 'POST',
            body: formData
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                const url = new URL(window.location.href);
                window.location.href = url.pathname + url.search;
            } else {
                alert('Error: ' + data.error);
                btn.disabled = false;
                btn.innerText = originalText;
                btn.style.opacity = '1';
            }
        })
        .catch(function(err) {
            alert('Connection error. Please refresh.');
            btn.disabled = false;
            btn.innerText = originalText;
            btn.style.opacity = '1';
        });
    };

    // DEBUG CLICK INTERCEPTOR
    document.addEventListener('mousedown', function(e) {
        console.log('Down on:', e.target.tagName, e.target.className);
        if (e.target.closest('.toggle-taken-btn')) {
            console.log('Caught toggle click via delegation');
            window.handleConsumptionToggle(e.target.closest('.toggle-taken-btn'));
        }
    }, true);
    </script>
  <?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <div class="page-header">
      <h1>Your Diet Plan</h1>
      <div class="view-toggle">
        <button class="view-btn active" data-view="daily">Daily</button>
        <button class="view-btn" data-view="weekly">Weekly</button>
        <button class="view-btn" data-view="monthly">Monthly</button>
      </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-overview" id="statsOverview">
      <div class="stat-card">
        <div class="stat-label">Taken / Planned</div>
        <div class="stat-value"><?php echo $taken_calories; ?> / <?php echo $total_calories; ?><span class="stat-unit">kcal</span></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Daily Target</div>
        <div class="stat-value"><?php echo $calorie_target; ?><span class="stat-unit">kcal</span></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Meals Done</div>
        <div class="stat-value">
           <?php 
             $done = count(array_filter($todays_meals, function($m) { return $m['is_consumed'] == 1; }));
             echo $done . ' / ' . count($todays_meals);
           ?>
        </div>
      </div>
    </div>

    <div id="dailyView">
      <div class="selector-row" id="daySelector">
        <?php 
          $days_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
          for($i = 1; $i <= 7; $i++) {
            $label = "Day $i<br><small>" . $days_labels[$i-1] . "</small>";
            $active = ($i == $selected_day_num) ? 'active' : '';
            $status_class = $diet_week_statuses[$i];
            echo "<button class='selector-btn $active $status_class' onclick='window.location.href=\"?day=$i\"'>$label</button>";
          }
        ?>
      </div>

      <div class="diet-content">
        <div class="meals-panel">
          <h2>Today's Meals</h2>

          <?php if (empty($plans)): ?>
            <div class="meal-card">
              <div class="meal-description">
                <p>No diet plan assigned for today. Please contact your trainer.</p>
              </div>
            </div>
          <?php else: ?>
            <?php 
              // 1. Trainer assigned meals
              $icons = ['breakfast' => '🌅', 'lunch' => '🥗', 'dinner' => '🌙', 'snack' => '🍎'];
              foreach ($plans as $meal):
                if ($meal['created_by'] == 'trainer'):
                  $icon = $icons[$meal['meal_time']] ?? '🍽️';
            ?>
                <div class="meal-card">
                  <div class="meal-header">
                    <div class="meal-title-row">
                      <div class="meal-icon <?php echo $meal['meal_time']; ?>"><?php echo $icon; ?></div>
                      <div>
                        <div class="meal-type"><?php echo ucfirst($meal['meal_time']); ?></div>
                        <div class="meal-name"><?php echo htmlspecialchars($meal['meal_name']); ?></div>
                      </div>
                    </div>
                    <div class="meal-time"><?php echo ucfirst($meal['meal_time']); ?></div>
                  </div>
                  <div class="meal-description">
                    <p><?php echo nl2br(htmlspecialchars($meal['food_items'])); ?></p>
                  </div>
                  <div style="display: flex; gap: 15px; align-items: center; padding-top: 10px; border-top: 1px solid #2a352a;">
                    <button type="button" 
                            class="btn toggle-taken-btn <?php echo $meal['is_consumed'] ? 'btn-primary' : 'btn-secondary'; ?>" 
                            style="flex: 1; padding: 12px; font-size: 13px; font-weight: bold; cursor: pointer !important;"
                            data-diet-id="<?php echo $meal['diet_plan_id']; ?>"
                            data-status="<?php echo $meal['is_consumed'] ? 1 : 0; ?>">
                      <?php echo $meal['is_consumed'] ? '✓ Meal Taken' : 'Mark as Taken'; ?>
                    </button>
                    <?php if (!empty($meal['calories'])): ?>
                      <div class="meal-detail-item" style="min-width: 80px;">
                        <div class="detail-value"><?php echo $meal['calories']; ?></div>
                        <div class="detail-label">kcal</div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
            <?php 
                endif;
              endforeach; 
              
              // 2. Member extra snacks
              foreach ($plans as $meal):
                if ($meal['created_by'] == 'member'):
            ?>
                <div class="meal-card" style="border-left: 4px solid #f0c040;">
                  <div class="meal-header">
                    <div class="meal-title-row">
                      <div class="meal-icon snack">⚡</div>
                      <div>
                        <div class="meal-type">Extra Intake</div>
                        <div class="meal-name"><?php echo htmlspecialchars($meal['meal_name']); ?></div>
                      </div>
                    </div>
                    <button type="button" class="btn" style="background: none; color: #ff4d4d; padding: 5px; font-size: 16px; cursor: pointer;" onclick="deleteIntake(<?php echo $meal['diet_plan_id']; ?>)">🗑️</button>
                  </div>
                  <div class="meal-description">
                    <p><?php echo nl2br(htmlspecialchars($meal['food_items'])); ?></p>
                  </div>
                  <div style="display: flex; gap: 15px; align-items: center; padding-top: 10px; border-top: 1px solid #2a352a;">
                    <div class="meal-detail-item" style="min-width: 60px;">
                        <div class="detail-value"><?php echo $meal['calories']; ?></div>
                        <div class="detail-label">kcal</div>
                    </div>
                    <?php if (!empty($meal['product_weight'])): ?>
                    <div class="meal-detail-item" style="min-width: 60px;">
                        <div class="detail-value"><?php echo $meal['product_weight']; ?></div>
                        <div class="detail-label">grams</div>
                    </div>
                    <?php endif; ?>
                    <div style="flex: 1; text-align: right; display: flex; gap: 8px; justify-content: flex-end;">
                        <button type="button" class="btn btn-secondary" style="padding: 6px 12px; font-size: 11px;" onclick='openEditIntake(<?php echo json_encode($meal); ?>)'>✏️ Edit</button>
                        <button type="button" class="btn" style="background: rgba(255, 77, 77, 0.1); color: #ff4d4d; border: 1px solid #ff4d4d33; padding: 6px 10px; font-size: 11px;" onclick="deleteIntake(<?php echo $meal['diet_plan_id']; ?>)">🗑️</button>
                    </div>
                  </div>
                </div>
            <?php 
                endif;
              endforeach;
            ?>
          <?php endif; ?>

          <!-- Integrated Quick Add -->
          <div id="extraIntakeForm" style="margin-top: 25px; background: linear-gradient(145deg, #1a201a, #151915); padding: 25px; border-radius: 14px; border: 1px solid #2a352a;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-size: 16px; margin: 0; color: #00d26a; text-transform: uppercase; letter-spacing: 1px;">⚡ Quick Log Extra Intake</h2>
                <span style="font-size: 11px; color: #888;">Adding to <strong>Day <?php echo $selected_day_num; ?></strong></span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label class="stat-label" style="display: block; margin-bottom: 5px;">Item Name</label>
                    <input type="text" id="extraMealName" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d; border-color: #2a352a;" placeholder="e.g. Protein Bar">
                </div>
                <div>
                    <label class="stat-label" style="display: block; margin-bottom: 5px;">Calories (kcal)</label>
                    <input type="number" id="extraCalories" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d; border-color: #2a352a;" value="0">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                <div>
                    <label class="stat-label" style="display: block; margin-bottom: 5px;">Weight (g / unit)</label>
                    <input type="number" id="extraWeight" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d; border-color: #2a352a;" value="0">
                </div>
                <div>
                    <label class="stat-label" style="display: block; margin-bottom: 5px;">Target Date</label>
                    <select id="extraDate" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d; border-color: #2a352a;">
                        <?php 
                        $days_labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                        for($i=1; $i<=7; $i++): ?>
                            <option value="<?php echo $week_dates[$i]; ?>" <?php echo ($i == $selected_day_num) ? 'selected' : ''; ?>>
                                Day <?php echo $i; ?> (<?php echo $days_labels[$i-1]; ?>)
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <button class="btn btn-primary" style="width: 100%;" onclick="logExtraIntake()">+ Save Entry for Day <?php echo $selected_day_num; ?></button>
          </div>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
          <div class="nutrition-card">
            <div class="nutrition-title">📊 Daily Nutrition</div>

            <div class="calorie-circle">
              <svg width="150" height="150">
                <circle class="calorie-bg" cx="75" cy="75" r="65"></circle>
                <?php 
                  $total_goal = max($calorie_target, $total_calories);
                  $dash_array = 408;
                  $dash_offset = $total_goal > 0 ? $dash_array - ($dash_array * min(1, $taken_calories / $total_goal)) : $dash_array;
                ?>
                <circle class="calorie-progress" cx="75" cy="75" r="65" stroke-dasharray="<?php echo $dash_array; ?>" stroke-dashoffset="<?php echo $dash_offset; ?>">
                </circle>
              </svg>
              <div class="calorie-text">
                <div class="calorie-value"><?php echo number_format($taken_calories); ?></div>
                <div class="calorie-label">of <?php echo number_format($total_calories); ?> planned</div>
              </div>
            </div>

            <div class="macro-list">
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Protein</span>
                  <span class="macro-value"><?php echo $calSummary['taken_protein'] ?? 0; ?>g / <?php echo $calSummary['planned_protein'] ?? 0; ?>g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill protein" style="width: <?php echo ($calSummary['planned_protein'] ?? 0) > 0 ? min(100, (($calSummary['taken_protein'] ?? 0) / $calSummary['planned_protein']) * 100) : 0; ?>%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Carbs</span>
                  <span class="macro-value"><?php echo $calSummary['taken_carbs'] ?? 0; ?>g / <?php echo $calSummary['planned_carbs'] ?? 0; ?>g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill carbs" style="width: <?php echo ($calSummary['planned_carbs'] ?? 0) > 0 ? min(100, (($calSummary['taken_carbs'] ?? 0) / $calSummary['planned_carbs']) * 100) : 0; ?>%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Fat</span>
                  <span class="macro-value"><?php echo $calSummary['taken_fat'] ?? 0; ?>g / <?php echo $calSummary['planned_fat'] ?? 0; ?>g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill fat" style="width: <?php echo ($calSummary['planned_fat'] ?? 0) > 0 ? min(100, (($calSummary['taken_fat'] ?? 0) / $calSummary['planned_fat']) * 100) : 0; ?>%"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="water-tracker">
            <div class="nutrition-title">💧 Water Intake</div>
            <div class="water-glasses" id="waterGlasses">
              <div class="water-glass filled">💧</div>
              <div class="water-glass filled">💧</div>
              <div class="water-glass filled">💧</div>
              <div class="water-glass filled">💧</div>
              <div class="water-glass filled">💧</div>
              <div class="water-glass filled">💧</div>
              <div class="water-glass">💧</div>
              <div class="water-glass">💧</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Weekly View -->
    <div id="weeklyView" class="hidden">
      <div class="week-grid" id="weekGrid">
        <?php
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $php_days = [1, 2, 3, 4, 5, 6, 0]; // Mon-Sun
        $current_day_of_week = date('w');
        $monday = strtotime('monday this week');
        for ($i = 0; $i < 7; $i++) {
          $date = date('d', $monday + $i * 86400);
          $php_day = $php_days[$i];
          $calories = isset($weekly_calories[$php_day]) ? number_format($weekly_calories[$php_day]) . ' kcal' : '—';
          $is_active = ($php_day == $current_day_of_week) ? ' active' : '';
          echo "<div class=\"week-day-card{$is_active}\" data-day=\"{$i}\">
                  <div class=\"week-day-name\">{$days[$i]}</div>
                  <div class=\"week-day-date\">{$date}</div>
                  <div class=\"week-day-calories\">{$calories}</div>
                </div>";
        }
        ?>
      </div>

      <div class="diet-content">
        <div class="meals-panel">
          <h2>Weekly Summary</h2>

          <div class="month-summary">
            <div class="month-stat-card">
              <div class="month-stat-icon">🔥</div>
              <div class="month-stat-value"><?php echo number_format(array_sum($weekly_calories)); ?></div>
              <div class="month-stat-label">Total Calories This Week</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">🎯</div>
              <div class="month-stat-value"><?php echo $weekly_adherence; ?>%</div>
              <div class="month-stat-label">Diet Plan Adherence</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">🥗</div>
              <div class="month-stat-value"><?php echo $weekly_plans_count; ?></div>
              <div class="month-stat-label">Meals Logged</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">📅</div>
              <div class="month-stat-value"><?php echo $weekly_days_tracked; ?>/7</div>
              <div class="month-stat-label">Days Logged</div>
            </div>
          </div>

          <div class="weekly-breakdown">
            <div class="nutrition-title">📈 Daily Breakdown</div>
            <?php
            for ($i = 0; $i < 7; $i++) {
              $day_name = $days[$i];
              $php_day = $php_days[$i];
              $calories = isset($weekly_calories[$php_day]) ? number_format($weekly_calories[$php_day]) : '—';
              echo "<div class=\"week-row\">
                      <span class=\"week-label\">{$day_name}</span>
                      <div class=\"week-stats\">
                        <div class=\"week-stat\">
                          <div class=\"week-stat-value\">{$calories}</div>
                          <div class=\"week-stat-label\">Calories</div>
                        </div>
                      </div>
                    </div>";
            }
            ?>
          </div>
        </div>

        <div class="side-panel">
          <div class="nutrition-card">
            <div class="nutrition-title">📊 Weekly Averages</div>
            <?php 
              $weekly_avg = $weekly_days_tracked > 0 ? array_sum($weekly_calories) / $weekly_days_tracked : 0;
            ?>
            <div class="calorie-circle">
              <svg width="150" height="150">
                <circle class="calorie-bg" cx="75" cy="75" r="65"></circle>
                <?php 
                  $avg_offset = $calorie_target > 0 ? 408 - (408 * min(1, $weekly_avg / $calorie_target)) : 408;
                ?>
                <circle class="calorie-progress" cx="75" cy="75" r="65" stroke-dasharray="408" stroke-dashoffset="<?php echo $avg_offset; ?>">
                </circle>
              </svg>
              <div class="calorie-text">
                <div class="calorie-value"><?php echo number_format($weekly_avg); ?></div>
                <div class="calorie-label">avg per day</div>
              </div>
            </div>

            <div class="macro-list">
              <p style="font-size: 11px; color: #666; text-align: center;">Average macros are calculated based on your daily intake this week.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly View -->
    <div id="monthlyView" class="hidden">
      <div class="selector-row">
        <button class="selector-btn active"><?php echo date('F Y'); ?></button>
      </div>

      <div class="diet-content">
        <div class="meals-panel">
          <h2>Monthly Overview</h2>

          <div class="month-summary">
            <div class="month-stat-card">
              <div class="month-stat-icon">🔥</div>
              <div class="month-stat-value"><?php echo number_format($monthly_total_calories); ?></div>
              <div class="month-stat-label">Total Calories Consumed</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">🎯</div>
              <div class="month-stat-value"><?php echo $adherence; ?>%</div>
              <div class="month-stat-label">Diet Plan Adherence</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">⚖️</div>
              <div class="month-stat-value"><?php echo $weight_change > 0 ? '+' : ''; ?><?php echo number_format($weight_change, 1); ?></div>
              <div class="month-stat-label">Weight Change (kg)</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">📅</div>
              <div class="month-stat-value"><?php echo $days_tracked; ?></div>
              <div class="month-stat-label">Days Tracked</div>
            </div>
          </div>

          <div class="weekly-breakdown">
            <div class="nutrition-title">📊 Monthly Achievements</div>
            <div class="week-row">
              <span class="week-label">🏆 Adherence</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value"><?php echo $adherence; ?>%</div>
                  <div class="week-stat-label">Goal Met</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">🥗 Total Meals</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value"><?php echo count(array_filter($all_plans, function($p) use ($month_start, $month_end) { return $p['plan_date'] >= $month_start && $p['plan_date'] <= $month_end; })); ?></div>
                  <div class="week-stat-label">Recorded</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="side-panel">
          <div class="nutrition-card">
            <div class="nutrition-title">📊 Monthly Averages</div>
            <?php 
              $monthly_avg = $days_tracked > 0 ? $monthly_total_calories / $days_tracked : 0;
            ?>
            <div class="calorie-circle">
              <svg width="150" height="150">
                <circle class="calorie-bg" cx="75" cy="75" r="65"></circle>
                <?php 
                  $m_avg_offset = $calorie_target > 0 ? 408 - (408 * min(1, $monthly_avg / $calorie_target)) : 408;
                ?>
                <circle class="calorie-progress" cx="75" cy="75" r="65" stroke-dasharray="408" stroke-dashoffset="<?php echo $m_avg_offset; ?>">
                </circle>
              </svg>
              <div class="calorie-text">
                <div class="calorie-value"><?php echo number_format($monthly_avg); ?></div>
                <div class="calorie-label">avg per day</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  <!-- Member Edit Intake Modal -->
  <div id="editIntakeModal" class="hidden" style="position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 10001; display: none; align-items: center; justify-content: center; padding: 20px;">
    <div style="background: #1a201a; border: 1px solid #2a352a; border-radius: 14px; width: 100%; max-width: 450px; padding: 30px;">
        <h2 style="color: #00d26a; font-size: 18px; margin-bottom: 20px;">Edit Extra Intake</h2>
        <input type="hidden" id="editIntakeId">
        
        <div style="margin-bottom: 15px;">
            <label class="stat-label">Item Name</label>
            <input type="text" id="editMealName" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d;">
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
            <div>
                <label class="stat-label">Calories</label>
                <input type="number" id="editCalories" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d;">
            </div>
            <div>
                <label class="stat-label">Weight (g)</label>
                <input type="number" id="editWeight" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d;">
            </div>
        </div>

        <div style="margin-bottom: 25px;">
            <label class="stat-label">Date</label>
            <select id="editDate" class="selector-btn" style="width: 100%; text-align: left; background: #0d110d;">
                <?php foreach($week_dates as $idx => $date): ?>
                    <option value="<?php echo $date; ?>">Day <?php echo $idx; ?> (<?php echo $week_dates[$idx]; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <button class="btn btn-secondary" style="flex: 1;" onclick="closeEditModal()">Cancel</button>
            <button class="btn btn-primary" style="flex: 2;" onclick="updateExtraIntake()">Update Entry</button>
        </div>
    </div>
  </div>

  <script>
    function openEditIntake(meal) {
        document.getElementById('editIntakeId').value = meal.diet_plan_id;
        document.getElementById('editMealName').value = meal.meal_name;
        document.getElementById('editCalories').value = meal.calories;
        document.getElementById('editWeight').value = meal.product_weight;
        document.getElementById('editDate').value = meal.plan_date;
        
        const modal = document.getElementById('editIntakeModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';
    }

    function closeEditModal() {
        const modal = document.getElementById('editIntakeModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';
    }

    function logExtraIntake() {
        const meal = document.getElementById('extraMealName').value;
        const cals = document.getElementById('extraCalories').value;
        const weight = document.getElementById('extraWeight').value;
        const date = document.getElementById('extraDate').value;

        if (!meal) { alert('Enter meal name'); return; }

        const formData = new FormData();
        formData.append('meal_name', meal);
        formData.append('calories', cals);
        formData.append('product_weight', weight);
        formData.append('plan_date', date);

        fetch('../handlers/member/add_extra_intake.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                let dayNum = 1;
                const select = document.getElementById('extraDate');
                if (select) {
                    const options = select.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value === date) {
                            dayNum = i + 1;
                            break;
                        }
                    }
                }
                window.location.href = '?day=' + dayNum;
            } else {
                alert('Error: ' + data.error);
            }
        });
    }

    function updateExtraIntake() {
        const id = document.getElementById('editIntakeId').value;
        const meal = document.getElementById('editMealName').value;
        const cals = document.getElementById('editCalories').value;
        const weight = document.getElementById('editWeight').value;
        const date = document.getElementById('editDate').value;

        const formData = new FormData();
        formData.append('diet_plan_id', id);
        formData.append('meal_name', meal);
        formData.append('calories', cals);
        formData.append('product_weight', weight);
        formData.append('plan_date', date);

        fetch('../handlers/member/edit_extra_intake.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                let dayNum = 1;
                const select = document.getElementById('editDate');
                if (select) {
                    const options = select.options;
                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value === date) {
                            dayNum = i + 1;
                            break;
                        }
                    }
                }
                window.location.href = '?day=' + dayNum;
            } else {
                alert('Error: ' + data.error);
            }
        });
    }

    function deleteIntake(id) {
        if (!confirm('Are you sure you want to delete this entry?')) return;
        
        const formData = new FormData();
        formData.append('diet_plan_id', id);

        fetch('../handlers/member/delete_diet_plan.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const urlParams = new URLSearchParams(window.location.search);
                const currentDay = urlParams.get('day');
                if (currentDay) {
                    window.location.href = '?day=' + currentDay;
                } else {
                    window.location.reload();
                }
            } else {
                alert('Error: ' + data.error);
            }
        });
    }

    // View Toggle
    const viewBtns = document.querySelectorAll('.view-btn');
    const dailyView = document.getElementById('dailyView');
    const weeklyView = document.getElementById('weeklyView');
    const monthlyView = document.getElementById('monthlyView');

    viewBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        viewBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const view = btn.dataset.view;
        dailyView.classList.add('hidden');
        weeklyView.classList.add('hidden');
        monthlyView.classList.add('hidden');

        if (view === 'daily') {
          dailyView.classList.remove('hidden');
        } else if (view === 'weekly') {
          weeklyView.classList.remove('hidden');
        } else if (view === 'monthly') {
          monthlyView.classList.remove('hidden');
        }
      });
    });

    // Day Selector (Daily View)
    const dayBtns = document.querySelectorAll('#daySelector .selector-btn');
    dayBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        dayBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });

    // Week Day Cards (Weekly View)
    const weekCards = document.querySelectorAll('.week-day-card');
    weekCards.forEach(card => {
      card.addEventListener('click', () => {
        weekCards.forEach(c => c.classList.remove('active'));
        card.classList.add('active');
      });
    });

    // Water Glass Toggle
    const waterGlasses = document.querySelectorAll('.water-glass');
    waterGlasses.forEach((glass, index) => {
      glass.addEventListener('click', () => {
        const isFilled = glass.classList.contains('filled');
        waterGlasses.forEach((g, i) => {
          if (i <= index && !isFilled) {
            g.classList.add('filled');
          } else if (i >= index && isFilled) {
            g.classList.remove('filled');
          }
        });
      });
    });

    // Month selector buttons
    const monthBtns = document.querySelectorAll('#monthlyView .selector-btn');
    monthBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        monthBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
      });
    });
    </script>
</body>
</html>