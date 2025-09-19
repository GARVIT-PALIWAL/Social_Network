<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/Post.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$post = new Post();

// Get form data
$content = trim($_POST['content'] ?? '');
$image_path = '';

// Validation
if (empty($content)) {
    echo json_encode(['success' => false, 'message' => 'Post content is required']);
    exit;
}

// Handle image upload if present
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/posts/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($file_extension, $allowed_extensions)) {
        $filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
            $image_path = 'uploads/posts/' . $filename;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed']);
        exit;
    }
}

// Create post
$post->user_id = $_SESSION['user_id'];
$post->content = $content;
$post->image_path = $image_path;

if ($post->create()) {
    echo json_encode([
        'success' => true, 
        'message' => 'Post created successfully!',
        'post_id' => $post->id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create post. Please try again.']);
}
?>
