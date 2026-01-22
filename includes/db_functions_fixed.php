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
