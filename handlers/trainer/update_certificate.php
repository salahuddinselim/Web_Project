<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';
requireLogin('trainer');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $trainer_id = $_SESSION['trainer_id'];
    
    if (isset($_FILES['certification']) && $_FILES['certification']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../../uploads/certificates/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_info = pathinfo($_FILES['certification']['name']);
        $extension = strtolower($file_info['extension']);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        
        if (in_array($extension, $allowed_extensions)) {
            $filename = 'cert_' . $trainer_id . '_' . time() . '.' . $extension;
            $target_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['certification']['tmp_name'], $target_file)) {
                global $pdo;
                $stmt = $pdo->prepare("UPDATE trainers SET certification = ? WHERE trainer_id = ?");
                $stmt->execute([$filename, $trainer_id]);
                
                $_SESSION['success_message'] = "Certification photo updated successfully!";
            } else {
                $_SESSION['error_message'] = "Failed to upload file.";
            }
        } else {
            $_SESSION['error_message'] = "Invalid file type. Only JPG, PNG, WEBP, and GIF are allowed.";
        }
    } else {
        $_SESSION['error_message'] = "No file selected or upload error.";
    }
    
    header("Location: ../../trainer/profile.php");
    exit;
}
