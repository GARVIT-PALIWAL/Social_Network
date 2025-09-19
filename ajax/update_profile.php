<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/User.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user = new User();
$user->getUserById($_SESSION['user_id']);

// Get form data
$first_name = trim($_POST['first_name'] ?? $user->first_name);
$last_name = trim($_POST['last_name'] ?? $user->last_name);
$bio = trim($_POST['bio'] ?? $user->bio);

// Validation
if (empty($first_name)) {
    echo json_encode(['success' => false, 'message' => 'First name is required']);
    exit;
}

if (empty($last_name)) {
    echo json_encode(['success' => false, 'message' => 'Last name is required']);
    exit;
}

// Handle profile picture upload
$profile_picture = $user->profile_picture;
if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = '../uploads/profiles/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    
    if (in_array($file_extension, $allowed_extensions)) {
        $filename = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $upload_path)) {
            // Delete old profile picture if it exists
            if ($user->profile_picture && file_exists('../uploads/profiles/' . $user->profile_picture)) {
                unlink('../uploads/profiles/' . $user->profile_picture);
            }
            $profile_picture = $filename;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only JPG, PNG, and GIF are allowed']);
        exit;
    }
}

// Update user profile
$user->first_name = $first_name;
$user->last_name = $last_name;
$user->bio = $bio;
$user->profile_picture = $profile_picture;

if ($user->updateProfile()) {
    // Update session data
    $_SESSION['first_name'] = $user->first_name;
    $_SESSION['last_name'] = $user->last_name;
    $_SESSION['profile_picture'] = $user->profile_picture;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Profile updated successfully!'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update profile. Please try again.']);
}
?>
