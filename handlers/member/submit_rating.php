<?php
/**
 * Submit Rating Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Ensure user is logged in as member
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'member') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $rating_type = $_POST['rating_type'] ?? '';
    $rating_value = intval($_POST['rating_value'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    
    // Validate inputs
    if (!in_array($rating_type, ['app', 'trainer'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating type']);
        exit();
    }
    
    if ($rating_value < 1 || $rating_value > 5) {
        echo json_encode(['success' => false, 'message' => 'Rating must be between 1 and 5']);
        exit();
    }

    // Determine trainer_id if applicable
    $trainer_id = null;
    if ($rating_type === 'trainer') {
        // Get member's assigned trainer
        $trainer_id = $_SESSION['trainer_id'] ?? null;
        if (!$trainer_id) {
            // Try fallback to DB if session is stale
            global $pdo;
            $stmt = $pdo->prepare("SELECT trainer_id FROM members WHERE member_id = ?");
            $stmt->execute([$member_id]);
            $member = $stmt->fetch();
            $trainer_id = $member['trainer_id'] ?? null;
        }
        
        if (!$trainer_id) {
             echo json_encode(['success' => false, 'message' => 'You do not have an assigned trainer to rate.']);
             exit();
        }
    }

    try {
        global $pdo;
        
        // Check if rating already exists for this type/member/(trainer) today? 
        // Or just allow multiple? The schema doesn't seem to have unique constraints on (member_id, type).
        // Let's allow multiple entries for history.
        
        $stmt = $pdo->prepare("INSERT INTO ratings (member_id, trainer_id, rating_type, rating_value, comments) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$member_id, $trainer_id, $rating_type, $rating_value, $comment]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        error_log("Rating error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
}
?>
