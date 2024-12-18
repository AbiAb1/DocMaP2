<?php
session_start();
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // Hash the password using MD5
    $hashedPassword = md5($password);

    // Prepare the SQL query to fetch the user details
    $stmt = $conn->prepare("SELECT UserID, Username, Password, Role, Status, dept_ID FROM useracc WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($userID, $db_username, $db_password, $role, $status, $deptID); // Add $deptID to bind result

   
    if ($stmt->fetch()) {
        // Check if the entered password matches the database password (not hashed)
        if ($hashedPassword === $db_password && $status === 'Approved') {
            // Store the user ID and username in session variables
            $_SESSION['user_id'] = $userID;
            $_SESSION['username'] = $db_username;
            $_SESSION['login_success'] = true; // Set session variable for successful login

            // Store dept_ID in session if it exists (only for users who have a department)
            if (!empty($deptID)) {
                $_SESSION['dept_ID'] = $deptID; // Store dept_ID in session
            }

            // Check the role and redirect accordingly
            if ($role === 'Teacher') {
                header("Location: dash.php"); // Redirect to teacher's dashboard
            } elseif ($role === 'Admin') {
                header("Location: Admin/dash_admin.php"); // Redirect to admin's dashboard
            } elseif ($role === 'Department Head') {
                $_SESSION['user_dept_id'] = $deptID; // Store the department ID for Department Head
                header("Location: DeptHead/dash_dhead.php"); // Redirect to department head's dashboard
            }
            exit();
        } else {
            // Invalid password or status not approved
            $_SESSION['login_success'] = false; // Set session variable for invalid login
            header("Location: index.php");
            exit();
        }
    } else {
        // No user found
        $_SESSION['login_success'] = false; // Set session variable for no user found
        header("Location: index.php");
        exit();
    }

    $stmt->close(); // Close the statement
    $conn->close(); // Close the connection
}
?>
