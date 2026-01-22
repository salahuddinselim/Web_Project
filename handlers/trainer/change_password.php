<?php

/**
 * Trainer Change Password Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../../includes/auth.php';

// Require trainer login
requireLogin('trainer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = 'All fields are required';
        header('Location: ../../trainer/profile.php');
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = 'New passwords do not match';
        header('Location: ../../trainer/profile.php');
        exit;
    }

    if (strlen($new_password) < 6) {
        $_SESSION['error_message'] = 'Password must be at least 6 characters';
        header('Location: ../../trainer/profile.php');
        exit;
    }

    global $pdo;
    try {
        // Get current password hash
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['error_message'] = 'User not found';
            header('Location: ../../trainer/profile.php');
            exit;
        }

        // Verify current password
        if (!password_verify($current_password, $user['password_hash'])) {
            $_SESSION['error_message'] = 'Current password is incorrect';
            header('Location: ../../trainer/profile.php');
            exit;
        }

        // Update password
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $updateStmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        if ($updateStmt->execute([$new_hash, $user_id])) {
            $_SESSION['success_message'] = 'Password changed successfully';
        } else {
            $_SESSION['error_message'] = 'Failed to update password';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: ../../trainer/profile.php');
    exit;
}
