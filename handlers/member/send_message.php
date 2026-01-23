<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $message_text = trim($_POST['message'] ?? '');

    if (empty($message_text)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit;
    }

    // Get member's assigned trainer
    global $pdo;
    $stmt = $pdo->prepare("SELECT t.user_id FROM members m JOIN trainers t ON m.trainer_id = t.trainer_id WHERE m.member_id = ?");
    $stmt->execute([$member_id]);
    $trainer = $stmt->fetch();

    if (!$trainer) {
        echo json_encode(['success' => false, 'message' => 'No assigned trainer found']);
        exit;
    }

    $trainer_user_id = $trainer['user_id'];
    $member_user_id = $_SESSION['user_id'];

    if (sendMessage($member_user_id, $trainer_user_id, $message_text)) {
        echo json_encode(['success' => true, 'message' => 'Message sent']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
