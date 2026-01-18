<?php
/**
 * Database Helper Functions
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get user by ID
 */
function getUserById($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

/**
 * Get member profile
 */
function getMemberProfile($member_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT m.*, u.username, u.email FROM members m 
                          JOIN users u ON m.user_id = u.user_id 
                          WHERE m.member_id = ?");
    $stmt->execute([$member_id]);
    return $stmt->fetch();
}

/**
 * Get trainer profile
 */
function getTrainerProfile($trainer_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT t.*, u.username, u.email FROM trainers t 
                          JOIN users u ON t.user_id = u.user_id 
                          WHERE t.trainer_id = ?");
    $stmt->execute([$trainer_id]);
    return $stmt->fetch();
}

/**
 * Get admin profile
 */
function getAdminProfile($admin_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT a.*, u.username, u.email FROM admins a 
                          JOIN users u ON a.user_id = u.user_id 
                          WHERE a.admin_id = ?");
    $stmt->execute([$admin_id]);
    return $stmt->fetch();
}

/**
 * Get assigned trainer for a member
 */
function getAssignedTrainer($member_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT t.* FROM trainers t 
                          JOIN members m ON t.trainer_id = m.trainer_id 
                          WHERE m.member_id = ?");
    $stmt->execute([$member_id]);
    return $stmt->fetch();
}

/**
 * Get all members assigned to a trainer
 */
function getTrainerMembers($trainer_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM members WHERE trainer_id = ? ORDER BY full_name");
    $stmt->execute([$trainer_id]);
    return $stmt->fetchAll();
}

/**
 * Get member's routines
 */
function getMemberRoutines($member_id, $active_only = true) {
    global $pdo;
    $sql = "SELECT r.*, t.full_name as trainer_name FROM routines r 
            JOIN trainers t ON r.trainer_id = t.trainer_id 
            WHERE r.member_id = ?";
    if ($active_only) {
        $sql .= " AND r.is_active = 1";
    }
    $sql .= " ORDER BY r.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id]);
    return $stmt->fetchAll();
}

/**
 * Get member's diet plans
 */
function getMemberDietPlans($member_id, $date = null) {
    global $pdo;
    $sql = "SELECT d.*, t.full_name as trainer_name FROM diet_plans d 
            LEFT JOIN trainers t ON d.trainer_id = t.trainer_id 
            WHERE d.member_id = ?";
    
    $params = [$member_id];
    if ($date) {
        $sql .= " AND d.plan_date = ?";
        $params[] = $date;
    }
    $sql .= " ORDER BY d.plan_date DESC, d.meal_time";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get member's progress tracking
 */
function getMemberProgress($member_id, $limit = 30) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM progress_tracking 
                          WHERE member_id = ? 
                          ORDER BY tracking_date DESC 
                          LIMIT ?");
    $stmt->execute([$member_id, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get available classes
 */
function getAvailableClasses($day = null) {
    global $pdo;
    $sql = "SELECT c.*, t.full_name as instructor 
            FROM classes c 
            JOIN trainers t ON c.trainer_id = t.trainer_id 
            WHERE c.is_active = 1";
    
    $params = [];
    if ($day) {
        $sql .= " AND c.schedule_day = ?";
        $params[] = $day;
    }
    $sql .= " ORDER BY c.schedule_day, c.schedule_time";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Get member's class bookings
 */
function getMemberBookings($member_id, $upcoming_only = true) {
    global $pdo;
    $sql = "SELECT cb.*, c.class_name, t.full_name as instructor, c.schedule_day, c.schedule_time, c.duration_minutes 
            FROM class_bookings cb 
            JOIN classes c ON cb.class_id = c.class_id 
            JOIN trainers t ON c.trainer_id = t.trainer_id 
            WHERE cb.member_id = ?";
    
    if ($upcoming_only) {
        $sql .= " AND cb.booking_date >= CURDATE() AND cb.status = 'booked'";
    }
    $sql .= " ORDER BY cb.booking_date, c.schedule_time";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$member_id]);
    return $stmt->fetchAll();
}

/**
 * Get messages between two users
 */
function getMessages($user1_id, $user2_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT m.*, u.username as sender_name 
                          FROM messages m 
                          JOIN users u ON m.sender_id = u.user_id 
                          WHERE (m.sender_id = ? AND m.receiver_id = ?) 
                             OR (m.sender_id = ? AND m.receiver_id = ?) 
                          ORDER BY m.sent_at ASC");
    $stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id]);
    return $stmt->fetchAll();
}

/**
 * Mark messages as read
 */
function markMessagesAsRead($receiver_id, $sender_id) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 
                          WHERE receiver_id = ? AND sender_id = ?");
    $stmt->execute([$receiver_id, $sender_id]);
}

/**
 * Get unread message count
 */
function getUnreadMessageCount($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages 
                          WHERE receiver_id = ? AND is_read = 0");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result['count'];
}

/**
 * Get all trainers
 */
function getAllTrainers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM trainers ORDER BY full_name");
    return $stmt->fetchAll();
}

/**
 * Get all members
 */
function getAllMembers() {
    global $pdo;
    $stmt = $pdo->query("SELECT m.*, t.full_name as trainer_name 
                        FROM members m 
                        LEFT JOIN trainers t ON m.trainer_id = t.trainer_id 
                        ORDER BY m.full_name");
    return $stmt->fetchAll();
}

/**
 * Get dashboard statistics for admin
 */
function getAdminStats() {
    global $pdo;
    
    $stats = [];
    
    // Total members
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM members");
    $stats['total_members'] = $stmt->fetch()['count'];
    
    // Total trainers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM trainers");
    $stats['total_trainers'] = $stmt->fetch()['count'];
    
    // Active classes
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM classes WHERE is_active = 1");
    $stats['active_classes'] = $stmt->fetch()['count'];
    
    // Recent registrations (last 30 days)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM members WHERE join_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
    $stats['recent_registrations'] = $stmt->fetch()['count'];
    
    return $stats;
}

/**
 * Get dashboard statistics for trainer
 */
function getTrainerStats($trainer_id) {
    global $pdo;
    
    $stats = [];
    
    // Total assigned members
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM members WHERE trainer_id = ?");
    $stmt->execute([$trainer_id]);
    $stats['total_members'] = $stmt->fetch()['count'];
    
    // Active routines
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM routines WHERE trainer_id = ? AND is_active = 1");
    $stmt->execute([$trainer_id]);
    $stats['active_routines'] = $stmt->fetch()['count'];
    
    // Unread messages
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM messages 
                          WHERE receiver_id = (SELECT user_id FROM trainers WHERE trainer_id = ?) 
                          AND is_read = 0");
    $stmt->execute([$trainer_id]);
    $stats['unread_messages'] = $stmt->fetch()['count'];
    
    return $stats;
}