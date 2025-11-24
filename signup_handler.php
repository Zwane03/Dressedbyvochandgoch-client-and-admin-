<?php
// Configuration for the database connection
$servername = "localhost";
$username = "root"; // Default XAMPP username
$password = "";     // Default XAMPP password (often empty)
$dbname = "my_app_db"; // Replace with your actual database name

// 1. Establish a database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form data was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Sanitize and retrieve form data
    $fullName = $conn->real_escape_string($_POST['fullName']);
    $email = $conn->real_escape_string($_POST['email']);
    

    $raw_password = $conn->real_escape_string($_POST['password']); 

    // 2. Prepare the SQL INSERT statement
    $sql = "INSERT INTO users (fullName, email, password) VALUES ('$fullName', '$email', '$raw_password')";

    // 3. Execute the statement
    if ($conn->query($sql) === TRUE) {
        // Redirect upon successful creation
        header("Location: select_portal.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    // If accessed directly without form submission
    echo "Access denied.";
}

// 4. Close the connection
$conn->close();
?>
