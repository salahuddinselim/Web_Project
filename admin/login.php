<?php
/**
 * Admin Login Page
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';

// Redirect if already logged in as admin
if (isLoggedIn() && $_SESSION['role'] === 'admin') {
    header("Location: dashboard.php");
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
    <title>Admin Login - Pranayom</title>
    <style>
      body {
        margin: 0;
        padding: 0;
        background-color: #e5e7eb;
        font-family: Arial, sans-serif;
      }

      .mainContainer {
        width: 100%;
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .loginCard {
        background-color: #111111;
        width: 60%;
        height: 600px;
        border-radius: 5px;
        display: flex;
        flex-direction: column;
        color: white;
        box-sizing: border-box;
        position: relative;
      }

      .topBar {
        width: 100%;
        height: 50px;
        border-bottom: 1px solid #333;
        display: flex;
        align-items: center;
        padding-left: 20px;
        box-sizing: border-box;
      }

      .logoText {
        font-family: "Times New Roman", serif;
        font-weight: bold;
        font-size: 18px;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .logoIcon {
        font-size: 20px;
      }

      .formContainer {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
      }

      .title {
        font-size: 40px;
        font-weight: bold;
        margin-bottom: 40px;
      }

      .inputGroup {
        display: flex;
        flex-direction: column;
        width: 50%;
        gap: 20px;
      }

      .input {
        background-color: #2a3b2a;
        border: none;
        height: 45px;
        border-radius: 5px;
        color: #ccc;
        padding-left: 15px;
        font-size: 14px;
        width: 100%;
        box-sizing: border-box;
        margin-top: 5px;
      }

      ::placeholder {
        color: #6b7280;
      }

      .forgotPass {
        text-align: center;
        color: #9ca3af;
        font-size: 12px;
        margin-top: 10px;
        cursor: pointer;
      }

      .loginBtn {
        background-color: #22c55e;
        color: black;
        font-weight: bold;
        border: none;
        height: 45px;
        border-radius: 5px;
        width: 30%;
        margin-top: 40px;
        cursor: pointer;
        font-size: 16px;
      }

      .loginBtn:hover {
        background-color: #1ea34d;
      }

      .error-msg {
        color: #ff6b6b;
        font-size: 14px;
        margin-top: 15px;
        display: <?php echo $error ? 'block' : 'none'; ?>;
      }

      .test-credentials {
        color: #6b7280;
        font-size: 12px;
        margin-top: 20px;
      }
    </style>
  </head>
  <body>
    <div class="mainContainer">
      <div class="loginCard">
        <div class="topBar">
          <div class="logoText"><span class="logoIcon">⚡</span> Pranayom</div>
        </div>

        <div class="formContainer">
          <div class="title">Welcome Back</div>

          <form method="POST" action="/Web_Project/handlers/admin_login_handler.php">
            <div class="inputGroup">
              <input type="text" name="username" class="input" placeholder="Username" required />
              <input type="password" name="password" class="input" placeholder="Password" required />
              <div class="forgotPass">Forgot Password?</div>
            </div>

            <button type="submit" class="loginBtn">
              Login
            </button>

            <p class="error-msg">
              <?php echo htmlspecialchars($error); ?>
            </p>

            <p class="test-credentials">
              Test credentials: admin1 / password123
            </p>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
