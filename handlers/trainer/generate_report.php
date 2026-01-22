<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('trainer');

$member_id = isset($_GET['member_id']) ? intval($_GET['member_id']) : 0;
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : '';

if (!$member_id || !$report_type) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing member or report type']);
    exit;
}

$member = getMemberProfile($member_id);
if (!$member) {
    http_response_code(404);
    echo json_encode(['error' => 'Member not found']);
    exit;
}

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
        http_response_code(400);
        echo json_encode(['error' => 'Invalid report type']);
        exit;
}

echo json_encode(['member' => $member, 'report_type' => $report_type, 'data' => $data]);
