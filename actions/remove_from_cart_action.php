<?php

require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $root = defined('ROOT_DIR') ? ROOT_DIR : '..';
    header("Location: $root/views/cart.php");
    exit;
}

$ip = get_ip();
$cartController = new CartController();

// Validate remove[] array of product IDs
if (isset($_POST['remove']) && is_array($_POST['remove']) && !empty($_POST['remove'])) {
    $removedCount = 0;

    foreach ($_POST['remove'] as $pid) {
        $product_id = intval($pid);
        if ($product_id > 0) {
            $cartController->removeFromCart($product_id, $ip);
            $removedCount++;
        }
    }

    if ($removedCount > 0) {
        $_SESSION['success'] = "Selected item(s) removed from cart.";
    }
} else {
    $_SESSION['warning'] = "Please select at least one item to remove.";
}

$root = defined('ROOT_DIR') ? ROOT_DIR : '..';
header("Location: $root/views/cart.php");
exit;
