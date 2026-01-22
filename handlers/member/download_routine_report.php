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
fputcsv($output, ['Title', 'Difficulty', 'Description', 'Trainer']);

foreach ($routines as $r) {
    fputcsv($output, [
        $r['title'] ?? '',
        ucfirst($r['difficulty_level'] ?? ''),
        $r['description'] ?? '',
        $r['trainer_name'] ?? ''
    ]);
}

fputcsv($output, []); // Empty line
fputcsv($output, ['--- Recent Progress Logs ---']);
fputcsv($output, ['Date', 'Weight (kg)', 'Heart Rate (bpm)', 'Sleep Hours', 'Mood', 'Notes']);

foreach ($progress as $p) {
    fputcsv($output, [
        $p['tracking_date'] ?? '',
        $p['weight_kg'] ?? '',
        $p['heart_rate'] ?? '',
        $p['sleep_hours'] ?? '',
        ucfirst($p['mood'] ?? ''),
        $p['notes'] ?? ''
    ]);
}

fclose($output);
exit();
