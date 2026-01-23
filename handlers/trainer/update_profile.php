<?php

/**
 * Trainer Profile Update Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

// Require trainer login
requireLogin('trainer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainer_id = $_SESSION['trainer_id'];
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experience_years = intval($_POST['experience_years'] ?? 0);

    if (empty($full_name) || empty($username) || empty($email)) {
        $_SESSION['error_message'] = 'Name, Username and Email are required';
        header('Location: ../../trainer/profile.php');
        exit;
    }

    global $pdo;
    try {
        // Check if username or email is taken
        $check_stmt = $pdo->prepare("SELECT user_id FROM users WHERE (username = ? OR email = ?) AND user_id != ?");
        $check_stmt->execute([$username, $email, $user_id]);
        if ($check_stmt->fetch()) {
            throw new Exception("Username or Email already in use.");
        }

        $pdo->beginTransaction();

        // 1. Update users table
        $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $stmt->execute([$username, $email, $user_id]);

        // 2. Update trainers table
        $stmt = $pdo->prepare("UPDATE trainers SET full_name = ?, phone = ?, bio = ?, specialization = ?, experience_years = ? WHERE trainer_id = ?");
        $stmt->execute([$full_name, $phone, $bio, $specialization, $experience_years, $trainer_id]);

        $pdo->commit();

        $_SESSION['success_message'] = 'Profile updated successfully';
        $_SESSION['full_name'] = $full_name;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['error_message'] = 'Update failed: ' . $e->getMessage();
    }

    header('Location: ../../trainer/profile.php');
    exit;
}
