<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];
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
      background: linear-gradient(135deg, #1a3d2a, #0d2818);
      border-color: #00d26a;
      color: #00d26a;
    }

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
  </style>
</head>

<body>
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
        <div class="stat-label">Today's Target</div>
        <div class="stat-value"><?php echo $total_calories; ?><span class="stat-unit">kcal</span></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Diet Status</div>
        <div class="stat-value"><?php echo count($todays_meals) > 0 ? 'Active' : 'No Plan'; ?></div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Meals</div>
        <div class="stat-value"><?php echo count($todays_meals); ?><span class="stat-unit">items</span></div>
      </div>
    </div>

$today = date('Y-m-d');
$plans = getMemberDietPlans($member_id, $today);
$todays_meals = [];
$total_calories = 0;
if (is_array($plans)) {
    foreach ($plans as $p) {
        if (isset($p['meal_time'])) {
            $todays_meals[$p['meal_time']] = $p;
            $total_calories += intval($p['calories'] ?? 0);
        }
    }
}
?>

    <div id="dailyView">
      <div class="selector-row" id="daySelector">
        <button class="selector-btn active" data-day="today">Today (<?php echo date('M d'); ?>)</button>
      </div>

      <div class="diet-content">
        <div class="meals-panel">
          <h2>Today's Meals</h2>

          <?php if (empty($todays_meals)): ?>
            <div class="meal-card">
                <div class="meal-description">
                    <p>No diet plan assigned for today. Please contact your trainer.</p>
                </div>
            </div>
          <?php else: ?>
            <?php foreach (['breakfast' => '🌅', 'lunch' => '🥗', 'dinner' => '🌙', 'snack' => '🍎'] as $time => $icon): ?>
                <?php if (isset($todays_meals[$time])): $meal = $todays_meals[$time]; ?>
                  <div class="meal-card">
                    <div class="meal-header">
                      <div class="meal-title-row">
                        <div class="meal-icon <?php echo $time; ?>"><?php echo $icon; ?></div>
                        <div>
                          <div class="meal-type"><?php echo ucfirst($time); ?></div>
                          <div class="meal-name"><?php echo htmlspecialchars($meal['meal_name']); ?></div>
                        </div>
                      </div>
                      <div class="meal-time"><?php echo ucfirst($time); ?></div>
                    </div>
                    <div class="meal-description">
                      <p><?php echo nl2br(htmlspecialchars($meal['food_items'])); ?></p>
                    </div>
                    <?php if (!empty($meal['calories'])): ?>
                    <div class="meal-details">
                      <div class="meal-detail-item">
                        <div class="detail-value"><?php echo $meal['calories']; ?></div>
                        <div class="detail-label">Calories</div>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Side Panel -->
        <div class="side-panel">
          <div class="nutrition-card">
            <div class="nutrition-title">📊 Daily Nutrition</div>

            <div class="calorie-circle">
              <svg width="150" height="150">
                <circle class="calorie-bg" cx="75" cy="75" r="65"></circle>
                <circle class="calorie-progress" cx="75" cy="75" r="65" stroke-dasharray="408" stroke-dashoffset="115">
                </circle>
              </svg>
              <div class="calorie-text">
                <div class="calorie-value">1,420</div>
                <div class="calorie-label">of 1,850 kcal</div>
              </div>
            </div>

            <div class="macro-list">
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Protein</span>
                  <span class="macro-value">93g / 120g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill protein" style="width: 78%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Carbs</span>
                  <span class="macro-value">133g / 180g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill carbs" style="width: 74%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Fat</span>
                  <span class="macro-value">56g / 65g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill fat" style="width: 86%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Fiber</span>
                  <span class="macro-value">22g / 30g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill fiber" style="width: 73%"></div>
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
        <div class="week-day-card" data-day="0">
          <div class="week-day-name">Mon</div>
          <div class="week-day-date">6</div>
          <div class="week-day-calories">1,820 kcal</div>
        </div>
        <div class="week-day-card" data-day="1">
          <div class="week-day-name">Tue</div>
          <div class="week-day-date">7</div>
          <div class="week-day-calories">1,750 kcal</div>
        </div>
        <div class="week-day-card" data-day="2">
          <div class="week-day-name">Wed</div>
          <div class="week-day-date">8</div>
          <div class="week-day-calories">1,900 kcal</div>
        </div>
        <div class="week-day-card active" data-day="3">
          <div class="week-day-name">Thu</div>
          <div class="week-day-date">9</div>
          <div class="week-day-calories">1,420 kcal</div>
        </div>
        <div class="week-day-card" data-day="4">
          <div class="week-day-name">Fri</div>
          <div class="week-day-date">10</div>
          <div class="week-day-calories">—</div>
        </div>
        <div class="week-day-card" data-day="5">
          <div class="week-day-name">Sat</div>
          <div class="week-day-date">11</div>
          <div class="week-day-calories">—</div>
        </div>
        <div class="week-day-card" data-day="6">
          <div class="week-day-name">Sun</div>
          <div class="week-day-date">12</div>
          <div class="week-day-calories">—</div>
        </div>
      </div>

      <div class="diet-content">
        <div class="meals-panel">
          <h2>Weekly Summary</h2>

          <div class="month-summary">
            <div class="month-stat-card">
              <div class="month-stat-icon">🔥</div>
              <div class="month-stat-value">5,890</div>
              <div class="month-stat-label">Total Calories This Week</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">🎯</div>
              <div class="month-stat-value">95%</div>
              <div class="month-stat-label">Diet Plan Adherence</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">💧</div>
              <div class="month-stat-value">24</div>
              <div class="month-stat-label">Glasses of Water</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">🥗</div>
              <div class="month-stat-value">12</div>
              <div class="month-stat-label">Meals Logged</div>
            </div>
          </div>

          <div class="weekly-breakdown">
            <div class="nutrition-title">📈 Daily Breakdown</div>
            <div class="week-row">
              <span class="week-label">Monday</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">1,820</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">115g</div>
                  <div class="week-stat-label">Protein</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">Tuesday</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">1,750</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">108g</div>
                  <div class="week-stat-label">Protein</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">Wednesday</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">1,900</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">122g</div>
                  <div class="week-stat-label">Protein</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">Thursday</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">1,420</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">93g</div>
                  <div class="week-stat-label">Protein</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="side-panel">
          <div class="nutrition-card">
            <div class="nutrition-title">📊 Weekly Averages</div>

            <div class="calorie-circle">
              <svg width="150" height="150">
                <circle class="calorie-bg" cx="75" cy="75" r="65"></circle>
                <circle class="calorie-progress" cx="75" cy="75" r="65" stroke-dasharray="408" stroke-dashoffset="40">
                </circle>
              </svg>
              <div class="calorie-text">
                <div class="calorie-value">1,722</div>
                <div class="calorie-label">avg per day</div>
              </div>
            </div>

            <div class="macro-list">
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Avg Protein</span>
                  <span class="macro-value">110g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill protein" style="width: 92%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Avg Carbs</span>
                  <span class="macro-value">165g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill carbs" style="width: 88%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Avg Fat</span>
                  <span class="macro-value">58g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill fat" style="width: 85%"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Monthly View -->
    <div id="monthlyView" class="hidden">
      <div class="selector-row">
        <button class="selector-btn active">January 2026</button>
        <button class="selector-btn">December 2025</button>
        <button class="selector-btn">November 2025</button>
      </div>

      <div class="diet-content">
        <div class="meals-panel">
          <h2>Monthly Overview</h2>

          <div class="month-summary">
            <div class="month-stat-card">
              <div class="month-stat-icon">🔥</div>
              <div class="month-stat-value">52,400</div>
              <div class="month-stat-label">Total Calories Consumed</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">🎯</div>
              <div class="month-stat-value">92%</div>
              <div class="month-stat-label">Diet Plan Adherence</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">⚖️</div>
              <div class="month-stat-value">-2.5</div>
              <div class="month-stat-label">Weight Change (kg)</div>
            </div>
            <div class="month-stat-card">
              <div class="month-stat-icon">📅</div>
              <div class="month-stat-value">28</div>
              <div class="month-stat-label">Days Tracked</div>
            </div>
          </div>

          <div class="weekly-breakdown">
            <div class="nutrition-title">📈 Weekly Breakdown</div>
            <div class="week-row">
              <span class="week-label">Week 1 (Jan 1-5)</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">9,250</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">98%</div>
                  <div class="week-stat-label">Adherence</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">Week 2 (Jan 6-12)</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">5,890</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">95%</div>
                  <div class="week-stat-label">Adherence</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">Previous Weeks</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">37,260</div>
                  <div class="week-stat-label">Calories</div>
                </div>
                <div class="week-stat">
                  <div class="week-stat-value">90%</div>
                  <div class="week-stat-label">Adherence</div>
                </div>
              </div>
            </div>
          </div>

          <div class="weekly-breakdown" style="margin-top: 20px;">
            <div class="nutrition-title">🏆 Monthly Achievements</div>
            <div class="week-row">
              <span class="week-label">🥇 Best Day</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">Jan 3</div>
                  <div class="week-stat-label">100% Goals</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">💧 Hydration Streak</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">12 Days</div>
                  <div class="week-stat-label">8+ Glasses</div>
                </div>
              </div>
            </div>
            <div class="week-row">
              <span class="week-label">🥗 Meals Logged</span>
              <div class="week-stats">
                <div class="week-stat">
                  <div class="week-stat-value">112</div>
                  <div class="week-stat-label">This Month</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="side-panel">
          <div class="nutrition-card">
            <div class="nutrition-title">📊 Monthly Averages</div>

            <div class="calorie-circle">
              <svg width="150" height="150">
                <circle class="calorie-bg" cx="75" cy="75" r="65"></circle>
                <circle class="calorie-progress" cx="75" cy="75" r="65" stroke-dasharray="408" stroke-dashoffset="32">
                </circle>
              </svg>
              <div class="calorie-text">
                <div class="calorie-value">1,872</div>
                <div class="calorie-label">avg per day</div>
              </div>
            </div>

            <div class="macro-list">
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Avg Protein</span>
                  <span class="macro-value">118g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill protein" style="width: 98%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Avg Carbs</span>
                  <span class="macro-value">172g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill carbs" style="width: 95%"></div>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Avg Fat</span>
                  <span class="macro-value">62g</span>
                </div>
                <div class="macro-bar">
                  <div class="macro-fill fat" style="width: 95%"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="nutrition-card">
            <div class="nutrition-title">📉 Trend Analysis</div>
            <div class="macro-list">
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Calories</span>
                  <span class="macro-value" style="color: #00d26a">↓ 5%</span>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Protein</span>
                  <span class="macro-value" style="color: #00d26a">↑ 12%</span>
                </div>
              </div>
              <div class="macro-item">
                <div class="macro-header">
                  <span class="macro-name">Water</span>
                  <span class="macro-value" style="color: #00d26a">↑ 20%</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Action Buttons -->
    <div class="action-row">
      <button class="btn btn-primary" onclick="window.location.href='../handlers/member/download_diet_report.php'">📄 Download Report</button>
      <button class="btn btn-secondary" onclick="window.location.href='member_chat.php'">💬 Ask Trainer</button>
    </div>
  </div>

  <script>
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
        // Toggle this glass and update all before it
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
