<?php
$servername = "doc-map2024_docmap";
$username = "mysql";
$password = "9d9a12fd2fb2264975bf";
$dbname = "docmap1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
