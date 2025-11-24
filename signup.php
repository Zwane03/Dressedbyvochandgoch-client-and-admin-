<?php
// Include the database connection configuration
require_once 'db_config.php';

// Check if the request method is POST (meaning the form was submitted)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize and Validate Input
    // Use filter_input for security and validation
    $fullName = filter_input(INPUT_POST, 'signupName', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'signupEmail', FILTER_SANITIZE_EMAIL);
    $password = $_POST['signupPassword']; // Raw password input

    // Basic validation check (you should add more robust checks)
    if (empty($fullName) || empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Handle error: redirect back or display a message
        header("Location: index.html?error=invalid_input");
        exit();
    }

    // 2. Hash the Password
    // IMPORTANT: Use password_hash() for secure password storage
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 3. Prepare SQL Statement to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password_hash) VALUES (?, ?, ?)");
    
    // Check if the statement preparation was successful
    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    // Bind parameters to the placeholder markers (?)
    $stmt->bind_param("sss", $fullName, $email, $passwordHash);

    // 4. Execute the statement and check for success
    if ($stmt->execute()) {
        // Success: Redirect to a success page or back to the index
        header("Location: index.html?status=signup_success");
    } else {
        // Error: Check for duplicate email error (code 1062 for MySQL)
        if ($conn->errno == 1062) {
             header("Location: index.html?error=email_exists");
        } else {
            // Other database error
            header("Location: index.html?error=db_error");
        }
    }

    // 5. Close the statement and connection
    $stmt->close();
    $conn->close();

} else {
    // If someone tries to access this script directly without submitting the form
    header("Location: index.html");
    exit();
}
?>