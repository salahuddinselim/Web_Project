<?php
/**
 * Admin Login Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        // Attempt admin login
        if (login($username, $password, 'admin')) {
            header("Location: /Web_Project/admin/dashboard.php");
            exit();
        } else {
            $error = 'Invalid admin credentials.';
        }
    }
    
    // Store error in session and redirect back
    $_SESSION['login_error'] = $error;
    header("Location: /Web_Project/admin/login.php");
    exit();
}
?>
