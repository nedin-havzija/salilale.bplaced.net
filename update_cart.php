<?php
session_start();
header('Content-Type: application/json');

function merge_cart_items(&$cart) {
    $merged = [];

    foreach ($cart as $item) {
        $id = $item['id'];
        if (!isset($merged[$id])) {
            $merged[$id] = $item;
        } else {
            $merged[$id]['quantity'] += $item['quantity'];
        }
    }

    $cart = array_values($merged);
}

// Validate input
if (!isset($_POST['id'], $_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$id = (int) $_POST['id'];
$action = $_POST['action'];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = &$_SESSION['cart'];

// Handle delete
if ($action === 'delete') {
    $_SESSION['cart'] = array_values(array_filter($cart, fn($item) => $item['id'] != $id));
    echo json_encode(['success' => true]);
    exit;
}

// Handle plus/minus
foreach ($cart as &$item) {
    if ($item['id'] == $id) {
        if ($action === 'plus') {
            $item['quantity'] += 1;
        } elseif ($action === 'minus') {
            $item['quantity'] = max(1, $item['quantity'] - 1);
        }
        break;
    }
}

// Merge to clean duplicates
merge_cart_items($_SESSION['cart']);

echo json_encode(['success' => true]);
