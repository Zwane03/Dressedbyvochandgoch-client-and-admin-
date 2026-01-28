<?php
include 'db.php';
header('Content-Type: application/json');
$sql = "SELECT id, item, qty, amount, description FROM sales ORDER BY created_at DESC";
$res = $conn->query($sql);
$out = [];
while($row = $res->fetch_assoc()) $out[] = $row;
echo json_encode($out);
?>