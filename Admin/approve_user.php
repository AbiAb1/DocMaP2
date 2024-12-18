<?php
include 'connection.php';

require '../vendor/autoload.php';  // Path to Composer autoload file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

$response = array('status' => 'error');

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
                $response['status'] = 'success';
                $stmt->close();

                // Fetch user details for email
                $stmt = $conn->prepare("SELECT email, fname, lname, Username, Password FROM useracc WHERE UserID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($user) {
                    // Send email for approval
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'proftal2024@gmail.com'; // SMTP username
                        $mail->Password   = 'ytkj saab gnkb cxwa'; // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587; // TCP port to connect to

                        // Recipients
                        $mail->setFrom('proftal2024@gmail.com', 'ProfTal');
                        $mail->addAddress($user['email'], $user['fname'] . ' ' . $user['lname']); // Add recipient

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Account Approved';
                        $mail->Body    = "Dear {$user['fname']} {$user['lname']},<br><br>Your account has been approved! Your username and password are:<br><br>Username: {$user['Username']}<br>Password: {$user['Password']}<br><br>You may now login to the ProfTal.<br><br>Best regards,<br>Admin";

                        $mail->send();
                        $response['email_status'] = 'sent'; // Add email status
                    } catch (Exception $e) {
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                        $response['email_status'] = 'failed'; // Add email status
                        $response['email_error'] = $mail->ErrorInfo; // Add error info
                    }
                }
            } else {
                error_log("SQL Error: " . $stmt->error);
                $response['error'] = "SQL Error: " . $stmt->error;
            }
        } else {
            error_log("SQL Prepare Error: " . $conn->error);
            $response['error'] = "SQL Prepare Error: " . $conn->error;
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE useracc SET Status = 'Rejected' WHERE UserID = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $response['status'] = 'success';
                $stmt->close();

                // Fetch user details for email
                $stmt = $conn->prepare("SELECT email, fname, lname FROM useracc WHERE UserID = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $user = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if ($user) {
                    // Send email for rejection
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'proftal2024@gmail.com'; // SMTP username
                        $mail->Password   = 'ytkj saab gnkb cxwa'; // SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587; // TCP port to connect to

                        // Recipients
                        $mail->setFrom('proftal2024@gmail.com', 'ProfTal');
                        $mail->addAddress($user['email'], $user['fname'] . ' ' . $user['lname']); // Add recipient

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Account Rejected';
                        $mail->Body    = "Dear {$user['fname']} {$user['lname']},<br><br>Your account has been rejected. Please contact support for further assistance.<br><br>Best regards,<br>Admin";

                        $mail->send();
                        $response['email_status'] = 'sent'; // Add email status
                    } catch (Exception $e) {
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                        $response['email_status'] = 'failed'; // Add email status
                        $response['email_error'] = $mail->ErrorInfo; // Add error info
                    }
                }
            } else {
                error_log("SQL Error: " . $stmt->error);
                $response['error'] = "SQL Error: " . $stmt->error;
            }
        } else {
            error_log("SQL Prepare Error: " . $conn->error);
            $response['error'] = "SQL Prepare Error: " . $conn->error;
        }
    }
    $conn->close();
    echo json_encode($response);
}
?>
