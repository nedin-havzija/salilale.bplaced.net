<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include __DIR__ . '/config/config.php';

$id = intval($_POST['id']);
$action = $_POST['action']; // 'plus' or 'minus'

foreach ($_SESSION['cart'] as $key => &$item) {
    if ($item['id'] == $id) {
        if ($action === 'plus') {
            $item['quantity']++;
        } elseif ($action === 'minus') {
            $item['quantity']--;
            if ($item['quantity'] <= 0) {
                unset($_SESSION['cart'][$key]);
            }
        }
        break;
    }
}
unset($item);

// ✅ Merge duplicates
merge_cart_items($_SESSION['cart']);

session_write_close();
echo json_encode(['success' => true]);
