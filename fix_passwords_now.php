<?php
/**
 * COMPLETE PASSWORD FIX - Alternative Method
 * This generates a NEW hash and updates the database
 */

echo "<h1>Complete Password Fix</h1>";
echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} pre{background:#f4f4f4;padding:10px;}</style>";

try {
    $pdo = new PDO(
        "mysql:host=localhost;dbname=pranayom_db;charset=utf8mb4",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<p class='success'>✓ Connected to database</p>";
    
    // Generate a FRESH hash right now
    $password = 'password123';
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<h2>Step 1: Generate New Hash</h2>";
    echo "<p>Password: <code>$password</code></p>";
    echo "<p>New Hash: <code>$new_hash</code></p>";
    
    // Verify the new hash works
    if (password_verify($password, $new_hash)) {
        echo "<p class='success'>✓ New hash verified successfully!</p>";
    } else {
        echo "<p class='error'>✗ Hash generation failed!</p>";
        exit;
    }
    
    // Show current state
    echo "<h2>Step 2: Current Database State</h2>";
    $stmt = $pdo->query("SELECT username, role, password_hash FROM users");
    $users = $stmt->fetchAll();
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Username</th><th>Role</th><th>Current Hash (first 40 chars)</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>" . substr($user['password_hash'], 0, 40) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Update each user individually
    echo "<h2>Step 3: Updating Each User</h2>";
    $update_stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE username = ?");
    
    foreach ($users as $user) {
        $result = $update_stmt->execute([$new_hash, $user['username']]);
        if ($result) {
            echo "<p class='success'>✓ Updated {$user['username']}</p>";
        } else {
            echo "<p class='error'>✗ Failed to update {$user['username']}</p>";
        }
    }
    
    // Verify the update worked
    echo "<h2>Step 4: Verify Update</h2>";
    $stmt = $pdo->query("SELECT username, password_hash FROM users");
    $updated_users = $stmt->fetchAll();
    
    $all_good = true;
    foreach ($updated_users as $user) {
        $works = password_verify($password, $user['password_hash']);
        if ($works) {
            echo "<p class='success'>✓ {$user['username']}: Password works!</p>";
        } else {
            echo "<p class='error'>✗ {$user['username']}: Password FAILED!</p>";
            $all_good = false;
        }
    }
    
    if ($all_good) {
        echo "<div style='background:#d4edda;padding:20px;margin:20px 0;border-radius:5px;border:2px solid #28a745;'>";
        echo "<h2 style='color:#155724;'>🎉 SUCCESS! All Passwords Fixed!</h2>";
        echo "<p><strong>You can now login with:</strong></p>";
        echo "<ul>";
        echo "<li>Username: <code>member1</code> | Password: <code>password123</code></li>";
        echo "<li>Username: <code>trainer1</code> | Password: <code>password123</code></li>";
        echo "<li>Username: <code>admin1</code> | Password: <code>password123</code></li>";
        echo "</ul>";
        echo "<p><a href='html/login.php' style='background:#28a745;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin-top:10px;'>Go to Login Page →</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background:#f8d7da;padding:20px;margin:20px 0;border-radius:5px;'>";
        echo "<h2>Still Having Issues</h2>";
        echo "<p>There may be a PHP configuration issue. Let me check...</p>";
        echo "<p>PHP Version: " . PHP_VERSION . "</p>";
        echo "<p>Password hashing available: " . (function_exists('password_hash') ? 'YES' : 'NO') . "</p>";
        echo "</div>";
    }
    
} catch (PDOException $e) {
    echo "<p class='error'>Database Error: " . $e->getMessage() . "</p>";
}
?>
