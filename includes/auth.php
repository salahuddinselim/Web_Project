<?php
/**
 * Authentication Functions
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../config/database.php';

// Session already started in config/session.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Login user
 */
function login($username, $password, $role = null) {
    global $pdo;
    
    try {
        // Prepare query based on role
        if ($role) {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role = ? AND is_active = 1");
            $stmt->execute([$username, $role]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
        }
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Update last login
            $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $updateStmt->execute([$user['user_id']]);
            
            // Get additional profile data based on role
            if ($user['role'] === 'member') {
                $profileStmt = $pdo->prepare("SELECT * FROM members WHERE user_id = ?");
                $profileStmt->execute([$user['user_id']]);
                $profile = $profileStmt->fetch();
                $_SESSION['member_id'] = $profile['member_id'];
                $_SESSION['full_name'] = $profile['full_name'];
                $_SESSION['profile_picture'] = $profile['profile_picture'];
                $_SESSION['trainer_id'] = $profile['trainer_id'];
            } elseif ($user['role'] === 'trainer') {
                $profileStmt = $pdo->prepare("SELECT * FROM trainers WHERE user_id = ?");
                $profileStmt->execute([$user['user_id']]);
                $profile = $profileStmt->fetch();
                $_SESSION['trainer_id'] = $profile['trainer_id'];
                $_SESSION['full_name'] = $profile['full_name'];
                $_SESSION['profile_picture'] = $profile['profile_picture'];
            } elseif ($user['role'] === 'admin') {
                $profileStmt = $pdo->prepare("SELECT * FROM admins WHERE user_id = ?");
                $profileStmt->execute([$user['user_id']]);
                $profile = $profileStmt->fetch();
                $_SESSION['admin_id'] = $profile['admin_id'];
                $_SESSION['full_name'] = $profile['full_name'];
            }
            
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout user
 */
function logout() {
    session_unset();
    session_destroy();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Check user role
 */
function checkRole($required_role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $required_role;
}

/**
 * Require login (redirect if not logged in)
 */
function requireLogin($role = null) {
    if (!isLoggedIn()) {
        if ($role === 'admin') {
            header("Location: /Web_Project/admin/login.php");
        } else {
            header("Location: /Web_Project/html/login.php");
        }
        exit();
    }
    
    if ($role && !checkRole($role)) {
        // Redirect to appropriate dashboard if wrong role
        if ($_SESSION['role'] === 'member') {
            header("Location: /Web_Project/html/member_dashboard.php");
        } elseif ($_SESSION['role'] === 'trainer') {
            header("Location: /Web_Project/trainer/dashboard.php");
        } elseif ($_SESSION['role'] === 'admin') {
            header("Location: /Web_Project/admin/dashboard.php");
        }
        exit();
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user role
 */
function getCurrentUserRole() {
    return $_SESSION['role'] ?? null;
}

/**
 * Get current user full name
 */
function getCurrentUserName() {
    return $_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User';
}
?>
