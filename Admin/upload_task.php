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
$ContentIDs = isset($_POST['grade']) ? $_POST['grade'] : [];
$Type = 'Task';
$Title = $_POST['title'];
$DueDate = $_POST['due-date'];
$taskContent = $_POST['instructions'];
$DueTime = $_POST['due-time'];
$timeStamp = date('Y-m-d H:i:s');
$ApprovalStatus = "Approved";

// Get schedule date and time from POST if the action is schedule
if ($_POST['taskAction'] === 'Schedule') {
    $ScheduleDate = $_POST['schedule-date'];
    $ScheduleTime = $_POST['schedule-time'];
    $Status = 'Schedule';
} else {
    $ScheduleDate = null;
    $ScheduleTime = null;
    $Status = $_POST['taskAction'] === 'Draft' ? 'Draft' : 'Assign';
}

// File upload handling with improved error handling
$uploadOk = 1;
$target_dir = realpath(__DIR__ . '/Attachments') . '/';
$allFilesUploaded = true;

if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0777, true) && !is_dir($target_dir)) {
        die(json_encode(["success" => false, "message" => "Failed to create directory for attachments."]));
    }
}

$uploadedFiles = [];
$uploadErrors = [];

if (isset($_FILES['file']) && count($_FILES['file']['name']) > 0 && !empty($_FILES['file']['name'][0])) {
    $fileCount = count($_FILES['file']['name']);

    for ($i = 0; $i < $fileCount; $i++) {
        $fileTmpName = $_FILES['file']['tmp_name'][$i];
        $fileOriginalName = basename($_FILES['file']['name'][$i]);
        $error = $_FILES['file']['error'][$i];

        // Handle file upload errors
        if ($error !== UPLOAD_ERR_OK) {
            switch ($error) {
                case UPLOAD_ERR_INI_SIZE:
                case UPLOAD_ERR_FORM_SIZE:
                    $uploadErrors[] = "File {$fileOriginalName} exceeds the maximum allowed size.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $uploadErrors[] = "File {$fileOriginalName} was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $uploadErrors[] = "No file was uploaded for {$fileOriginalName}.";
                    break;
                default:
                    $uploadErrors[] = "An unknown error occurred while uploading {$fileOriginalName}.";
                    break;
            }
            $allFilesUploaded = false;
            continue;
        }

        $fileType = strtolower(pathinfo($fileOriginalName, PATHINFO_EXTENSION));
        $fileSize = $_FILES['file']['size'][$i];
        $fileMimeType = mime_content_type($fileTmpName);

        $randomNumber = rand(100000, 999999);
        $fileName = $randomNumber . "_" . $fileOriginalName;
        $target_file = $target_dir . $fileName;

        if ($fileSize > 5000000) {
            $allFilesUploaded = false;
            $uploadErrors[] = "File {$fileOriginalName} exceeds 5MB limit.";
            continue;
        }

        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'pptx');
        if (!in_array($fileType, $allowedTypes)) {
            $allFilesUploaded = false;
            $uploadErrors[] = "File type {$fileType} not allowed for {$fileOriginalName}.";
            continue;
        }

        if (move_uploaded_file($fileTmpName, $target_file)) {
            // GitHub Repository Details
            $githubRepo = "AbiAb1/DocMaP";
            $branch = "main";
            $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/Attachments/$fileName";

            // Fetch GitHub Token from Environment Variables
            $githubToken = getenv('GITHUB_TOKEN');
            if (!$githubToken) {
                $allFilesUploaded = false;
                $uploadErrors[] = "GITHUB_TOKEN environment variable not set.";
                continue;
            }

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

            $ch = curl_init($uploadUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);

            if ($response === false) {
                $allFilesUploaded = false;
                $uploadErrors[] = "GitHub upload failed for {$fileName}: " . $curlError;
            } else {
                $responseData = json_decode($response, true);
                if ($httpCode == 201) {
                    $githubDownloadUrl = $responseData['content']['download_url'];

                    $uploadedFiles[] = [
                        'fileName' => $fileName,
                        'fileMimeType' => $fileMimeType,
                        'fileSize' => $fileSize,
                        'githubUrl' => $githubDownloadUrl
                    ];
                } else {
                    $allFilesUploaded = false;
                    $uploadErrors[] = "GitHub upload failed for {$fileName}: HTTP status code {$httpCode} - " . $response;
                }
            }

            curl_close($ch);

            if (file_exists($target_file)) {
                unlink($target_file);
            }
        } else {
            $allFilesUploaded = false;
            $uploadErrors[] = "Failed to move uploaded file {$fileOriginalName}";
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

            foreach ($uploadedFiles as $file) {
                $docuStmt = $conn->prepare("INSERT INTO attachment (UserID, ContentID, TaskID, name, mimeType, size, uri, TimeStamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $timestamp = date("Y-m-d H:i:s");
                $docuStmt->bind_param("ssssssss", $UserID, $ContentID, $TaskID, $file['fileName'], $file['fileMimeType'], $file['fileSize'], $file['githubUrl'], $timestamp);

                $docuStmt->execute();
                $docuStmt->close();
            }

            if ($_POST['taskAction'] === 'Assign') {
                // ... (Your existing notification and SMS sending logic remains unchanged) ...
            }

            $stmt->close();
        }
    }
}

// Set response
header('Content-Type: application/json');
$response = ["success" => $allFilesUploaded, "message" => "Tasks created."];

if (!$allFilesUploaded) {
    $response["message"] = "Tasks created, but some issues occurred:";
    $response["errors"] = $uploadErrors;
}

echo json_encode($response, JSON_PRETTY_PRINT);

$conn->close();
?>
