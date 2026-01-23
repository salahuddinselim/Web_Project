<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $user_id = $_SESSION['user_id'];
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($full_name) || empty($username) || empty($email)) {
        $_SESSION['error_message'] = 'Name, Username and Email are required';
        header('Location: ../../html/member_profile.php');
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

        // 2. Update members table
        $stmt = $pdo->prepare("UPDATE members SET full_name = ?, phone = ?, address = ? WHERE member_id = ?");
        $stmt->execute([$full_name, $phone, $address, $member_id]);

        $pdo->commit();

        $_SESSION['success_message'] = 'Profile updated successfully';
        $_SESSION['full_name'] = $full_name;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['error_message'] = 'Update failed: ' . $e->getMessage();
    }

    header('Location: ../../html/member_profile.php');
    exit;
}
