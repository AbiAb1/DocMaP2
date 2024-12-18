<?php
session_start();
require 'connection.php'; // Ensure this path is correct relative to the mysql of your GitHub repository

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure the upload directory exists in the correct location relative to the repository
$upload_dir = $_SERVER['DOCUMENT_mysql'] . '/img/UserProfile/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Check if a file was uploaded
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

    // Validate file extension
    if (!in_array($file_ext, $allowed_ext)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
        exit;
    }

    // Generate a unique filename
    $unique_filename = uniqid() . '.' . $file_ext;
    $file_path = $upload_dir . $unique_filename;

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($file_tmp, $file_path)) {
        // Update the user's profile picture in the database
        $stmt = $conn->prepare("UPDATE useracc SET profile = ? WHERE UserID = ?");
        $stmt->bind_param("si", $unique_filename, $user_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully', 'filename' => $unique_filename]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
    }
} else {
    // Handle file upload errors
    $error_message = 'Unknown error';
    if (isset($_FILES['file']['error'])) {
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $error_message = 'File size exceeds the allowed limit.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $error_message = 'File was only partially uploaded.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $error_message = 'No file uploaded.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $error_message = 'Missing temporary folder.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $error_message = 'Failed to write file to disk.';
                break;
            case UPLOAD_ERR_EXTENSION:
                $error_message = 'A PHP extension stopped the file upload.';
                break;
        }
    }
    echo json_encode(['status' => 'error', 'message' => $error_message]);
}

$conn->close();
?>
