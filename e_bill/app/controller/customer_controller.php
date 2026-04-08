<?php
// ================================================
// Customer Controller
// app/controller/customer_controller.php
// ================================================

require_once __DIR__ . '/../config/config.php';

// Get all users
function getAllUsers() {
    global $conn;
    return $conn->query("
        SELECT u.*,
               COUNT(b.id) AS total_bills,
               SUM(CASE WHEN b.status = 'unpaid' THEN b.amount_due ELSE 0 END) AS total_unpaid
        FROM user u
        LEFT JOIN bill b ON u.id = b.user_id
        GROUP BY u.id
        ORDER BY u.dateCreated DESC
    ");
}

// Get single user by ID
function getUserById($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// Update user status (active/inactive)
function updateUserStatus($id, $status) {
    global $conn;
    $stmt = $conn->prepare("UPDATE user SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}

// Update user profile
function updateUserProfile($id, $data) {
    global $conn;
    $stmt = $conn->prepare("
        UPDATE user SET
        firstName = ?, middleName = ?, lastname = ?,
        emailAddress = ?, contactNumber = ?, dateOfBirth = ?,
        street = ?, barangay = ?, city = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "sssssssssi",
        $data['firstName'],
        $data['middleName'],
        $data['lastname'],
        $data['email'],
        $data['contact'],
        $data['dateOfBirth'],
        $data['street'],
        $data['barangay'],
        $data['city'],
        $id
    );
    if ($stmt->execute()) {
        return ['success' => true];
    } else {
        return ['success' => false, 'error' => 'Failed to update profile.'];
    }
}

// -----------------------------------------------
// Upload / Update Profile Picture
// -----------------------------------------------
function updateProfilePicture($user_id, $file) {
    global $conn;

    // Validate file was actually uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'No file uploaded or upload error.'];
    }

    // Allowed MIME types
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $finfo         = finfo_open(FILEINFO_MIME_TYPE);
    $mime          = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_types)) {
        return ['success' => false, 'error' => 'Only JPG, PNG, GIF, or WEBP images are allowed.'];
    }

    // Max size: 2MB
    if ($file['size'] > 2 * 1024 * 1024) {
        return ['success' => false, 'error' => 'Image must be smaller than 2MB.'];
    }

    // Create upload directory if it doesn't exist
    $upload_dir = __DIR__ . '/../../uploads/profile_pictures/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Delete old profile picture if it exists
    $old = getUserById($user_id);
    if (!empty($old['profile_picture'])) {
        $old_path = $upload_dir . $old['profile_picture'];
        if (file_exists($old_path)) {
            unlink($old_path);
        }
    }

    // Generate unique filename
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . strtolower($ext);
    $dest     = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => false, 'error' => 'Failed to save image. Please try again.'];
    }

    // Save filename to DB
    $stmt = $conn->prepare("UPDATE user SET profile_picture = ? WHERE id = ?");
    $stmt->bind_param("si", $filename, $user_id);
    if ($stmt->execute()) {
        return ['success' => true, 'filename' => $filename];
    } else {
        return ['success' => false, 'error' => 'Failed to update profile picture in database.'];
    }
}

// -----------------------------------------------
// Get profile picture URL (with generated fallback)
// -----------------------------------------------
function getProfilePictureUrl($user) {
    if (!empty($user['profile_picture'])) {
        return BASE_URL . '../uploads/profile_pictures/' . htmlspecialchars($user['profile_picture']);
    }
    // Fallback: auto-generated letter avatar
    $name = urlencode(($user['firstName'] ?? 'U') . ' ' . ($user['lastname'] ?? ''));
    return 'https://ui-avatars.com/api/?name=' . $name . '&background=1a6fa3&color=fff&size=128&bold=true';
}

// Delete user (also cleans up profile picture)
function deleteUser($id) {
    global $conn;
    $user = getUserById($id);
    if (!empty($user['profile_picture'])) {
        $path = __DIR__ . '/../../uploads/profile_pictures/' . $user['profile_picture'];
        if (file_exists($path)) unlink($path);
    }
    $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// Get users dropdown
function getUsersDropdown() {
    global $conn;
    return $conn->query("
        SELECT id, firstName, lastname, meter_number
        FROM user
        ORDER BY firstName ASC
    ");
}
?>