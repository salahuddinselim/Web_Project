<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('member');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$member_id = $_SESSION['member_id'];
$meal_name = trim($_POST['meal_name'] ?? 'Member Input');
$food_items = trim($_POST['food_items'] ?? $meal_name);
$calories = intval($_POST['calories'] ?? 0);
$product_weight = floatval($_POST['product_weight'] ?? 0);
$plan_date = $_POST['plan_date'] ?? date('Y-m-d');

global $pdo;
try {
    $stmt = $pdo->prepare("INSERT INTO diet_plans (member_id, meal_name, meal_time, food_items, calories, product_weight, created_by, plan_date, is_consumed) VALUES (?, ?, 'snack', ?, ?, ?, 'member', ?, 1)");
    $success = $stmt->execute([
        $member_id,
        $meal_name,
        $food_items,
        $calories,
        $product_weight,
        $plan_date
    ]);
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
