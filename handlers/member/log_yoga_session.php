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
$routine_id = isset($_POST['routine_id']) ? intval($_POST['routine_id']) : null;
$duration = intval($_POST['duration'] ?? 0);
$session_date = $_POST['session_date'] ?? date('Y-m-d');
$notes = trim($_POST['notes'] ?? '');

if ($duration <= 0) {
    echo json_encode(['error' => 'Duration must be greater than 0']);
    exit;
}

global $pdo;
try {
    $stmt = $pdo->prepare("INSERT INTO yoga_sessions (member_id, routine_id, session_date, duration_minutes, notes) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([
        $member_id,
        $routine_id,
        $session_date,
        $duration,
        $notes
    ]);
    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
