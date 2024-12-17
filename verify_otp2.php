<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
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
        <h2>Change Credentials</h2>
        <div class="progress-bar">
            <div class="progress-container">
                <div class="progress-step completed">1</div>
                <div class="progress-line completed "></div>
                <div class="progress-step completed">2</div>
                <div class="progress-line"></div>
                <div class="progress-step">3</div>
            </div>
        </div>
        <p>Enter the OTP that was sent to your email.</p>
        <form action="verify_otp2.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
            <input type="hidden" name="userId" value="<?php echo htmlspecialchars($_GET['userId']); ?>">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <button type="submit">Verify OTP</button>
        </form>

    </div>
</div>
</body>
</html>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the connection file
include 'connection.php'; // Ensure the path is correct

// Check if request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = htmlspecialchars($_POST['email']);
    $otp = htmlspecialchars($_POST['otp']);
    $userId = intval($_POST['userId']); // Get userId from POST

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>
                Swal.fire({
                    title: 'Invalid Email Format',
                    text: 'Please enter a valid email address.',
                    icon: 'error'
                });
              </script>";
        exit;
    }

    // Check if email matches the user ID
    $sql = 'SELECT UserID FROM useracc WHERE email = ? AND UserID = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $email, $userId); // Bind email and userId
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $userId = $user['UserID'];

        // Check OTP in the database
        $sql = 'SELECT otp, created_at FROM otp WHERE UserID = ? ORDER BY otp_ID DESC LIMIT 1';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $otpData = $result->fetch_assoc();

        if ($otpData) {
            $storedOtp = $otpData['otp'];
            $createdAt = $otpData['created_at'];
            $expiry = date("Y-m-d H:i:s", strtotime($createdAt . ' + 10 minutes')); // OTP expires in 10 minutes

            if ($otp === $storedOtp && date("Y-m-d H:i:s") <= $expiry) {
                // OTP is valid, redirect to password reset page
                echo "<script>
                        Swal.fire({
                            title: 'OTP Verified',
                            text: 'OTP verified successfully.',
                            icon: 'success'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'reset_pass2.php?email=$email';
                            }
                        });
                      </script>";
            } else {
                echo "<script>
                        Swal.fire({
                            title: 'Invalid or Expired OTP',
                            text: 'Invalid OTP or OTP has expired.',
                            icon: 'error'
                        });
                      </script>";
            }
        } else {
            echo "<script>
                    Swal.fire({
                        title: 'No OTP Found',
                        text: 'No OTP found for this email address.',
                        icon: 'error'
                    });
                  </script>";
        }
    } else {
        echo "<script>
                Swal.fire({
                    title: 'No Account Found',
                    text: 'No account found with that email address.',
                    icon: 'error'
                });
              </script>";
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>




</body>
</html>
