<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not authenticated
    header("Location: ../index.php");
    exit();
}

include 'connection.php'; // Include your database connection

// Function to fetch pending tasks along with user names
function getPendingTasks($conn) {
    $query = "
        SELECT tasks.TaskID, tasks.Title, tasks.Type, tasks.taskContent, tasks.DueDate, tasks.DueTime, tasks.Status, useracc.fname, useracc.lname
        FROM tasks
        JOIN useracc ON tasks.UserID = useracc.UserID
        WHERE tasks.ApprovalStatus = 'Pending'
        ORDER BY 
            CASE 
                WHEN tasks.Status = 'Schedule' THEN 1
                ELSE 2
            END,
            tasks.TaskID ASC
    ";
    $result = mysqli_query($conn, $query);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Function to log messages to logfile.log
function write_log($message) {
    $logfile = 'logfile.log'; // Path to your log file
    $currentDate = date('Y-m-d H:i:s');
    $logMessage = "[{$currentDate}] - {$message}\n";
    file_put_contents($logfile, $logMessage, FILE_APPEND); // Append to logfile
}

// Function to send bulk SMS
function send_bulk_sms($conn, $ContentID, $notificationTitle, $Title, $DueDate, $DueTime) {
    $mobileQuery = $conn->prepare("
        SELECT ua.mobile, UPPER(CONCAT(ua.fname, ' ', ua.lname)) AS FullName 
        FROM usercontent uc
        JOIN useracc ua ON uc.UserID = ua.UserID
        WHERE uc.ContentID = ?
    ");
    $mobileQuery->bind_param("i", $ContentID);
    $mobileQuery->execute();
    $mobileResult = $mobileQuery->get_result();

    if ($mobileResult->num_rows > 0) {
        $mobileNumbers = [];
        $messages = [];

        while ($row = $mobileResult->fetch_assoc()) {
            $mobileNumbers[] = $row['mobile']; // Add mobile number to the array
            // Pass the DueDate and DueTime to the SMS message
            $messages[] = "NEW TASK ALERT!\n\nHi " . $row['FullName'] . "! " . $notificationTitle . " \"" . $Title . "\" Due on " . $DueDate . " at " . $DueTime . ". Don't miss it! Have a nice day!";
        }

        // Create comma-separated list of mobile numbers
        $mobileNumbersList = implode(",", $mobileNumbers);

        // Log the message and mobile numbers
        write_log("Mobile numbers for ContentID $ContentID: $mobileNumbersList");
        write_log("Messages to be sent: " . implode(" | ", $messages));

        // Send SMS using Semaphore API (example)
        $api_url = "https://api.semaphore.co/api/v4/messages"; // Semaphore API URL
        $api_key = "d796c0e11273934ac9d789536133684a"; // Your Semaphore API key

        foreach ($messages as $index => $message) {
            $number = $mobileNumbers[$index]; // Get the corresponding mobile number

            // Prepare POST data
            $postData = [
                'apikey' => $api_key,
                'number' => $number, // Individual number
                'message' => $message
            ];

            // Initialize cURL session
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // Execute cURL request
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                write_log("Error sending SMS to number ($number): " . curl_error($ch));
            } else {
                write_log("SMS sent successfully to number: $number");
            }
            curl_close($ch);
        }
    } else {
        write_log("No mobile numbers found for ContentID $ContentID");
    }
}
// Function to approve or reject tasks
function updateTaskStatus($conn, $taskIDs, $status) {
    $ids = implode(',', array_map('intval', $taskIDs));
    
    // Begin Transaction
    mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);

    $query = "UPDATE tasks SET ApprovalStatus = ? WHERE TaskID IN ($ids)";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt === false) {
        write_log("Error preparing statement: " . mysqli_error($conn));
        mysqli_rollback($conn);
        return false;
    }

    mysqli_stmt_bind_param($stmt, "s", $status);
    if (!mysqli_stmt_execute($stmt)) {
        write_log("Error executing statement: " . mysqli_stmt_error($stmt));
        mysqli_rollback($conn);
        return false;
    }

    if ($status === 'Approved') {
        foreach ($taskIDs as $TaskID) {
            // Fetch the task details
            $taskDetailsQuery = $conn->prepare("
                SELECT t.ContentID, t.UserID, t.Title, t.taskContent, t.DueDate, t.DueTime, ua.fname AS creatorName 
                FROM tasks t 
                JOIN useracc ua ON t.UserID = ua.UserID 
                WHERE t.TaskID = ?
            ");
            $taskDetailsQuery->bind_param("i", $TaskID);
            $taskDetailsQuery->execute();
            $taskDetails = $taskDetailsQuery->get_result()->fetch_assoc();

            $ContentID = $taskDetails['ContentID'];
            $creatorUserID = $taskDetails['UserID'];
            $creatorName = $taskDetails['creatorName'];
            $taskContent = $taskDetails['taskContent'];
            $taskTitle = $taskDetails['Title'];
            $DueDate = $taskDetails['DueDate'];
            $DueTime = $taskDetails['DueTime'];

            // Fetch associated users
            $userContentQuery = $conn->prepare("SELECT ua.UserID FROM usercontent uc 
                                                JOIN useracc ua ON uc.UserID = ua.UserID 
                                                WHERE uc.ContentID = ?");
            $userContentQuery->bind_param("i", $ContentID);
            $userContentQuery->execute();
            $userResult = $userContentQuery->get_result();

            if ($userResult) {
                while ($row = $userResult->fetch_assoc()) {
                    $userInContentId = $row['UserID'];

                    // Insert into task_user table
                    $taskUserSql = "INSERT INTO task_user (ContentID, TaskID, UserID, Status) VALUES (?, ?, ?, 'Assigned')";
                    $taskUserStmt = $conn->prepare($taskUserSql);
                    $taskUserStmt->bind_param("sss", $ContentID, $TaskID, $userInContentId);
                    if (!$taskUserStmt->execute()) {
                        write_log("Error inserting into task_user: " . $taskUserStmt->error);
                        mysqli_rollback($conn);
                        return false;
                    }
                    $taskUserStmt->close();
                }

                // Fetch content title
                $contentQuery = $conn->prepare("SELECT Title FROM feedcontent WHERE ContentID = ?");
                $contentQuery->bind_param("s", $ContentID);
                $contentQuery->execute();
                $contentResult = $contentQuery->get_result();
                $contentTitle = $contentResult->num_rows > 0 ? $contentResult->fetch_assoc()['Title'] : "Unknown Content";

                // Create notification
                $notificationTitle = "$creatorName Posted a new Task! ($contentTitle)";
                $notificationContent = "$taskTitle: $taskContent";
                $status = 1;

                $notifStmt = $conn->prepare("INSERT INTO notifications (UserID, TaskID, ContentID, Title, Content, Status) VALUES (?, ?, ?, ?, ?, ?)");
                $notifStmt->bind_param("sssssi", $creatorUserID, $TaskID, $ContentID, $notificationTitle, $notificationContent, $status);

                if ($notifStmt->execute()) {
                    $notifID = $notifStmt->insert_id;
                
                    // Additional notification for approval
                    $approvalNotificationTitle = "Your Task has been Approved!";
                    $approvalNotificationContent = "Task: $taskTitle has been approved by the administrator.";
                    $approvalNotifStmt = $conn->prepare("INSERT INTO notifications (UserID, TaskID, ContentID, Title, Content, Status) VALUES (?, ?, ?, ?, ?, ?)");
                    $approvalNotifStmt->bind_param("sssssi", $creatorUserID, $TaskID, $ContentID, $approvalNotificationTitle, $approvalNotificationContent, $status);
                
                    if ($approvalNotifStmt->execute()) {
                        $approvalNotifID = $approvalNotifStmt->insert_id;
                
                        // Link the approval notification to the creator
                        $notifUserApprovalStmt = $conn->prepare("INSERT INTO notif_user (NotifID, UserID, Status, TimeStamp) VALUES (?, ?, ?, ?)");
                        $notifUserApprovalStmt->bind_param("iiss", $approvalNotifID, $creatorUserID, $status, $timestamp);
                
                        if (!$notifUserApprovalStmt->execute()) {
                            write_log("Error inserting approval notification for task creator: " . $notifUserApprovalStmt->error);
                            mysqli_rollback($conn);
                            return false;
                        }
                        $notifUserApprovalStmt->close();
                    } else {
                        write_log("Error inserting approval notification: " . $approvalNotifStmt->error);
                        mysqli_rollback($conn);
                        return false;
                    }
                    $approvalNotifStmt->close();
                
                    // Insert into notif_user for each associated user
                    foreach ($userResult as $row) {
                        $userInContentId = $row['UserID'];
                        $notifUserStmt = $conn->prepare("INSERT INTO notif_user (NotifID, UserID, Status, TimeStamp) VALUES (?, ?, ?, ?)");
                        $notifUserStmt->bind_param("iiss", $notifID, $userInContentId, $status, $timestamp);
                        if (!$notifUserStmt->execute()) {
                            write_log("Error inserting into notif_user: " . $notifUserStmt->error);
                            mysqli_rollback($conn);
                            return false;
                        }
                        $notifUserStmt->close();
                    }
                
                    // Call send_bulk_sms after all notifications are set up
                    send_bulk_sms($conn, $ContentID, $notificationTitle, $taskTitle, $DueDate, $DueTime);
                } else {
                    write_log("Error inserting into notifications: " . $notifStmt->error);
                    mysqli_rollback($conn);
                    return false;
                }
                
                $notifStmt->close();
                $contentQuery->close();

            }
            $userContentQuery->close();
        }
    }

    // Commit Transaction
    if (mysqli_commit($conn)) {
        write_log("Successfully updated tasks with IDs: " . implode(', ', $taskIDs) . " to status: $status");
    } else {
        write_log("Failed to commit transaction for tasks with IDs: " . implode(', ', $taskIDs));
        mysqli_rollback($conn);
        return false;
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $taskIDs = json_decode($_POST['taskIDs']);
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'Approved' : 'Rejected';

    if (updateTaskStatus($conn, $taskIDs, $status)) {
        echo json_encode(["status" => "success", "message" => "$status tasks successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update tasks."]);
    }
    exit;
}

// Fetch pending tasks for GET request
$tasks = getPendingTasks($conn);
echo json_encode($tasks);
?>