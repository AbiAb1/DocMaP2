<?php
include 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Path to Composer autoload file

header('Content-Type: application/json');

$response = array('status' => 'error');

// Enable detailed error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE useracc SET Status = 'Approved' WHERE UserID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->close();

                // Fetch user details for sending email
                $stmt = $conn->prepare("SELECT email, fname, lname, Username, Password FROM useracc WHERE UserID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($user) {
                    sendEmail($user, 'Approved', "Your account has been approved! Your username and password are:<br><br>Username: {$user['Username']}<br>Password: {$user['Password']}");
                }

                $response['status'] = 'success';
            } else {
                handleSQLError($stmt);
            }
        } else {
            handleSQLPrepareError($conn);
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE useracc SET Status = 'Rejected' WHERE UserID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->close();

                // Fetch user details for sending email
                $stmt = $conn->prepare("SELECT email, fname, lname FROM useracc WHERE UserID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($user) {
                    sendEmail($user, 'Rejected', "Your account has been rejected. Please contact support for further assistance.");
                }

                $response['status'] = 'success';
            } else {
                handleSQLError($stmt);
            }
        } else {
            handleSQLPrepareError($conn);
        }
    }

    $conn->close();
    echo json_encode($response);
}

// Helper function to send emails
function sendEmail($user, $status, $messageBody) {
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'proftal2024@gmail.com';
        $mail->Password   = 'ytkj saab gnkb cxwa'; // Change this for security
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('proftal2024@gmail.com', 'ProfTal');
        $mail->addAddress($user['email'], "{$user['fname']} {$user['lname']}");

        // Content
        $mail->isHTML(true);
        $mail->Subject = "Account $status";
        $mail->Body    = "Dear {$user['fname']} {$user['lname']},<br><br>$messageBody<br><br>Best regards,<br>Admin";

        $mail->send();
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
    }
}

// Helper function to handle SQL errors
function handleSQLError($stmt) {
    error_log("SQL Error: " . $stmt->error);
    $response['error'] = "SQL Error: " . $stmt->error;
}

// Helper function to handle SQL preparation errors
function handleSQLPrepareError($conn) {
    error_log("SQL Prepare Error: " . $conn->error);
    $response['error'] = "SQL Prepare Error: " . $conn->error;
}
?>
