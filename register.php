<?php

// Set headers to allow cross-origin requests (necessary for local testing)

header("Access-Control-Allow-Origin: *");

header("Content-Type: application/json; charset=UTF-8");

header("Access-Control-Allow-Methods: POST");

header("Access-Control-Max-Age: 3600");

header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");



// --- 1. Database Configuration (XAMPP Default) ---

$host = "localhost";

$db_name = "dressedbygochandvoch"; // Your specified database name

$username = "root";               // Default XAMPP MySQL user

$password = "";                   // Default XAMPP MySQL password (empty)



$conn = null;



try {

    // Attempt to connect to the database

    $conn = new PDO("mysql:host={$host};dbname={$db_name}", $username, $password);

    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $exception) {

    http_response_code(500);

    echo json_encode(["message" => "Database connection error: " . $exception->getMessage()]);

    exit();

}



// --- 2. Get Data from Client ---

$data = json_decode(file_get_contents("php://input"));



// Check if data is valid and required fields are present

if (empty($data->fullName) || empty($data->email) || empty($data->password)) {

    http_response_code(400);

    echo json_encode(["message" => "Missing required fields (Full Name, Email, or Password)."]);

    exit();

}



$fullName = htmlspecialchars(strip_tags($data->fullName));

$email = htmlspecialchars(strip_tags($data->email));

$plainPassword = $data->password;



// --- 3. Check for Existing Email ---

$query = "SELECT user_id FROM users WHERE email = ? LIMIT 1";

$stmt = $conn->prepare($query);

$stmt->bindParam(1, $email);

$stmt->execute();



if ($stmt->rowCount() > 0) {

    http_response_code(409); // Conflict

    echo json_encode(["message" => "Registration failed. This email is already registered."]);

    exit();

}



// --- 4. Securely Hash the Password ---

// Use PASSWORD_DEFAULT for best practices (currently bcrypt)

$passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);



// --- 5. Insert New User Record ---

$query = "INSERT INTO users (full_name, email, password_hash) VALUES (:full_name, :email, :password_hash)";



$stmt = $conn->prepare($query);



// Bind the values

$stmt->bindParam(':full_name', $fullName);

$stmt->bindParam(':email', $email);

$stmt->bindParam(':password_hash', $passwordHash);



if ($stmt->execute()) {

    http_response_code(201); // Created

    echo json_encode(["message" => "User successfully created."]);

} else {

    http_response_code(500);

    echo json_encode(["message" => "Failed to create user."]);

}

?>
