<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Network - Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="auth-body">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1><i class="fas fa-users"></i> Social Network</h1>
                <p>Join our community! Create your account to get started.</p>
            </div>

            <form id="signup-form" class="auth-form" enctype="multipart/form-data">
                <!-- Profile Picture Section -->
                <div class="profile-picture-section">
                    <div class="profile-picture-container">
                        <div class="profile-picture-placeholder" id="profile-picture-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                        <img id="profile-picture-preview" src="" alt="Profile Preview" style="display: none;">
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
                        <button type="button" id="remove-profile-btn" class="remove-profile-btn" style="display: none;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <button type="button" id="upload-profile-btn" class="upload-profile-btn">
                        Upload Profile Pic
                    </button>
                </div>

                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <div class="date-input-container">
                        <input type="date" id="date_of_birth" name="date_of_birth" required>
                        <i class="fas fa-calendar-alt date-icon"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter password" required>
                    </div>
                    <div class="form-group">
                        <label for="re_password">Re-Password</label>
                        <input type="password" id="re_password" name="re_password" placeholder="Confirm password" required>
                    </div>
                </div>

                <div class="password-hint">
                    <small>Use A-Z, a-z, 0-9, !@#$%^&* in password</small>
                </div>

                <button type="submit" class="btn btn-primary btn-full">Create Account</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>

            <div id="message" class="message"></div>
        </div>
    </div>

    <script src="js/auth.js"></script>
</body>
</html>
