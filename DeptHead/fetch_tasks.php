<?php
include('connection.php');
session_start();

if (!isset($_SESSION['user_dept_id'])) {
    echo json_encode(['error' => 'User not logged in or department not assigned.']);
    exit;
}

$user_dept_id = $_SESSION['user_dept_id'];

$departmentsQuery = "SELECT dept_ID, dept_name FROM department WHERE dept_ID = ?";
$stmt_dept = $conn->prepare($departmentsQuery);
if (!$stmt_dept) {
    echo json_encode(['error' => 'Error preparing department query: ' . $conn->error]);
    exit;
}
$stmt_dept->bind_param('i', $user_dept_id);
$stmt_dept->execute();
$departmentsResult = $stmt_dept->get_result();
$stmt_dept->close(); // Close the statement

$departments = [];
if ($departmentsResult && $departmentsResult->num_rows > 0) {
    while ($dept = $departmentsResult->fetch_assoc()) {
        $deptID = $dept['dept_ID'];
        $deptName = $dept['dept_name'];

        $submittedQuery = "SELECT COUNT(UserID) AS totalSubmit 
                           FROM task_user 
                           INNER JOIN feedcontent ON task_user.ContentID = feedcontent.ContentID 
                           WHERE task_user.Status IN ('Submitted', 'Approved', 'Rejected') 
                           AND feedcontent.dept_ID = ?";
        $submittedStmt = $conn->prepare($submittedQuery);
        if (!$submittedStmt) {
            echo json_encode(['error' => 'Error preparing submitted query: ' . $conn->error]);
            exit;
        }
        $submittedStmt->bind_param('i', $deptID);
        $submittedStmt->execute();
        $submittedResult = $submittedStmt->get_result();
        $submittedStmt->close();

        $assignedQuery = "SELECT COUNT(UserID) AS totalAssigned 
                          FROM task_user 
                          INNER JOIN feedcontent ON task_user.ContentID = feedcontent.ContentID 
                          WHERE feedcontent.dept_ID = ?";
        $assignedStmt = $conn->prepare($assignedQuery);
        if (!$assignedStmt) {
            echo json_encode(['error' => 'Error preparing assigned query: ' . $conn->error]);
            exit;
        }
        $assignedStmt->bind_param('i', $deptID);
        $assignedStmt->execute();
        $assignedResult = $assignedStmt->get_result();
        $assignedStmt->close();

        $submittedRow = $submittedResult->fetch_assoc();
        $assignedRow = $assignedResult->fetch_assoc();

        $totalSubmit = $submittedRow['totalSubmit'] ?? 0;
        $totalAssigned = $assignedRow['totalAssigned'] ?? 0;

        $departments[] = [
            'dept_ID' => $deptID,
            'dept_name' => $deptName,
            'totalSubmit' => $totalSubmit,
            'totalAssigned' => $totalAssigned
        ];
    }
    echo json_encode(['departments' => $departments]);
} else {
    echo json_encode(['error' => 'No department found for the logged-in user.']);
}

?>