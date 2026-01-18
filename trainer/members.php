<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$members = getTrainerMembers($trainer_id);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Member Details Trainer</title>
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

    .page-header h2 {
      margin: 0;
      margin-bottom: 20px;
      font-size: 24px;
      font-weight: bold;
    }

    /* Members List Card */
    .members-card {
      background-color: #0d120f;
      /* Dark background matching image */
      border: 1px solid #1f2b23;
      border-radius: 8px;
      padding: 20px;
    }

    .members-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .members-table th {
      text-align: left;
      padding: 15px;
      color: #e5e5e5;
      border-bottom: 1px solid #2a3830;
      font-weight: 500;
    }

    .members-table td {
      padding: 15px;
      border-bottom: 1px solid #1f2b23;
      color: #a1a1aa;
      vertical-align: middle;
    }

    .members-table tr:hover td {
      background-color: #111a14;
    }

    .members-table tr:last-child td {
      border-bottom: none;
    }

    /* Status Pills */
    .status-pill {
      display: inline-block;
      padding: 6px 0;
      width: 120px;
      text-align: center;
      border-radius: 20px;
      font-size: 12px;
      font-weight: 500;
    }

    .status-active {
      background-color: #1f3b26;
      /* Dark Green bg */
      color: #ffffff;
      border: 1px solid #2a4030;
    }

    .status-inactive {
      background-color: transparent;
      color: #a1a1aa;
      border: 1px solid #2a3830;
    }

    /* Buttons & Links */
    .btn-view {
      background-color: #2a352a;
      color: #00d26a;
      border: 1px solid #00d26a;
      padding: 5px 12px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 13px;
      transition: 0.3s;
    }

    .btn-view:hover {
      background-color: #00d26a;
      color: black;
    }

    .progress-link {
      color: #00d26a;
      text-decoration: underline;
      cursor: pointer;
      font-weight: 500;
    }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.7);
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background-color: #1a201a;
      margin: auto;
      padding: 30px;
      border: 1px solid #333;
      width: 50%;
      max-width: 600px;
      border-radius: 8px;
      position: relative;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
    }

    .close-btn {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close-btn:hover,
    .close-btn:focus {
      color: white;
      text-decoration: none;
      cursor: pointer;
    }

    .modal-header {
      margin-bottom: 20px;
      border-bottom: 1px solid #333;
      padding-bottom: 10px;
    }

    .modal-textarea {
      width: 100%;
      height: 150px;
      background-color: #0d110d;
      color: #ddd;
      border: 1px solid #333;
      padding: 10px;
      border-radius: 4px;
      resize: vertical;
      margin-bottom: 20px;
      font-family: inherit;
    }
  </style>
</head>

<body>
  <div class="mainContainer">
<?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

    <!-- Main Content -->
    <div class="mainContent">
      <div class="page-header">
        <h2>Assigned Members</h2>
      </div>

      <div class="members-card">
        <table class="members-table">
          <thead>
            <tr>
              <th width="15%">Name</th>
              <th width="25%">Diet Plan</th>
              <th width="25%">Yoga Routine</th>
              <th width="25%">Progress</th>
              <th width="10%">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($members)): ?>
            <tr>
              <td colspan="5" style="text-align: center; padding: 30px;">No members assigned to you yet.</td>
            </tr>
            <?php else: ?>
              <?php foreach ($members as $m): ?>
              <tr>
                <td><strong><?php echo htmlspecialchars($m['full_name']); ?></strong><br>
                    <span class="status-pill <?php echo $m['status'] == 'active' ? 'status-active' : 'status-inactive'; ?>"
                    style="margin-top:5px; width:auto; padding: 2px 8px; font-size: 10px;">
                    <?php echo ucfirst($m['status']); ?></span>
                </td>
                <td><button class="btn-view"
                    onclick="openModal('Diet Plan', '<?php echo addslashes($m['full_name']); ?>', 'Standard diet plan for focus on health.')">View</button>
                </td>
                <td><button class="btn-view"
                    onclick="openModal('Yoga Routine', '<?php echo addslashes($m['full_name']); ?>', 'Focus on Hatha Yoga and meditation.')">View</button>
                </td>
                <td><span class="progress-link"
                    onclick="openModal('Progress', '<?php echo addslashes($m['full_name']); ?>', 'Status: Active and improving.')">Steady</span>
                </td>
                <td><button class="btn-view" style="color: #00d26a; border-color: #00d26a;">Save</button></td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Edit/View Modal -->
  <div id="detailsModal" class="modal">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <div class="modal-header">
        <h2 id="modalTitle">Details</h2>
        <p style="color: #888; font-size: 14px; margin-top: 5px;" id="modalSubtitle">Member Name</p>
      </div>
      <textarea id="modalText" class="modal-textarea"></textarea>
      <div style="text-align: right;">
        <button class="btn-view" style="color: white; border-color: #555; margin-right: 10px;"
          onclick="closeModal()">Cancel</button>
        <button class="btn-view" style="background-color: #00d26a; color: black; border: none; font-weight: bold;"
          onclick="saveModalChanges()">Save Changes</button>
      </div>
    </div>
  </div>

  <script>
    const modal = document.getElementById("detailsModal");
    const titleEl = document.getElementById("modalTitle");
    const subTitleEl = document.getElementById("modalSubtitle");
    const textEl = document.getElementById("modalText");

    function openModal(type, name, content) {
      modal.style.display = "flex";
      titleEl.innerText = "Edit " + type;
      subTitleEl.innerText = "For Member: " + name;
      textEl.value = content;
    }

    function closeModal() {
      modal.style.display = "none";
    }

    function saveModalChanges() {
      // Logic to save changes would go here
      alert("Changes saved successfully!");
      closeModal();
    }

    // Close if clicked outside
    window.onclick = function (event) {
      if (event.target == modal) {
        closeModal();
      }
    }
  </script>
</body>

</html>
