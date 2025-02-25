<?php
session_start();
if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: index.php"); // Redirect if not logged in
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Panel</title>
</head>
<body>
    <h1>Welcome, Admin!</h1>
    <p>Here you can add, edit, and delete items.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
