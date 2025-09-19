<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/Post.php';

$post = new Post();

$limit = intval($_GET['limit'] ?? 10);
$offset = intval($_GET['offset'] ?? 0);

$posts = $post->getAllPosts($limit, $offset);

// Add like status for current user if logged in
if (isset($_SESSION['user_id'])) {
    foreach ($posts as &$post_data) {
        $post_data['is_liked'] = $post->isLikedByUser($_SESSION['user_id'], $post_data['id']);
    }
}

echo json_encode([
    'success' => true,
    'posts' => $posts
]);
?>

