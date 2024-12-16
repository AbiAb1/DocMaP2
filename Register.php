<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
            background-image: url("assets/images/portfolio-left-dec.jpg"), url("assets/images/portfolio-right-dec.jpg");
        }

        .container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            width: 1100px;
            max-width: 100%;
        }

        .illustration {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .illustration img {
            max-width: 80%;
            height: auto;
            border-radius: 8px;
        }

        .registration-form h2 {
            margin-bottom: 20px;
            font-size: 28px;
            color: #9B2035;
            font-weight: 600;
        }

        .registration-form button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s, box-shadow 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .registration-form button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .registration-form button:hover:not(:disabled) {
            background-color: #0056b3;
        }

        .registration-form button:active:not(:disabled) {
            background-color: #9B2035 !important;
            box-shadow: inset 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .checkbox-list label {
            display: inline-block;
            white-space: normal;
        }

        .checkbox-list input[type="checkbox"] {
            vertical-align: middle;
        }
        .error-message {
            color: red;
            font-size: 0.875em;
            display: none;
        }
    </style>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-6 illustration">
            <img src="assets/images/Sign-up-amico.png" alt="Illustration">
        </div>
        <div class="col-md-6">
            <div class="registration-form">
                <h2>Register</h2>
                <form id="registration-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="firstname">First Name:</label>
                            <input type="text" id="firstname" name="firstname" class="form-control" required>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="middleinitial">M.I.:</label>
                            <input type="text" id="middleinitial" name="middleinitial" maxlength="1" class="form-control">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="lastname">Last Name:</label>
                            <input type="text" id="lastname" name="lastname" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="birthday">Birthday:</label>
                            <input type="date" id="birthday" name="birthday" class="form-control" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="mobile">Mobile Number:</label>
                            <input type="tel" id="mobile" name="mobile" class="form-control" maxlength="11" required>
                            <span id="mobile-error" class="error-message">Mobile number must be 11 digits and start with '09'.</span>                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="gender">Sex:</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group col-md-7">
                            <label for="ranking">Teacher Ranking:</label>
                            <select id="ranking" name="ranking" class="form-control" required>
                                <option value="">Select Ranking</option>
                                <option value="Teacher I">Teacher I</option>
                                <option value="Teacher II">Teacher II</option>
                                <option value="Teacher III">Teacher III</option>
                                <option value="Master Teacher I">Master Teacher I</option>
                                <option value="Master Teacher II">Master Teacher II</option>
                                <option value="Master Teacher III">Master Teacher III</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">   
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                        <span id="email-error" class="error-message">Please enter a valid Google email address.</span>
                        <span id="email-duplicate-error" class="error-message">This email is already taken.</span>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea id="address" name="address" class="form-control" rows="2" style="resize: vertical;" required></textarea>
                    </div>

                    <div class="checkbox-list">
                        <input type="checkbox" id="certification1" name="certification1" required>
                        <label for="certification1">I certify that all information provided is correct.</label><br>
                    </div>
                    <button type="submit" id="register-button" disabled style="margin-top: 20px;">Register</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function validateMobile() {
        const mobileInput = document.getElementById('mobile');
        const mobileError = document.getElementById('mobile-error');
        const mobileValue = mobileInput.value;

        // Check if the mobile number starts with '09' and is exactly 11 digits
        const isValidMobile = /^09\d{9}$/.test(mobileValue);

        if (!isValidMobile && mobileValue.length > 0) {
            mobileError.style.display = 'block';
            mobileInput.classList.add('is-invalid');
        } else {
            mobileError.style.display = 'none';
            mobileInput.classList.remove('is-invalid');
        }
    }

    function validateForm() {
        const form = document.getElementById('registration-form');
        const registerButton = document.getElementById('register-button');
        
        const firstname = document.getElementById('firstname').value;
        const lastname = document.getElementById('lastname').value;
        const gender = document.getElementById('gender').value;
        const birthday = document.getElementById('birthday').value;
        const mobile = document.getElementById('mobile').value;
        const address = document.getElementById('address').value;
        const email = document.getElementById('email').value;
        const certification1 = document.getElementById('certification1').checked;

        const isValidMobile = /^09\d{9}$/.test(mobile);
        const isValidEmail = /^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(email);

        // Enable the register button only if all fields are valid
        registerButton.disabled = !(firstname && lastname && gender && birthday && isValidMobile && isValidEmail && address && certification1);
    }

    const formElements = document.querySelectorAll('#registration-form input, #registration-form select');
    formElements.forEach(element => {
        element.addEventListener('input', () => {
            validateMobile();
            validateEmail();
            validateForm();
        });
    });

    document.addEventListener('DOMContentLoaded', validateForm);

    function validateEmail() {
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('email-error');
        const emailDuplicateError = document.getElementById('email-duplicate-error');
        const emailValue = emailInput.value;

        // Check if it's a valid Google email
        const isValidEmail = /^[a-zA-Z0-9._%+-]+@gmail\.com$/.test(emailValue);
        
        if (!isValidEmail && emailValue.length > 0) {
            emailError.style.display = 'block';
            emailInput.classList.add('is-invalid');
            emailDuplicateError.style.display = 'none'; // Hide duplicate error if email is invalid
        } else {
            emailError.style.display = 'none';
            emailInput.classList.remove('is-invalid');
            if (isValidEmail) {
                checkEmailDuplicate(emailValue); // Check for duplicates only if email format is valid
            }
        }
    }

    function checkEmailDuplicate(email) {
        const emailDuplicateError = document.getElementById('email-duplicate-error');
        const emailInput = document.getElementById('email');
        const registerButton = document.getElementById('register-button');

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "check_email.php", true); // Server-side script to check for duplicate email
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.exists) {
                    emailDuplicateError.style.display = 'block';
                    emailInput.classList.add('is-invalid');
                    registerButton.disabled = true; // Disable register button if duplicate email found
                } else {
                    emailDuplicateError.style.display = 'none';
                    emailInput.classList.remove('is-invalid');
                    validateForm(); // Re-enable the register button if no duplicate
                }
            }
        };
        xhr.send("email=" + email);
    }

    document.getElementById('email').addEventListener('input', validateEmail);
    document.addEventListener('DOMContentLoaded', function() {
        const birthdayInput = document.getElementById('birthday');
        
        // Calculate the minimum date for a person to be at least 20 years old
        const today = new Date();
        const minYear = today.getFullYear() - 20;
        const minDate = new Date(minYear, today.getMonth(), today.getDate());
        
        // Format the minDate as yyyy-mm-dd
        const minDateFormatted = minDate.toISOString().split('T')[0];
        
        // Set the min attribute of the date input
        birthdayInput.setAttribute('max', minDateFormatted);
    });
</script>


<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the Composer autoload file
require 'vendor/autoload.php'; // Ensure this path is correct

// Include your database connection file
include 'connection.php'; // Assuming this file contains the database connection setup

use PhpOffice\PhpSpreadsheet\IOFactory;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to generate a random string for password
function generateRandomString($length = 4) {
    return bin2hex(random_bytes($length)); // Generates a random string of specified length
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $firstname = htmlspecialchars($_POST['firstname']);
    $middleinitial = !empty($_POST['middleinitial']) ? htmlspecialchars($_POST['middleinitial']) : 'N/A';
    $lastname = htmlspecialchars($_POST['lastname']);
    $gender = htmlspecialchars($_POST['gender']);
    $birthday = htmlspecialchars($_POST['birthday']);
    $mobile = htmlspecialchars($_POST['mobile']);
    $address = htmlspecialchars($_POST['address']);
    $email = htmlspecialchars($_POST['email']);
    $ranking = htmlspecialchars($_POST['ranking']);

    // Load the Excel file
    $spreadsheet = IOFactory::load('Admin/TeacherData/LNHS-Teachers.xlsx'); // Update with your Excel file path
    $worksheet = $spreadsheet->getActiveSheet();
    
    $found = false; // Flag to check if data is found

    // Loop through rows in the Excel file
    foreach ($worksheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Iterate all cells
        
        $phoneNumber = ''; // Placeholder for phone number
        $emailAddress = ''; // Placeholder for email

        foreach ($cellIterator as $cell) {
            $columnIndex = $cell->getColumn(); // Get the column index
            $cellValue = $cell->getValue(); // Get the cell value

            // Assuming phone number is in column B (2) and email is in column C (3)
            if ($columnIndex == 'E') { // Change to your actual column index for phone number
                $phoneNumber = $cellValue;
            }
            if ($columnIndex == 'F') { // Change to your actual column index for email
                $emailAddress = $cellValue;
            }
        }

        // Check if there is a match
        if ($mobile === $phoneNumber && $email === $emailAddress) {
            $found = true;
            break; // Exit loop if a match is found
        }
    }

    // Check if the email already exists in the useracc table
    $emailCheckQuery = "SELECT * FROM useracc WHERE Email = ?";
    $emailCheckStmt = $conn->prepare($emailCheckQuery);
    $emailCheckStmt->bind_param("s", $email);
    $emailCheckStmt->execute();
    $emailCheckResult = $emailCheckStmt->get_result();

    if ($emailCheckResult->num_rows > 0) {
        // Email already exists
        echo "
            <script>
                Swal.fire({
                    title: 'Email Exists',
                    text: 'The email $email is already registered. Please use a different email.',
                    icon: 'error'
                });
            </script>
        ";
    } else {
        // Generate a unique username and password
        $username = strtolower($firstname[0] . $lastname);
        $password = generateRandomString(4); // Generates an 8-character password
        $hashedPassword = md5($password); // MD5 hash for the password

        // Determine status based on whether a match was found
        $status = $found ? 'Approved' : 'Pending';

        // Use prepared statements for database insertion
        $stmt = $conn->prepare("INSERT INTO useracc (Username, Password, fname, mname, lname, bday, sex, address, email, Rank, mobile, Status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssssss", $password, $hashedPassword, $firstname, $middleinitial, $lastname, $birthday, $gender, $address, $email, $ranking, $mobile, $status);
        if ($stmt->execute()) {
            // Fetch user details from useracc table
            $userDetailsQuery = "SELECT email, fname, lname FROM useracc WHERE email = ?";
            $userDetailsStmt = $conn->prepare($userDetailsQuery);
            $userDetailsStmt->bind_param("s", $email);
            $userDetailsStmt->execute();
            $userDetailsResult = $userDetailsStmt->get_result();
        
            if ($userDetailsResult->num_rows > 0) {
                $userDetails = $userDetailsResult->fetch_assoc();
        
                // Send email if the status is approved
                if ($status === "Approved") {
                    // Send email for approval
                    $mail = new PHPMailer(true);
                    try {
                        // Server settings
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'proftal2024@gmail.com'; // SMTP username
                        $mail->Password   = 'ytkj saab gnkb cxwa'; // SMTP password (consider using an environment variable)
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587; // TCP port to connect to
        
                        // Recipients
                        $mail->setFrom('proftal2024@gmail.com', 'ProfTal');
                        $mail->addAddress($userDetails['email'], $userDetails['fname'] . ' ' . $userDetails['lname']); // Add recipient
        
                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = 'Account Approved';
                        $mail->Body    = "Dear {$userDetails['fname']} {$userDetails['lname']},<br><br>Your account has been approved! Your username and password are:<br><br>Username: $password<br>Password: $password<br><br>You may now login to ProfTal.<br><br>Best regards,<br>Admin";
        
                        $mail->send();
                        echo "
                            <script>
                                Swal.fire({
                                    title: 'Congratulations!',
                                    text: 'Your account has been approved!. An email has been sent to your account.',
                                    icon: 'success'
                                }).then(function() {
                                    window.location.href = 'index.php'; // Redirect after confirmation
                                });
                            </script>
                        ";
                    } catch (Exception $e) {
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                        echo "
                            <script>
                                Swal.fire({
                                    title: 'Email Error',
                                    text: 'Email could not be sent.',
                                    icon: 'error'
                                });
                            </script>
                        ";
                    }
                } else {
                    // If the status is pending, show a different alert
                    echo "
                        <script>
                            Swal.fire({
                                title: 'Thank You for Registering!',
                                text: 'Your account is waiting to be approved by the admin.',
                                icon: 'info'
                            }).then(function() {
                                window.location.href = 'index.php'; // Redirect after confirmation
                            });
                        </script>
                    ";
                }
            }
        } else {
            echo "
                <script>
                    Swal.fire({
                        title: 'Registration Failed',
                        text: 'An error occurred while creating your account.',
                        icon: 'error'
                    });
                </script>
            ";
        }

        $stmt->close();
        $userDetailsStmt->close();
    }

    $emailCheckStmt->close();
    $conn->close();
}
?>







</body>
</html>
