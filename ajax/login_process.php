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
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validation
if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

// Attempt login
if ($user->login($email, $password)) {
    // Set session
    $_SESSION['user_id'] = $user->id;
    $_SESSION['username'] = $user->username;
    $_SESSION['email'] = $user->email;
    $_SESSION['first_name'] = $user->first_name;
    $_SESSION['last_name'] = $user->last_name;
    $_SESSION['profile_picture'] = $user->profile_picture;
    
    echo json_encode([
        'success' => true, 
        'message' => 'Login successful!',
        'redirect' => 'index.php'
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}
?>
