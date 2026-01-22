<?php
session_start();
require_once '../config/database.php';
require_once '../includes/db_functions.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$member_id = $_SESSION['member_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message_text = trim($_POST['message']);

    if (empty($message_text)) {
        echo json_encode(['success' => false, 'message' => 'Message cannot be empty']);
        exit;
    }

    // Get the assigned trainer for this member
    $trainer = getAssignedTrainer($member_id);

    if (!$trainer) {
        echo json_encode(['success' => false, 'message' => 'No assigned trainer found']);
        exit;
    }

    $trainer_user_id = $trainer['user_id'];

    // Send the message
    $result = sendMessage($member_id, $trainer_user_id, $message_text);

    if ($result) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to send message']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
