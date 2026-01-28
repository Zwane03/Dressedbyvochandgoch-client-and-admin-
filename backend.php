<?php
include 'db.php';
header('Content-Type: application/json');
$type = $_POST['type'] ?? '';

if($type === 'appointment'){
    $name = $_POST['name'] ?? '';
    $datetime = $_POST['datetime'] ?? '';
    $service = $_POST['service'] ?? '';
    $stmt = $conn->prepare('INSERT INTO bookings (customer, datetime, service, status) VALUES (?, ?, ?, "Pending")');
    $stmt->bind_param('sss',$name,$datetime,$service);
    if($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Appointment added successfully']);
    else echo json_encode(['status'=>'error','message'=>$stmt->error]);
    exit;
}

if($type === 'design'){
    $name = $_POST['designName'] ?? '';
    $price = $_POST['price'] ?? 0;
    $desc = $_POST['desc'] ?? '';
    // handle file upload
    $imageName = '';
    if(isset($_FILES['image']) && $_FILES['image']['error']===0){
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = uniqid('design_') . '.' . $ext;
        $dest = __DIR__ . '/uploads/' . $imageName;
        if(!move_uploaded_file($_FILES['image']['tmp_name'], $dest)){
            echo json_encode(['status'=>'error','message'=>'Failed to move uploaded file']); exit;
        }
    }
    $stmt = $conn->prepare('INSERT INTO designs (name, price, description, image) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('sdss', $name, $price, $desc, $imageName);
    if($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Design uploaded successfully']);
    else echo json_encode(['status'=>'error','message'=>$stmt->error]);
    exit;
}

if($type === 'update_booking'){
    $id = $_POST['id'] ?? 0;
    $status = $_POST['status'] ?? '';
    $stmt = $conn->prepare('UPDATE bookings SET status=? WHERE id=?');
    $stmt->bind_param('si', $status, $id);
    if($stmt->execute()) echo json_encode(['status'=>'success','message'=>'Booking updated']);
    else echo json_encode(['status'=>'error','message'=>$stmt->error]);
    exit;
}

echo json_encode(['status'=>'error','message'=>'Unknown request']);
?>