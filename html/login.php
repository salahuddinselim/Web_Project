<?php
/**
 * Member/Trainer Login Page
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if ($_SESSION['role'] === 'member') {
        header("Location: member_dashboard.php");
    } elseif ($_SESSION['role'] === 'trainer') {
        header("Location: ../trainer/dashboard.php");
    }
    exit();
}

// Get error message from session if exists
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Pranayom</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    /* Login Page Specific Styles - Dark Theme */
    body {
      background-color: #f0f2f5;
    }

    .login-container {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 80vh;
      background-color: #f0f2f5;
    }

    .login-box {
      background-color: #1a1a1a;
      width: 500px;
      padding: 50px;
      border-radius: 10px;
      text-align: center;
      color: white;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    .login-header {
      display: flex;
      align-items: center;
      margin-bottom: 30px;
    }

    .login-header h3 {
      margin-left: 10px;
      font-size: 18px;
    }

    .welcome-text {
      font-size: 36px;
      font-weight: bold;
      margin-bottom: 40px;
    }

    .toggle-container {
      background-color: #2a3b2a;
      border-radius: 5px;
      display: flex;
      padding: 5px;
      margin-bottom: 20px;
    }

    .toggle-btn {
      flex: 1;
      background: transparent;
      color: #8f9f8f;
      border: none;
      padding: 8px;
      cursor: pointer;
      border-radius: 5px;
      font-size: 14px;
    }

    .toggle-btn.active {
      background-color: #1a1a1a;
      color: white;
    }

    .input-group {
      text-align: left;
      margin-bottom: 20px;
    }

    .input-group input {
      width: 100%;
      padding: 15px;
      background-color: #2d3830;
      border: 1px solid #3d4840;
      border-radius: 5px;
      color: white;
      font-size: 14px;
      outline: none;
    }

    .input-group input::placeholder {
      color: #8f9f8f;
    }

    .agree {
      text-align: center;
      font-size: 15px;
      color: #8f9f8f;
      margin-bottom: 30px;
      display: block;
      text-decoration: none;
    }

    .btn-login-main {
      width: 100%;
      padding: 12px;
      background-color: #00ea6a;
      color: black;
      border: none;
      border-radius: 5px;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
    }

    .btn-login-main:hover {
      background-color: #00c25a;
    }

    .error-msg {
      color: #ff6b6b;
      font-size: 14px;
      margin-top: 15px;
      display: <?php echo $error ? 'block' : 'none'; ?>;
    }
  </style>
</head>

<body>
  <div class="mainContent" style="background-color: #121712">
    <!-- Navbar -->
    <div class="navbar" style="background-color: #121712">
      <div class="logo">
        <h3><a href="index.php" style="color: white">Pranayom</a></h3>
      </div>
      <div class="list">
        <li><a href="index.php" style="color: white">Home</a></li>
      </div>
    </div>

    <div class="secBox" style="
          background-color: #121712;
          min-height: 80vh;
          justify-content: center;
          align-items: center;
        ">
      <div class="login-box">
        <div class="login-header">
          <span style="font-size: 20px">⚡</span>
          <h3>Pranayom</h3>
        </div>

        <div class="welcome-text">Welcome Back</div>

        <!-- Login Form -->
        <form method="POST" action="../handlers/login_handler.php" id="loginForm">
          <!-- Toggle -->
          <div class="toggle-container">
            <button type="button" class="toggle-btn active" id="btn-member" onclick="setRole('member')">
              Member
            </button>
            <button type="button" class="toggle-btn" id="btn-trainer" onclick="setRole('trainer')">
              Trainer
            </button>
          </div>

          <input type="hidden" name="role" id="role" value="member" />

          <div class="input-group">
            <input type="text" name="username" id="username" placeholder="Username" required />
          </div>
          <div class="input-group">
            <input type="password" name="password" id="password" placeholder="Password" required />
          </div>

          <a class="agree" href="terms.php">
            <input type="checkbox" required /> I agree to the terms and conditions
          </a>

          <button type="submit" class="btn-login-main">Login</button>
          
          <p class="error-msg">
            <?php echo htmlspecialchars($error); ?>
          </p>
          
          <p style="color: grey; font-size: 10px; margin-top: 20px">
            Test credentials: member_afia / password123 or trainer_sarah / password123
          </p>
        </form>
      </div>
    </div>
  </div>

  <script>
    let currentRole = "member";

    function setRole(role) {
      currentRole = role;
      document.getElementById("role").value = role;
      document
        .getElementById("btn-member")
        .classList.toggle("active", role === "member");
      document
        .getElementById("btn-trainer")
        .classList.toggle("active", role === "trainer");
    }
  </script>
</body>

</html>
