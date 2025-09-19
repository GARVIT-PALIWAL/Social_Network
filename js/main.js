// Main JavaScript for social network functionality
document.addEventListener('DOMContentLoaded', function() {
    const createPostForm = document.getElementById('create-post-form');
    const postsFeed = document.getElementById('posts-feed');
    const logoutBtn = document.getElementById('logout-btn');

    // Handle logout
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('ajax/logout_process.php', {
                    method: 'POST'
                });
                
                const result = await response.json();
                
                if (result.success) {
                    window.location.href = result.redirect;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    // Handle create post form submission
    if (createPostForm) {
        createPostForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(createPostForm);
            const content = document.getElementById('post-content').value.trim();
            
            if (!content) {
                alert('Please enter some content for your post.');
                return;
            }
            
            try {
                const response = await fetch('ajax/create_post.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Clear form
                    createPostForm.reset();
                    document.getElementById('post-content').value = '';
                    
                    // Reload posts
                    loadPosts();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            }
        });
    }

    // Handle like buttons
    document.addEventListener('click', async function(e) {
        if (e.target.closest('.like-btn')) {
            e.preventDefault();
            const likeBtn = e.target.closest('.like-btn');
            const postId = likeBtn.dataset.postId;
            
            try {
                const formData = new FormData();
                formData.append('post_id', postId);
                
                const response = await fetch('ajax/like_post.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    const likeCount = likeBtn.querySelector('.like-count');
                    const currentCount = parseInt(likeCount.textContent);
                    
                    if (result.action === 'liked') {
                        likeBtn.classList.add('liked');
                        likeCount.textContent = currentCount + 1;
                    } else {
                        likeBtn.classList.remove('liked');
                        likeCount.textContent = currentCount - 1;
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    });

    // Handle comment buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.comment-btn')) {
            const commentBtn = e.target.closest('.comment-btn');
            const postId = commentBtn.dataset.postId;
            const commentsSection = document.getElementById(`comments-${postId}`);
            
            if (commentsSection.style.display === 'none') {
                commentsSection.style.display = 'block';
                loadComments(postId);
            } else {
                commentsSection.style.display = 'none';
            }
        }
    });

    // Handle comment form submission
    document.addEventListener('submit', async function(e) {
        if (e.target.classList.contains('comment-form')) {
            e.preventDefault();
            
            const form = e.target;
            const postId = form.dataset.postId;
            const content = form.querySelector('input[type="text"]').value.trim();
            
            if (!content) {
                alert('Please enter a comment.');
                return;
            }
            
            try {
                const formData = new FormData();
                formData.append('post_id', postId);
                formData.append('content', content);
                
                const response = await fetch('ajax/add_comment.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    form.querySelector('input[type="text"]').value = '';
                    loadComments(postId);
                    
                    // Update comment count
                    const commentBtn = document.querySelector(`[data-post-id="${postId}"].comment-btn`);
                    const commentCount = commentBtn.querySelector('.comment-count');
                    commentCount.textContent = parseInt(commentCount.textContent) + 1;
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            }
        }
    });

    // Load comments for a post
    async function loadComments(postId) {
        try {
            const response = await fetch(`ajax/get_comments.php?post_id=${postId}`);
            const result = await response.json();
            
            if (result.success) {
                const commentsList = document.getElementById(`comments-list-${postId}`);
                commentsList.innerHTML = '';
                
                result.comments.forEach(comment => {
                    const commentDiv = document.createElement('div');
                    commentDiv.className = 'comment';
                    commentDiv.innerHTML = `
                        <img src="${comment.profile_picture ? 'uploads/profiles/' + comment.profile_picture : 'https://via.placeholder.com/32'}" 
                             alt="Profile" class="profile-pic">
                        <div class="comment-content">
                            <div class="comment-author">${comment.first_name} ${comment.last_name}</div>
                            <div class="comment-text">${comment.content}</div>
                        </div>
                    `;
                    commentsList.appendChild(commentDiv);
                });
            }
        } catch (error) {
            console.error('Error loading comments:', error);
        }
    }

    // Load posts
    async function loadPosts() {
        try {
            const response = await fetch('ajax/get_posts.php');
            const result = await response.json();
            
            if (result.success) {
                postsFeed.innerHTML = '';
                
                result.posts.forEach(post => {
                    const postDiv = document.createElement('div');
                    postDiv.className = 'post';
                    postDiv.dataset.postId = post.id;
                    postDiv.innerHTML = `
                        <div class="post-header">
                            <img src="${post.author_profile_picture ? 'uploads/profiles/' + post.author_profile_picture : 'https://via.placeholder.com/40'}" 
                                 alt="Profile" class="profile-pic">
                            <div class="post-author">
                                <h3>${post.author_first_name} ${post.author_last_name}</h3>
                                <span class="post-time">${new Date(post.created_at).toLocaleDateString('en-US', { 
                                    month: 'short', 
                                    day: 'numeric', 
                                    year: 'numeric',
                                    hour: 'numeric',
                                    minute: '2-digit'
                                })}</span>
                            </div>
                        </div>
                        <div class="post-content">
                            <p>${post.content.replace(/\n/g, '<br>')}</p>
                            ${post.image_path ? `<img src="${post.image_path}" alt="Post image" class="post-image">` : ''}
                        </div>
                        <div class="post-actions">
                            <button class="like-btn ${post.is_liked ? 'liked' : ''}" data-post-id="${post.id}">
                                <i class="fas fa-heart"></i>
                                <span class="like-count">${post.likes_count}</span>
                            </button>
                            <button class="comment-btn" data-post-id="${post.id}">
                                <i class="fas fa-comment"></i>
                                <span class="comment-count">${post.comments_count}</span>
                            </button>
                        </div>
                        <div class="comments-section" id="comments-${post.id}" style="display: none;">
                            <div class="add-comment">
                                <form class="comment-form" data-post-id="${post.id}">
                                    <input type="text" placeholder="Write a comment..." required>
                                    <button type="submit">Post</button>
                                </form>
                            </div>
                            <div class="comments-list" id="comments-list-${post.id}">
                                <!-- Comments will be loaded here -->
                            </div>
                        </div>
                    `;
                    postsFeed.appendChild(postDiv);
                });
            }
        } catch (error) {
            console.error('Error loading posts:', error);
        }
    }
});
