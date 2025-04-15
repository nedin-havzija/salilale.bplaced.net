<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/app/models/cart_helpers.php';

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(['success' => false]);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if item exists in cart and increase quantity
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] === $id) {
        $item['quantity'] += 1;
        $found = true;
        break;
    }
}
unset($item);

// If not found, add new item
if (!$found) {
    $_SESSION['cart'][] = ['id' => $id, 'quantity' => 1];
}

// ✅ Merge duplicates
merge_cart_items($_SESSION['cart']);

echo json_encode([
    'success' => true,
    'cart_count' => array_sum(array_column($_SESSION['cart'], 'quantity'))
]);