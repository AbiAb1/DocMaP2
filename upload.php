<?php
session_start();

// Redirect to index.php if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include 'connection.php';

$response = array('success' => false, 'error' => null);

if (isset($_POST['task_id']) && isset($_POST['content_id'])) {
    $task_id = $_POST['task_id'];
    $content_id = $_POST['content_id'];
    $user_id = $_SESSION['user_id'];

    // Update document status to 'Submitted' if not already submitted
    $updateStatusQuery = "UPDATE documents SET Status = 1 WHERE UserID = '$user_id' AND TaskID = '$task_id' AND ContentID = '$content_id' AND Status != 1";
    $updateStatusResult = mysqli_query($conn, $updateStatusQuery);

    if (!$updateStatusResult) {
        $response['error'] = "Error updating document status: " . mysqli_error($conn);
        echo json_encode($response);
        exit();
    }

    // If there is a file uploaded
    if (!empty($_FILES['files']['name'][0])) {
        $uploadDirectory = 'Documents/';

        foreach ($_FILES['files']['name'] as $key => $name) {
            $fileTmpName = $_FILES['files']['tmp_name'][$key];
            $originalFileName = $name;

            // Generate unique file name
            $rd2 = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $fileName = $rd2 . '_' . $originalFileName;
            $localFilePath = $uploadDirectory . $fileName;

            // Move file locally first
            if (!move_uploaded_file($fileTmpName, $localFilePath)) {
                $response['error'] = "Failed to move file locally: $name";
                echo json_encode($response);
                exit();
            }

            // Upload file to GitHub
            $githubRepo = "AbiAb1/DocMaP2"; // Your GitHub username/repo
            $branch = "extra"; // Branch where you want to upload
            $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/Documents/$fileName";

            $content = base64_encode(file_get_contents($localFilePath));
            $data = json_encode([
                "message" => "Adding a new file to upload folder",
                "content" => $content,
                "branch" => $branch
            ]);

            $githubToken = getenv('GITHUB_TOKEN');

            if (!$githubToken) {
                $response['error'] = "GitHub token is not set in the environment variables.";
                echo json_encode($response);
                exit();
            }

            $headers = [
                "Authorization: token $githubToken",
                "Content-Type: application/json",
                "User-Agent: DocMaP"
            ];

            $ch = curl_init($uploadUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $responseGitHub = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode != 201) {
                $response['error'] = "Error uploading file to GitHub: $responseGitHub";
                echo json_encode($response);
                exit();
            }

            $responseData = json_decode($responseGitHub, true);
            $githubDownloadUrl = $responseData['content']['download_url'];

            // Proceed to database operations
            // Retrieve GradeLevelFolderID using ContentID
            $gradeLevelQuery = "SELECT GradeLevelFolderID FROM gradelevelfolders WHERE ContentID = '$content_id' LIMIT 1";
            $gradeLevelResult = mysqli_query($conn, $gradeLevelQuery);

            if ($gradeLevelResult && mysqli_num_rows($gradeLevelResult) > 0) {
                $gradeLevelRow = mysqli_fetch_assoc($gradeLevelResult);
                $gradeLevelFolderID = $gradeLevelRow['GradeLevelFolderID'];
            } else {
                $response['error'] = "Error retrieving GradeLevelFolderID.";
                echo json_encode($response);
                exit();
            }

            // Fetch UserFolderID by first getting UserContentID from usercontent table using ContentID
            $getUserContentQuery = "SELECT UserContentID FROM usercontent WHERE UserID = '$user_id' AND ContentID = '$content_id'";
            $getUserContentResult = mysqli_query($conn, $getUserContentQuery);

            if ($getUserContentResult && mysqli_num_rows($getUserContentResult) > 0) {
                $userContentRow = mysqli_fetch_assoc($getUserContentResult);
                $userContentID = $userContentRow['UserContentID'];

                $getUserFolderQuery = "SELECT UserFolderID FROM userfolders WHERE UserContentID = '$userContentID'";
                $getUserFolderResult = mysqli_query($conn, $getUserFolderQuery);

                if ($getUserFolderResult && mysqli_num_rows($getUserFolderResult) > 0) {
                    $userFolderRow = mysqli_fetch_assoc($getUserFolderResult);
                    $userFolderID = $userFolderRow['UserFolderID'];
                } else {
                    $response['error'] = "Error retrieving UserFolderID.";
                    echo json_encode($response);
                    exit();
                }
            } else {
                $response['error'] = "Error retrieving UserContentID.";
                echo json_encode($response);
                exit();
            }

            // Insert new file info into database
            $sql = "INSERT INTO documents (GradeLevelFolderID, UserFolderID, UserID, ContentID, TaskID, name, uri, mimeType, size, Status, TimeStamp) 
                    VALUES ('$gradeLevelFolderID', '$userFolderID', '$user_id', '$content_id', '$task_id', '$fileName', '$githubDownloadUrl', 'application/octet-stream', '0', 1, NOW())";

            if (mysqli_query($conn, $sql)) {
                $response['success'] = true;
            } else {
                $response['error'] = "Error inserting new file info into database: " . mysqli_error($conn);
                echo json_encode($response);
                exit();
            }
        }
    }

    // Update task_user status to 'Submitted'
    $updateTaskUserQuery = "UPDATE task_user SET Status = 'Submitted', SubmitDate = NOW() WHERE TaskID = ? AND UserID = ? AND Status != 'Submitted'";
    $updateTaskUserStmt = $conn->prepare($updateTaskUserQuery);
    $updateTaskUserStmt->bind_param("ii", $task_id, $user_id);

    if (!$updateTaskUserStmt->execute()) {
        $response['error'] = "Error updating task_user status: " . mysqli_error($conn);
    } else {
        $response['success'] = true;
    }
} else {
    $response['error'] = "Task ID and Content ID are required.";
}

echo json_encode($response);
exit();
?>
