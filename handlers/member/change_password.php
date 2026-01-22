<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_SESSION['member_id'];
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $_SESSION['error_message'] = 'All fields are required';
        header('Location: ../../html/member_profile.php');
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error_message'] = 'New passwords do not match';
        header('Location: ../../html/member_profile.php');
        exit;
    }
    
    if (strlen($new_password) < 6) {
        $_SESSION['error_message'] = 'Password must be at least 6 characters';
        header('Location: ../../html/member_profile.php');
        exit;
    }
    
    // Verify current password
    global $pdo;
    $stmt = $pdo->prepare("SELECT u.password_hash FROM users u 
                          JOIN members m ON u.user_id = m.user_id 
                          WHERE m.member_id = ?");
    $stmt->execute([$member_id]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($current_password, $user['password_hash'])) {
        $_SESSION['error_message'] = 'Current password is incorrect';
        header('Location: ../../html/member_profile.php');
        exit;
    }
    
    // Update password
    $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users u 
                          JOIN members m ON u.user_id = m.user_id 
                          SET u.password_hash = ? 
                          WHERE m.member_id = ?");
    
    if ($stmt->execute([$new_hash, $member_id])) {
        $_SESSION['success_message'] = 'Password changed successfully';
    } else {
        $_SESSION['error_message'] = 'Password change failed';
    }
    
    header('Location: ../../html/member_profile.php');
    exit;
}
?>
