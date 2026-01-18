<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('admin');
$admin_name = $_SESSION['full_name'];
$stats = getAdminStats();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Pranayom</title>
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
      margin-bottom: 10px;
    }

    .subHeader {
      color: #aaa;
      font-size: 14px;
      margin-bottom: 40px;
    }

    h2 {
      font-size: 18px;
      margin-bottom: 20px;
      color: #ddd;
    }

    /* KPI Cards */
    .kpiContainer {
      display: flex;
      gap: 20px;
      margin-bottom: 40px;
    }

    .kpiCard {
      flex: 1;
      background-color: #1a201a;
      padding: 20px;
      border-radius: 8px;
    }

    .kpiLabel {
      font-size: 12px;
      color: #aaa;
      margin-bottom: 10px;
    }

    .kpiValue {
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 5px;
    }

    .kpiChange {
      font-size: 12px;
      color: #00d26a;
    }

    /* Activity Table */
    .tableContainer {
      background-color: #1a201a;
      border-radius: 8px;
      padding: 20px;
    }

    .activityTable {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .activityTable th {
      text-align: left;
      border-bottom: 1px solid #333;
      padding: 10px 0;
      color: #aaa;
      font-weight: normal;
    }

    .activityTable td {
      padding: 15px 0;
      border-bottom: 1px solid #2a2a2a;
    }

    .activityTable tr:last-child td {
      border-bottom: none;
    }

    .detailText {
      color: #aaa;
    }

    .status-badge {
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 11px;
      font-weight: bold;
    }

    .status-present {
      background-color: rgba(34, 197, 94, 0.2);
      color: #4ade80;
      border: 1px solid #22c55e;
    }

    .status-ex {
      background-color: rgba(239, 68, 68, 0.2);
      color: #f87171;
      border: 1px solid #ef4444;
    }
  </style>
</head>

<body>
<?php include __DIR__ . '/../includes/admin_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Admin Dashboard</h1>
    <div class="subHeader" style="
          display: flex;
          justify-content: space-between;
          align-items: center;
        ">
      <span>Central hub for managing all aspects of the Yoga Center</span>
      <button class="action-btn" onclick="location.href='profile.html'" style="
            padding: 8px 15px;
            background-color: #1a201a;
            border: 1px solid #333;
            color: white;
          ">
        Edit Profile
      </button>
    </div>

    <h2>Key Performance Indicators</h2>
    <div class="kpiContainer">
        <div class="kpiCard">
          <div class="kpiLabel">Total Members</div>
          <div class="kpiValue"><?php echo $stats['total_members']; ?></div>
        </div>
        <div class="kpiCard">
          <div class="kpiLabel">Total Trainers</div>
          <div class="kpiValue"><?php echo $stats['total_trainers']; ?></div>
        </div>
        <div class="kpiCard">
          <div class="kpiLabel">Active Classes</div>
          <div class="kpiValue"><?php echo $stats['active_classes']; ?></div>
        </div>
        <div class="kpiCard">
          <div class="kpiLabel">Recent Reg.</div>
          <div class="kpiValue">+<?php echo $stats['recent_registrations']; ?></div>
        </div>
    </div>

    <h2>Members Overview</h2>
    <div class="tableContainer" style="margin-bottom: 40px;">
      <table class="activityTable">
        <thead>
          <tr>
            <th width="30%">Member Name</th>
            <th width="30%">Join Date</th>
            <th width="20%">Plan</th>
            <th width="20%" style="text-align: right">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Olivia</td>
            <td class="detailText">Jan 15, 2024</td>
            <td class="detailText">Monthly</td>
            <td style="text-align: right"><span class="status-badge status-present">Present</span></td>
          </tr>
          <tr>
            <td>Noah Carter</td>
            <td class="detailText">Feb 10, 2024</td>
            <td class="detailText">Yearly</td>
            <td style="text-align: right"><span class="status-badge status-present">Present</span></td>
          </tr>
          <tr>
            <td>Emma Wilson</td>
            <td class="detailText">Nov 05, 2023</td>
            <td class="detailText">Weekly</td>
            <td style="text-align: right"><span class="status-badge status-ex">Ex-Member</span></td>
          </tr>
          <tr>
            <td>Lucas Brown</td>
            <td class="detailText">Dec 20, 2023</td>
            <td class="detailText">Monthly</td>
            <td style="text-align: right"><span class="status-badge status-present">Present</span></td>
          </tr>
        </tbody>
      </table>
    </div>

    <h2>Trainers Overview</h2>
    <div class="tableContainer">
      <table class="activityTable">
        <thead>
          <tr>
            <th width="30%">Trainer Name</th>
            <th width="30%">Specialization</th>
            <th width="20%">Experience</th>
            <th width="20%" style="text-align: right">Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>Liyen</td>
            <td class="detailText">Yoga & Pilates</td>
            <td class="detailText">5 Years</td>
            <td style="text-align: right"><span class="status-badge status-present">Present</span></td>
          </tr>
          <tr>
            <td>Marcus Johnson</td>
            <td class="detailText">Strength Training</td>
            <td class="detailText">8 Years</td>
            <td style="text-align: right"><span class="status-badge status-present">Present</span></td>
          </tr>
          <tr>
            <td>Sarah Lee</td>
            <td class="detailText">Cardio & HIIT</td>
            <td class="detailText">3 Years</td>
            <td style="text-align: right"><span class="status-badge status-ex">Ex-Trainer</span></td>
          </tr>
          <tr>
            <td>David Chen</td>
            <td class="detailText">Meditation</td>
            <td class="detailText">6 Years</td>
            <td style="text-align: right"><span class="status-badge status-present">Present</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>
