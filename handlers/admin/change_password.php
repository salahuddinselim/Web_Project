<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Ensure user is logged in as admin
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "All password fields are required.";
        header("Location: ../../admin/profile.php");
        exit;
    }

    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = "New passwords do not match.";
        header("Location: ../../admin/profile.php");
        exit;
    }

    if (strlen($new_password) < 6) {
        $_SESSION['error_message'] = "New password must be at least 6 characters long.";
        header("Location: ../../admin/profile.php");
        exit;
    }

    try {
        // Verify current password
        $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($current_password, $user['password_hash'])) {
            $_SESSION['error_message'] = "Incorrect current password.";
            header("Location: ../../admin/profile.php");
            exit;
        }

        // Update password
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        $stmt->execute([$new_hash, $user_id]);

        $_SESSION['success_message'] = "Password changed successfully!";

    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Database error: " . $e->getMessage();
    }

    header("Location: ../../admin/profile.php");
    exit;
} else {
    header("Location: ../../admin/profile.php");
    exit;
}
