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

// Check if user already liked this post
if ($post->isLikedByUser($user_id, $post_id)) {
    // Unlike the post
    if ($post->unlikePost($user_id, $post_id)) {
        echo json_encode(['success' => true, 'action' => 'unliked', 'message' => 'Post unliked']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to unlike post']);
    }
} else {
    // Like the post
    if ($post->likePost($user_id, $post_id)) {
        echo json_encode(['success' => true, 'action' => 'liked', 'message' => 'Post liked']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to like post']);
    }
}
?>
