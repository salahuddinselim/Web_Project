<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('trainer');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

$routine_id = intval($_POST['routine_id'] ?? 0);
$trainer_id = $_SESSION['trainer_id'];
$action = $_POST['action'] ?? '';

try {
    if ($action === 'delete') {
        $success = deleteRoutine($routine_id, $trainer_id);
        echo json_encode(['success' => $success]);
    } else {
        $data = [
            'title' => $_POST['title'] ?? '',
            'description' => $_POST['description'] ?? '',
            'scheduled_date' => $_POST['scheduled_date'] ?? null,
            'exercises' => $_POST['exercises_json'] ?? '[]'
        ];
        $success = updateRoutine($routine_id, $trainer_id, $data);
        echo json_encode(['success' => $success]);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
