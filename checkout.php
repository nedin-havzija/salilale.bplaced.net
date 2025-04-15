<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/config/config.php';

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
    <link rel="stylesheet" href="app/views/assets/css/bootstrap.min.css">
    <style>
        body {
    font-family: 'Segoe UI', sans-serif;
    background: #f5f7fa;
    color: #1a1a1a;
    margin: 0;
}

.site-header {
    background: #fff;
    padding: 1rem 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.header-cart {
    position: relative;
    font-size: 1.5rem;
    text-decoration: none;
    color: #333;
}

.cart-number {
    background: crimson;
    color: white;
    border-radius: 50%;
    font-size: 0.75rem;
    padding: 0.25rem 0.55rem;
    position: absolute;
    top: -8px;
    right: -10px;
    font-weight: bold;
    box-shadow: 0 0 0 2px #fff;
}

h2 {
    font-size: 2.2rem;
    font-weight: 700;
    color: #1c1c1c;
    margin-bottom: 1.5rem;
    text-align: center;
}

.table {
    background-color: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.table th {
    background-color: #f2f2f2;
    font-weight: bold;
    padding: 1rem;
}

.table td,
.table th {
    text-align: center;
    vertical-align: middle !important;
    padding: 1.2rem;
}

.table tr:hover {
    background-color: #f9fbff;
}

.table img {
    border-radius: 12px;
    width: 80px;
    height: 80px;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.table img:hover {
    transform: scale(1.05);
}

.quantity-btn {
    width: 35px;
    height: 35px;
    font-size: 18px;
    border-radius: 6px;
    font-weight: bold;
    transition: background 0.2s ease;
}

.quantity-btn:hover {
    background-color: #e6e6e6;
    color: black;
}

.mx-2 {
    margin: 0 0.75rem;
    font-weight: 500;
    font-size: 16px;
}

h3 {
    font-weight: bold;
    color: #0f172a;
    font-size: 1.8rem;
    text-align: right;
}

.btn-primary {
    background-color: #0d6efd;
    border: none;
    padding: 0.6rem 1.4rem;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 5px 10px rgba(0, 123, 255, 0.2);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #084cdf;
    transform: translateY(-2px);
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
    padding: 0.6rem 1.4rem;
    border-radius: 12px;
    font-weight: 600;
    box-shadow: 0 5px 10px rgba(108, 117, 125, 0.2);
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background-color: #545b62;
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .table thead {
        display: none;
    }

    .table, .table tbody, .table tr, .table td {
        display: block;
        width: 100%;
    }

    .table tr {
        margin-bottom: 1rem;
        background-color: white;
        padding: 1rem;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .table td {
        text-align: right;
        padding-left: 50%;
        position: relative;
    }

    .table td::before {
        content: attr(data-label);
        position: absolute;
        left: 1rem;
        width: 45%;
        font-weight: bold;
        text-align: left;
    }
}
    </style>
</head>
<body>

<header class="site-header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <a href="index.php"><img src="app/views/assets/logo.png" width="160" alt="Logo"></a>
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
                                 alt="<?= htmlspecialchars($item['name']); ?>">
                        </td>
                        <td><?= htmlspecialchars($item['name']); ?></td>
                        <td>€ <?= number_format($item['price'], 2); ?></td>
                        <td>
                            <div class="d-flex align-items-center justify-content-center">
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
            <a href="payment.php?total=<?= number_format($total_price, 2, '.', ''); ?>" class="btn btn-primary">Zur Kasse</a>
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
