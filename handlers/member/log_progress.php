<?php
/**
 * Log Progress Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../config/database.php';

header('Content-Type: application/json');

// Require member login
requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $weight = $_POST['weight'] ?? null;
    $heart_rate = $_POST['heart_rate'] ?? null;
    $sleep_hours = $_POST['sleep_hours'] ?? null;
    $mood = $_POST['mood'] ?? null;
    $notes = $_POST['notes'] ?? '';
    
    try {
        // Check if entry exists for today
        $checkStmt = $pdo->prepare("SELECT progress_id FROM progress_tracking 
                                    WHERE member_id = ? AND tracking_date = CURDATE()");
        $checkStmt->execute([$member_id]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            // Update existing entry
            $stmt = $pdo->prepare("UPDATE progress_tracking 
                                  SET weight_kg = ?, heart_rate = ?, sleep_hours = ?, mood = ?, notes = ?
                                  WHERE progress_id = ?");
            $stmt->execute([$weight, $heart_rate, $sleep_hours, $mood, $notes, $existing['progress_id']]);
        } else {
            // Insert new entry
            $stmt = $pdo->prepare("INSERT INTO progress_tracking 
                                  (member_id, tracking_date, weight_kg, heart_rate, sleep_hours, mood, notes) 
                                  VALUES (?, CURDATE(), ?, ?, ?, ?, ?)");
            $stmt->execute([$member_id, $weight, $heart_rate, $sleep_hours, $mood, $notes]);
        }
        
        echo json_encode(['success' => true, 'message' => 'Progress logged successfully']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error logging progress']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
