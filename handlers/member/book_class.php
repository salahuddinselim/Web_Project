<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $class_id = intval($_POST['class_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($class_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid class ID']);
        exit;
    }

    global $pdo;

    if ($action === 'book') {
        // Check if already booked
        $stmt = $pdo->prepare("SELECT booking_id FROM class_bookings WHERE member_id = ? AND class_id = ? AND status = 'booked'");
        $stmt->execute([$member_id, $class_id]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Already booked']);
            exit;
        }

        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO class_bookings (member_id, class_id, booking_date) VALUES (?, ?, CURDATE())");
        if ($stmt->execute([$member_id, $class_id])) {
            echo json_encode(['success' => true, 'message' => 'Booked successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking failed']);
        }
    } elseif ($action === 'cancel') {
        // Cancel booking
        $stmt = $pdo->prepare("UPDATE class_bookings SET status = 'cancelled' WHERE member_id = ? AND class_id = ? AND status = 'booked'");
        if ($stmt->execute([$member_id, $class_id])) {
            echo json_encode(['success' => true, 'message' => 'Cancelled successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Cancellation failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>