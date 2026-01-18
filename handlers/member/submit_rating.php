<?php
/**
 * Submit Rating Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Require member login
requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $rating_type = $_POST['rating_type'] ?? ''; // 'app' or 'trainer'
    $rating_value = intval($_POST['rating_value'] ?? 0);
    $trainer_id = $_SESSION['trainer_id'] ?? null;
    
    if (!in_array($rating_type, ['app', 'trainer']) || $rating_value < 1 || $rating_value > 5) {
        echo json_encode(['success' => false, 'message' => 'Invalid rating data']);
        exit();
    }
    
    try {
        // Insert rating
        $stmt = $pdo->prepare("INSERT INTO ratings 
                              (member_id, rating_type, rating_value, trainer_id) 
                              VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $member_id, 
            $rating_type, 
            $rating_value, 
            $rating_type === 'trainer' ? $trainer_id : null
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Rating submitted successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error submitting rating']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
