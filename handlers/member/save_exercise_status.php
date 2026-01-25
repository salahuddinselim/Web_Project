<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('member');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$routine_id = intval($_POST['routine_id'] ?? 0);
$member_id = $_SESSION['member_id'];
$completed_json = $_POST['completed_exercises'] ?? '[]';

global $pdo;
try {
    $stmt = $pdo->prepare("UPDATE routines SET completed_exercises = ? WHERE routine_id = ? AND member_id = ?");
    $success = $stmt->execute([$completed_json, $routine_id, $member_id]);
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
