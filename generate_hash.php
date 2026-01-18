<?php
/**
 * Generate Password Hash for password123
 * Run this file once to get the correct hash
 */

$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n";
echo "\nCopy this hash to your sample_data.sql file\n";
?>
