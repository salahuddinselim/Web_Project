<!DOCTYPE html>
<html>
<head>
    <title>Password Test</title>
</head>
<body>
    <h2>Password Hash Test</h2>
    <?php
    // The password we're testing
    $password = 'password123';
    
    // The hash from our database
    $hash = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq';
    
    echo "<p><strong>Testing password:</strong> $password</p>";
    echo "<p><strong>Against hash:</strong> $hash</p>";
    
    if (password_verify($password, $hash)) {
        echo "<p style='color: green; font-weight: bold;'>✓ PASSWORD MATCHES! Login should work.</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>✗ PASSWORD DOES NOT MATCH! There's still an issue.</p>";
    }
    
    // Also test database connection
    echo "<hr><h3>Database Connection Test</h3>";
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
        echo "<p style='color: green;'>✓ Database connection successful!</p>";
        
        // Check if users table exists and has data
        $stmt = $pdo->query("SELECT username, email, role FROM users LIMIT 3");
        $users = $stmt->fetchAll();
        
        if (count($users) > 0) {
            echo "<p style='color: green;'>✓ Users table has data:</p>";
            echo "<ul>";
            foreach ($users as $user) {
                echo "<li>{$user['username']} ({$user['role']}) - {$user['email']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p style='color: red;'>✗ Users table is empty! You need to import sample_data.sql</p>";
        }
        
        // Test actual login query
        echo "<hr><h3>Login Query Test</h3>";
        $test_username = 'member1';
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND role IN ('member', 'trainer')");
        $stmt->execute([$test_username]);
        $user = $stmt->fetch();
        
        if ($user) {
            echo "<p style='color: green;'>✓ Found user: {$user['username']}</p>";
            echo "<p>Stored hash: {$user['password_hash']}</p>";
            
            if (password_verify($password, $user['password_hash'])) {
                echo "<p style='color: green; font-weight: bold;'>✓✓ PASSWORD VERIFICATION WORKS WITH DATABASE!</p>";
                echo "<p style='background: #d4edda; padding: 10px; border-radius: 5px;'><strong>SUCCESS!</strong> Login should work now. Try logging in with:<br>Username: member1<br>Password: password123</p>";
            } else {
                echo "<p style='color: red; font-weight: bold;'>✗ Password in database doesn't match!</p>";
                echo "<p style='background: #f8d7da; padding: 10px; border-radius: 5px;'>You need to run this SQL in phpMyAdmin:<br><code>UPDATE users SET password_hash = '$2y$10\$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq';</code></p>";
            }
        } else {
            echo "<p style='color: red;'>✗ User 'member1' not found in database!</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
        echo "<p>Make sure:<br>- XAMPP MySQL is running<br>- Database 'pranayom_db' exists<br>- You've imported schema.sql and sample_data.sql</p>";
    }
    ?>
</body>
</html>
