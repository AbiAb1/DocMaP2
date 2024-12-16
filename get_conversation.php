<?php
session_start();
require 'connection.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You need to log in to view the conversation.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$content_id = isset($_GET['content_id']) ? intval($_GET['content_id']) : 0;
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

if (empty($content_id) || empty($task_id)) {
    echo json_encode(['error' => 'Content ID and Task ID are required.']);
    exit();
}

$messages = [];

// Get the conversation messages ordered by CommentID from the comments table
$sql_comments = "SELECT comments.Comment, comments.IncomingID, comments.OutgoingID, useracc.fname, useracc.lname, useracc.profile
                 FROM comments 
                 JOIN useracc ON comments.IncomingID = useracc.UserID 
                 WHERE comments.ContentID = ? AND comments.TaskID = ? 
                 ORDER BY comments.CommentID";

if ($stmt_comments = $conn->prepare($sql_comments)) {
    $stmt_comments->bind_param("ii", $content_id, $task_id);
    $stmt_comments->execute();
    $result = $stmt_comments->get_result();
    while ($row = $result->fetch_assoc()) {
        $row['profile'] = htmlspecialchars($row['profile']);
        $row['FullName'] = htmlspecialchars($row['fname'] . ' ' . $row['lname']);
        $row['Comment'] = htmlspecialchars($row['Comment']);
        $row['source'] = 'comments'; // Identifier for the source table
        $messages[] = $row;
    }
    $stmt_comments->close();
} else {
    echo json_encode(['error' => 'Error preparing statement for comments: ' . $conn->error]);
    $conn->close();
    exit();
}

// Get the comment from the task_user table
$sql_task_user = "SELECT Comment,Status,ApproveDate, RejectDate FROM task_user WHERE TaskID = ? AND ContentID = ? AND UserID = ?";
if ($stmt_task_user = $conn->prepare($sql_task_user)) {
    $stmt_task_user->bind_param("iii", $task_id, $content_id, $user_id);
    $stmt_task_user->execute();
    $result_task_user = $stmt_task_user->get_result();
    if ($row_task_user = $result_task_user->fetch_assoc()) {
        $task_user_comment = [
            'Comment' => htmlspecialchars($row_task_user['Comment']),
            'Status' => htmlspecialchars($row_task_user['Status']),
            'ApproveDate' => htmlspecialchars($row_task_user['ApproveDate']),
            'RejectDate' => htmlspecialchars($row_task_user['RejectDate']),
            'FullName' => 'Task Remarks', // Distinct identifier for task_user comments
            'source' => 'task_user' // Identifier for the source table
        ];
        $messages[] = $task_user_comment;
    }
    $stmt_task_user->close();
} else {
    echo json_encode(['error' => 'Error preparing statement for task_user: ' . $conn->error]);
    $conn->close();
    exit();
}

echo json_encode(['messages' => $messages]);

$conn->close();
?>
