<?php
session_start();
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/auth.php';

// Ensure user is logged in as admin
requireLogin('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = $_SESSION['admin_id'];
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Basic validation
    if (empty($username) || empty($full_name) || empty($email)) {
        $_SESSION['error_message'] = "Username, Name and Email are required.";
        header("Location: ../../admin/profile.php");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // 1. Update users table (username, email)
        // Check if username/email is taken by another user
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $stmt->execute([$username, $email, $user_id]);
        if ($stmt->fetch()) {
            throw new Exception("Username or Email is already in use by another account.");
        }

        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$username, $email, $user_id]);

        // 2. Update admins table (full_name, phone)
        $stmt = $pdo->prepare("UPDATE admins SET full_name = ?, phone = ? WHERE admin_id = ?");
        $stmt->execute([$full_name, $phone, $admin_id]);

        // Update session variables
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;

        $pdo->commit();
        $_SESSION['success_message'] = "Profile updated successfully!";

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error updating profile: " . $e->getMessage();
    }

    header("Location: ../../admin/profile.php");
    exit;
} else {
    // Redirect if accessed directly
    header("Location: ../../admin/profile.php");
    exit;
}
