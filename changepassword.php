<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php'; // Ensure PHPMailer is installed

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start(); // Start the session

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Please log in to perform this action.']);
    exit;
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $user_id = $_SESSION['user_id'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
        exit;
    }

    // Include your database connection
    require_once 'connection.php'; // Ensure connection.php defines $conn

    // Check if email matches the user ID
    $sql = 'SELECT UserID FROM useracc WHERE email = ? AND UserID = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $userId = $user['UserID'];

        // Generate a unique OTP
        $otp = rand(100000, 999999);
        $createdAt = date("Y-m-d H:i:s");

        // Insert OTP into the database
        $sql = 'INSERT INTO OTP (otp, created_at, UserID) VALUES (?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $otp, $createdAt, $userId);
        $stmt->execute();

        // Send OTP email using PHPMailer
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'proftal2024@gmail.com';
            $mail->Password = 'ytkj saab gnkb cxwa'; // Replace with your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('proftal2024@gmail.com', 'ProfTal');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP code is: $otp<br><br>It will expire in 10 minutes.";
            $mail->AltBody = "Your OTP code is: $otp\n\nIt will expire in 10 minutes.";

            $mail->send();
            echo json_encode(['status' => 'success', 'message' => 'An OTP has been sent to your email address.', 'email' => $email]);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => "Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No account found with that email address for the current user.']);
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
