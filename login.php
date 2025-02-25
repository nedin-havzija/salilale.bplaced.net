<?php
session_start();

// Check if login request is received
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Simple authentication (change this later for a real system)
    if ($username === "admin" && $password === "admin") {
        $_SESSION["admin"] = true; // Store login session
        echo "success"; // Send success response
    } else {
        echo "error"; // Send error response
    }
}
?>
