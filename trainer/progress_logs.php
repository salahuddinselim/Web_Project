<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$trainer_name = $_SESSION['full_name'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Progress Logs</title>
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
      font-size: 28px;
      font-weight: bold;
    }

    /* Key Metrics */
    .section-title {
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 15px;
      color: #ffffff;
    }

    .metrics-grid {
      display: flex;
      gap: 20px;
      margin-bottom: 40px;
    }

    .metric-card {
      flex: 1;
      border: 1px solid #4ade80;
      /* Slight green border highlight or white as per image? Image looks white/grey border */
      border: 1px solid #ffffff;
      border-radius: 8px;
      padding: 20px;
      background-color: #0e0e0e;
    }

    .metric-label {
      font-size: 14px;
      color: #ffffff;
      margin-bottom: 10px;
      display: block;
      font-weight: 500;
    }

    .metric-value {
      font-size: 32px;
      font-weight: bold;
      color: #ffffff;
    }

    /* Report Generation Form */
    .form-group {
      margin-bottom: 20px;
      max-width: 500px;
    }

    .form-label {
      display: block;
      margin-bottom: 8px;
      color: #e5e5e5;
      font-size: 14px;
    }

    .form-input {
      width: 100%;
      padding: 10px 15px;
      background-color: #1f3b26;
      /* Dark green input */
      border: 1px solid #2a4030;
      border-radius: 6px;
      color: #a0bba5;
      font-size: 14px;
      outline: none;
      box-sizing: border-box;
    }

    .generate-btn {
      background-color: #22c55e;
      color: #000000;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
      margin-bottom: 40px;
    }

    .generate-btn:hover {
      background-color: #4ade80;
    }

    /* Recent Reports Table - Dark Theme */
    .reports-table-container {
      background-color: #0d120f;
      border: 1px solid #1f2b23;
      border-radius: 8px;
      overflow: hidden;
    }

    .reports-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
      background-color: #0d120f;
    }

    .reports-table th {
      text-align: left;
      padding: 15px 20px;
      color: #e5e5e5;
      border-bottom: 1px solid #2a3830;
      font-weight: bold;
    }

    .reports-table td {
      padding: 15px 20px;
      color: #a1a1aa;
      border-bottom: 1px solid #1f2b23;
    }

    .reports-table tr:hover td {
      background-color: #111a14;
    }

    .download-link {
      color: #00d26a;
      text-decoration: underline;
      cursor: pointer;
      font-weight: 500;
      border: 1px solid #00d26a;
      /* Added button look for clarity in dark mode */
      padding: 5px 10px;
      border-radius: 4px;
      text-decoration: none;
      display: inline-block;
      transition: 0.3s;
    }

    .download-link:hover {
      background-color: #00d26a;
      color: black;
    }

    /* Report Modal & Graphs */
    .report-modal {
      display: none;
      position: fixed;
      z-index: 2000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.85);
      justify-content: center;
      align-items: center;
      padding: 20px;
      box-sizing: border-box;
    }

    .report-content {
      background-color: #1a201a;
      width: 100%;
      max-width: 800px;
      max-height: 90vh;
      overflow-y: auto;
      border-radius: 12px;
      border: 1px solid #333;
      padding: 40px;
      color: #fff;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .report-header {
      border-bottom: 2px solid #333;
      padding-bottom: 20px;
      margin-bottom: 30px;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
    }

    .report-title {
      font-size: 28px;
      font-weight: bold;
      color: #00d26a;
      margin-bottom: 5px;
    }

    .report-meta {
      color: #aaa;
      font-size: 14px;
    }

    .chart-container {
      margin-bottom: 40px;
      background-color: #0d120f;
      padding: 20px;
      border-radius: 8px;
      border: 1px solid #222;
    }

    .chart-title {
      font-size: 18px;
      margin-bottom: 20px;
      color: #eee;
    }

    /* CSS Bar Chart */
    .bar-chart {
      display: flex;
      align-items: flex-end;
      justify-content: space-around;
      height: 200px;
      padding-top: 20px;
      border-bottom: 1px solid #444;
    }

    .bar {
      width: 40px;
      background-color: #22c55e;
      transition: height 0.5s ease;
      position: relative;
      border-radius: 4px 4px 0 0;
    }

    .bar:hover {
      background-color: #4ade80;
    }

    .bar-label {
      margin-top: 10px;
      text-align: center;
      font-size: 12px;
      color: #aaa;
    }

    .bar-value {
      position: absolute;
      top: -25px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 12px;
      color: #fff;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-box {
      background-color: #222;
      padding: 20px;
      border-radius: 8px;
      border-left: 4px solid #00d26a;
    }

    .close-report {
      float: right;
      font-size: 24px;
      cursor: pointer;
      color: #aaa;
    }

    .close-report:hover {
      color: #fff;
    }
  </style>
</head>

<body>
  <div class="mainContainer">
    <?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="mainContent">
      <div class="page-header">
        <h2>Statistics and Reports</h2>
      </div>

      <div class="section-title">Member Progress Statistics</div>
      <div class="metrics-grid">
        <div class="metric-card">
          <span class="metric-label">Avg Attendance</span>
          <div class="metric-value">85%</div>
        </div>
        <div class="metric-card">
          <span class="metric-label">Health Score Imp.</span>
          <div class="metric-value">+12%</div>
        </div>
        <div class="metric-card">
          <span class="metric-label">Goals Completed</span>
          <div class="metric-value">45</div>
        </div>
      </div>

      <div class="section-title">Generate Patient Report</div>
      <div class="form-group">
        <label class="form-label">Select Patient/Member</label>
        <select class="form-input" id="memberSelect">
          <?php
          $members = getTrainerMembers($trainer_id);
          foreach ($members as $member):
          ?>
            <option value="<?php echo $member['member_id']; ?>"><?php echo htmlspecialchars($member['full_name']); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Report Type</label>
        <select class="form-input" id="reportTypeSelect">
          <option>Attendance Report</option>
          <option>Health Statistics</option>
          <option>Progress Summary</option>
          <option>Full Profile</option>
        </select>
      </div>
      <button class="generate-btn" onclick="showReport()">
        Generate Report
      </button>

      <div class="section-title">Recent Patient Reports</div>
      <div class="reports-table-container">
        <table class="reports-table">
          <thead>
            <tr>
              <th width="25%">Member Name</th>
              <th width="35%">Report Type</th>
              <th width="20%">Date</th>
              <th width="20%">Action</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamic recent reports will be loaded here -->
            <tr>
              <td colspan="4" style="text-align:center;color:#888;">No recent reports found.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  </div>

  <!-- Detailed Report Modal -->
  <div id="reportModal" class="report-modal">
    <div class="report-content">
      <div class="close-report" onclick="closeReport()">&times;</div>
      <div class="report-header">
        <div>
          <div class="report-title">Comprehensive Health Report</div>
          <div class="report-meta">Generated on: <span id="reportDate"></span></div>
          <div class="report-meta">Patient: <strong style="color:white" id="reportMemberName"></strong> | ID: <span id="reportMemberId"></span></div>
        </div>
        <div style="text-align: right;">
          <button class="generate-btn" style="margin:0; padding: 8px 15px; font-size: 12px;"
            onclick="downloadPDF()">Download PDF</button>
        </div>
      </div>

      <!-- Key Stats Grid -->
      <div class="stats-grid" id="reportStatsGrid">
        <!-- Dynamic stats will be loaded here -->
      </div>

      <!-- Chart 1: Weight Progress -->
      <div class="chart-container">
        <div class="chart-title" id="weightChartTitle">Weight Progress</div>
        <div class="bar-chart" id="weightBarChart">
          <!-- Dynamic weight bars -->
        </div>
      </div>

      <!-- Chart 2: Activity Level -->
      <div class="chart-container" style="margin-bottom: 0;">
        <div class="chart-title" id="activityChartTitle">Weekly Activity (Hours)</div>
        <div class="bar-chart" id="activityBarChart">
          <!-- Dynamic activity bars -->
        </div>
      </div>

    </div>
  </div>

  <script>
    function showReport() {
      const memberId = document.getElementById('memberSelect').value;
      const reportType = document.getElementById('reportTypeSelect').value;
      if (!memberId || !reportType) return;
      fetch('../handlers/trainer/generate_report.php?member_id=' + memberId + '&report_type=' + encodeURIComponent(reportType))
        .then(res => res.json())
        .then(data => {
          if (data.error) {
            alert(data.error);
            return;
          }
          document.getElementById('reportDate').innerText = new Date().toLocaleDateString();
          document.getElementById('reportMemberName').innerText = data.member.full_name;
          document.getElementById('reportMemberId').innerText = '#MEM-' + data.member.member_id;
          let statsHtml = '';
          let weightBars = '';
          // Only show relevant data for each report type
          if (data.report_type === 'Attendance Report' && data.data.attendance && data.data.attendance.length > 0) {
            const attended = data.data.attendance.filter(a => a.status === 'attended').length;
            const total = data.data.attendance.length;
            const rate = total ? Math.round((attended / total) * 100) : 0;
            statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>ATTENDANCE RATE</div><div style='font-size:24px;font-weight:bold;'>${rate}%</div></div>`;
          } else if ((data.report_type === 'Health Statistics' || data.report_type === 'Progress Summary') && data.data.progress && data.data.progress.length > 0) {
            const latest = data.data.progress[0];
            statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>CURRENT WEIGHT</div><div style='font-size:24px;font-weight:bold;'>${latest.weight_kg || '-'} kg</div></div>`;
            statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>AVG HEART RATE</div><div style='font-size:24px;font-weight:bold;'>${latest.heart_rate || '-'} bpm</div></div>`;
            data.data.progress.slice(0, 6).reverse().forEach(p => {
              const h = p.weight_kg ? Math.round(2 * p.weight_kg) : 40;
              weightBars += `<div style='display:flex;flex-direction:column;align-items:center;'><div class='bar' style='height:${h}px;'><span class='bar-value'>${p.weight_kg || '-'}<\/span><\/div><div class='bar-label'>${p.tracking_date || ''}<\/div><\/div>`;
            });
          } else if (data.report_type === 'Full Profile') {
            if (data.data.profile) {
              statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>EMAIL</div><div style='font-size:18px;font-weight:bold;'>${data.data.profile.email}</div></div>`;
              statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>PHONE</div><div style='font-size:18px;font-weight:bold;'>${data.data.profile.phone}</div></div>`;
            }
            if (data.data.progress && data.data.progress.length > 0) {
              const latest = data.data.progress[0];
              statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>CURRENT WEIGHT</div><div style='font-size:24px;font-weight:bold;'>${latest.weight_kg || '-'} kg</div></div>`;
            }
            if (data.data.attendance && data.data.attendance.length > 0) {
              const attended = data.data.attendance.filter(a => a.status === 'attended').length;
              const total = data.data.attendance.length;
              const rate = total ? Math.round((attended / total) * 100) : 0;
              statsHtml += `<div class="stat-box"><div style='color:#aaa;font-size:12px;margin-bottom:5px;'>ATTENDANCE RATE</div><div style='font-size:24px;font-weight:bold;'>${rate}%</div></div>`;
            }
            if (data.data.progress && data.data.progress.length > 0) {
              data.data.progress.slice(0, 6).reverse().forEach(p => {
                const h = p.weight_kg ? Math.round(2 * p.weight_kg) : 40;
                weightBars += `<div style='display:flex;flex-direction:column;align-items:center;'><div class='bar' style='height:${h}px;'><span class='bar-value'>${p.weight_kg || '-'}<\/span><\/div><div class='bar-label'>${p.tracking_date || ''}<\/div><\/div>`;
              });
            }
          }
          document.getElementById('reportStatsGrid').innerHTML = statsHtml;
          document.getElementById('weightBarChart').innerHTML = weightBars;
          document.getElementById('activityBarChart').innerHTML = '<div style="color:#888;text-align:center;width:100%">No activity data<\/div>';
          document.getElementById('reportModal').style.display = 'flex';
        });
    }

    function downloadPDF() {
      const memberId = document.getElementById('memberSelect').value;
      const reportType = document.getElementById('reportTypeSelect').value;
      if (!memberId || !reportType) return;
      window.open('../handlers/trainer/download_report_pdf.php?member_id=' + memberId + '&report_type=' + encodeURIComponent(reportType), '_blank');
    }

    function closeReport() {
      document.getElementById('reportModal').style.display = 'none';
    }

    // Close on outside click
    window.onclick = function(event) {
      const modal = document.getElementById('reportModal');
      if (event.target == modal) {
        closeReport();
      }
    }
  </script>
</body>

</html>