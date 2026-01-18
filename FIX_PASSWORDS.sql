-- =====================================================
-- FIX PASSWORD HASHES
-- Run this in phpMyAdmin SQL tab
-- =====================================================

UPDATE users 
SET password_hash = '$2y$10$e0MYzXyjpJS7Pd0RVvHwHeFpDMJcJYrKZRV7z1p9qJfLQqQY7qJVq';

-- Verify it worked
SELECT username, role, 
       SUBSTRING(password_hash, 1, 30) as hash_preview 
FROM users;
