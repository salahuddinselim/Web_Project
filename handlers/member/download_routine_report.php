<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
require_once __DIR__ . '/../../includes/fpdf.php';

requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

// Get routines and progress
$routines = getMemberRoutines($member_id, false);
$progress = getMemberProgress($member_id, 30);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Routine & Progress Report', 0, 1, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, 'Member: ' . $member_name, 0, 1);
$pdf->Cell(0, 10, 'Generated: ' . date('Y-m-d H:i:s'), 0, 1);
$pdf->Ln(5);

// Section 1: Assigned Routines
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Assigned Routines', 0, 1);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(60, 8, 'Title', 1);
$pdf->Cell(30, 8, 'Difficulty', 1);
$pdf->Cell(50, 8, 'Trainer', 1);
$pdf->Cell(40, 8, 'Type', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 9);
foreach ($routines as $r) {
    $pdf->Cell(60, 8, substr($r['title'] ?? '', 0, 25), 1);
    $pdf->Cell(30, 8, ucfirst($r['difficulty_level'] ?? ''), 1);
    $pdf->Cell(50, 8, substr($r['trainer_name'] ?? '', 0, 20), 1);
    $pdf->Cell(40, 8, ucfirst($r['routine_type'] ?? ''), 1);
    $pdf->Ln();
}

$pdf->Ln(10);

// Section 2: Recent Progress Logs
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Recent Progress Logs (Last 30 Days)', 0, 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(30, 8, 'Date', 1);
$pdf->Cell(25, 8, 'Weight (kg)', 1);
$pdf->Cell(25, 8, 'Heart Rate', 1);
$pdf->Cell(25, 8, 'Sleep (hrs)', 1);
$pdf->Cell(30, 8, 'Mood', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 8);
foreach ($progress as $p) {
    $pdf->Cell(30, 8, $p['tracking_date'] ?? '', 1);
    $pdf->Cell(25, 8, $p['weight_kg'] ?? '-', 1);
    $pdf->Cell(25, 8, $p['heart_rate'] ?? '-', 1);
    $pdf->Cell(25, 8, $p['sleep_hours'] ?? '-', 1);
    $pdf->Cell(30, 8, ucfirst($p['mood'] ?? '-'), 1);
    $pdf->Ln();
}

$filename = "Routine_Report_" . date('Y-m-d') . ".pdf";
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$pdf->Output('D', $filename);
exit();
