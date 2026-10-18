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

// Check if update and qty arrays are set
if (isset($_POST['update']) && isset($_POST['qty']) && is_array($_POST['update']) && is_array($_POST['qty'])) {
    $product_ids = $_POST['update'];
    $quantities = $_POST['qty'];

    $updatedCount = 0;

    foreach ($product_ids as $index => $pid) {
        $product_id = intval($pid);
        $qty = isset($quantities[$index]) ? intval($quantities[$index]) : 1;

        // Validate product_id and that new_qty is a positive integer (>= 1)
        if ($product_id > 0 && $qty >= 1) {
            $cartController->updateQty($product_id, $qty, $ip);
            $updatedCount++;
        }
    }

    if ($updatedCount > 0) {
        $_SESSION['success'] = "Cart updated successfully!";
    }
}

$root = defined('ROOT_DIR') ? ROOT_DIR : '..';
header("Location: $root/views/cart.php");
exit;
