<?php
// fetch_data.php

include 'connection.php';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$year = isset($_GET['year']) ? intval($_GET['year']) : null;

$sql = "SELECT COUNT(*) AS count, MONTH(TimeStamp) AS month FROM documents";
if ($year) {
    $sql .= " WHERE YEAR(TimeStamp) = $year";
}
$sql .= " GROUP BY month ORDER BY month";

$result = $conn->query($sql);
$data = array_fill(0, 12, 0); // Initialize data for each month

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[$row['month'] - 1] = intval($row['count']);
    }
}

echo json_encode($data);
$conn->close();
?>
