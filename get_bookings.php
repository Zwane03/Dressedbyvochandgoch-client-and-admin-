<?php
include 'db.php';
header('Content-Type: application/json');
$sql = "SELECT id, customer, DATE_FORMAT(datetime, '%Y-%m-%d %H:%i') as datetime, service, status FROM bookings ORDER BY datetime ASC";
$res = $conn->query($sql);
$out = [];
while($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>