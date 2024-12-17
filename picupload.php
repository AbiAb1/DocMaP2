<?php
session_start();
require 'connection.php'; // Ensure this path is correct

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure the upload directory exists
$upload_dir = 'img/UserProfile/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true) or die(json_encode(['status' => 'error', 'message' => 'Could not create upload directory.']);
}

// Check if a file was uploaded and handle various upload errors
if (isset($_FILES['file'])) {
    $file_error = $_FILES['file']['error'];
    if ($file_error === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION)); //Lowercase for consistency

        //Basic file type validation. Add more as needed.
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_extensions)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.']);
            exit;
        }

        $unique_filename = uniqid() . '.' . $file_ext;
        $file_path = $upload_dir . $unique_filename;

        // Move the uploaded file to the desired directory
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Update the user's profile column in the database
            $stmt = $conn->prepare("UPDATE useracc SET profile = ? WHERE UserID = ?");
            $stmt->bind_param("si", $unique_filename, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Profile picture updated successfully', 'filename' => $unique_filename]);
            } else {
                //Clean up the uploaded file if database update fails.
                unlink($file_path);
                echo json_encode(['status' => 'error', 'message' => 'Database update failed: ' . $stmt->error]); //Include database error for debugging.
            }
            $stmt->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
        }
    } else {
        $error_messages = [
            UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION => 'File upload stopped by extension.',
        ];
        $message = isset($error_messages[$file_error]) ? $error_messages[$file_error] : 'Unknown upload error.';
        echo json_encode(['status' => 'error', 'message' => $message]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
}

$conn->close();
?>
