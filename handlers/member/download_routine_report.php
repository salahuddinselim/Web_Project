<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get routines
$routines = getMemberRoutines($member_id, false); // false = include inactive? Or just all active.
// Also maybe include progress tracking logs?
$progress = getMemberProgress($member_id, 100);

// Filename
$filename = "Routine_Report_" . date('Y-m-d') . ".csv";

// Set headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Section 1: Assigned Routines
fputcsv($output, ['--- Assigned Routines ---']);
fputcsv($output, ['Title', 'Difficulty', 'Description', 'Trainer Created']);

foreach ($routines as $r) {
    fputcsv($output, [
        $r['title'],
        ucfirst($r['difficulty_level']),
        $r['description'],
        $r['created_at'] // Or trainer name if available
    ]);
}

fputcsv($output, []); // Empty line
fputcsv($output, ['--- Recent Progress Logs ---']);
fputcsv($output, ['Date', 'Weight (kg)', 'BMI', 'Notes']);

foreach ($progress as $p) {
    fputcsv($output, [
        $p['tracking_date'],
        $p['weight'],
        $p['bmi'],
        $p['notes']
    ]);
}

fclose($output);
exit();
