<?php
/**
 * Logout Handler
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../includes/auth.php';

// Logout user
logout();

// Redirect to homepage
header("Location: /Web_Project/html/index.php");
exit();
?>
