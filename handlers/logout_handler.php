<?php
/**
 * Logout Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';

// Store the role before logging out
$user_role = $_SESSION['role'] ?? null;

// Logout user
logout();

// Redirect based on previous role
if ($user_role === 'admin') {
    header("Location: /Web_Project/admin/login.php");
} else {
    header("Location: /Web_Project/html/index.php");
}
exit();
?>
