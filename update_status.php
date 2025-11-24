<?php
header('Content-Type: application/json');
include 'db_connect.php';

$data = json_decode(file_get_contents("php://input"), true);

if(isset($data['id']) && isset($data['status'])) {
    $id = $data['id'];
    $status = $data['status'];

    $sql = "UPDATE bookings SET status='$status' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $conn->error]);
    }
}
$conn->close();
?>