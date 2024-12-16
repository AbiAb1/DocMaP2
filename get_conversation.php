<?php
session_start();
require 'connection.php';

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

// Fetch comments
$sql_comments = "SELECT comments.Comment, comments.IncomingID, comments.OutgoingID, useracc.fname, useracc.lname, useracc.profile FROM comments JOIN useracc ON comments.IncomingID = useracc.UserID WHERE comments.ContentID = ? AND comments.TaskID = ? ORDER BY comments.CommentID";
$stmt_comments = $conn->prepare($sql_comments);
$stmt_comments->bind_param("ii", $content_id, $task_id);
if ($stmt_comments->execute()) {
    $result = $stmt_comments->get_result();
    while ($row = $result->fetch_assoc()) {
        $messages[] = [
            'Comment' => htmlspecialchars($row['Comment'] ?? ''), // Handle null values
            'IncomingID' => intval($row['IncomingID']),
            'OutgoingID' => intval($row['OutgoingID']),
            'FullName' => htmlspecialchars($row['fname'] . ' ' . $row['lname']),
            'profile' => htmlspecialchars($row['profile'] ?? ''), // Handle null values
            'source' => 'comments'
        ];
    }
} else {
    echo json_encode(['error' => 'Error preparing statement for comments: ' . $conn->error]);
    exit();
}

// Fetch task_user comments
$sql_task_user = "SELECT Comment, Status, ApproveDate, RejectDate FROM task_user WHERE TaskID = ? AND ContentID = ? AND UserID = ?";
$stmt_task_user = $conn->prepare($sql_task_user);
$stmt_task_user->bind_param("iii", $task_id, $content_id, $user_id);
if ($stmt_task_user->execute()) {
    $result_task_user = $stmt_task_user->get_result();
    if ($row_task_user = $result_task_user->fetch_assoc()) {
        $task_user_comment = [
            'Comment' => htmlspecialchars($row_task_user['Comment'] ?? ''), // Handle null values
            'Status' => htmlspecialchars($row_task_user['Status']),
            'ApproveDate' => htmlspecialchars($row_task_user['ApproveDate'] ?? ''), // Handle null values
            'RejectDate' => htmlspecialchars($row_task_user['RejectDate'] ?? ''), // Handle null values
            'FullName' => 'Task Remarks',
            'source' => 'task_user'
        ];
        $messages[] = $task_user_comment;
    }
} else {
    echo json_encode(['error' => 'Error preparing statement for task_user: ' . $conn->error]);
    exit();
}

// Set the Content-Type header before any output
header('Content-Type: application/json'); 

$json_response = json_encode($messages);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['error' => 'Error encoding JSON: ' . json_last_error_msg()]);
    exit();
}
echo $json_response;

$conn->close();
?>
