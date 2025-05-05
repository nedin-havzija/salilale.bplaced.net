<?php
session_start();
$total = $_SESSION['total_price'] ?? ($_GET['total'] ?? 0);
$shipping = 6.90;
$tax_rate = 0.081;
$tax = $total * $tax_rate;
$total_with_tax = $total + $shipping + $tax;

include __DIR__ . '/config/config.php';
$cart = $_SESSION['cart'] ?? [];
$food_items = [];

if (!empty($cart)) {
    $ids = array_column($cart, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $quantities = array_column($cart, 'quantity', 'id');
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
  <title>Zur Kasse – FOODHave</title>
  <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
  <script> (function() { emailjs.init('jBo_qyx7uWt6jlBvq'); })(); </script>
  <link rel="stylesheet" href="app/views/assets/css/bootstrap.min.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap');

    :root {
      --color-background: #fae3ea;
      --color-primary: #fc8080;
      --font-family-base: 'Poppins', sans-serif;
    }

    body {
      background-color: var(--color-background);
      font-family: var(--font-family-base);
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    .iphone {
      background: #fff;
      border-radius: 2em;
      width: 100%;
      max-width: 400px;
      padding: 2em;
      box-shadow: 0 0 1em rgba(0, 0, 0, 0.05);
    }

    h1 {
      font-size: 1.5rem;
      margin-bottom: 1.5em;
      text-align: center;
    }

    .card {
      background-color: var(--color-primary);
      border-radius: 1em;
      color: white;
      padding: 1em;
      margin-bottom: 1.5em;
    }

    .form-control {
      margin-bottom: 1rem;
      border-radius: 0.75em;
    }

    .form-select {
      margin-bottom: 1rem;
      border-radius: 0.75em;
    }

    .summary-table td {
      font-size: 0.95rem;
      padding: 0.3em 0;
    }

    .summary-table tfoot td {
      font-weight: 600;
      padding-top: 0.5em;
      border-top: 1px solid #ddd;
    }

    .btn-submit {
      background-color: var(--color-primary);
      color: white;
      border: none;
      padding: 0.75em;
      width: 100%;
      border-radius: 999px;
      font-weight: 600;
      font-size: 1rem;
    }

    .btn-submit:hover {
      background-color: #e96363;
    }

    #success-message {
      display: none;
      text-align: center;
      color: green;
      margin-top: 1em;
      font-weight: 600;
    }

    #home-button {
      display: none;
      margin-top: 1em;
      text-align: center;
    }

    #home-button a {
      display: inline-block;
      padding: 0.75em 1.5em;
      border-radius: 999px;
      background-color: #f5f5f5;
      text-decoration: none;
      color: #333;
      font-weight: 600;
      border: 2px solid #ddd;
      transition: background 0.3s, color 0.3s;
    }

    #home-button a:hover {
      background-color: var(--color-primary);
      color: white;
      border-color: var(--color-primary);
    }
  </style>
</head>
<body>

<div class="iphone">
  <h1>Zur Kasse</h1>

  <div class="card">
    <strong>Lieferadresse</strong><br>
    FOODHave Kunde<br>
    Beispielstrasse 42<br>
    8000 Zürich
  </div>

  <form id="checkout-form">
    <input type="hidden" id="total" value="<?= number_format($total, 2, '.', ''); ?>">
    <input type="hidden" id="shipping" value="<?= number_format($shipping, 2, '.', ''); ?>">
    <input type="hidden" id="tax" value="<?= number_format($tax, 2, '.', ''); ?>">
    <input type="hidden" id="totalWithTax" value="<?= number_format($total_with_tax, 2, '.', ''); ?>">

    <input type="text" class="form-control" id="name" placeholder="Name" required>
    <input type="email" class="form-control" id="email" placeholder="E-Mail" required>
    <input type="text" class="form-control" id="adresse" placeholder="Adresse" required>

    <select id="zahlung" class="form-select" required>
      <option value="">Zahlungsmethode wählen</option>
      <option value="Barzahlung">Barzahlung</option>
      <option value="Kreditkarte">Kreditkarte</option>
      <option value="PayPal">PayPal</option>
    </select>

    <h5 class="mt-4">Zusammenfassung</h5>
    <table class="summary-table" width="100%">
      <tbody>
        <?php foreach ($food_items as $item): ?>
          <tr>
            <td><?= htmlspecialchars($item['name']); ?> x<?= $item['quantity']; ?></td>
            <td align="right">€<?= number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></td>
          </tr>
        <?php endforeach; ?>
        <tr>
          <td>Versandkosten</td>
          <td align="right">€<?= number_format($shipping, 2, ',', '.'); ?></td>
        </tr>
        <tr>
          <td>MWSt (8.1%)</td>
          <td align="right">€<?= number_format($tax, 2, ',', '.'); ?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td>Gesamt</td>
          <td align="right">€<?= number_format($total_with_tax, 2, ',', '.'); ?></td>
        </tr>
      </tfoot>
    </table>

    <button type="submit" class="btn-submit mt-4">Jetzt bestellen</button>
  </form>

  <div id="success-message">🎉 Bestellung erfolgreich gesendet!</div>

  <div id="home-button">
    <a href="index.php">Zurück zur Startseite</a>
  </div>
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

    const data = {
      order_id: 'ORD-' + Math.floor(Math.random() * 1000000),
      email: document.getElementById('email').value,
      name: document.getElementById('name').value,
      adresse: document.getElementById('adresse').value,
      zahlung: document.getElementById('zahlung').value,
      shipping: document.getElementById('shipping').value,
      tax: document.getElementById('tax').value,
      total: document.getElementById('totalWithTax').value,
      orders: buildOrders()
    };

    emailjs.send('service_emym93z', 'template_6xsptq8', data).then(function () {
      document.getElementById('success-message').style.display = 'block';
      document.getElementById('home-button').style.display = 'block';
      fetch('clear_session.php');
      document.getElementById('checkout-form').reset();
    }, function (err) {
      console.error('❌ Email error:', err);
      alert("Fehler beim Versenden der Bestellung.");
    });
  });
</script>

</body>
</html>
