<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('trainer');

header('Content-Type: application/json');

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
$data_type = isset($_GET['type']) ? $_GET['type'] : '';

if (!$member_id || !$data_type) {
    echo json_encode(['error' => 'Missing member_id or type parameter']);
    exit;
}

// Verify trainer has access to this member
$trainer_id = $_SESSION['trainer_id'];
$members = getTrainerMembers($trainer_id);
$member_ids = array_column($members, 'member_id');
if (!in_array($member_id, $member_ids)) {
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$member = getMemberProfile($member_id);
$response = [
    'member' => [
        'member_id' => $member['member_id'],
        'full_name' => $member['full_name'],
        'email' => $member['email'],
        'phone' => $member['phone'],
        'membership_type' => $member['membership_type'] ?? 'Basic',
        'join_date' => $member['join_date'] ?? '',
        'gender' => $member['gender'] ?? ''
    ]
];

switch ($data_type) {
    case 'profile':
        // Profile data already in $response['member']
        break;
    case 'diet':
        $response['diet_plans'] = getMemberDietPlans($member_id, null);
        break;
    case 'routines':
        $response['routines'] = getMemberRoutines($member_id, true);
        break;
    case 'progress':
        $response['progress'] = getMemberProgress($member_id, 30);
        break;
    default:
        $response['error'] = 'Invalid type parameter';
}

echo json_encode($response);
