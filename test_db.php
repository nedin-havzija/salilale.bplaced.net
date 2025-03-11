<?php
// Lokale MySQL-Datenbankverbindung
$host = '127.0.0.1'; // Oder 'localhost'
$dbname = 'food_store'; // Ersetze mit deinem lokalen DB-Namen
$username = 'root'; // Standardmäßig 'root' in XAMPP
$password = ''; // Standardmäßig kein Passwort in XAMPP

try {
    // Verbindung zur Datenbank herstellen
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Test-Abfrage: Zeigt vorhandene Tabellen an
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Erfolgreiche Verbindung
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }

} catch (PDOException $e) {
    // Fehler anzeigen, falls Verbindung fehlschlägt
    echo '<p style="color: red;">❌ Fehler bei der Verbindung: ' . $e->getMessage() . '</p>';
}
?>
