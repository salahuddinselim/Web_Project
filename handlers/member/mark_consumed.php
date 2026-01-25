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
$is_consumed = intval($_POST['is_consumed'] ?? 1);

global $pdo;
try {
    $stmt = $pdo->prepare("UPDATE diet_plans SET is_consumed = ? WHERE diet_plan_id = ? AND member_id = ?");
    $success = $stmt->execute([$is_consumed, $diet_plan_id, $member_id]);
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
