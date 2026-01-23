<?php
// Test password hash verification
$password = 'password123';
$hash_from_db = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq';

echo "Testing password: $password\n";
echo "Against hash: $hash_from_db\n\n";

if (password_verify($password, $hash_from_db)) {
    echo "✅ Password verification SUCCESSFUL\n";
} else {
    echo "❌ Password verification FAILED\n";
    echo "\nGenerating new hash for 'password123':\n";
    $new_hash = password_hash($password, PASSWORD_DEFAULT);
    echo "$new_hash\n";
}
?>
