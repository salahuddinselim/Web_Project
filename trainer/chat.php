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
  <title>Chat Trainer</title>
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

    /* Chat Layout */
    .chat-container {
      flex: 1;
      display: flex;
      overflow: hidden;
    }

    /* Contacts Pane */
    .contacts-pane {
      width: 300px;
      background-color: #0e0e0e;
      border-right: 1px solid #1f2b23;
      display: flex;
      flex-direction: column;
      padding: 20px;
    }

    .search-bar {
      margin-bottom: 20px;
    }

    .search-input {
      width: 100%;
      padding: 10px 15px;
      background-color: #1c2620;
      border: 1px solid #2a3830;
      border-radius: 6px;
      color: #a0bba5;
      font-size: 14px;
      outline: none;
      box-sizing: border-box;
    }

    .contact-list {
      flex: 1;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .contact-item {
      display: flex;
      align-items: center;
      padding: 10px;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .contact-item:hover {
      background-color: #1a1f1c;
    }

    .contact-item.active {
      background-color: #1f3b26;
      /* Highlight active contact */
    }

    .contact-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: #555;
      margin-right: 15px;
      overflow: hidden;
      position: relative;
    }

    .contact-avatar img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .online-status {
      width: 10px;
      height: 10px;
      background-color: #22c55e;
      border-radius: 50%;
      position: absolute;
      bottom: 0px;
      right: 0px;
      border: 2px solid #0e0e0e;
    }

    .contact-info h5 {
      margin: 0;
      font-size: 14px;
      color: #ffffff;
    }

    .contact-info span {
      font-size: 12px;
      color: #a1a1aa;
    }

    /* Conversation Pane */
    .conversation-pane {
      flex: 1;
      display: flex;
      flex-direction: column;
      background-color: #0e0e0e;
      position: relative;
    }

    .chat-header {
      padding: 20px;
      border-bottom: 1px solid #1f2b23;
      display: flex;
      align-items: center;
    }

    .chat-header h3 {
      margin: 0;
      font-size: 18px;
    }

    .messages-area {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      display: flex;
      flex-direction: column;
      gap: 15px;
    }

    /* Message Bubbles */
    .message-row {
      display: flex;
      margin-bottom: 15px;
    }

    .message-row.received {
      justify-content: flex-start;
    }

    .message-row.sent {
      justify-content: flex-end;
    }

    .message-bubble {
      max-width: 60%;
      padding: 15px;
      border-radius: 12px;
      font-size: 14px;
      line-height: 1.4;
      position: relative;
    }

    .received .message-bubble {
      background-color: #1a1f1c;
      /* Dark Grey */
      color: #d1d5db;
      border-top-left-radius: 0;
    }

    .sent .message-bubble {
      background-color: #22c55e;
      /* Bright Green */
      color: #000000;
      border-top-right-radius: 0;
      font-weight: 500;
    }

    .message-meta {
      font-size: 10px;
      margin-bottom: 4px;
      color: #6b7280;
    }

    .sent .message-meta {
      text-align: right;
      color: #14532d;
      opacity: 0.8;
    }

    /* Input Area */
    .input-area {
      padding: 20px;
      background-color: #15261b;
      /* Slightly lighter footer bg */
      display: flex;
      gap: 10px;
      align-items: center;
    }

    .chat-input {
      flex: 1;
      background-color: transparent;
      border: none;
      color: #ffffff;
      outline: none;
      font-family: inherit;
    }

    .chat-input::placeholder {
      color: #6b7280;
    }

    .icon-btn {
      background: none;
      border: none;
      color: #6b7280;
      cursor: pointer;
      font-size: 16px;
    }

    .send-btn {
      background-color: #22c55e;
      color: #000000;
      border: none;
      padding: 8px 20px;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    .send-btn:hover {
      background-color: #4ade80;
    }

    /* Shared Files (Static Visuals) */
    .shared-section {
      padding: 0 20px;
    }

    .shared-title {
      font-size: 12px;
      font-weight: bold;
      margin-bottom: 10px;
      display: block;
    }

    .file-item {
      display: flex;
      align-items: center;
      gap: 10px;
      background-color: #1a1f1c;
      padding: 8px;
      border-radius: 6px;
      margin-bottom: 5px;
      font-size: 12px;
    }
  </style>
</head>

<body>
  <div class="mainContainer">
    <?php include __DIR__ . '/../includes/trainer_sidebar.php'; ?>

    <!-- Chat Layout -->
    <div class="chat-container">
      <!-- Left: Contacts -->
      <div class="contacts-pane">
        <div class="search-bar">
          <input type="text" class="search-input" placeholder="Search" />
        </div>
        <div class="contact-list">
          <?php
          // Get assigned members from database
          $assignedMembers = getTrainerMembers($trainer_id);
          if (empty($assignedMembers)) {
            echo '<p style="padding: 20px; color: #888;">No members assigned to you yet.</p>';
          } else {
            foreach ($assignedMembers as $idx => $member) {
              $isActive = $idx === 0;
              $profilePic = $member['profile_picture'] ? "../uploads/profile_pics/" . htmlspecialchars($member['profile_picture']) : "../images/spiritual-young-man-practicing-yoga-indoors.jpg";
              echo '<div class="contact-item' . ($isActive ? ' active' : '') . '" data-member-id="' . $member['user_id'] . '" data-member-name="' . htmlspecialchars($member['full_name']) . '" data-member-pic="' . $profilePic . '">';
              echo '<div class="contact-avatar"><img src="' . $profilePic . '" alt="' . htmlspecialchars($member['full_name']) . '" />';
              if ($isActive) echo '<div class="online-status"></div>';
              echo '</div>';
              echo '<div class="contact-info"><h5>' . htmlspecialchars($member['full_name']) . '</h5><span>Active now</span></div>';
              echo '</div>';
            }
          }
          ?>
        </div>
      </div>

      <!-- Right: Conversation -->
      <div class="conversation-pane">
        <div class="chat-header">
          <h3 id="selectedMemberName"><?php echo !empty($assignedMembers) ? htmlspecialchars($assignedMembers[0]['full_name']) : 'Select a member'; ?></h3>
        </div>

        <div class="messages-area" id="messagesArea">
          <?php
          // Load messages for first selected member
          if (!empty($assignedMembers)) {
            $firstMember = $assignedMembers[0];
            $memberUserId = $firstMember['user_id'] ?? null;
            if ($memberUserId) {
              $trainerUserId = $_SESSION['user_id'];
              $messages = getMessages($memberUserId, $trainerUserId);
              if (!empty($messages)) {
                foreach ($messages as $msg) {
                  $isSent = $msg['sender_id'] == $trainerUserId;
                  $sender = getUserById($msg['sender_id']);
                  $senderName = $isSent ? 'You' : htmlspecialchars($sender['full_name'] ?? 'Member');
                  $senderImage = $sender['profile_picture'] ? '../uploads/profile_pics/' . htmlspecialchars($sender['profile_picture']) : '../images/default_avatar.jpg';
                  $align = $isSent ? 'sent' : 'received';
          ?>
                  <div class="message-row <?php echo $align; ?>">
                    <div style="<?php echo $align === 'received' ? 'margin-right' : 'margin-left'; ?>: 10px">
                      <img src="<?php echo $senderImage; ?>" style="border-radius: 50%; width: 30px; height: 30px" />
                    </div>
                    <div>
                      <div class="message-meta"><?php echo $senderName; ?></div>
                      <div class="message-bubble"><?php echo htmlspecialchars($msg['message_text']); ?></div>
                    </div>
                  </div>
          <?php
                }
              } else {
                echo '<div style="text-align:center;color:#888;">No messages yet.</div>';
              }
            }
          } else {
            echo '<div style="text-align:center;color:#888;">No member selected.</div>';
          }
          ?>

          <!-- Shared Files Area (Static) -->
          <div class="shared-section">
            <span class="shared-title">Shared Files</span>
            <div class="file-item"><i>📄</i> Class Schedule.pdf</div>
            <div class="file-item"><i>📄</i> Membership Agreement.pdf</div>
          </div>
        </div>

        <div class="input-area">
          <button class="icon-btn">➕</button>
          <input
            type="text"
            class="chat-input"
            id="messageInput"
            placeholder="Type a message" />
          <button class="send-btn" onclick="sendMessage()">Send</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Store assigned members in JS
    const assignedMembers = <?php echo json_encode(array_map(function ($m) {
                              return [
                                'user_id' => $m['user_id'],
                                'full_name' => $m['full_name'],
                                'profile_picture' => $m['profile_picture'] ? '../uploads/profile_pics/' . $m['profile_picture'] : '../images/spiritual-young-man-practicing-yoga-indoors.jpg'
                              ];
                            }, $assignedMembers ?? [])); ?>;

    let selectedMemberId = assignedMembers.length > 0 ? assignedMembers[0].user_id : null;
    let selectedMemberName = assignedMembers.length > 0 ? assignedMembers[0].full_name : '';
    let selectedMemberPic = assignedMembers.length > 0 ? assignedMembers[0].profile_picture : '';

    // Contact switching
    document.querySelectorAll('.contact-item').forEach(function(item) {
      item.addEventListener('click', function() {
        document.querySelectorAll('.contact-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        selectedMemberId = this.getAttribute('data-member-id');
        selectedMemberName = this.getAttribute('data-member-name');
        selectedMemberPic = this.getAttribute('data-member-pic');
        document.getElementById('selectedMemberName').textContent = selectedMemberName;
        
        // Reload messages via AJAX
        fetch('chat_messages.php?member_id=' + selectedMemberId)
          .then(res => res.text())
          .then(html => {
            const messagesArea = document.getElementById('messagesArea');
            messagesArea.innerHTML = html;
            messagesArea.scrollTop = messagesArea.scrollHeight;
          });
      });
    });

    function sendMessage() {
      const input = document.getElementById("messageInput");
      const messageText = input.value.trim();
      const messagesArea = document.getElementById("messagesArea");
      
      if (messageText && selectedMemberId) {
        const formData = new FormData();
        formData.append('member_user_id', selectedMemberId);
        formData.append('message', messageText);

        fetch('../handlers/trainer/send_message.php', {
            method: 'POST',
            body: formData
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              const newMsgHTML = `
                <div class="message-row sent">
                    <div style="margin-left: 10px; order: 1;">
                        <img src="../images/default_avatar.jpg" style="border-radius: 50%; width: 30px; height: 30px" />
                    </div>
                    <div>
                        <div class="message-meta">You</div>
                        <div class="message-bubble">${messageText}</div>
                    </div>
                </div>
              `;
              // Remove "No messages" text if it exists
              if (messagesArea.innerHTML.includes('No messages yet')) {
                  messagesArea.innerHTML = '';
              }
              messagesArea.insertAdjacentHTML("beforeend", newMsgHTML);
              input.value = "";
              messagesArea.scrollTop = messagesArea.scrollHeight;
            } else {
              alert(data.message);
            }
          })
          .catch(err => alert("Error sending message"));
      }
    }

    document.getElementById("messageInput").addEventListener("keypress", function(e) {
      if (e.key === "Enter") {
        sendMessage();
      }
    });
  </script>
</body>

</html>