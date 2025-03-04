<?php
$host = "127.0.0.1";  // Use IP instead of "localhost" to avoid issues
$dbname = "food_store";  // Your database name
$username = "root";  // Default MySQL user in XAMPP
$password = "";  // Default XAMPP password is empty

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
