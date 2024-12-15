<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proftal4";

// Log file path
$log_file = 'logfile.log';

// Function to write to log file
function write_log($message) {
    global $log_file;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    write_log("Connection failed: " . $conn->connect_error);
    $response = array("success" => false, "message" => "Connection failed.");
    echo json_encode($response);
    exit();
}

write_log("Database connected successfully.");

// Get data from form
$UserID = $_SESSION['user_id'];
$ContentIDs = isset($_POST['grade']) ? $_POST['grade'] : [];
$Type = 'Announcement';
$Title = $_POST['title'];
$DueDate = $_POST['due-date'];
$taskContent = $_POST['instructions'];
$DueTime = $_POST['due-time'];
$timeStamp = date('Y-m-d H:i:s');

// Check if all necessary data is provided
if (empty($ContentIDs) || empty($Title) || empty($taskContent) || empty($DueDate) || empty($DueTime)) {
    write_log("Missing required fields: ContentIDs, Title, taskContent, DueDate, or DueTime.");
    $response = array("success" => false, "message" => "Please fill in all the required fields.");
    echo json_encode($response);
    exit();
}

if ($_POST['taskAction'] === 'Schedule') {
    $ScheduleDate = $_POST['schedule-date'];
    $ScheduleTime = $_POST['schedule-time'];
    $Status = 'Schedule';
} else {
    $ScheduleDate = null;
    $ScheduleTime = null;
    $Status = $_POST['taskAction'] === 'Draft' ? 'Draft' : 'Assign';
}

write_log("Received form data: UserID = $UserID, ContentIDs = " . implode(", ", $ContentIDs) . ", Type = $Type, Title = $Title, DueDate = $DueDate, taskContent = $taskContent, DueTime = $DueTime, Status = $Status, Schedule Date = $ScheduleDate, Schedule Time = $ScheduleTime");

// Insert task into tasks table for each ContentID
foreach ($ContentIDs as $ContentID) {
    if ($Status == 'Schedule') {
        // Prepare SQL for scheduled tasks
        $sql = "INSERT INTO tasks (UserID, ContentID, Type, Title, taskContent, DueDate, DueTime, Status, Schedule_Date, Schedule_Time) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            write_log("Error preparing SQL for scheduled task: " . $conn->error);
            continue;
        }
        $stmt->bind_param("ssssssssss", $UserID, $ContentID, $Type, $Title, $taskContent, $DueDate, $DueTime, $Status, $ScheduleDate, $ScheduleTime);
    } else {
        // Prepare SQL for non-scheduled tasks
        $sql = "INSERT INTO tasks (UserID, ContentID, Type, Title, taskContent, DueDate, DueTime, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            write_log("Error preparing SQL for non-scheduled task: " . $conn->error);
            continue;
        }
        $stmt->bind_param("ssssssss", $UserID, $ContentID, $Type, $Title, $taskContent, $DueDate, $DueTime, $Status);
    }

    // Execute the statement
    if (!$stmt->execute()) {
        write_log("Error executing SQL: " . $stmt->error);
        continue;
    }
    
    $TaskID = $stmt->insert_id;  // Get the auto-incremented TaskID
    write_log("Task added with ID: $TaskID, UserID: $UserID, ContentID: $ContentID");

    // Insert into task_user table with NULL status
    $taskUserSql = "INSERT INTO task_user (ContentID, TaskID, UserID, Status) VALUES (?, ?, ?, NULL)";
    $taskUserStmt = $conn->prepare($taskUserSql);
    if ($taskUserStmt) {
        // Assuming you have a way to get users associated with ContentID
        $userContentQuery = $conn->prepare("SELECT UserID FROM usercontent WHERE ContentID = ?");
        $userContentQuery->bind_param("s", $ContentID);
        $userContentQuery->execute();
        $userResult = $userContentQuery->get_result();

        while ($row = $userResult->fetch_assoc()) {
            $userInContentId = $row['UserID'];
            $taskUserStmt->bind_param("ssi", $ContentID, $TaskID, $userInContentId);
            if (!$taskUserStmt->execute()) {
                write_log("Error inserting into task_user for TaskID: $TaskID, UserID: $userInContentId");
            }
        }
        $taskUserStmt->close();
    } else {
        write_log("Error preparing task_user statement: " . $conn->error);
    }

    // Fetch user name for notifications
    $userQuery = $conn->prepare("SELECT fname FROM useracc WHERE UserID = ?");
    $userQuery->bind_param("s", $UserID);
    if (!$userQuery->execute()) {
        write_log("Error fetching user name for UserID: $UserID");
    }
    $userName = $userQuery->get_result()->fetch_assoc()['fname'];

    // Fetch content title for notifications
    $contentQuery = $conn->prepare("SELECT Title FROM feedcontent WHERE ContentID = ?");
    $contentQuery->bind_param("s", $ContentID);
    if (!$contentQuery->execute()) {
        write_log("Error fetching content title for ContentID: $ContentID");
    }
    $contentResult = $contentQuery->get_result();
    $contentTitle = $contentResult->num_rows > 0 ? $contentResult->fetch_assoc()['Title'] : "Unknown Content";

    // Create notification
    $notificationTitle = "$userName Posted a new $Type! ($contentTitle)";
    $notificationContent = "$Title: $taskContent";

    $notifStmt = $conn->prepare("INSERT INTO notifications (UserID, TaskID, ContentID, Title, Content, Status) VALUES (?, ?, ?, ?, ?, ?)");
    $status = 1;
    if ($notifStmt) {
        $notifStmt->bind_param("sssssi", $UserID, $TaskID, $ContentID, $notificationTitle, $notificationContent, $status);
        if (!$notifStmt->execute()) {
            write_log("Error inserting into notifications: " . $notifStmt->error);
        }

        // Now insert into notif_user table
        $NotifID = $notifStmt->insert_id;  // Get the auto-incremented NotifID

        // Fetch department head
        $deptQuery = $conn->prepare("SELECT dept_ID FROM feedcontent WHERE ContentID = ?");
        $deptQuery->bind_param("s", $ContentID);
        $deptQuery->execute();
        $deptID = $deptQuery->get_result()->fetch_assoc()['dept_ID'];

        // Fetch department head user ID from useracc
        $deptHeadQuery = $conn->prepare("SELECT UserID FROM useracc WHERE dept_ID = ? AND role = 'Department Head'");
        $deptHeadQuery->bind_param("s", $deptID);
        $deptHeadQuery->execute();
        $deptHeadResult = $deptHeadQuery->get_result();
        
        if ($deptHeadResult->num_rows > 0) {
            $deptHeadUserID = $deptHeadResult->fetch_assoc()['UserID'];
            // Insert department head into notif_user table
            $notifUserStmt = $conn->prepare("INSERT INTO notif_user (NotifID, UserID, Status, TimeStamp) VALUES (?, ?, ?, ?)");
            $status = 1;  // Status set to 1 for active notification
            $timeStamp = date('Y-m-d H:i:s');  // Current timestamp

            $notifUserStmt->bind_param("iiis", $NotifID, $deptHeadUserID, $status, $timeStamp);
            if (!$notifUserStmt->execute()) {
                write_log("Error inserting into notif_user for Dept Head NotifID: $NotifID, UserID: $deptHeadUserID: " . $notifUserStmt->error);
            }
            $notifUserStmt->close();
        }
        $deptHeadQuery->close();  // Close department head query

       // Insert into notif_user table for each user who should be notified
$userContentQuery = $conn->prepare("SELECT UserID FROM usercontent WHERE ContentID = ?");
$userContentQuery->bind_param("s", $ContentID);
$userContentQuery->execute();
$userResult = $userContentQuery->get_result();

while ($row = $userResult->fetch_assoc()) {
    $userInContentId = $row['UserID'];

    // Check if the user already has this notification
    $notifUserCheckQuery = $conn->prepare("SELECT COUNT(*) FROM notif_user WHERE NotifID = ? AND UserID = ?");
    $notifUserCheckQuery->bind_param("ii", $NotifID, $userInContentId);
    $notifUserCheckQuery->execute();
    $notifUserCheckResult = $notifUserCheckQuery->get_result();
    $notifUserCount = $notifUserCheckResult->fetch_row()[0];

    if ($notifUserCount == 0) {
        // Insert user into notif_user table
        $notifUserStmt = $conn->prepare("INSERT INTO notif_user (NotifID, UserID, Status, TimeStamp) VALUES (?, ?, ?, ?)");
        $status = 1;  // Status set to 1 for active notification
        $timeStamp = date('Y-m-d H:i:s');  // Current timestamp

        if ($notifUserStmt) {
            $notifUserStmt->bind_param("iiis", $NotifID, $userInContentId, $status, $timeStamp);
            if (!$notifUserStmt->execute()) {
                write_log("Error inserting into notif_user for NotifID: $NotifID, UserID: $userInContentId: " . $notifUserStmt->error);
            }
        }
        $notifUserStmt->close();  // Close the statement
    } else {
        // Log that the user already has the notification
        write_log("User $userInContentId already has the notification (NotifID: $NotifID).");
    }
}


        $notifStmt->close();  // Close notification statement
    }

    // Close statements
    $userQuery->close();
    $contentQuery->close();
    $stmt->close();

    // Send SMS
    $api_url = "https://api.semaphore.co/api/v4/messages";
    $api_key = "d796c0e11273934ac9d789536133684a";
    $notificationContent = "$Title: $taskContent";
    $mobileQuery = $conn->prepare("
SELECT ua.mobile, UPPER(CONCAT(ua.fname, ' ', ua.lname)) AS FullName
FROM usercontent uc
JOIN useracc ua ON uc.UserID = ua.UserID
JOIN notif_user nu ON nu.UserID = ua.UserID
JOIN notifications n ON n.NotifID = nu.NotifID
JOIN tasks t ON t.TaskID = n.TaskID
WHERE uc.ContentID = ? AND t.TaskID = ?

UNION

SELECT ua.mobile, UPPER(CONCAT(ua.fname, ' ', ua.lname)) AS FullName
FROM useracc ua
JOIN feedcontent fc ON ua.dept_ID = fc.dept_ID
WHERE ua.role = 'Department Head' AND fc.dept_ID = ?
");
$mobileQuery->bind_param("ssi", $ContentID, $TaskID, $deptID);  // Bind the parameters correctly

    $mobileQuery->execute();
    $mobileResult = $mobileQuery->get_result();
    
    if ($mobileResult->num_rows > 0) {
        while ($row = $mobileResult->fetch_assoc()) {
            $mobileNumber = $row['mobile'];
            $FullName = $row['FullName'];
            $message = "NEW ANNOUNCEMENT ALERT!\n\nHi " . $row['FullName'] . "! " . $notificationTitle . " \"" . $Title . "\". Don't miss it!";
            $postData = [
                'apikey' => $api_key,
                'number' => $mobileNumber,
                'message' => $message
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $api_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_errno($ch)) {
                write_log("Error sending SMS to number ($mobileNumber): " . curl_error($ch));
            } else {
                write_log("SMS sent successfully to number: $mobileNumber");
            }
            curl_close($ch);
        }
    } else {
        write_log("No mobile numbers found for ContentID $ContentID");
    }
}

header('Content-Type: application/json');

// Set response
$response = array("success" => true, "message" => "Announcements created successfully.");
echo json_encode($response);

// Close connection
$conn->close();
write_log("Database connection closed.");
?>