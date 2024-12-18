<?php

// Define GitHub repository details
$githubRepo = "AbiAb1/DocMaP2"; // Your GitHub username/repo
$branch = "extra"; // Branch where you want to upload

// Check if the file was uploaded via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES["file"])) {
    // Get the uploaded file details
    $fileTmpName = $_FILES["file"]["tmp_name"];
    $originalFileName = $_FILES["file"]["name"];

    // Sanitize the original file name
    $sanitizedFileName = preg_replace('/[^a-zA-Z0-9_.]/', '', str_replace([' ', '-'], '_', $originalFileName));
    
    $fileType = strtolower(pathinfo($sanitizedFileName, PATHINFO_EXTENSION));

    // Validate file type
    if ($fileType != "xls" && $fileType != "xlsx") {
        echo json_encode(['status' => 'error', 'message' => 'Only Excel files are allowed.']);
        exit;
    }

    // Generate unique file name
    $uniqueFileName = uniqid() . "_" . $sanitizedFileName;

    // Prepare GitHub API URL
    $uploadUrl = "https://api.github.com/repos/$githubRepo/contents/Admin/TeacherData/$uniqueFileName";

    // Read the file content
    $content = base64_encode(file_get_contents($fileTmpName));

    // Prepare the request body
    $data = json_encode([
        "message" => "Adding a new file to TeacherData folder",
        "content" => $content,
        "branch" => $branch
    ]);

    // Get GitHub token from environment variables
    $githubToken = getenv('GITHUB_TOKEN');

    if (!$githubToken) {
        echo json_encode(['status' => 'error', 'message' => 'GitHub token is not set in the environment variables.']);
        exit;
    }

    // Prepare the headers
    $headers = [
        "Authorization: token $githubToken",
        "Content-Type: application/json",
        "User-Agent: DocMaP"
    ];

    // Initialize cURL
    $ch = curl_init($uploadUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Handle the response
    if ($response === false) {
        echo json_encode(['status' => 'error', 'message' => 'cURL error: ' . curl_error($ch)]);
        exit;
    } else {
        $responseData = json_decode($response, true);
        if ($httpCode == 201) {
            // File uploaded successfully to GitHub
            $githubDownloadUrl = $responseData['content']['download_url'];
            echo json_encode(['status' => 'success', 'message' => 'File uploaded to GitHub successfully.', 'url' => $githubDownloadUrl]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error uploading file to GitHub: ' . $response]);
        }
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No file uploaded.']);
}
?>
