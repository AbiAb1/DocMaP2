<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'connection.php';

// Log file path
$log_file = 'logfile.log';

// Function to write to log file
function write_log($message) {
    global $log_file;
    $timestamp = date("Y-m-d H:i:s");
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logfile.log');

write_log("Database connected successfully.");

// Get data from form
$UserID = $_SESSION['user_id'];
$ContentIDs = isset($_POST['grade']) ? $_POST['grade'] : []; // Get all selected ContentIDs as an array
$Type = 'Task';
$Title = $_POST['title'];
$DueDate = $_POST['due-date'];
$taskContent = $_POST['instructions'];
$DueTime = $_POST['due-time'];
$timeStamp = date('Y-m-d H:i:s'); // Current timestamp
$ApprovalStatus = "Pending"; // Set ApprovalStatus to Approved

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

write_log("Received form data: UserID = $UserID, ContentIDs = " . implode(", ", $ContentIDs) . ", Type = $Type, Title = $Title, DueDate = $DueDate, taskContent = $taskContent, DueTime = $DueTime, Status = $Status, Schedule Date = $ScheduleDate, Schedule Time = $ScheduleTime");

// File upload handling
$uploadOk = 1;
$target_dir = __DIR__ . '/Attachments/'; // Absolute path to the directory
$allFilesUploaded = true;

if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true); // Create directory if not exists
}

$uploadedFiles = [];

if (isset($_FILES['file']) && count($_FILES['file']['name']) > 0 && !empty($_FILES['file']['name'][0])) {
    $fileCount = count($_FILES['file']['name']);
    write_log("Number of files to upload: $fileCount");

    for ($i = 0; $i < $fileCount; $i++) {
        $fileTmpName = $_FILES['file']['tmp_name'][$i];
        $fileOriginalName = basename($_FILES['file']['name'][$i]);
        $fileType = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['file']['size'][$i];
        $fileMimeType = mime_content_type($fileTmpName);

        // Sanitize file name
        $fileOriginalName = preg_replace('/[^a-zA-Z0-9_.]/', '', str_replace([' ', '-'], '_', $fileOriginalName));

        // Generate a random file name
        $randomNumber = rand(100000, 999999);
        $fileName = $randomNumber . "_" . $fileOriginalName;
        $target_file = $target_dir . $fileName;


        // Check file size
        if ($fileSize > 5000000) { // Limit to 5MB
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
            $githubRepo = "AbiAb1/DocMaP2"; // GitHub username/repo
            $branch = "extra";
            $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/DeptHead/Attachments/$fileName";
        
            // Fetch GitHub Token from Environment Variables
            $githubToken = $_ENV['GITHUB_TOKEN']?? null;
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
                } 
            }
        
            curl_close($ch);
        
            // Optionally Delete Local File After Upload
            if (file_exists($target_file)) {
                unlink($target_file);

            }
        } else {
            $allFilesUploaded = false;
        }        
    }
}

// Insert task into tasks table for each ContentID
foreach ($ContentIDs as $ContentID) {
    $sql = "INSERT INTO tasks (UserID, ContentID, Type, Title, taskContent, DueDate, DueTime, Schedule_Date, Schedule_Time, Status, ApprovalStatus) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sssssssssss", $UserID, $ContentID, $Type, $Title, $taskContent, $DueDate, $DueTime, $ScheduleDate, $ScheduleTime, $Status, $ApprovalStatus);

        if ($stmt->execute()) {
            $TaskID = $stmt->insert_id;
            write_log("Task added with ID: $TaskID, UserID: $UserID, ContentID: $ContentID");

            // Insert files into attachment table using the fetched TaskID
            foreach ($uploadedFiles as $file) {
                $docuStmt = $conn->prepare("INSERT INTO attachment (UserID, ContentID, TaskID, name, mimeType, size, uri, TimeStamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $timestamp = date("Y-m-d H:i:s");
                $docuStmt->bind_param("ssssssss", $UserID, $ContentID, $TaskID, $file['fileName'], $file['fileMimeType'], $file['fileSize'], $file['target_file'], $timestamp);

                if (!$docuStmt->execute()) {
                    write_log("Error inserting into attachment: " . $docuStmt->error);
                }
                $docuStmt->close();
            }
        } else {
            write_log("Error inserting into tasks: " . $stmt->error);
        }

        $stmt->close();
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
