<?php
include 'connection.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');
$response = array('status' => 'error');

ob_start(); // Start output buffering

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if (!$id || !$action) {
        $response['message'] = 'Invalid input data';
        echo json_encode($response);
        exit;
    }

    try {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE useracc SET Status = 'Approved' WHERE UserID = ?");
            if ($stmt) {
                $stmt->bind_param("i", $id);
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $stmt->close();

                    $stmt = $conn->prepare("SELECT email, fname, lname, Username, Password FROM useracc WHERE UserID = ?");
                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $user = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    if ($user) {
                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtp.gmail.com';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'proftal2024@gmail.com';
                        $mail->Password = 'ytkj saab gnkb cxwa';
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port = 587;

                        $mail->setFrom('proftal2024@gmail.com', 'ProfTal');
                        $mail->addAddress($user['email'], "{$user['fname']} {$user['lname']}");

                        $mail->isHTML(true);
                        $mail->Subject = 'Account Approved';
                        $mail->Body = "Dear {$user['fname']} {$user['lname']},<br>Your account has been approved!<br>";

                        $mail->send();
                        $response['email_status'] = 'sent';
                    }
                }
            } else {
                throw new Exception("SQL Prepare Error: " . $conn->error);
            }
        }
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
        error_log($e->getMessage());
    }
}

ob_end_clean(); // Clean output buffer
echo json_encode($response);
$conn->close();
?>
