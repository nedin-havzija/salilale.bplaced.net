<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(["username" => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && md5($password) === $user["password"]) {
        $_SESSION["admin"] = ($user["role"] === "admin");
        $_SESSION["username"] = $username;
        echo "success";
    } else {
        echo "error";
    }
}
?>
