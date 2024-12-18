<?php
ob_start(); 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'connection.php';


// Get data from form
$UserID = $_SESSION['user_id'];
$ContentIDs = isset($_POST['grade']) ? $_POST['grade'] : []; // Get all selected ContentIDs as an array
$Type = 'Task';
$Title = $_POST['title'];
$DueDate = $_POST['due-date'];
$taskContent = $_POST['instructions'];
$DueTime = $_POST['due-time'];
$timeStamp = date('Y-m-d H:i:s'); // Current timestamp
$ApprovalStatus = "Pending";

// Get schedule date and time from POST if the action is schedule
if ($_POST['taskAction'] === 'Schedule') {
    $ScheduleDate = $_POST['schedule-date'];
    $ScheduleTime = $_POST['schedule-time'];
    $Status = 'Schedule';
} else {
    $ScheduleDate = null;
    $ScheduleTime = null;
    $Status = $_POST['taskAction'] === 'Draft' ? 'Draft' : 'Assign'; // Set to Draft if action is draft
}


// File upload handling with GitHub integration
$uploadOk = 1;
$target_dir = realpath(__DIR__ . '/Attachments') . '/'; // Absolute path to the directory
$allFilesUploaded = true;
echo $target_dir;

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // Create directory if not exists
}

$uploadedFiles = [];

if (isset($_FILES['file']) && count($_FILES['file']['name']) > 0 && !empty($_FILES['file']['name'][0])) {
    $fileCount = count($_FILES['file']['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        $fileTmpName = $_FILES['file']['tmp_name'][$i];
        $fileOriginalName = basename($_FILES['file']['name'][$i]);
        $fileType = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['file']['size'][$i];
        $fileMimeType = mime_content_type($fileTmpName);

        // Generate a random file name
        $randomNumber = rand(100000, 999999);
        $fileName = $randomNumber . "_" . $fileOriginalName;
        $target_file = $target_dir . $fileName;


        // Check file size
        if ($fileSize > 5000000) { // Limit to 5MB
            write_log("File too large: $fileOriginalName");
            $allFilesUploaded = false;
            continue;
        }

        // Allow certain file formats
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'pptx');
        if (!in_array($fileType, $allowedTypes)) {
            $allFilesUploaded = false;
            continue;
        }

        if (move_uploaded_file($fileTmpName, $target_file)) {
        
            // GitHub Repository Details
            $githubRepo = "AbiAb1/DocMaP"; // GitHub username/repo
            $branch = "main";
            $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/Attachments/$fileName";
        
            // Fetch GitHub Token from Environment Variables
            $githubToken = getenv('GITHUB_TOKEN');
            if (!$githubToken) {
                continue;
            }
        
            // Prepare File Data for GitHub
            $content = base64_encode(file_get_contents($target_file));
            $data = json_encode([
                "message" => "Adding a new file to upload folder",
                "content" => $content,
                "branch" => $branch
            ]);
        
            $headers = [
                "Authorization: token $githubToken",
                "Content-Type: application/json",
                "User-Agent: DocMaP"
            ];
        
            // GitHub API Call
            $ch = curl_init($uploadUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
            if ($response === false) {
            } else {
                $responseData = json_decode($response, true);
                if ($httpCode == 201) { // Successful upload
                    $githubDownloadUrl = $responseData['content']['download_url'];
        
                    // Save File Information to the Database
                    $uploadedFiles[] = [
                        'fileName' => $fileName,
                        'fileMimeType' => $fileMimeType,
                        'fileSize' => $fileSize,
                        'githubUrl' => $githubDownloadUrl
                    ];
                } else {
                    write_log("Error uploading file to GitHub: " . $response);
                }
            }
        
            curl_close($ch);
        
            // Optionally Delete Local File After Upload
            if (file_exists($target_file)) {
                unlink($target_file);

            }
        } else {
            write_log("Error uploading file locally: $fileOriginalName");
            $allFilesUploaded = false;
        }        
    }
} else {
    write_log("No files uploaded or file input is empty.");
}

// Insert task into tasks table for each ContentID
foreach ($ContentIDs as $ContentID) {
    // Prepare the SQL for inserting into tasks
    $sql = "INSERT INTO tasks (UserID, ContentID, Type, Title, taskContent, DueDate, DueTime, Schedule_Date, Schedule_Time, Status, ApprovalStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        
        $stmt->bind_param("sssssssssss", $UserID, $ContentID, $Type, $Title, $taskContent, $DueDate, $DueTime,$ScheduleDate, $ScheduleTime, $Status, $ApprovalStatus);

        if ($stmt->execute()) {
            $TaskID = $stmt->insert_id;

            // Insert files into attachment table using the fetched TaskID
            foreach ($uploadedFiles as $file) {
                $docuStmt = $conn->prepare("INSERT INTO attachment (UserID, ContentID, TaskID, name, mimeType, size, uri, TimeStamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $timestamp = date("Y-m-d H:i:s"); // Current timestamp
                $docuStmt->bind_param("ssssssss", $UserID, $ContentID, $TaskID, $file['fileName'], $file['fileMimeType'], $file['fileSize'], $file['githubUrl'], $timestamp);

                // Execute the statement for the attachment table
                if (!$docuStmt->execute()) {
                    write_log("Error inserting into attachment: " . $docuStmt->error);
                }
                $docuStmt->close(); // Close statement after each ContentID
            }

            if ($_POST['taskAction'] === 'Assign') { // Only proceed if taskAction is 'Assign'
                // Fetch users associated with the ContentID from usercontent
                $userContentQuery = $conn->prepare("
                SELECT ua.UserID, uc.Status
                FROM usercontent uc
                JOIN useracc ua ON uc.UserID = ua.UserID
                WHERE uc.ContentID = ?
                AND uc.Status = 1
                ");
                $userContentQuery->bind_param("i", $ContentID); // Assuming ContentID is an integer
                $userContentQuery->execute();
                $userResult = $userContentQuery->get_result();

                if ($userResult) {
                    while ($row = $userResult->fetch_assoc()) {
                        $userInContentId = $row['UserID'];
                        // Insert into task_user for each user associated with this ContentID
                        $taskUserSql = "INSERT INTO task_user (ContentID, TaskID, UserID, Status) VALUES (?, ?, ?, 'Assigned')";
                        $taskUserStmt = $conn->prepare($taskUserSql);
                        if ($taskUserStmt) {
                            $taskUserStmt->bind_param("sss", $ContentID, $TaskID, $userInContentId);
                            if (!$taskUserStmt->execute()) {
                                write_log("Error inserting into task_user: " . $taskUserStmt->error);
                            }
                            $taskUserStmt->close();
                        } else {
                            write_log("Error preparing task_user statement: " . $conn->error);
                        }
                    }
                } else {
                    write_log("Error fetching users for ContentID $ContentID: " . $conn->error);
                }

                // Fetch user name for notifications
                $userQuery = $conn->prepare("SELECT CONCAT(fname, ' ', lname) AS fullName FROM useracc WHERE UserID = ?");
                $userQuery->bind_param("s", $UserID);
                $userQuery->execute();
                $userName = $userQuery->get_result()->fetch_assoc()['fullName'];
                write_log("Fetched user name: $userName for UserID: $UserID");


                // Fetch content title for notifications
                $contentQuery = $conn->prepare("SELECT Title , Captions FROM feedcontent WHERE ContentID = ?");
                $contentQuery->bind_param("s", $ContentID);
                $contentQuery->execute();
                $contentResult = $contentQuery->get_result();

                if ($contentResult->num_rows > 0) {
                    $row = $contentResult->fetch_assoc();
                    $contentTitle = $row['Title'];
                    $contentCaptions = $row['Captions'];
                    
                    // Concatenate Title and Captions
                    $fullContent = $contentTitle . ' - ' . $contentCaptions; // Adjust the separator as needed
                    write_log("Fetched content: $fullContent for ContentID: $ContentID");
                } else {
                    $fullContent = "Unknown Content"; // Default value if no content found
                    write_log("No content found for ContentID: $ContentID");
                }

                // Create notification
                $notificationTitle = "$userName posted a new $Type! ($fullContent)";
                $notificationContent = "$Title: $taskContent";

                $notifStmt = $conn->prepare("INSERT INTO notifications (UserID, TaskID, ContentID, Title, Content, Status) VALUES (?, ?, ?, ?, ?, ?)");
                $status = 1;
                $notifStmt->bind_param("sssssi", $UserID, $TaskID, $ContentID, $notificationTitle, $notificationContent, $status);

                if ($notifStmt->execute()) {
                    $notifID = $notifStmt->insert_id;  // Get the inserted NotifID
                    write_log("Notification added for TaskID $TaskID, Title: $notificationTitle");

                    // Insert into notif_user table for each user associated with this ContentID
                    $userContentQuery = $conn->prepare("SELECT ua.UserID FROM usercontent uc JOIN useracc ua ON uc.UserID = ua.UserID WHERE uc.ContentID = ?");
                    $userContentQuery->bind_param("i", $ContentID);
                    $userContentQuery->execute();
                    $userContentResult = $userContentQuery->get_result();

                    if ($userContentResult) {
                        while ($row = $userContentResult->fetch_assoc()) {
                            $userInContentId = $row['UserID'];

                            // Insert into notif_user for each user
                            $notifUserStmt = $conn->prepare("INSERT INTO notif_user (NotifID, UserID, Status, TimeStamp) VALUES (?, ?, ?, ?)");
                            $timestamp = date("Y-m-d H:i:s");  // Current timestamp
                            $status = 1;  // Status is 1 for all users
                            $notifUserStmt->bind_param("iiss", $notifID, $userInContentId, $status, $timestamp);

                            if ($notifUserStmt->execute()) {
                                write_log("Notification user inserted: NotifID $notifID, UserID $userInContentId");
                            } else {
                                write_log("Error inserting into notif_user: " . $notifUserStmt->error);
                            }

                            $notifUserStmt->close(); // Close after each insertion
                        }
                    } else {
                        write_log("Error fetching users for ContentID $ContentID: " . $conn->error);
                    }
                    // Fetch mobile numbers for bulk SMS
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
                

                    // Close user query
                    $userContentQuery->close();
                } else {
                    write_log("Error inserting into notifications: " . $notifStmt->error);
                }

                $notifStmt->close(); // Close notification statement

                $userQuery->close();
                $contentQuery->close();
            } // End of if $_POST['taskAction'] === 'Assign'

            $stmt->close(); // Close statement after each iteration
        } else {
            write_log("Error inserting into tasks: " . $stmt->error);
        }
    } else {
        write_log("Error preparing tasks statement: " . $conn->error);
    }
}

// Set response
header('Content-Type: application/json');
$response = array("success" => true, "message" => "Tasks created successfully.");
if (!$allFilesUploaded) {
    $response = array("success" => false, "message" => "Tasks created, but some files may not have been uploaded.");
}
echo json_encode($response);

$conn->close();
write_log("Database connection closed.");
?>
