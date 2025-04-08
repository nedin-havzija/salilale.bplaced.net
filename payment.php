<?php
session_start();
$total = $_SESSION['total_price'] ?? ($_GET['total'] ?? 0);
$shipping = 6.90;
$tax_rate = 0.061;
$tax = $total * $tax_rate;
$total_with_tax = $total + $shipping + $tax;

include "config.php";
$cart = $_SESSION['cart'] ?? [];
$food_items = [];

if (!empty($cart)) {
    $ids = array_column($cart, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $quantities = [];
    foreach ($cart as $item) {
        $quantities[$item['id']] = $item['quantity'];
    }

    foreach ($items as $item) {
        $item['quantity'] = $quantities[$item['id']] ?? 1;
        $food_items[] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Kasse – Food Website</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
  <script>
    (function() {
      emailjs.init('jBo_qyx7uWt6jlBvq');
    })();
  </script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f5f7fa;
      padding: 2rem;
    }

    .kassen-box {
      background: white;
      max-width: 600px;
      margin: auto;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .form-control {
      margin-bottom: 1rem;
    }

    .btn-primary {
      width: 100%;
      padding: 0.75rem;
      font-weight: 600;
      border-radius: 10px;
    }

    .total {
      text-align: center;
      font-size: 1.3rem;
      margin-bottom: 1.5rem;
      font-weight: bold;
    }

    #success-message {
      display: none;
      color: green;
      font-weight: bold;
      text-align: center;
      margin-top: 1rem;
    }
  </style>
</head>
<body>

<div class="kassen-box">
  <h2>Zur Kasse</h2>
  <div class="total">Gesamtbetrag: € <?= number_format($total_with_tax, 2, ',', '.'); ?></div>

  <form id="checkout-form">
    <input type="hidden" id="total" value="<?= number_format($total, 2, '.', ''); ?>">
    <input type="hidden" id="shipping" value="<?= number_format($shipping, 2, '.', ''); ?>">
    <input type="hidden" id="tax" value="<?= number_format($tax, 2, '.', ''); ?>">
    <input type="hidden" id="totalWithTax" value="<?= number_format($total_with_tax, 2, '.', ''); ?>">

    <label for="name">Name</label>
    <input type="text" id="name" class="form-control" required>

    <label for="email">E-Mail</label>
    <input type="email" id="email" class="form-control" required>

    <label for="adresse">Adresse</label>
    <input type="text" id="adresse" class="form-control" required>

    <label for="zahlung">Zahlungsmethode</label>
    <select id="zahlung" class="form-control" required>
      <option value="">Bitte wählen...</option>
      <option value="Barzahlung">Barzahlung</option>
      <option value="Kreditkarte">Kreditkarte</option>
      <option value="PayPal">PayPal</option>
    </select>

    <button type="submit" class="btn btn-primary mt-3">Bestellung abschließen</button>
  </form>

  <div id="success-message">🎉 Deine Bestellung wurde erfolgreich abgeschickt!</div>
  <a href="index.php" id="home-button" class="btn btn-outline-secondary mt-3" style="display: none;">
  Zurück zur Startseite
</a>
</div>

<script>
  const foodItems = <?= json_encode($food_items ?? []) ?>;

  const buildOrders = () => {
    return foodItems.map(item => ({
      name: item.name,
      units: item.quantity,
      price: (item.price * item.quantity).toFixed(2),
      image_url: item.image ? `http://localhost/food_website/uploads/${item.image}` : 'https://via.placeholder.com/64'
    }));
  };

  document.getElementById('checkout-form').addEventListener('submit', function (e) {
    e.preventDefault();

    const shipping = document.getElementById('shipping')?.value || "0.00";
    const tax = document.getElementById('tax')?.value || "0.00";
    const total = document.getElementById('totalWithTax')?.value || "0.00";

    const emailData = {
      order_id: 'ORD-' + Math.floor(Math.random() * 1000000),
      email: document.getElementById('email').value,
      name: document.getElementById('name').value,
      adresse: document.getElementById('adresse').value,
      zahlung: document.getElementById('zahlung').value,
      shipping: shipping,
      tax: tax,
      total: total,
      orders: buildOrders()
    };

    console.log("📦 Sende folgende Daten an EmailJS:", emailData); // debug

    emailjs.send('service_emym93z', 'template_6xsptq8', emailData)
      .then(function (res) {
        console.log('✅ Email sent', res);
        document.getElementById('success-message').style.display = 'block';
        fetch('clear_session.php');
        document.getElementById('checkout-form').reset();
      }, function (err) {
        console.error('❌ Email error:', err);
        alert("Fehler beim Versenden der Bestellung.");
      });
  });
  document.getElementById('success-message').style.display = 'block';
fetch('clear_session.php'); // ← Session wird hier geleert
document.getElementById('checkout-form').reset();

document.getElementById('home-button').style.display = 'inline-block'; // ← Button sichtbar machen
</script>

</body>
</html>
