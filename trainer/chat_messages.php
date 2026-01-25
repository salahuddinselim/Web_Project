<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db_functions.php';
requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];
$trainer_user_id = $_SESSION['user_id'];
$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
if (!$member_id) {
    echo '<div style="text-align:center;color:#888;">No member selected.</div>';
    exit;
}
$member = getUserById($member_id);
if (!$member) {
    echo '<div style="text-align:center;color:#888;">Member not found.</div>';
    exit;
}
$messages = getMessages($member_id, $trainer_user_id);
if (!empty($messages)) {
    foreach ($messages as $msg) {
        $isSent = $msg['sender_id'] == $trainer_user_id;
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
