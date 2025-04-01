<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include "config.php";

// --- Merge cart properly ---
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$merged = [];

foreach ($cart as $item) {
    $id = $item['id'];
    if (!isset($merged[$id])) {
        $merged[$id] = ['id' => $id, 'quantity' => $item['quantity']];
    } else {
        $merged[$id]['quantity'] += $item['quantity'];
    }
}

$_SESSION['cart'] = array_values($merged);
$cart = $_SESSION['cart'];

// --- Fetch food items with mapped quantities ---
$food_items = [];
$total_price = 0;

if (!empty($cart)) {
    $food_ids = array_column($cart, 'id');
    $placeholders = implode(',', array_fill(0, count($food_ids), '?'));
    $stmt = $conn->prepare("SELECT * FROM food_items WHERE id IN ($placeholders)");
    $stmt->execute($food_ids);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $quantities = [];
    foreach ($cart as $c) {
        $quantities[$c['id']] = $c['quantity'];
    }

    foreach ($items as $item) {
        $item['quantity'] = $quantities[$item['id']] ?? 1;
        $total_price += $item['price'] * $item['quantity'];
        $food_items[] = $item;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - Food Website</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css?v=1.9">
</head>
<body>

<header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <a href="index.php"><img src="logo.png" width="160" alt="Logo"></a>
            </div>
            <div class="col-lg-10 text-end">
                <a href="checkout.php" class="header-btn header-cart">
                    <i class="uil uil-shopping-bag"></i>
                    <span class="cart-number"><?= count($cart); ?></span>
                </a>
            </div>
        </div>
    </div>
</header>

<div class="container">
    <h2 class="text-center mt-5">Warenkorb</h2>

    <?php if (count($food_items) > 0): ?>
        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>Bild</th>
                    <th>Name</th>
                    <th>Preis</th>
                    <th>Quantität</th>
                    <th>Gesamt</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($food_items as $item): ?>
                    <tr>
                        <td>
                            <img src="<?= $item['image'] ? 'uploads/' . htmlspecialchars(basename($item['image'])) : 'assets/images/no-image.png'; ?>"
                                 alt="<?= htmlspecialchars($item['name']); ?>" style="width: 80px; height: 80px;">
                        </td>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td>€ <?= number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <button class="btn btn-sm btn-outline-secondary quantity-btn" data-id="<?= $item['id']; ?>" data-action="minus">&minus;</button>
                                <span class="mx-2"><?= $item['quantity']; ?></span>
                                <button class="btn btn-sm btn-outline-secondary quantity-btn" data-id="<?= $item['id']; ?>" data-action="plus">+</button>
                            </div>
                        </td>
                        <td>€ <?= number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="text-right mt-4">
            <h3>Gesamtpreis: € <?= number_format($total_price, 2); ?></h3>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="index.php" class="btn btn-secondary">Zurück zur Speisekarte</a>
            <a href="payment.php" class="btn btn-primary">Zur Kasse</a>
        </div>
    <?php else: ?>
        <p class="text-center">Ihr Warenkorb ist leer.</p>
        <div class="text-center">
            <a href="index.php" class="btn btn-secondary">Zur Speisekarte</a>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
$(document).ready(function () {
    $('.quantity-btn').click(function () {
        const id = $(this).data('id');
        const action = $(this).data('action');

        $.post('update_cart.php', { id: id, action: action }, function (res) {
            if (res.success !== false) {
                location.reload();
            }
        }, 'json');
    });
});
</script>

</body>
</html>