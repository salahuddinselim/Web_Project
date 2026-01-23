<?php
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/db_functions.php';

requireLogin('trainer');
$trainer_id = $_SESSION['trainer_id'];

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'add' || $action === 'edit') {
            $title = $_POST['title'] ?? '';
            $content_type = $_POST['content_type'] ?? 'article';
            $content_body = $_POST['content_body'] ?? '';
            $tags = $_POST['tags'] ?? '';
            $content_id = $_POST['content_id'] ?? null;

            if (empty($title)) {
                echo json_encode(['success' => false, 'message' => 'Title is required']);
                exit;
            }

            $file_path = '';
            $thumbnail = '';

            // Handle Video Upload
            if ($content_type === 'video') {
                if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../../uploads/videos/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    
                    $file_ext = pathinfo($_FILES['video_file']['name'], PATHINFO_EXTENSION);
                    $file_name = uniqid('video_') . '.' . $file_ext;
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['video_file']['tmp_name'], $target_file)) {
                        $file_path = 'uploads/videos/' . $file_name;
                    }
                } elseif ($action === 'add') {
                    echo json_encode(['success' => false, 'message' => 'Video file is required']);
                    exit;
                }
            }

            // Handle Article Photo Upload
            if ($content_type === 'article') {
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = __DIR__ . '/../../uploads/content/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                    
                    $file_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                    $file_name = uniqid('content_') . '.' . $file_ext;
                    $target_file = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
                        $thumbnail = 'uploads/content/' . $file_name;
                    }
                }
            }

            if ($action === 'add') {
                $stmt = $pdo->prepare("INSERT INTO workout_content (trainer_id, title, content_body, content_type, thumbnail, file_path, tags) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$trainer_id, $title, $content_body, $content_type, $thumbnail, $file_path, $tags]);
                echo json_encode(['success' => true, 'message' => 'Content added successfully']);
            } else {
                // Edit logic
                $sql = "UPDATE workout_content SET title = ?, content_body = ?, tags = ?";
                $params = [$title, $content_body, $tags];
                
                if (!empty($file_path)) {
                    $sql .= ", file_path = ?";
                    $params[] = $file_path;
                }
                if (!empty($thumbnail)) {
                    $sql .= ", thumbnail = ?";
                    $params[] = $thumbnail;
                }
                
                $sql .= " WHERE content_id = ? AND trainer_id = ?";
                $params[] = $content_id;
                $params[] = $trainer_id;
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                echo json_encode(['success' => true, 'message' => 'Content updated successfully']);
            }
        } elseif ($action === 'delete') {
            $content_id = $_POST['content_id'] ?? null;
            if ($content_id) {
                // Delete files if possible
                $stmt = $pdo->prepare("SELECT file_path, thumbnail FROM workout_content WHERE content_id = ? AND trainer_id = ?");
                $stmt->execute([$content_id, $trainer_id]);
                $content = $stmt->fetch();
                
                if ($content) {
                    if (!empty($content['file_path'])) {
                        $abs_path = __DIR__ . '/../../' . $content['file_path'];
                        if (file_exists($abs_path)) unlink($abs_path);
                    }
                    if (!empty($content['thumbnail'])) {
                        $abs_path = __DIR__ . '/../../' . $content['thumbnail'];
                        if (file_exists($abs_path)) unlink($abs_path);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM workout_content WHERE content_id = ? AND trainer_id = ?");
                    $stmt->execute([$content_id, $trainer_id]);
                    echo json_encode(['success' => true, 'message' => 'Content deleted successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Content not found']);
                }
            }
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>
