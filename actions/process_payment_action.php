<?php

require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../core/paystack_config.php";
require_once __DIR__ . "/../controllers/CartController.php";

function payment_fail($msg)
{
    header("Location: ../views/payment_failed.php?error=" . urlencode($msg));
    exit;
}

if (!is_logged_in()) {
    header("Location: ../views/login.php");
    exit;
}

$reference = trim($_GET['ref'] ?? $_POST['ref'] ?? '');
if ($reference === '' || !preg_match('/^[A-Za-z0-9._=-]+$/', $reference)) {
    payment_fail("Missing or invalid payment reference.");
}

$customer_id = get_user_id();
$ip = get_ip();
$cartController = new CartController();

// Expected amount comes from OUR cart, never from the client
$total = $cartController->getCartTotal($ip);
if ($total <= 0) {
    payment_fail("Your cart is empty or this payment was already processed.");
}

// Verify the transaction server-side with Paystack
$ch = curl_init('https://api.paystack.co/transaction/verify/' . rawurlencode($reference));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . PAYSTACK_SECRET_KEY],
    CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($ch);
$curlErr = curl_error($ch);
curl_close($ch);

if ($response === false) {
    payment_fail("Could not reach Paystack to verify payment: " . $curlErr);
}

$result = json_decode($response, true);
if (!$result || empty($result['status']) || ($result['data']['status'] ?? '') !== 'success') {
    payment_fail("Payment could not be verified.");
}

// Check the amount paid and currency match the cart (amounts are in pesewas)
$paid = (int)($result['data']['amount'] ?? 0);
if ($paid < (int)round($total * 100) || ($result['data']['currency'] ?? '') !== 'GHS') {
    payment_fail("Amount paid does not match your order total.");
}

// Record the order
$order_id = $cartController->processPayment($customer_id, $ip, $reference, $paid / 100);
if (!$order_id) {
    $detail = !empty($_SESSION['db_error']) ? " (" . $_SESSION['db_error'] . ")" : "";
    unset($_SESSION['db_error']);
    payment_fail("Payment succeeded but the order could not be saved{$detail}. Contact support with reference $reference.");
}

header("Location: ../views/payment_success.php?order_id=" . $order_id);
exit;
