<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get available classes from database
$classes = getAvailableClasses();
$member_bookings = getMemberBookings($member_id);

// Filter only active bookings (status = 'booked')
$booked_class_ids = [];
foreach ($member_bookings as $booking) {
    if ($booking['status'] === 'booked') {
        $booked_class_ids[] = $booking['class_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Available Classes - Pranayom</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: sans-serif;
    }

    body {
      background-color: #121712;
      /* Very dark green/black */
      color: white;
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar (Same as Dashboard) */
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
      font-size: 20px;
      margin-bottom: 30px;
      color: white;
    }

    /* Classes List */
    .class-list {
      display: flex;
      flex-direction: column;
      gap: 30px;
    }

    .class-card {
      background-color: #1a201a;
      border-radius: 12px;
      display: flex;
      overflow: hidden;
      justify-content: space-between;
      /* height: 200px; */
    }

    .class-info {
      padding: 30px;
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
    }

    .class-info h3 {
      font-size: 18px;
      margin-bottom: 10px;
      color: white;
    }

    .class-info p {
      font-size: 13px;
      color: #aaa;
      line-height: 1.5;
      margin-bottom: 20px;
    }

    .btn-enroll {
      background-color: #2a352a;
      color: white;
      padding: 8px 25px;
      border: none;
      border-radius: 5px;
      font-size: 12px;
      cursor: pointer;
      width: fit-content;
      font-weight: bold;
    }

    .btn-enroll:hover {
      background-color: #3a453a;
    }

    .class-image-container {
      width: 300px;
      background-color: #ffe0b2;
      /* Default bg */
      position: relative;
    }

    .class-image-container img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

  <!-- Main Content -->
  <div class="main-content">
    <h1>Available Classes</h1>

    <h2>Upcoming Classes</h2>

    <div class="class-list">
      <?php if (empty($classes)): ?>
        <p>No classes available at the moment.</p>
      <?php else: ?>
        <?php foreach ($classes as $class): ?>
          <div class="class-card">
            <div class="class-info">
              <h3><?php echo htmlspecialchars($class['class_name']); ?></h3>
              <p>
                <?php echo htmlspecialchars($class['description']); ?> Instructor:
                <?php echo htmlspecialchars($class['instructor']); ?>, Day: <?php echo htmlspecialchars($class['schedule_day']); ?>, Time: <?php echo htmlspecialchars($class['schedule_time']); ?>
              </p>
              <?php if (in_array($class['class_id'], $booked_class_ids)): ?>
                <button class="btn-enroll" data-class-id="<?php echo $class['class_id']; ?>" style="background-color: #d9534f;">Cancel Enrollment</button>
              <?php else: ?>
                <button class="btn-enroll" data-class-id="<?php echo $class['class_id']; ?>">Enroll</button>
              <?php endif; ?>
            </div>
            <div class="class-image-container" style="background-color: #ffe0b2">
              <img src="../images/pregnant-woman-holding-fitness-mat.jpg" alt="<?php echo htmlspecialchars($class['class_name']); ?>" />
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <script>
    const enrollButtons = document.querySelectorAll(".btn-enroll");

    enrollButtons.forEach((button) => {
      button.addEventListener("click", function() {
        const classId = this.getAttribute('data-class-id');
        const action = this.textContent === "Enroll" ? 'book' : 'cancel';

        fetch('../handlers/member/book_class.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `class_id=${classId}&action=${action}`
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              if (action === 'book') {
                this.textContent = "Cancel Enrollment";
                this.style.backgroundColor = "#d9534f";
              } else {
                this.textContent = "Enroll";
                this.style.backgroundColor = "#2a352a";
              }
              alert(data.message);
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
          });
      });
    });
  </script>
</body>

</html>