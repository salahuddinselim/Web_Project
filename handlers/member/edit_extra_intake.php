<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('member');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$diet_plan_id = intval($_POST['diet_plan_id'] ?? 0);
$member_id = $_SESSION['member_id'];
$meal_name = trim($_POST['meal_name'] ?? '');
$calories = intval($_POST['calories'] ?? 0);
$product_weight = floatval($_POST['product_weight'] ?? 0);
$plan_date = $_POST['plan_date'] ?? date('Y-m-d');

if ($diet_plan_id <= 0) {
    echo json_encode(['error' => 'Invalid diet plan ID']);
    exit;
}

global $pdo;
try {
    // Only allow members to edit items THEY created
    $stmt = $pdo->prepare("UPDATE diet_plans 
                          SET meal_name = ?, food_items = ?, calories = ?, product_weight = ?, plan_date = ? 
                          WHERE diet_plan_id = ? AND member_id = ? AND created_by = 'member'");
    
    $success = $stmt->execute([
        $meal_name,
        $meal_name, // Use meal_name as food_items for simplicity if not provided
        $calories,
        $product_weight,
        $plan_date,
        $diet_plan_id,
        $member_id
    ]);
    
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
