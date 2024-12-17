<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" type="image/png" href="img/Logo/docmap-logo-1.png">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.5s;
        }

        .container {
            display: flex;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 900px;
            max-width: 100%;
            height: 500px;
            
        }

        .illustration {
            width: 50%;
            background: url("assets/images/passw.png") no-repeat center center;
            background-size: cover;
            position: relative;
        }

        .forgot-password-container {
            padding: 60px;
            width: 50%;
            box-sizing: border-box;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .forgot-password-container img {
            width: 100px;
            margin-bottom: 20px;
            padding-left:110px;
        }

        .forgot-password-container h2 {
            margin-bottom: 20px;
            font-size: 28px;
            font-weight: 600;
            color: #333;
        }

        .progress-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            position: relative;
        }

        .progress-container {
            display: flex;
            align-items: center;
            width: 100%;
            position: relative;
        }

        .progress-step {
            width: 40px;
            height: 40px;
            background-color: #D3D3D3;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }

        .progress-step.completed {
            background-color: #9B2035;
        }

        .progress-line {
            height: 4px;
            flex-grow: 1;
            background-color: #ddd;
            position: relative;
            top: 50%;
            transform: translateY(-50%);
            z-index: 0;
            margin: 0 10px;
        }

        .progress-line.completed {
            background-color: #861c2e;
        }

        .forgot-password-container p {
            margin-bottom: 20px;
            font-size: 14px;
            color: #777;
        }

        .forgot-password-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        .forgot-password-container button {
            width: 100%;
            padding: 12px;
            background-color: #9B2035;
            border: none;
            border-radius: 90px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
            display: flex;
            justify-content: center; /* Center the button text */
            margin-left:15px;
        }

        .forgot-password-container button:hover {
            background-color: #861c2e;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="illustration"></div>
        <div class="forgot-password-container">
            <img src="img/Logo/docmap-logo-2.png" alt="Logo">
            <h2>Password Recovery</h2>
            <div class="progress-bar">
                <div class="progress-container">
                    <div class="progress-step completed">1</div>
                    <div class="progress-line  "></div>
                    <div class="progress-step ">2</div>
                    <div class="progress-line"></div>
                    <div class="progress-step">3</div>
                   
                </div>
            </div>
            <p>Enter your email address to receive an OTP.</p>
            <form action="forgot_pass.php" method="POST">
                <input type="email" name="email" placeholder="Your email address" required>
                <button type="submit">Send OTP</button>
            </form>
        </div>
    </div>

    <?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the path to autoload.php is correct
require 'vendor/autoload.php'; // Include PHPMailer
require 'connection.php'; // Include your connection.php file to use the existing connection setup

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>Swal.fire('Invalid email format', '', 'error');</script>";
        exit;
    }

    // Check if email exists and get user ID
    $sql = 'SELECT UserID FROM useracc WHERE email = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $userId = $user['UserID'];

        // Generate a unique OTP
        $otp = rand(100000, 999999);
        $createdAt = date("Y-m-d H:i:s");

        // Insert OTP into the database
        $sql = 'INSERT INTO otp (otp, created_at, UserID) VALUES (?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $otp, $createdAt, $userId);
        $stmt->execute();

        // Send OTP email using PHPMailer
        $mail = new PHPMailer(true); // Passing `true` enables exceptions

        try {
            //Server settings
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = 'proftal2024@gmail.com';                // SMTP username
            $mail->Password   = 'ytkj saab gnkb cxwa';                  // SMTP password (App Password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption
            $mail->Port       = 587;                                    // TCP port to connect to

            //Recipients
            $mail->setFrom('proftal2024@gmail.com', 'DocMap');
            $mail->addAddress($email);                                  // Add a recipient

            // Content
            $mail->isHTML(true);                                        // Set email format to HTML
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP code is: $otp<br><br>It will expire in 10 minutes.";
            $mail->AltBody = "Your OTP code is: $otp\n\nIt will expire in 10 minutes.";

            $mail->send();
            echo "<script>
                    Swal.fire({
                        title: 'OTP Sent',
                        text: 'An OTP has been sent to your email address.',
                        icon: 'success'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'verify_otp.php?email=$email';
                        }
                    });
                  </script>";
        } catch (Exception $e) {
            echo "<script>Swal.fire('Failed to send OTP', 'Mailer Error: {$mail->ErrorInfo}', 'error');</script>";
        }
    } else {
        echo "<script>Swal.fire('No account found', 'No account found with that email address.', 'error');</script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
