<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get all progress logs
$progressLogs = getMemberProgress($member_id, 30);
$latestProgress = !empty($progressLogs) ? $progressLogs[0] : null;
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Progress - Pranayom</title>
    <style>
      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
      }

      body {
        background-color: #121712; /* Very dark green/black */
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
        margin-bottom: 30px;
      }

      h2 {
        font-size: 18px;
        margin-bottom: 20px;
        color: white;
      }

      /* Metrics Section */
      .metrics-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
      }

      .metric-card {
        background-color: #1a201a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 20px;
      }

      .metric-title {
        font-size: 14px;
        color: #ccc;
        margin-bottom: 10px;
      }

      .metric-value {
        font-size: 36px;
        font-weight: bold;
        margin-bottom: 5px;
      }

      .metric-trend {
        font-size: 12px;
        color: #888;
        margin-bottom: 20px;
      }

      .trend-up {
        color: #00d26a;
      }
      .trend-down {
        color: #d32f2f;
      }

      .metric-graph {
        height: 100px;
        width: 100%;
      }

      /* SVG Graph Styling */
      svg {
        width: 100%;
        height: 100%;
        overflow: visible;
      }
      path.line {
        fill: none;
        stroke: #888;
        stroke-width: 2;
      }

      .graph-labels {
        display: flex;
        justify-content: space-between;
        font-size: 10px;
        color: #666;
        margin-top: 10px;
      }

      /* Workout Stats */
      .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 40px;
      }

      .stat-card {
        background-color: #1a201a;
        border: 1px solid #333;
        border-radius: 12px;
        padding: 25px;
      }

      .stat-title {
        color: #ccc;
        font-size: 14px;
        margin-bottom: 10px;
      }
      .stat-value {
        font-size: 28px;
        font-weight: bold;
      }

      /* Recent Workouts Table */
      .workouts-table-container {
        background-color: #1a201a;
        border: 1px solid #333;
        border-radius: 12px;
        overflow: hidden;
      }

      table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
      }

      th,
      td {
        text-align: left;
        padding: 15px 20px;
        border-bottom: 1px solid #333;
      }

      th {
        background-color: #1f261f;
        color: #aaa;
        font-weight: normal;
      }

      tr:last-child td {
        border-bottom: none;
      }

      tr:hover {
        background-color: #222;
      }
    </style>
  </head>
  <body>
<?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="main-content">
      <h1>Your Progress</h1>

      <h2>Health Metrics Over Time</h2>
      <div class="metrics-grid">
        <!-- Card 1: Weight -->
        <div class="metric-card">
          <div class="metric-title">Weight (lbs)</div>
          <div class="metric-value">150</div>
          <div class="metric-trend">
            Last 30 Days <span class="trend-down">-2%</span>
          </div>
          <div class="metric-graph">
            <!-- Fake SVG Data -->
            <svg viewBox="0 0 300 100" preserveAspectRatio="none">
              <path
                class="line"
                d="M0,80 Q30,20 60,60 T120,60 T180,20 T240,80 T300,50"
                style="stroke: #aaa"
              />
            </svg>
            <div class="graph-labels">
              <span>Week 1</span><span>Week 2</span><span>Week 3</span
              ><span>Week 4</span>
            </div>
          </div>
        </div>

        <!-- Card 2: Heart Rate -->
        <div class="metric-card">
          <div class="metric-title">Heart Rate (bpm)</div>
          <div class="metric-value">72</div>
          <div class="metric-trend">
            Last 30 Days <span class="trend-up">+1%</span>
          </div>
          <div class="metric-graph">
            <svg viewBox="0 0 300 100" preserveAspectRatio="none">
              <path
                class="line"
                d="M0,60 Q40,90 80,40 T160,50 T240,80 T300,30"
                style="stroke: #aaa"
              />
            </svg>
            <div class="graph-labels">
              <span>Week 1</span><span>Week 2</span><span>Week 3</span
              ><span>Week 4</span>
            </div>
          </div>
        </div>

        <!-- Card 3: Sleep -->
        <div class="metric-card">
          <div class="metric-title">Sleep (hours)</div>
          <div class="metric-value">7.5</div>
          <div class="metric-trend">
            Last 30 Days <span class="trend-up">+5%</span>
          </div>
          <div class="metric-graph">
            <svg viewBox="0 0 300 100" preserveAspectRatio="none">
              <path
                class="line"
                d="M0,70 Q50,30 100,70 T200,80 T300,40"
                style="stroke: #aaa"
              />
            </svg>
            <div class="graph-labels">
              <span>Week 1</span><span>Week 2</span><span>Week 3</span
              ><span>Week 4</span>
            </div>
          </div>
        </div>

        <!-- Card 4: Mood -->
        <div class="metric-card">
          <div class="metric-title">Mood</div>
          <div class="metric-value">Good</div>
          <div class="metric-trend">Last 30 Days 0%</div>
          <div class="metric-graph">
            <svg viewBox="0 0 300 100" preserveAspectRatio="none">
              <path
                class="line"
                d="M0,50 Q40,20 80,50 T160,50 T240,20 T300,40"
                style="stroke: #aaa"
              />
            </svg>
            <div class="graph-labels">
              <span>Week 1</span><span>Week 2</span><span>Week 3</span
              ><span>Week 4</span>
            </div>
          </div>
        </div>
      </div>

      <h2>Workout Statistics</h2>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-title">Total Yoga Duration</div>
          <div class="stat-value">120 hours</div>
        </div>
        <div class="stat-card">
          <div class="stat-title">Average Workout Effectiveness</div>
          <div class="stat-value">8/10</div>
        </div>
      </div>

      <h2>Recent Workouts</h2>
      <div class="workouts-table-container">
        <table>
          <thead>
            <tr>
              <th>Date</th>
              <th>Workout Type</th>
              <th>Duration</th>
              <th>Effectiveness</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>2023-11-15</td>
              <td>Morning Yoga Flow</td>
              <td>60 mins</td>
              <td>9/10</td>
            </tr>
            <tr>
              <td>2023-11-14</td>
              <td>Evening Relaxation</td>
              <td>45 mins</td>
              <td>7/10</td>
            </tr>
            <tr>
              <td>2023-11-12</td>
              <td>Power Yoga</td>
              <td>75 mins</td>
              <td>8/10</td>
            </tr>
            <tr>
              <td>2023-11-10</td>
              <td>Gentle Yoga</td>
              <td>60 mins</td>
              <td>7/10</td>
            </tr>
            <tr>
              <td>2023-11-08</td>
              <td>Yoga for Flexibility</td>
              <td>45 mins</td>
              <td>8/10</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </body>
</html>
