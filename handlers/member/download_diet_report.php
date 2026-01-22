<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
require_once __DIR__ . '/../../includes/fpdf.php'; // Assuming FPDF is installed here

requireLogin('member');
$member_id = $_SESSION['member_id'];
$member_name = $_SESSION['full_name'];

$plans = getMemberDietPlans($member_id, null);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'Diet Plan Report for ' . $member_name, 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Date', 1);
$pdf->Cell(25, 10, 'Meal Time', 1);
$pdf->Cell(40, 10, 'Meal Name', 1);
$pdf->Cell(20, 10, 'Calories', 1);
$pdf->Cell(0, 10, 'Food Items', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);
foreach ($plans as $plan) {
    $pdf->Cell(30, 10, $plan['plan_date'], 1);
    $pdf->Cell(25, 10, ucfirst($plan['meal_time']), 1);
    $pdf->Cell(40, 10, $plan['meal_name'], 1);
    $pdf->Cell(20, 10, $plan['calories'], 1);
    $pdf->MultiCell(0, 10, $plan['food_items'], 1);
}

$filename = "Diet_Plan_" . date('Y-m-d') . ".pdf";
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$pdf->Output('D', $filename);
exit();
