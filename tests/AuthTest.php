<?php
require_once __DIR__ . '/../includes/auth.php';

class AuthTest {
    
    public function testFailedLogin() {
        $result = login('nonexistent', 'wrongpassword');
        assertEqual(false, $result, "Login should fail for invalid credentials");
    }
    
    public function testSuccessfulLogin() {
        // This assumes admin1 / password123 exists in DB with the correct hash
        // We use the sample data hash for verification in this test context
        $result = login('admin1', 'password123');
        assertEqual(true, $result, "Login should succeed for valid credentials");
    }
    
    public function testLogout() {
        $_SESSION['logged_in'] = true;
        logout();
        assertEqual(false, isset($_SESSION['logged_in']), "Session should be cleared after logout");
    }
}
