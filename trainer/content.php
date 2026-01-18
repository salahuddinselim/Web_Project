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
    <title>Content Management</title>
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
        position: relative;
        /* For popup positioning */
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
        position: relative;
      }

      .page-header {
        margin-bottom: 20px;
      }

      .page-header h2 {
        margin: 0;
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 10px;
      }

      .nav-tabs {
        border-bottom: 1px solid #1f2b23;
        margin-bottom: 20px;
      }

      .nav-tab {
        display: inline-block;
        padding: 10px 0;
        margin-right: 20px;
        color: #ffffff;
        border-bottom: 2px solid #ffffff;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
      }

      /* Search Bar */
      .search-container {
        margin-bottom: 20px;
      }

      .search-input {
        width: 100%;
        padding: 12px 15px;
        background-color: #1c2620;
        /* Dark green search bg */
        border: 1px solid #2a3830;
        border-radius: 6px;
        color: #a0bba5;
        font-size: 14px;
        outline: none;
        box-sizing: border-box;
      }

      .search-input::placeholder {
        color: #a0bba5;
        opacity: 0.7;
      }

      /* Content Table */
      .content-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        border: 1px solid #1f2b23;
        border-radius: 8px;
        /* To round corners of table borders requires separate handling or overflow hidden on container, simple border for now */
      }

      .content-table th {
        text-align: left;
        padding: 15px;
        color: #e5e5e5;
        background-color: #0d120f;
        border-bottom: 1px solid #2a3830;
        font-weight: 500;
      }

      .content-table td {
        padding: 15px;
        border-bottom: 1px solid #1f2b23;
        color: #ffffff;
        background-color: #0d120f;
        vertical-align: middle;
      }

      .content-table tr:hover td {
        background-color: #111a14;
      }

      .category-pill {
        display: inline-block;
        padding: 6px 20px;
        background-color: #1f3b26;
        color: #ffffff;
        border-radius: 6px;
        font-size: 12px;
        min-width: 80px;
        text-align: center;
      }

      .action-link {
        color: #a1a1aa;
        text-decoration: none;
        font-weight: bold;
        cursor: pointer;
      }

      .action-link:hover {
        color: #ffffff;
      }

      /* Add Content Button */
      .add-btn {
        position: absolute;
        bottom: 40px;
        right: 40px;
        background-color: #22c55e;
        color: #000000;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        font-size: 14px;
      }

      .add-btn:hover {
        background-color: #4ade80;
      }

      /* Popup Modal */
      .modal-overlay {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1000;
        /* Sit on top */
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgba(0, 0, 0, 0.6);
        /* Black w/ opacity */
        justify-content: center;
        align-items: center;
      }

      .modal-content {
        background-color: #15261b;
        /* Dark Green Modal BG */
        padding: 30px;
        border-radius: 12px;
        width: 500px;
        position: relative;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .close-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: #a7d9f5;
        /* Light blue from image reference */
        border: none;
        border-radius: 4px;
        width: 24px;
        height: 24px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #000;
      }

      .modal-textarea {
        width: 100%;
        height: 120px;
        background-color: #0d120f;
        /* Darker input area */
        border: none;
        border-radius: 8px;
        padding: 15px;
        color: #a1a1aa;
        font-family: inherit;
        resize: none;
        outline: none;
        box-sizing: border-box;
      }

      .upload-area {
        background-color: #0d120f;
        border-radius: 8px;
        padding: 15px;
        text-align: center;
        color: #a1a1aa;
        font-size: 12px;
        cursor: pointer;
        border: 1px dashed #2a3830;
      }

      .save-btn {
        background-color: #ef4444;
        /* Red/Orange for Save */
        color: #ffffff;
        border: none;
        padding: 8px 30px;
        border-radius: 20px;
        /* Pill shape */
        font-weight: bold;
        cursor: pointer;
        align-self: center;
      }
    </style>
  </head>

  <body>
    <div class="mainContainer">
<?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

      <!-- Main Content -->
      <div class="mainContent">
        <div class="page-header">
          <h2>Content Management</h2>
          <div class="nav-tabs">
            <span class="nav-tab">All Content</span>
          </div>
        </div>

        <div class="search-container">
          <input
            type="text"
            class="search-input"
            placeholder="Search content"
          />
        </div>

        <table class="content-table">
          <thead>
            <tr>
              <th width="30%">Title</th>
              <th width="15%">Type</th>
              <th width="20%">Category</th>
              <th width="15%">Visibility</th>
              <th width="20%">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>Yoga for Beginners</td>
              <td>Video</td>
              <td><span class="category-pill">Yoga</span></td>
              <td>Public</td>
              <td>
                <a
                  class="action-link"
                  onclick="alert('Edit Content: Yoga for Beginners')"
                  >Edit</a
                >
              </td>
            </tr>
            <tr>
              <td>Meditation Techniques</td>
              <td>Article</td>
              <td><span class="category-pill">Meditation</span></td>
              <td>Private</td>
              <td>
                <a
                  class="action-link"
                  onclick="alert('Edit Content: Meditation Techniques')"
                  >Edit</a
                >
              </td>
            </tr>
            <tr>
              <td>Advanced Yoga Poses</td>
              <td>Video</td>
              <td><span class="category-pill">Yoga</span></td>
              <td>Public</td>
              <td>
                <a
                  class="action-link"
                  onclick="alert('Edit Content: Advanced Yoga Poses')"
                  >Edit</a
                >
              </td>
            </tr>
            <tr>
              <td>Mindfulness Practices</td>
              <td>Article</td>
              <td><span class="category-pill">Meditation</span></td>
              <td>Public</td>
              <td>
                <a
                  class="action-link"
                  onclick="alert('Edit Content: Mindfulness Practices')"
                  >Edit</a
                >
              </td>
            </tr>
            <tr>
              <td>Pilates for Core Strength</td>
              <td>Video</td>
              <td><span class="category-pill">Pilates</span></td>
              <td>Public</td>
              <td>
                <a
                  class="action-link"
                  onclick="alert('Edit Content: Pilates for Core Strength')"
                  >Edit</a
                >
              </td>
            </tr>
          </tbody>
        </table>

        <button class="add-btn" onclick="openModal()">Add Content</button>
      </div>
    </div>

    <!-- Popup Modal -->
    <div id="contentModal" class="modal-overlay">
      <div class="modal-content">
        <button class="close-btn" onclick="closeModal()">×</button>

        <textarea class="modal-textarea" placeholder="Enter Details"></textarea>

        <div
          class="upload-area"
          onclick="alert('Upload functionality would trigger here')"
        >
          Upload Media
        </div>

        <button class="save-btn" onclick="saveContent()">Save</button>
      </div>
    </div>

    <script>
      function openModal() {
        document.getElementById("contentModal").style.display = "flex";
      }

      function closeModal() {
        document.getElementById("contentModal").style.display = "none";
      }

      function saveContent() {
        // Retrieve content (just for demo)
        const details = document.querySelector(".modal-textarea").value;
        alert("Content Saved!\nDetails: " + (details ? details : "None"));
        closeModal();
        // In a real app, this would append to the table or send to server
      }

      // Close modal if user clicks outside of it
      window.onclick = function (event) {
        const modal = document.getElementById("contentModal");
        if (event.target == modal) {
          closeModal();
        }
      };
    </script>
  </body>
</html>
