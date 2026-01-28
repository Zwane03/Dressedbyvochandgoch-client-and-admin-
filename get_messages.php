<?php
include 'db.php';
header('Content-Type: application/json');
$sql = "SELECT id, customer_name, message, created_at FROM messages ORDER BY created_at DESC LIMIT 20";
$res = $conn->query($sql);
$out = [];
while($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>