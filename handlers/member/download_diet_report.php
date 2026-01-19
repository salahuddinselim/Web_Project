<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get diet plans for today and future, or all? Let's get today's plan primarily, or a weekly summary.
// The user asked for "the report". A simple comprehensive report is best.
// Let's get the plan for the current week or just "All upcoming plans".
// For simplicity and utility, let's export ALL diet plans assigned.

// We need a function to get ALL plans, or reuse getMemberDietPlans without date?
// getMemberDietPlans uses date if provided. If null, it gets ALL?
// Let's check db_functions.php.
// Yes: if ($date) { $sql .= " AND d.plan_date = ?"; }
// So if we pass null, it returns all.

$plans = getMemberDietPlans($member_id, null);

// Filename
$filename = "Diet_Plan_" . date('Y-m-d') . ".csv";

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// CSV Header
fputcsv($output, ['Date', 'Meal Time', 'Meal Name', 'Calories', 'Food Items', 'Notes']);

// CSV Data
foreach ($plans as $plan) {
    fputcsv($output, [
        $plan['plan_date'],
        ucfirst($plan['meal_time']),
        $plan['meal_name'],
        $plan['calories'],
        $plan['food_items'],
        $plan['notes'] ?? ''
    ]);
}

fclose($output);
exit();
