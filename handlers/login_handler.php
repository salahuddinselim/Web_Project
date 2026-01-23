<?php
/**
 * Login Handler for Members and Trainers
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'member'; // Default to member
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Attempt login (role can be 'member' or 'trainer')
        if (login($username, $password, $role)) {
            // Redirect based on role
            if ($_SESSION['role'] === 'member') {
                header("Location: ../html/member_dashboard.php");
            } elseif ($_SESSION['role'] === 'trainer') {
                header("Location: ../trainer/dashboard.php");
            }
            exit();
        } else {
            $error = 'Invalid username or password.';
        }
    }
    
    // Store error in session and redirect back
    $_SESSION['login_error'] = $error;
    header("Location: ../html/login.php");
    exit();
}
?>
