// Profile page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const editProfileBtn = document.getElementById('edit-profile-btn');
    const editProfileModal = document.getElementById('edit-profile-modal');
    const closeEditModal = document.getElementById('close-edit-modal');
    const cancelEdit = document.getElementById('cancel-edit');
    const editProfileForm = document.getElementById('edit-profile-form');
    const editAvatarBtn = document.getElementById('edit-avatar-btn');
    const profilePicture = document.getElementById('profile-picture');
    const deletePostBtns = document.querySelectorAll('.delete-post-btn');

    // Handle edit profile button
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            editProfileModal.classList.add('show');
        });
    }

    // Handle close modal buttons
    if (closeEditModal) {
        closeEditModal.addEventListener('click', function() {
            editProfileModal.classList.remove('show');
        });
    }

    if (cancelEdit) {
        cancelEdit.addEventListener('click', function() {
            editProfileModal.classList.remove('show');
        });
    }

    // Close modal when clicking outside
    if (editProfileModal) {
        editProfileModal.addEventListener('click', function(e) {
            if (e.target === editProfileModal) {
                editProfileModal.classList.remove('show');
            }
        });
    }

    // Handle edit profile form submission
    if (editProfileForm) {
        editProfileForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(editProfileForm);
            
            try {
                const response = await fetch('ajax/update_profile.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Profile updated successfully!');
                    location.reload();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            }
        });
    }

    // Handle profile picture upload
    if (editAvatarBtn) {
        editAvatarBtn.addEventListener('click', function() {
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.onchange = async function(e) {
                const file = e.target.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('profile_picture', file);
                    
                    try {
                        const response = await fetch('ajax/update_profile.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const result = await response.json();
                        
                        if (result.success) {
                            // Update profile picture immediately
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                profilePicture.src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            alert(result.message);
                        }
                    } catch (error) {
                        alert('An error occurred. Please try again.');
                        console.error('Error:', error);
                    }
                }
            };
            fileInput.click();
        });
    }

    // Handle delete post buttons
    deletePostBtns.forEach(btn => {
        btn.addEventListener('click', async function() {
            if (confirm('Are you sure you want to delete this post?')) {
                const postId = this.dataset.postId;
                
                try {
                    const formData = new FormData();
                    formData.append('post_id', postId);
                    
                    const response = await fetch('ajax/delete_post.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        // Remove post from DOM
                        const postElement = this.closest('.post');
                        postElement.remove();
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    alert('An error occurred. Please try again.');
                    console.error('Error:', error);
                }
            }
        });
    });
});
