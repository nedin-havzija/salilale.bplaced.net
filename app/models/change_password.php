<?php
session_start();
include __DIR__ . '/../../config/config.php';

if (!isset($_SESSION["admin"]) || $_SESSION["admin"] !== true) {
    header("Location: /food_website/index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!empty($_POST["user_id"]) && !empty($_POST["new_password"])) {
        $userId = $_POST["user_id"];
        $newPassword = trim($_POST["new_password"]);
        $hashedPassword = md5($newPassword); // Match login logic

        $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            "password" => $hashedPassword,
            "id" => $userId
        ]);

        header("Location: /food_website/admin.php?message=password_updated");
        exit;
    } else {
        echo "<p style='color:red;'>Fehlende Eingaben für Passwortänderung.</p>";
    }
} else {
    echo "<p style='color:red;'>Ungültige Anfrage.</p>";
}
?>
