<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/Post.php';

$post = new Post();

$post_id = intval($_GET['post_id'] ?? 0);

if ($post_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid post ID']);
    exit;
}

$comments = $post->getComments($post_id);

echo json_encode([
    'success' => true,
    'comments' => $comments
]);
?>
