<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('trainer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainer_user_id = $_SESSION['user_id'];
    $receiver_user_id = intval($_POST['member_user_id'] ?? 0);
    $message_text = trim($_POST['message'] ?? '');

    if (empty($message_text)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit;
    }

    if (!$receiver_user_id) {
        echo json_encode(['success' => false, 'message' => 'No receiver selected']);
        exit;
    }

    // Verify assignment
    global $pdo;
    $stmt = $pdo->prepare("SELECT member_id FROM members WHERE user_id = ? AND trainer_id = ?");
    $stmt->execute([$receiver_user_id, $_SESSION['trainer_id']]);
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'This member is not assigned to you.']);
        exit;
    }

    if (sendMessage($trainer_user_id, $receiver_user_id, $message_text)) {
        echo json_encode(['success' => true, 'message' => 'Message sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
