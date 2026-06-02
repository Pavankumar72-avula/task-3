<?php
session_start();

// ========== DATABASE CONNECTION ==========
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'task3_db');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to utf8
mysqli_set_charset($conn, "utf8");

// ========== HELPER FUNCTIONS ==========

/**
 * Hash password using password_hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Sanitize input to prevent SQL injection
 */
function sanitize($input) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($input));
}

/**
 * Validate email format
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Check if email already exists
 */
function emailExists($email) {
    global $conn;
    $email = sanitize($email);
    $query = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

/**
 * Check if username already exists
 */
function usernameExists($username) {
    global $conn;
    $username = sanitize($username);
    $query = "SELECT id FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $query);
    return mysqli_num_rows($result) > 0;
}

// ========== AUTHENTICATION SYSTEM ==========

/**
 * Register new user
 */
function registerUser($firstname, $lastname, $username, $email, $password) {
    global $conn;
    
    // Validate inputs
    if (empty($firstname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if (strlen($password) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters'];
    }
    
    if (!validateEmail($email)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    if (emailExists($email)) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    if (usernameExists($username)) {
        return ['success' => false, 'message' => 'Username already taken'];
    }
    
    // Sanitize inputs
    $firstname = sanitize($firstname);
    $lastname = sanitize($lastname);
    $username = sanitize($username);
    $email = sanitize($email);
    $hashedPassword = hashPassword($password);
    $created_at = date('Y-m-d H:i:s');
    
    // Insert user
    $query = "INSERT INTO users (firstname, lastname, username, email, password, created_at) 
              VALUES ('$firstname', '$lastname', '$username', '$email', '$hashedPassword', '$created_at')";
    
    if (mysqli_query($conn, $query)) {
        return ['success' => true, 'message' => 'Account created successfully! You can now login.'];
    } else {
        return ['success' => false, 'message' => 'Error creating account: ' . mysqli_error($conn)];
    }
}

/**
 * Login user
 */
function loginUser($email, $password) {
    global $conn;
    
    // Validate inputs
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email and password are required'];
    }
    
    // Sanitize input
    $email = sanitize($email);
    
    // Fetch user
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 0) {
        return ['success' => false, 'message' => 'Email or password is incorrect'];
    }
    
    $user = mysqli_fetch_assoc($result);
    
    // Verify password
    if (!verifyPassword($password, $user['password'])) {
        return ['success' => false, 'message' => 'Email or password is incorrect'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['firstname'] . ' ' . $user['lastname'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];
    
    // Update last login
    $now = date('Y-m-d H:i:s');
    mysqli_query($conn, "UPDATE users SET last_login = '$now' WHERE id = {$user['id']}");
    
    return ['success' => true, 'message' => 'Login successful!', 'redirect' => 'dashboard.php'];
}

/**
 * Logout user
 */
function logoutUser() {
    session_destroy();
    header('Location: login.php');
    exit();
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// ========== CRUD OPERATIONS ==========

/**
 * Get all users
 */
function getAllUsers() {
    global $conn;
    $query = "SELECT id, firstname, lastname, username, email, role, status, created_at FROM users ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Get user by ID
 */
function getUserById($id) {
    global $conn;
    $id = sanitize($id);
    $query = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_assoc($result);
}

/**
 * Update user profile
 */
function updateUserProfile($id, $firstname, $lastname, $email) {
    global $conn;
    
    if (empty($firstname) || empty($lastname) || empty($email)) {
        return ['success' => false, 'message' => 'All fields are required'];
    }
    
    if (!validateEmail($email)) {
        return ['success' => false, 'message' => 'Invalid email format'];
    }
    
    // Check if email is already used by another user
    $currentUser = getUserById($id);
    if ($email !== $currentUser['email'] && emailExists($email)) {
        return ['success' => false, 'message' => 'Email already in use'];
    }
    
    $firstname = sanitize($firstname);
    $lastname = sanitize($lastname);
    $email = sanitize($email);
    $id = sanitize($id);
    $updated_at = date('Y-m-d H:i:s');
    
    $query = "UPDATE users SET firstname = '$firstname', lastname = '$lastname', email = '$email', updated_at = '$updated_at' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        // Update session if updating own profile
        if ($_SESSION['user_id'] == $id) {
            $_SESSION['user_email'] = $email;
            $_SESSION['user_name'] = $firstname . ' ' . $lastname;
        }
        return ['success' => true, 'message' => 'Profile updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Error updating profile'];
    }
}

/**
 * Update user password
 */
function updatePassword($id, $oldPassword, $newPassword, $confirmPassword) {
    global $conn;
    
    if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
        return ['success' => false, 'message' => 'All password fields are required'];
    }
    
    if ($newPassword !== $confirmPassword) {
        return ['success' => false, 'message' => 'New passwords do not match'];
    }
    
    if (strlen($newPassword) < 8) {
        return ['success' => false, 'message' => 'Password must be at least 8 characters'];
    }
    
    $user = getUserById($id);
    
    if (!verifyPassword($oldPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Current password is incorrect'];
    }
    
    $hashedPassword = hashPassword($newPassword);
    $id = sanitize($id);
    $updated_at = date('Y-m-d H:i:s');
    
    $query = "UPDATE users SET password = '$hashedPassword', updated_at = '$updated_at' WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        return ['success' => true, 'message' => 'Password updated successfully'];
    } else {
        return ['success' => false, 'message' => 'Error updating password'];
    }
}

/**
 * Upload profile picture
 */
function uploadProfilePicture($id, $file) {
    // Validate file
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, and GIF are allowed'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'File size exceeds 5MB limit'];
    }
    
    // Create uploads directory if not exists
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'profile_' . $id . '_' . time() . '.' . $ext;
    $filepath = 'uploads/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // Update database
        global $conn;
        $filepath = sanitize($filepath);
        $id = sanitize($id);
        
        $query = "UPDATE users SET profile_picture = '$filepath' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            return ['success' => true, 'message' => 'Profile picture uploaded successfully', 'filepath' => $filepath];
        } else {
            unlink($filepath); // Delete file if DB update fails
            return ['success' => false, 'message' => 'Error saving file information'];
        }
    } else {
        return ['success' => false, 'message' => 'Error uploading file'];
    }
}

/**
 * Delete user
 */
function deleteUser($id) {
    global $conn;
    
    $id = sanitize($id);
    $query = "DELETE FROM users WHERE id = '$id'";
    
    if (mysqli_query($conn, $query)) {
        return ['success' => true, 'message' => 'User deleted successfully'];
    } else {
        return ['success' => false, 'message' => 'Error deleting user'];
    }
}

/**
 * Get user count
 */
function getUserCount() {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM users";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

/**
 * Get today's registrations
 */
function getTodayRegistrations() {
    global $conn;
    $today = date('Y-m-d');
    $query = "SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = '$today'";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    return $row['count'];
}

// ========== HANDLE FORM SUBMISSIONS ==========

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'register') {
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $result = registerUser($firstname, $lastname, $username, $email, $password);
        echo json_encode($result);
        exit();
    }
    
    elseif ($action === 'login') {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        $result = loginUser($email, $password);
        echo json_encode($result);
        exit();
    }
    
    elseif ($action === 'update_profile') {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }
        
        $firstname = $_POST['firstname'] ?? '';
        $lastname = $_POST['lastname'] ?? '';
        $email = $_POST['email'] ?? '';
        
        $result = updateUserProfile($_SESSION['user_id'], $firstname, $lastname, $email);
        echo json_encode($result);
        exit();
    }
    
    elseif ($action === 'update_password') {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }
        
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        $result = updatePassword($_SESSION['user_id'], $oldPassword, $newPassword, $confirmPassword);
        echo json_encode($result);
        exit();
    }
    
    elseif ($action === 'upload_picture') {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }
        
        if (!isset($_FILES['profile_picture'])) {
            echo json_encode(['success' => false, 'message' => 'No file uploaded']);
            exit();
        }
        
        $result = uploadProfilePicture($_SESSION['user_id'], $_FILES['profile_picture']);
        echo json_encode($result);
        exit();
    }
}

// ========== HANDLE AJAX REQUESTS ==========

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $request = $_GET['request'] ?? '';
    
    if ($request === 'check_email') {
        $email = $_GET['email'] ?? '';
        if (!empty($email)) {
            $exists = emailExists($email);
            echo json_encode(['exists' => $exists]);
        }
        exit();
    }
    
    elseif ($request === 'check_username') {
        $username = $_GET['username'] ?? '';
        if (!empty($username)) {
            $exists = usernameExists($username);
            echo json_encode(['exists' => $exists]);
        }
        exit();
    }
    
    elseif ($request === 'get_user') {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Not logged in']);
            exit();
        }
        
        $user = getUserById($_SESSION['user_id']);
        echo json_encode(['success' => true, 'user' => $user]);
        exit();
    }
    
    elseif ($request === 'get_all_users') {
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit();
        }
        
        $users = getAllUsers();
        echo json_encode(['success' => true, 'users' => $users, 'count' => count($users)]);
        exit();
    }
    
    elseif ($request === 'get_stats') {
        if (!isAdmin()) {
            echo json_encode(['success' => false, 'message' => 'Access denied']);
            exit();
        }
        
        $totalUsers = getUserCount();
        $todayRegistrations = getTodayRegistrations();
        
        echo json_encode([
            'success' => true,
            'total_users' => $totalUsers,
            'today_registrations' => $todayRegistrations
        ]);
        exit();
    }
}

// ========== LOGOUT HANDLING ==========

if (isset($_GET['logout'])) {
    logoutUser();
}

?>
