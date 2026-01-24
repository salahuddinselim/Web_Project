<?php
/**
 * Quick utility to generate password hashes for database insertion
 */
if (php_sapi_name() !== 'cli') {
    die("This script must be run from the command line.");
}

if ($argc < 2) {
    echo "Usage: php hash_password.php <password>\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash:     $hash\n\n";
echo "SQL Example:\n";
echo "UPDATE users SET password_hash = '$hash' WHERE username = 'your_username';\n";
