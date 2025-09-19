<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/Post.php';
require_once 'classes/User.php';

$post = new Post();
$user = new User();

// Get current user info
$user->getUserById($_SESSION['user_id']);

// Get posts for the feed
$posts = $post->getAllPosts(20, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network - Home</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header class="header">
            <div class="header-content">
                <h1><i class="fas fa-users"></i> Social Network</h1>
                <nav class="nav">
                    <a href="index.php" class="nav-link active"><i class="fas fa-home"></i> Home</a>
                    <a href="profile.php" class="nav-link"><i class="fas fa-user"></i> Profile</a>
                    <a href="#" id="logout-btn" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Create Post Section -->
            <div class="create-post-section">
                <div class="post-form">
                    <div class="user-info">
                        <img src="<?php echo $user->profile_picture ? 'uploads/profiles/' . $user->profile_picture : 'https://via.placeholder.com/40'; ?>" 
                             alt="Profile" class="profile-pic">
                        <span class="user-name"><?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?></span>
                    </div>
                    <form id="create-post-form" enctype="multipart/form-data">
                        <textarea id="post-content" placeholder="What's on your mind?" required></textarea>
                        <div class="post-actions">
                            <label for="post-image" class="file-input-label">
                                <i class="fas fa-image"></i> Add Photo
                            </label>
                            <input type="file" id="post-image" name="image" accept="image/*" style="display: none;">
                            <button type="submit" class="btn btn-primary">Post</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Posts Feed -->
            <div class="posts-feed" id="posts-feed">
                <?php foreach ($posts as $post_data): ?>
                <div class="post" data-post-id="<?php echo $post_data['id']; ?>">
                    <div class="post-header">
                        <img src="<?php echo $post_data['author_profile_picture'] ? 'uploads/profiles/' . $post_data['author_profile_picture'] : 'https://via.placeholder.com/40'; ?>" 
                             alt="Profile" class="profile-pic">
                        <div class="post-author">
                            <h3><?php echo htmlspecialchars($post_data['author_first_name'] . ' ' . $post_data['author_last_name']); ?></h3>
                            <span class="post-time"><?php echo date('M j, Y g:i A', strtotime($post_data['created_at'])); ?></span>
                        </div>
                    </div>
                    <div class="post-content">
                        <p><?php echo nl2br(htmlspecialchars($post_data['content'])); ?></p>
                        <?php if ($post_data['image_path']): ?>
                        <img src="<?php echo $post_data['image_path']; ?>" alt="Post image" class="post-image">
                        <?php endif; ?>
                    </div>
                    <div class="post-actions">
                        <button class="like-btn <?php echo isset($post_data['is_liked']) && $post_data['is_liked'] ? 'liked' : ''; ?>" 
                                data-post-id="<?php echo $post_data['id']; ?>">
                            <i class="fas fa-heart"></i>
                            <span class="like-count"><?php echo $post_data['likes_count']; ?></span>
                        </button>
                        <button class="comment-btn" data-post-id="<?php echo $post_data['id']; ?>">
                            <i class="fas fa-comment"></i>
                            <span class="comment-count"><?php echo $post_data['comments_count']; ?></span>
                        </button>
                    </div>
                    
                    <!-- Comments Section -->
                    <div class="comments-section" id="comments-<?php echo $post_data['id']; ?>" style="display: none;">
                        <div class="add-comment">
                            <form class="comment-form" data-post-id="<?php echo $post_data['id']; ?>">
                                <input type="text" placeholder="Write a comment..." required>
                                <button type="submit">Post</button>
                            </form>
                        </div>
                        <div class="comments-list" id="comments-list-<?php echo $post_data['id']; ?>">
                            <!-- Comments will be loaded here -->
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
</body>
</html>
