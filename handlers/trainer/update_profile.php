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
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experience_years = intval($_POST['experience_years'] ?? 0);

    if (empty($full_name)) {
        $_SESSION['error_message'] = 'Full name is required';
        header('Location: ../../trainer/profile.php');
        exit;
    }

    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE trainers SET full_name = ?, phone = ?, bio = ?, specialization = ?, experience_years = ? WHERE trainer_id = ?");
        if ($stmt->execute([$full_name, $phone, $bio, $specialization, $experience_years, $trainer_id])) {
            $_SESSION['success_message'] = 'Profile updated successfully';
            $_SESSION['full_name'] = $full_name;
        } else {
            $_SESSION['error_message'] = 'Update failed';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = 'Database error: ' . $e->getMessage();
    }

    header('Location: ../../trainer/profile.php');
    exit;
}
