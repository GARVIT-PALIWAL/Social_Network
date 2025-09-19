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

$post_id = intval($_POST['post_id'] ?? 0);
$user_id = $_SESSION['user_id'];

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

if ($post->deletePost($post_id, $user_id)) {
    echo json_encode(['success' => true, 'message' => 'Post deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to delete post or you do not have permission']);
}
?>
