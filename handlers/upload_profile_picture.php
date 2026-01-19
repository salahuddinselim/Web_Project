<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/db_functions.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../html/login.php");
    exit;
}

$user_role = $_SESSION['role']; // 'member', 'trainer', 'admin'
$redirect_url = "";

// Determine redirect URL based on role
switch ($user_role) {
    case 'member':
        $redirect_url = "../html/member_profile.php";
        $id_column = "member_id";
        $table_name = "members";
        $entity_id = $_SESSION['member_id'];
        break;
    case 'trainer':
        $redirect_url = "../trainer/profile.php";
        $id_column = "trainer_id";
        $table_name = "trainers";
        $entity_id = $_SESSION['trainer_id'];
        break;
    case 'admin':
        $redirect_url = "../admin/profile.php";
        $id_column = "admin_id";
        $table_name = "admins";
        $entity_id = $_SESSION['admin_id'];
        break;
    default:
        die("Unauthorized role.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    $file = $_FILES['profile_picture'];
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = __DIR__ . '/../uploads/profile_pics/';

    // Create directory if not exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Validate file
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error_message'] = "File upload failed with error code: " . $file['error'];
        header("Location: $redirect_url");
        exit;
    }

    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
        header("Location: $redirect_url");
        exit;
    }

    if ($file['size'] > $max_size) {
        $_SESSION['error_message'] = "File size too large. Max 5MB.";
        header("Location: $redirect_url");
        exit;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = $user_role . '_' . $entity_id . '_' . time() . '.' . $extension;
    $target_path = $upload_dir . $new_filename;

    // Move file
    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        // Update database
        try {
            $stmt = $pdo->prepare("UPDATE $table_name SET profile_picture = ? WHERE $id_column = ?");
            $stmt->execute([$new_filename, $entity_id]);
            
            $_SESSION['success_message'] = "Profile picture updated successfully!";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Database error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Failed to move uploaded file.";
    }
} else {
    $_SESSION['error_message'] = "No file uploaded.";
}

header("Location: $redirect_url");
exit;
?>
