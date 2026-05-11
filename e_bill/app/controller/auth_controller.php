<?php
// ================================================
// Authentication Controller
// app/controller/auth_controller.php
// ================================================

require_once __DIR__ . '/../config/config.php';

// -----------------------------------------------
// Login — checks admin table first, then user
// -----------------------------------------------
function loginUser($username, $password) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['admin_id']       = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['admin_name']     = $admin['firstName'] . ' ' . $admin['lastname'];
            $_SESSION['role']           = 'admin';
            return ['success' => true, 'role' => 'admin'];
        } else {
            return ['success' => false, 'error' => 'Incorrect password. Please try again.'];
        }
    }

    $stmt2 = $conn->prepare("SELECT * FROM user WHERE username = ?");
    $stmt2->bind_param("s", $username);
    $stmt2->execute();
    $result2 = $stmt2->get_result();

    if ($result2->num_rows === 1) {
        $user = $result2->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            if ($user['status'] === 'inactive') {
                return ['success' => false, 'error' => 'Your account is inactive. Please contact the administrator.'];
            }
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user_id']              = $user['id'];
            $_SESSION['user_username']        = $user['username'];
            $_SESSION['user_name']            = $user['firstName'] . ' ' . $user['lastname'];
            $_SESSION['user_email']           = $user['emailAddress'];
            $_SESSION['user_profile_picture'] = $user['profile_picture'] ?? null;
            $_SESSION['role']                 = 'user';
            return ['success' => true, 'role' => 'user'];
        } else {
            return ['success' => false, 'error' => 'Incorrect password. Please try again.'];
        }
    }

    return ['success' => false, 'error' => 'Username not found. Please try again.'];
}

// -----------------------------------------------
// Register new user (with optional profile picture)
// -----------------------------------------------
function registerUser($data, $file = null) {
    global $conn;

    // Duplicate checks
    $check = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $check->bind_param("s", $data['username']);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        return ['success' => false, 'error' => 'Username already taken. Please choose another.'];
    }

    $check2 = $conn->prepare("SELECT id FROM user WHERE emailAddress = ?");
    $check2->bind_param("s", $data['email']);
    $check2->execute();
    $check2->store_result();
    if ($check2->num_rows > 0) {
        return ['success' => false, 'error' => 'Email address already registered.'];
    }

    $check3 = $conn->prepare("SELECT id FROM user WHERE meter_number = ?");
    $check3->bind_param("s", $data['meter_number']);
    $check3->execute();
    $check3->store_result();
    if ($check3->num_rows > 0) {
        return ['success' => false, 'error' => 'Meter number already registered.'];
    }

    $uuid     = generateUUID();
    $password = password_hash($data['password'], PASSWORD_DEFAULT);

    // Handle optional profile picture — saves to app/uploads/profile_pictures/
    $profile_picture = null;
    if ($file && $file['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        $finfo         = finfo_open(FILEINFO_MIME_TYPE);
        $mime          = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowed_types)) {
            return ['success' => false, 'error' => 'Profile picture must be JPG, PNG, GIF, or WEBP.'];
        }
        if ($file['size'] > 2 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Profile picture must be smaller than 2MB.'];
        }

        // Use UPLOADS_PATH from config.php → app/uploads/profile_pictures/
        $upload_dir = UPLOADS_PATH . 'profile_pictures/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $ext             = pathinfo($file['name'], PATHINFO_EXTENSION);
        $profile_picture = 'user_' . $uuid . '_' . time() . '.' . strtolower($ext);
        $dest            = $upload_dir . $profile_picture;

        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            return ['success' => false, 'error' => 'Failed to upload profile picture.'];
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO user
        (uuid, meter_number, profile_picture, firstName, middleName, lastname,
         emailAddress, contactNumber, dateOfBirth, username, password,
         street, barangay, city)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssssssssss",
        $uuid,
        $data['meter_number'],
        $profile_picture,
        $data['firstName'],
        $data['middleName'],
        $data['lastname'],
        $data['email'],
        $data['contact'],
        $data['dateOfBirth'],
        $data['username'],
        $password,
        $data['street'],
        $data['barangay'],
        $data['city']
    );

    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

// -----------------------------------------------
// Logout
// -----------------------------------------------
function logoutUser() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    session_unset();
    session_destroy();
    header("Location: " . BASE_URL . "index.php");
    exit();
}

// -----------------------------------------------
// Generate UUID
// -----------------------------------------------
function generateUUID() {
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
?>