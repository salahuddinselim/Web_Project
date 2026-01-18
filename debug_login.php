<!DOCTYPE html>
<html>
<head>
    <title>Login Debug</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { background: #e3f2fd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
        th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background: #4CAF50; color: white; }
        code { background: #f4f4f4; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>🔍 Login System Debug</h1>
    
    <?php
    // Test database connection
    try {
        $pdo = new PDO(
            "mysql:host=localhost;dbname=pranayom_db;charset=utf8mb4",
            "root",
            "",
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        echo "<p class='success'>✓ Database connected successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
        echo "<p>Make sure XAMPP MySQL is running and database 'pranayom_db' exists.</p>";
        exit;
    }
    
    // Check all users in database
    echo "<h2>📋 All Users in Database:</h2>";
    $stmt = $pdo->query("SELECT user_id, username, email, role, is_active FROM users ORDER BY role, username");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Active</th></tr>";
        foreach ($users as $user) {
            $active = $user['is_active'] ? '✓ Yes' : '✗ No';
            echo "<tr>";
            echo "<td>{$user['user_id']}</td>";
            echo "<td><strong>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$active}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='error'>✗ No users found! You need to import sample_data.sql</p>";
        exit;
    }
    
    // Test password verification for each user
    echo "<h2>🔐 Password Verification Test (password123):</h2>";
    $test_password = 'password123';
    
    echo "<table>";
    echo "<tr><th>Username</th><th>Role</th><th>Password Works?</th><th>Hash (first 30 chars)</th></tr>";
    
    $stmt = $pdo->query("SELECT username, role, password_hash FROM users ORDER BY role, username");
    $users = $stmt->fetchAll();
    
    foreach ($users as $user) {
        $works = password_verify($test_password, $user['password_hash']);
        $status = $works ? "<span class='success'>✓ YES</span>" : "<span class='error'>✗ NO</span>";
        $hash_preview = substr($user['password_hash'], 0, 30) . '...';
        
        echo "<tr>";
        echo "<td><strong>{$user['username']}</strong></td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$status}</td>";
        echo "<td><code>{$hash_preview}</code></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test actual login function
    echo "<h2>🧪 Testing Login Function:</h2>";
    
    require_once __DIR__ . '/includes/auth.php';
    
    $test_cases = [
        ['username' => 'member1', 'password' => 'password123', 'role' => 'member'],
        ['username' => 'trainer1', 'password' => 'password123', 'role' => 'trainer'],
        ['username' => 'admin1', 'password' => 'password123', 'role' => 'admin'],
    ];
    
    echo "<table>";
    echo "<tr><th>Test</th><th>Username</th><th>Password</th><th>Role</th><th>Result</th></tr>";
    
    foreach ($test_cases as $test) {
        // Clear session before each test
        session_unset();
        
        $result = login($test['username'], $test['password'], $test['role']);
        $status = $result ? "<span class='success'>✓ SUCCESS</span>" : "<span class='error'>✗ FAILED</span>";
        
        echo "<tr>";
        echo "<td>Login Test</td>";
        echo "<td><code>{$test['username']}</code></td>";
        echo "<td><code>{$test['password']}</code></td>";
        echo "<td>{$test['role']}</td>";
        echo "<td>{$status}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Show correct credentials
    echo "<div class='info'>";
    echo "<h3>✅ Correct Login Credentials:</h3>";
    echo "<p>Based on the database, use these credentials:</p>";
    echo "<ul>";
    
    $stmt = $pdo->query("SELECT username, role FROM users WHERE is_active = 1 ORDER BY role, username");
    $active_users = $stmt->fetchAll();
    
    foreach ($active_users as $user) {
        echo "<li><strong>{$user['role']}</strong>: Username = <code>{$user['username']}</code>, Password = <code>password123</code></li>";
    }
    echo "</ul>";
    echo "</div>";
    
    // Fix instructions if passwords don't work
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE password_hash = '\$2y\$10\$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq'");
    $correct_hash_count = $stmt->fetch()['count'];
    
    if ($correct_hash_count < count($users)) {
        echo "<div style='background: #fff3cd; padding: 15px; margin: 20px 0; border-radius: 5px; border-left: 4px solid #ffc107;'>";
        echo "<h3>⚠️ Password Hash Issue Detected!</h3>";
        echo "<p>Some users don't have the correct password hash. Run this SQL in phpMyAdmin:</p>";
        echo "<pre style='background: #f4f4f4; padding: 10px; border-radius: 3px;'>UPDATE users SET password_hash = '\$2y\$10\$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq';</pre>";
        echo "</div>";
    }
    ?>
    
    <hr>
    <p><a href="html/login.php">← Go to Login Page</a></p>
</body>
</html>
