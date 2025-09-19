<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'classes/User.php';
require_once 'classes/Post.php';

$user = new User();
$post = new Post();

// Get current user info
$user->getUserById($_SESSION['user_id']);

// Get user's posts
$user_posts = $post->getPostsByUserId($_SESSION['user_id'], 20, 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network - Profile</title>
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
                    <a href="index.php" class="nav-link"><i class="fas fa-home"></i> Home</a>
                    <a href="profile.php" class="nav-link active"><i class="fas fa-user"></i> Profile</a>
                    <a href="#" id="logout-btn" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-cover">
                    <div class="profile-avatar">
                        <img src="<?php echo $user->profile_picture ? 'uploads/profiles/' . $user->profile_picture : 'https://via.placeholder.com/120'; ?>" 
                             alt="Profile Picture" id="profile-picture">
                        <button class="edit-avatar-btn" id="edit-avatar-btn">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                </div>
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user->first_name . ' ' . $user->last_name); ?></h2>
                    <p class="username">@<?php echo htmlspecialchars($user->username); ?></p>
                    <?php if ($user->bio): ?>
                    <p class="bio"><?php echo nl2br(htmlspecialchars($user->bio)); ?></p>
                    <?php endif; ?>
                    <p class="join-date">Joined <?php echo date('F Y', strtotime($user->created_at)); ?></p>
                </div>
            </div>

            <!-- Profile Actions -->
            <div class="profile-actions">
                <button class="btn btn-secondary" id="edit-profile-btn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>

            <!-- Edit Profile Modal -->
            <div class="modal" id="edit-profile-modal">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>Edit Profile</h3>
                        <button class="close-btn" id="close-edit-modal">&times;</button>
                    </div>
                    <form id="edit-profile-form" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="edit-first-name">First Name</label>
                            <input type="text" id="edit-first-name" name="first_name" value="<?php echo htmlspecialchars($user->first_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-last-name">Last Name</label>
                            <input type="text" id="edit-last-name" name="last_name" value="<?php echo htmlspecialchars($user->last_name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-bio">Bio</label>
                            <textarea id="edit-bio" name="bio" rows="3"><?php echo htmlspecialchars($user->bio); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-profile-picture">Profile Picture</label>
                            <input type="file" id="edit-profile-picture" name="profile_picture" accept="image/*">
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" id="cancel-edit">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- User's Posts -->
            <div class="profile-posts">
                <h3>Your Posts</h3>
                <div class="posts-feed" id="profile-posts-feed">
                    <?php foreach ($user_posts as $post_data): ?>
                    <div class="post" data-post-id="<?php echo $post_data['id']; ?>">
                        <div class="post-header">
                            <img src="<?php echo $post_data['author_profile_picture'] ? 'uploads/profiles/' . $post_data['author_profile_picture'] : 'https://via.placeholder.com/40'; ?>" 
                                 alt="Profile" class="profile-pic">
                            <div class="post-author">
                                <h3><?php echo htmlspecialchars($post_data['author_first_name'] . ' ' . $post_data['author_last_name']); ?></h3>
                                <span class="post-time"><?php echo date('M j, Y g:i A', strtotime($post_data['created_at'])); ?></span>
                            </div>
                            <button class="delete-post-btn" data-post-id="<?php echo $post_data['id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
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
            </div>
        </main>
    </div>

    <script src="js/main.js"></script>
    <script src="js/profile.js"></script>
</body>
</html>
