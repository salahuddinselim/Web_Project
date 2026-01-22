<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($full_name)) {
        $_SESSION['error'] = 'Full name is required';
        header('Location: ../html/member_profile.php');
        exit;
    }

    global $pdo;
    $stmt = $pdo->prepare("UPDATE members SET full_name = ?, phone = ?, address = ? WHERE member_id = ?");
    if ($stmt->execute([$full_name, $phone, $address, $member_id])) {
        $_SESSION['success'] = 'Profile updated successfully';
        // Update session name
        $_SESSION['full_name'] = $full_name;
    } else {
        $_SESSION['error'] = 'Update failed';
    }

    header('Location: ../html/member_profile.php');
    exit;
}
