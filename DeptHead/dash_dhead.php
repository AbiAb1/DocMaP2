<?php
session_start();
if (!isset($_SESSION['user_dept_id'])) {
    echo "Department ID not found in session.";
    exit;
}
echo "<h1>Hello, Department Head! Your Department ID is: " . $_SESSION['user_dept_id'] . "</h1>";
?>