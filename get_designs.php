<?php
include 'db.php';
header('Content-Type: application/json');
$sql = "SELECT id, name, price, description, image FROM designs ORDER BY id DESC";
$res = $conn->query($sql);
$out = [];
while($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>