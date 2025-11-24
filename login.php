<?php
// Start a session to store user login status
session_start();

// Include the database connection configuration
require_once 'db_config.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Sanitize Input
    $email = filter_input(INPUT_POST, 'loginEmail', FILTER_SANITIZE_EMAIL);
    $password = $_POST['loginPassword']; // Raw password input

    // Basic validation
    if (empty($email) || empty($password) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.html?error=login_invalid_input");
        exit();
    }

    // 2. Prepare SQL Statement: Select user by email
    $stmt = $conn->prepare("SELECT user_id, full_name, email, password_hash FROM users WHERE email = ?");
    
    if ($stmt === false) {
        die("MySQL prepare error: " . $conn->error);
    }

    // Bind parameter and execute
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // 3. Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // 4. Verify Password
        // Use password_verify() to check the submitted password against the stored hash
        if (password_verify($password, $user['password_hash'])) {
            
            // Success: Password is correct. Set session variables.
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['email'] = $user['email'];

            // Redirect to the home page (index.html) with a success status
            header("Location: index.html?status=login_success");
        } else {
            // Failure: Invalid password
            header("Location: index.html?error=login_failed");
        }
    } else {
        // Failure: No user found with that email
        header("Location: index.html?error=login_failed");
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