<?php
include "config.php";

try {
    $stmt = $conn->query("SHOW TABLES");
    echo "✅ Database connection successful! Tables found:<br>";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . implode("", $row) . "<br>";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
