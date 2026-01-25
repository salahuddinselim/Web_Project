<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('trainer');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$diet_plan_id = intval($_POST['diet_plan_id'] ?? 0);
$trainer_id = $_SESSION['trainer_id'];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'delete') {
        $success = deleteDietPlan($diet_plan_id, $trainer_id);
        echo json_encode(['success' => $success]);
    } else {
        $data = [
            'meal_name' => $_POST['meal_name'] ?? '',
            'food_items' => $_POST['food_items'] ?? '',
            'calories' => intval($_POST['calories'] ?? 0),
            'protein_grams' => intval($_POST['protein'] ?? 0),
            'carbs_grams' => intval($_POST['carbs'] ?? 0),
            'fat_grams' => intval($_POST['fat'] ?? 0),
            'product_weight' => floatval($_POST['product_weight'] ?? 0),
            'plan_date' => $_POST['plan_date'] ?? date('Y-m-d')
        ];
        $success = updateDietPlan($diet_plan_id, $trainer_id, $data);
        echo json_encode(['success' => $success]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
