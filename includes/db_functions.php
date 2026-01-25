<?php
/**
* Get member's attendance records
*/
function getMemberAttendance($member_id, $limit = 30)
{
global $pdo;
$stmt = $pdo->prepare("SELECT cb.*, c.class_name, c.schedule_day, c.schedule_time, c.duration_minutes, c.class_type, t.full_name as trainer_name
FROM class_bookings cb
JOIN classes c ON cb.class_id = c.class_id
JOIN trainers t ON c.trainer_id = t.trainer_id
WHERE cb.member_id = ?
ORDER BY cb.booking_date DESC, c.schedule_time LIMIT ?");
$stmt->execute([$member_id, $limit]);
return $stmt->fetchAll();
}

/**
 * Database Helper Functions
 * Pranayom Fitness Management System
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Get user by ID
 */
function getUserById($user_id)
{
    global $pdo;
    // Join with role tables to get profile info (full_name, profile_picture)
    $stmt = $pdo->prepare("SELECT u.*, 
                          COALESCE(m.full_name, t.full_name, a.full_name) as full_name,
                          COALESCE(m.profile_picture, t.profile_picture, a.profile_picture) as profile_picture
                          FROM users u 
                          LEFT JOIN members m ON u.user_id = m.user_id
                          LEFT JOIN trainers t ON u.user_id = t.user_id
                          LEFT JOIN admins a ON u.user_id = a.user_id
                          WHERE u.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

/**
 * Get member profile
 */
function getMemberProfile($member_id)
{
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
function getTrainerProfile($trainer_id)
{
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
function getAdminProfile($admin_id)
{
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
function getAssignedTrainer($member_id)
{
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
function getTrainerMembers($trainer_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM members WHERE trainer_id = ? ORDER BY full_name");
    $stmt->execute([$trainer_id]);
    return $stmt->fetchAll();
}

/**
 * Get member's routines
 */
function getMemberRoutines($member_id, $active_only = true)
{
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
function getMemberDietPlans($member_id, $date = null)
{
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
function getMemberProgress($member_id, $limit = 30)
{
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
function getAvailableClasses($day = null)
{
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
function getMemberBookings($member_id, $upcoming_only = true)
{
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
 * Mark messages as read
 */
function markMessagesAsRead($receiver_id, $sender_id)
{
    global $pdo;
    $stmt = $pdo->prepare("UPDATE messages SET is_read = 1 
                          WHERE receiver_id = ? AND sender_id = ?");
    $stmt->execute([$receiver_id, $sender_id]);
}

/**
 * Get unread message count
 */
function getUnreadMessageCount($user_id)
{
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
function getAllTrainers()
{
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM trainers ORDER BY full_name");
    return $stmt->fetchAll();
}

/**
 * Get all members
 */
function getAllMembers()
{
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
function getAdminStats()
{
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
function getTrainerStats($trainer_id)
{
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

/**
 * Get messages between two users
 */
function getMessages($user1_id, $user2_id, $limit = 50)
{
    global $pdo;
    $sql = "SELECT m.* FROM messages m 
            WHERE (m.sender_id = ? AND m.receiver_id = ?) 
               OR (m.sender_id = ? AND m.receiver_id = ?) 
            ORDER BY m.sent_at DESC LIMIT ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user1_id, $user2_id, $user2_id, $user1_id, $limit]);
    return $stmt->fetchAll();
}

/**
 * Send a message
 */
function sendMessage($sender_id, $receiver_id, $message_text)
{
    global $pdo;
    $stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)");
    return $stmt->execute([$sender_id, $receiver_id, $message_text]);
}
/**
 * Get member's calorie summary for a specific date
 */
function getMemberCalorieSummary($member_id, $date = null) {
    global $pdo;
    if (!$date) $date = date('Y-m-d');
    
    // Total planned (everything for that day)
    $stmt = $pdo->prepare("SELECT 
        SUM(calories) as planned_calories, 
        SUM(protein_grams) as planned_protein, 
        SUM(carbs_grams) as planned_carbs, 
        SUM(fat_grams) as planned_fat 
        FROM diet_plans WHERE member_id = ? AND plan_date = ?");
    $stmt->execute([$member_id, $date]);
    $planned = $stmt->fetch(PDO::FETCH_ASSOC);

    // Total consumed (only where is_consumed = 1)
    $stmt = $pdo->prepare("SELECT 
        SUM(calories) as taken_calories, 
        SUM(protein_grams) as taken_protein, 
        SUM(carbs_grams) as taken_carbs, 
        SUM(fat_grams) as taken_fat 
        FROM diet_plans WHERE member_id = ? AND plan_date = ? AND is_consumed = 1");
    $stmt->execute([$member_id, $date]);
    $taken = $stmt->fetch(PDO::FETCH_ASSOC);

    return array_merge($planned, $taken);
}

/**
 * Get member's yoga session time for a date range
 */
function getMemberYogaSummary($member_id, $start_date, $end_date) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT SUM(duration_minutes) as total_minutes FROM yoga_sessions WHERE member_id = ? AND session_date BETWEEN ? AND ?");
    $stmt->execute([$member_id, $start_date, $end_date]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['total_minutes'] ?? 0;
}

/**
 * Get all yoga sessions for a member
 */
function getMemberYogaSessions($member_id, $limit = 30) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM yoga_sessions WHERE member_id = ? ORDER BY session_date DESC LIMIT ?");
    $stmt->execute([$member_id, $limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Function to update diet plan
 */
function updateDietPlan($diet_plan_id, $trainer_id, $data) {
    global $pdo;
    // Allow update if it's the trainer's plan OR if the plan belongs to a member assigned to this trainer
    $stmt = $pdo->prepare("UPDATE diet_plans d 
                          SET d.meal_name = ?, d.food_items = ?, d.calories = ?, d.protein_grams = ?, d.carbs_grams = ?, d.fat_grams = ?, d.plan_date = ?, d.product_weight = ? 
                          WHERE d.diet_plan_id = ? 
                          AND (d.trainer_id = ? OR d.member_id IN (SELECT member_id FROM members WHERE trainer_id = ?))");
    return $stmt->execute([
        $data['meal_name'],
        $data['food_items'],
        $data['calories'],
        $data['protein_grams'],
        $data['carbs_grams'],
        $data['fat_grams'],
        $data['plan_date'],
        $data['product_weight'] ?? 0,
        $diet_plan_id,
        $trainer_id,
        $trainer_id
    ]);
}

/**
 * Function to delete diet plan
 */
function deleteDietPlan($diet_plan_id, $trainer_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM diet_plans 
                          WHERE diet_plan_id = ? 
                          AND (trainer_id = ? OR member_id IN (SELECT member_id FROM members WHERE trainer_id = ?))");
    return $stmt->execute([$diet_plan_id, $trainer_id, $trainer_id]);
}

/**
 * Function to update routine
 */
function updateRoutine($routine_id, $trainer_id, $data) {
    global $pdo;
    $stmt = $pdo->prepare("UPDATE routines SET title = ?, description = ?, exercises = ?, scheduled_date = ? WHERE routine_id = ? AND trainer_id = ?");
    return $stmt->execute([
        $data['title'],
        $data['description'],
        $data['exercises'],
        $data['scheduled_date'],
        $routine_id,
        $trainer_id
    ]);
}

/**
 * Function to delete routine
 */
function deleteRoutine($routine_id, $trainer_id) {
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM routines WHERE routine_id = ? AND trainer_id = ?");
    return $stmt->execute([$routine_id, $trainer_id]);
}

/**
 * Get trainer's uploaded content
 */
function getTrainerContent($trainer_id)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM workout_content WHERE trainer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$trainer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
