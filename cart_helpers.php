<?php
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
