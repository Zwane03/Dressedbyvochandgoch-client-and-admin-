<?php
$servername = "localhost";
$username = "root"; // Default XAMPP user
$password = "";     // Default XAMPP password is empty
$dbname = "dressed_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>