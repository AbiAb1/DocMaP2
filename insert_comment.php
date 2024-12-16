<?php
session_start();
require 'connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You need to log in to post a comment.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$content_id = isset($_POST['content_id']) ? intval($_POST['content_id']) : 0;
$task_id = isset($_POST['task_id']) ? intval($_POST['task_id']) : 0;
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

// Validate input
if (empty($content_id) || empty($task_id) || empty($message)) {
    echo json_encode(['error' => 'Content ID, Task ID, and message are required.']);
    exit();
}

// Insert comment into the database
$sql = "INSERT INTO comments (ContentID, TaskID, IncomingID, OutgoingID, Comment) VALUES (?, ?, ?, ?, ?)";
if ($stmt = $conn->prepare($sql)) {
    $incoming_id = $user_id; // The user sending the message
    $outgoing_id = isset($_POST['outgoing_id']) ? intval($_POST['outgoing_id']) : 0; // The recipient ID

    // Bind parameters with correct types
    $stmt->bind_param("iiiss", $content_id, $task_id, $incoming_id, $outgoing_id, $message);

    if ($stmt->execute()) {
        // Comment inserted successfully, do nothing here
    } else {
        echo json_encode(['error' => 'Error inserting comment: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Error preparing statement: ' . $conn->error]);
}

$conn->close();
?>
