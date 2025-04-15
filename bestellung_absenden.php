<?php
session_start();

// Daten absichern
$name = htmlspecialchars($_POST['name']);
$email = htmlspecialchars($_POST['email']);
$adresse = htmlspecialchars($_POST['adresse']);
$zahlung = htmlspecialchars($_POST['zahlung']);
$total = htmlspecialchars($_POST['total']);

$cart = $_SESSION['cart'] ?? [];

$bestelltext = "Neue Bestellung von $name\n\nAdresse: $adresse\nE-Mail: $email\nZahlungsmethode: $zahlung\n\n";

$gesamt = 0;
if (!empty($cart)) {
    include __DIR__ . '/config/config.php';

    $ids = array_column($cart, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $mengen = [];
    foreach ($cart as $item) {
        $mengen[$item['id']] = $item['quantity'];
    }

    foreach ($items as $item) {
        $anzahl = $mengen[$item['id']] ?? 1;
        $zeile = "{$item['name']} × $anzahl = € " . number_format($item['price'] * $anzahl, 2, ',', '.');
        $bestelltext .= "$zeile\n";
        $gesamt += $item['price'] * $anzahl;
    }

    $bestelltext .= "\nGesamt: € " . number_format($gesamt, 2, ',', '.');
}

// Mail senden (lokal evtl. deaktiviert, produktiv: SMTP nutzen)
$an = "deine@emailadresse.de";
$betreff = "Neue Bestellung von $name";
$headers = "From: $email";

mail($an, $betreff, $bestelltext, $headers);

// Session aufräumen
unset($_SESSION['cart']);
unset($_SESSION['total_price']);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bestellung abgeschlossen</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: sans-serif;
            text-align: center;
            padding: 3rem;
        }

        .box {
            background: white;
            max-width: 500px;
            margin: auto;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
        }

        h2 {
            color: green;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>🎉 Bestellung erfolgreich!</h2>
    <p>Vielen Dank, <strong><?= $name ?></strong>! Deine Bestellung im Wert von <strong>€ <?= number_format($gesamt, 2, ',', '.') ?></strong> wurde erfolgreich übermittelt.</p>
    <a href="index.php" class="btn btn-primary mt-3">Zurück zur Speisekarte</a>
</div>

</body>
</html>
