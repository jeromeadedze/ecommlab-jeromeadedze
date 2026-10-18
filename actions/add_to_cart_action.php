<?php

require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/CartController.php";

// Check if add_cart parameter is present in GET
if (!isset($_GET['add_cart']) || empty($_GET['add_cart'])) {
    $referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
    header("Location: $referer");
    exit;
}

$product_id = intval($_GET['add_cart']);
$ip = get_ip();
$customer_id = get_user_id(); // null if guest, integer if logged in
$qty = isset($_GET['qty']) ? max(1, intval($_GET['qty'])) : 1;

$cartController = new CartController();

// Check if item is already in the cart
if ($cartController->isInCart($product_id, $ip)) {
    $_SESSION['warning'] = "This item is already in your cart. Go to cart to update quantity.";
    $referer = $_SERVER['HTTP_REFERER'] ?? '../views/cart.php';
    header("Location: $referer");
    exit;
}

// Add to cart
$result = $cartController->addToCart($product_id, $qty, $ip, $customer_id);

if ($result) {
    $_SESSION['success'] = "Product added to cart successfully!";
} else {
    $_SESSION['error'] = "Failed to add product to cart.";
}

// Redirect back to referring page or cart page
$referer = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: $referer");
exit;
