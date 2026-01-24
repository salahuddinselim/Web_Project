<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get assigned trainer
global $pdo;
$stmt = $pdo->prepare("SELECT t.trainer_id, t.full_name, t.user_id, t.profile_picture FROM members m JOIN trainers t ON m.trainer_id = t.trainer_id WHERE m.member_id = ?");
$stmt->execute([$member_id]);
$trainer = $stmt->fetch();

$no_trainer = false;
$trainer_name = '';
$trainer_user_id = 0;
$trainer_picture = 'default_avatar.jpg';
$messages = [];

if (!$trainer) {
    $no_trainer = true;
} else {
    $trainer_name = $trainer['full_name'];
    $trainer_user_id = $trainer['user_id'];
    $trainer_picture = $trainer['profile_picture'] ?: 'default_avatar.jpg';
    $messages = getMessages($_SESSION['user_id'], $trainer_user_id);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Chat - Pranayom</title>
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
      height: 100vh;
      overflow: hidden;
      /* Prevent body scroll, handle inside chat */
    }

    /* Sidebar */
    .sidebar {
      width: 250px;
      background-color: #0d110d;
      padding: 30px 20px;
      border-right: 1px solid #222;
      display: flex;
      flex-direction: column;
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

    /* Chat Layout */
    .chat-container {
      display: flex;
      flex: 1;
      height: 100%;
    }

    /* Contact List Column */
    .contacts-sidebar {
      width: 300px;
      background-color: #1a201a;
      border-right: 1px solid #222;
      padding: 20px;
      display: flex;
      flex-direction: column;
    }

    .search-bar {
      background-color: #2a302a;
      border-radius: 8px;
      padding: 10px;
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 20px;
      color: #aaa;
    }

    .search-bar input {
      background: none;
      border: none;
      color: white;
      outline: none;
      width: 100%;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.2s;
      margin-bottom: 5px;
    }

    .contact-item:hover,
    .contact-item.active {
      background-color: #2a302a;
    }

    .contact-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #444;
      overflow: hidden;
      position: relative;
    }

    .contact-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .status-dot {
      width: 10px;
      height: 10px;
      background-color: #00d26a;
      border-radius: 50%;
      position: absolute;
      bottom: 0px;
      right: 0px;
      border: 2px solid #1a201a;
    }

    .contact-info h4 {
      font-size: 14px;
      margin-bottom: 3px;
    }

    .contact-info p {
      font-size: 11px;
      color: #888;
    }

    /* Main Chat Window */
    .chat-window {
      flex: 1;
      display: flex;
      flex-direction: column;
      background-color: #121712;
    }

    .chat-header {
      padding: 20px 40px;
      border-bottom: 1px solid #222;
      font-size: 20px;
      font-weight: bold;
    }

    .messages-area {
      flex: 1;
      padding: 30px 40px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .message-group {
      display: flex;
      gap: 15px;
      max-width: 70%;
    }

    .message-group.received {
      align-self: flex-start;
    }

    .message-group.sent {
      align-self: flex-end;
      flex-direction: row-reverse;
    }

    .msg-avatar {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      overflow: hidden;
      flex-shrink: 0;
      background-color: #333;
    }

    .msg-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .msg-content {
      display: flex;
      flex-direction: column;
      gap: 5px;
    }

    .sender-name {
      font-size: 10px;
      color: #888;
    }

    .bubble {
      padding: 15px 20px;
      border-radius: 12px;
      font-size: 13px;
      line-height: 1.5;
    }

    .received .bubble {
      background-color: #1f261f;
      color: #ddd;
      border-top-left-radius: 0;
    }

    .sent .bubble {
      background-color: #00d26a;
      color: black;
      font-weight: 500;
      border-top-right-radius: 0;
    }

    /* Input Area */
    .input-area {
      padding: 20px 40px;
      background-color: #1a201a;
      /* Slightly lighter than main bg */
      border-top: 1px solid #222;
    }

    .input-box {
      background-color: #2a302a;
      border-radius: 8px;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      gap: 10px;
      height: 60px;
    }

    .input-box input {
      flex: 1;
      background: none;
      border: none;
      color: white;
      outline: none;
      font-size: 14px;
    }

    .input-icons {
      display: flex;
      gap: 15px;
      color: #888;
      font-size: 18px;
      cursor: pointer;
    }

    .btn-send {
      background-color: #00d26a;
      color: black;
      font-weight: bold;
      border: none;
      padding: 8px 20px;
      border-radius: 5px;
      cursor: pointer;
    }

    .btn-send:hover {
      background-color: #00b05a;
    }

    /* Shared Section Placeholders */
    .shared-section {
      padding: 0 40px;
      margin-bottom: 20px;
    }

    .shared-title {
      font-weight: bold;
      margin-bottom: 10px;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <?php if ($no_trainer): ?>
  <!-- No Trainer Modal -->
  <div id="noTrainerModal" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;display:flex;justify-content:center;align-items:center;">
    <div style="background:#1a201a;padding:40px;border-radius:12px;text-align:center;max-width:400px;border:1px solid #00d26a;">
      <div style="font-size:64px;margin-bottom:20px;">⚠️</div>
      <h2 style="color:#00d26a;margin-bottom:15px;">No Trainer Assigned</h2>
      <p style="color:#aaa;margin-bottom:25px;line-height:1.6;">You don't have a trainer assigned yet. Please contact the administrator to get a trainer assigned to your account.</p>
      <a href="member_dashboard.php" style="display:inline-block;background:#00d26a;color:black;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:bold;">Go to Dashboard</a>
    </div>
  </div>
  <?php endif; ?>
  
  <?php include __DIR__ . '/../includes/member_sidebar.php'; ?>

  <!-- Chat Container -->
  <div class="chat-container">
    <!-- Contacts List -->
    <div class="contacts-sidebar">
      <div class="search-bar">
        <span>🔍</span>
        <input type="text" placeholder="Search" />
      </div>

      <div class="contact-item active">
        <div class="contact-avatar">
          <img src="../uploads/profile_pics/<?php echo htmlspecialchars($trainer_picture); ?>" alt="<?php echo htmlspecialchars($trainer_name); ?>" />
          <div class="status-dot"></div>
        </div>
        <div class="contact-info">
          <h4><?php echo htmlspecialchars($trainer_name); ?></h4>
          <p>Active now</p>
        </div>
      </div>
    </div>

    <!-- Chat Window -->
    <div class="chat-window">
      <div class="chat-header">Chat with <?php echo htmlspecialchars($trainer_name); ?></div>

      <div class="messages-area">
        <?php
        // Reverse messages for display (oldest first)
        $messages_reversed = array_reverse($messages);
        foreach ($messages_reversed as $msg) {
          $is_sent = $msg['sender_id'] == $member_id;
          $sender_name = $is_sent ? 'You' : $trainer_name;
          $class = $is_sent ? 'sent' : 'received';
          $align = $is_sent ? 'right' : 'left';
          echo "<div class=\"message-group {$class}\">
                  <div class=\"msg-content\">
                    <div class=\"sender-name\" style=\"text-align: {$align}\">{$sender_name}</div>
                    <div class=\"bubble\">" . htmlspecialchars($msg['message_text']) . "</div>
                  </div>
                </div>";
        }
        ?>

        <div class="shared-section">
          <div class="shared-title">Shared Media</div>
        </div>
        <div class="shared-section">
          <div class="shared-title">Shared Files</div>
        </div>
      </div>

      <div class="shared-section">
        <div class="shared-title">Shared Media</div>
      </div>
      <div class="shared-section">
        <div class="shared-title">Shared Files</div>
      </div>

      <div class="input-area">
        <div class="input-box">
          <input type="text" placeholder="Type a message" />
          <div class="input-icons">
            <span>🖼️</span>
            <span>📄</span>
            <span>😊</span>
          </div>
          <button class="btn-send" onclick="sendMessage()">
            Send
          </button>
        </div>
      </div>
    </div>
  </div>
  <script>
    function sendMessage() {
      const input = document.querySelector('.input-box input');
      const messageText = input.value.trim();

      if (messageText) {
        // Send message via AJAX
        fetch('../handlers/member/send_message.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'message=' + encodeURIComponent(messageText)
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              // Add the message to the chat immediately
              const messagesArea = document.querySelector('.messages-area');
              const newMsgHTML = `
            <div class="message-group sent">
              <div class="msg-content">
                <div class="sender-name" style="text-align: right">You</div>
                <div class="bubble">${messageText}</div>
              </div>
            </div>`;
              messagesArea.insertAdjacentHTML('beforeend', newMsgHTML);
              input.value = '';
              messagesArea.scrollTop = messagesArea.scrollHeight;
            } else {
              alert('Failed to send message: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('Error sending message');
          });
      }
    }

    // Allow Enter key to send
    document.querySelector('.input-box input').addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        sendMessage();
      }
    });
  </script>
</body>

</html>