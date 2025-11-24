<?php
header('Content-Type: application/json');
include 'db_connect.php';

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);

if($data) {
    $booking_ref = 'BK' . time(); // Generate unique ID
    $name = $data['name'];
    $email = $data['email'];
    $phone = $data['phone'];
    $service = $data['service'];
    $date = $data['date'];
    $notes = $data['notes'];

    $sql = "INSERT INTO bookings (booking_ref, customer_name, email, phone, service_type, booking_date, notes) 
            VALUES ('$booking_ref', '$name', '$email', '$phone', '$service', '$date', '$notes')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["message" => "Booking created successfully", "ref" => $booking_ref]);
    } else {
        echo json_encode(["message" => "Error: " . $conn->error]);
    }
}
$conn->close();
?>