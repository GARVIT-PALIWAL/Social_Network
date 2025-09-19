<?php
session_start();
header('Content-Type: application/json');

require_once '../classes/User.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user = new User();

// Get form data
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$re_password = $_POST['re_password'] ?? '';
$date_of_birth = $_POST['date_of_birth'] ?? '';
$profile_picture = '';

// Split full name into first and last name
$name_parts = explode(' ', $full_name, 2);
$first_name = trim($name_parts[0] ?? '');
$last_name = trim($name_parts[1] ?? '');
$username = strtolower(str_replace(' ', '', $full_name)); // Generate username from full name

// Validation
$errors = [];

if (empty($full_name)) {
    $errors[] = 'Full name is required';
} elseif (strlen($full_name) < 2) {
    $errors[] = 'Full name must be at least 2 characters long';
}

if (empty($date_of_birth)) {
    $errors[] = 'Date of birth is required';
} else {
    $birth_date = new DateTime($date_of_birth);
    $today = new DateTime();
    $age = $today->diff($birth_date)->y;
    if ($age < 13) {
        $errors[] = 'You must be at least 13 years old to register';
    }
}

if (empty($email)) {
    $errors[] = 'Email is required';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email format';
}

if (empty($password)) {
    $errors[] = 'Password is required';
} elseif (strlen($password) < 6) {
    $errors[] = 'Password must be at least 6 characters long';
} elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', $password)) {
    $errors[] = 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character';
}

if ($password !== $re_password) {
    $errors[] = 'Passwords do not match';
}

// Handle profile picture upload
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
            $profile_picture = $filename;
        }
    } else {
        $errors[] = 'Invalid file type for profile picture. Only JPG, PNG, and GIF are allowed';
    }
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Check if email already exists
$user->email = $email;
if ($user->emailExists()) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit;
}

// Check if username already exists
$user->username = $username;
if ($user->usernameExists()) {
    echo json_encode(['success' => false, 'message' => 'Username already exists']);
    exit;
}

// Create user
$user->email = $email;
$user->username = $username;
$user->password = $password;
$user->first_name = $first_name;
$user->last_name = $last_name;
$user->bio = $bio;
$user->profile_picture = $profile_picture;

if ($user->create()) {
    // Set session
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['email'] = $user->email;
    $_SESSION['first_name'] = $user->first_name;
    $_SESSION['last_name'] = $user->last_name;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Account created successfully!',
        'redirect' => 'index.php'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to create account. Please try again.']);
}
?>
