<?php
session_start();
require 'connection.php'; // Ensure this path is correct

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Ensure the upload directory exists
$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/img/UserProfile/';

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
            // Git auto-commit process
            $repo_dir = 'https://github.com/AbiAb1/DocMaP.git'; // Change this to the correct path to your git repository
            $commit_message = "Auto-commit: Uploaded profile picture for user $user_id";

            // Change directory to the repository
            chdir($repo_dir);

            // Stage the file for commit
            exec("git add img/UserProfile/$unique_filename", $output, $return_var);
            if ($return_var !== 0) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to stage file for Git commit']);
                exit;
            }

            // Commit the file
            exec("git commit -m \"$commit_message\"", $output, $return_var);
            if ($return_var !== 0) {
                echo json_encode(['status' => 'error', 'message' => 'Git commit failed']);
                exit;
            }

            // Push the changes to GitHub
            exec("git push origin main", $output, $return_var); // Replace 'main' with your branch name if different
            if ($return_var !== 0) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to push changes to GitHub']);
                exit;
            }

            echo json_encode(['status' => 'success', 'message' => 'Profile updated successfully and changes committed to GitHub', 'filename' => $unique_filename]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No valid file uploaded']);
}

$conn->close();
?>
