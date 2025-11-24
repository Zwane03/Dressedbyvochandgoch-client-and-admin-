<?php
header('Content-Type: application/json');
include 'db_connect.php';

$sql = "SELECT * FROM bookings ORDER BY created_at DESC";
$result = $conn->query($sql);

$bookings = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
}

echo json_encode($bookings);
$conn->close();
?>