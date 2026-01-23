<?php
/**
 * Password Reset Utility for trainer1
 * Run this script once to reset the password, then DELETE this file for security
 */

require_once __DIR__ . '/config/database.php';

// Generate new hash for password123
$new_password = 'password123';
$new_hash = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Update trainer1 password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = 'trainer1'");
    $result = $stmt->execute([$new_hash]);
    
    if ($result) {
        echo "<h2 style='color: green;'>✅ Password Reset Successful!</h2>";
        echo "<p>Username: <strong>trainer1</strong></p>";
        echo "<p>Password: <strong>password123</strong></p>";
        echo "<p>New hash: <code>$new_hash</code></p>";
        echo "<hr>";
        echo "<p style='color: red; font-weight: bold;'>⚠️ IMPORTANT: Delete this file (reset_trainer_password.php) immediately for security!</p>";
        echo "<p><a href='html/login.php'>Go to Login Page</a></p>";
    } else {
        echo "<h2 style='color: red;'>❌ Failed to update password</h2>";
    }
    
    // Also update all other users to use the same password for consistency
    echo "<hr><h3>Updating all user passwords to 'password123'...</h3>";
    $users = ['admin1', 'trainer2', 'member1', 'member2', 'member3', 'member_afia'];
    foreach ($users as $username) {
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
        if ($stmt->execute([$new_hash, $username])) {
            echo "<p>✅ Updated password for: <strong>$username</strong></p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<h2 style='color: red;'>❌ Database Error</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
