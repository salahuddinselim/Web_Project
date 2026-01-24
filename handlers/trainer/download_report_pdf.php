<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
require_once __DIR__ . '/../../includes/fpdf.php';
requireLogin('trainer');

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : '';

if (!$member_id || !$report_type) {
    die('Missing member or report type');
}

$member = getMemberProfile($member_id);
if (!$member) {
    die('Member not found');
}

// Fetch data
$data = [];
switch ($report_type) {
    case 'Attendance Report':
        $data['attendance'] = getMemberAttendance($member_id);
        break;
    case 'Health Statistics':
        $data['progress'] = getMemberProgress($member_id, 12);
        break;
    case 'Progress Summary':
        $data['progress'] = getMemberProgress($member_id, 12);
        break;
    case 'Full Profile':
        $data['profile'] = $member;
        $data['progress'] = getMemberProgress($member_id, 12);
        $data['attendance'] = getMemberAttendance($member_id);
        break;
    default:
        die('Invalid report type');
}


$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Member Report: ' . $report_type, 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Name: ' . $member['full_name'], 0, 1);
$pdf->Cell(0, 10, 'Member ID: ' . $member['member_id'], 0, 1);
$pdf->Ln(5);

if ($report_type === 'Attendance Report' && !empty($data['attendance'])) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Recent Attendance:', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    foreach ($data['attendance'] as $a) {
        $pdf->Cell(0, 8, 'Date: ' . $a['booking_date'] . ' | Class: ' . $a['class_name'] . ' | Status: ' . $a['status'], 0, 1);
    }
    $pdf->Ln(3);
} elseif (($report_type === 'Health Statistics' || $report_type === 'Progress Summary') && !empty($data['progress'])) {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Recent Progress:', 0, 1);
    $pdf->SetFont('Arial', '', 11);
    foreach ($data['progress'] as $p) {
        $pdf->Cell(0, 8, 'Date: ' . $p['tracking_date'] . ' | Weight: ' . $p['weight_kg'] . ' kg | HR: ' . $p['heart_rate'] . ' bpm', 0, 1);
    }
    $pdf->Ln(3);
} elseif ($report_type === 'Full Profile') {
    if (!empty($data['profile'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Profile:', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 8, 'Email: ' . $data['profile']['email'], 0, 1);
        $pdf->Cell(0, 8, 'Phone: ' . $data['profile']['phone'], 0, 1);
        $pdf->Cell(0, 8, 'DOB: ' . $data['profile']['date_of_birth'], 0, 1);
        $pdf->Cell(0, 8, 'Membership: ' . $data['profile']['membership_type'], 0, 1);
        $pdf->Ln(3);
    }
    if (!empty($data['progress'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Recent Progress:', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        foreach ($data['progress'] as $p) {
            $pdf->Cell(0, 8, 'Date: ' . $p['tracking_date'] . ' | Weight: ' . $p['weight_kg'] . ' kg | HR: ' . $p['heart_rate'] . ' bpm', 0, 1);
        }
        $pdf->Ln(3);
    }
    if (!empty($data['attendance'])) {
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Recent Attendance:', 0, 1);
        $pdf->SetFont('Arial', '', 11);
        foreach ($data['attendance'] as $a) {
            $pdf->Cell(0, 8, 'Date: ' . $a['booking_date'] . ' | Class: ' . $a['class_name'] . ' | Status: ' . $a['status'], 0, 1);
        }
        $pdf->Ln(3);
    }
}
$filename = 'member_report_' . $member_id . '_' . date('Ymd') . '.pdf';
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$pdf->Output('D', $filename);
exit();
