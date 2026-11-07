<?php

require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../core/paystack_config.php";
require_once __DIR__ . "/../controllers/CartController.php";

function payment_fail($message)
{
    $_SESSION['payment_error'] = $message;
    header("Location: ../views/payment_failed.php");
    exit;
}

// Must be logged in to pay
if (!is_logged_in()) {
    header("Location: ../views/login.php");
    exit;
}

$reference = trim($_GET['ref'] ?? $_POST['ref'] ?? '');
if ($reference === '' || !preg_match('/^[A-Za-z0-9._=-]+$/', $reference)) {
    payment_fail("Missing or invalid payment reference.");
}

$ip = get_ip();
$customer_id = get_user_id();
$cartController = new CartController();

$expected = $cartController->getCartTotal($ip);
if ($expected <= 0) {
    payment_fail("Your cart is empty, so there is nothing to pay for.");
}

// ---- Verify the transaction server-side with Paystack (never trust the client) ----
$ch = curl_init("https://api.paystack.co/transaction/verify/" . rawurlencode($reference));
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "Authorization: Bearer " . PAYSTACK_SECRET_KEY,
        "Cache-Control: no-cache",
    ],
    CURLOPT_TIMEOUT => 30,
]);
$response = curl_exec($ch);
$curlError = curl_error($ch);
curl_close($ch);

if ($response === false) {
    payment_fail("Could not reach Paystack to verify the payment: " . $curlError);
}

$result = json_decode($response, true);
if (!is_array($result) || empty($result['status']) || !isset($result['data'])) {
    payment_fail("Paystack could not verify this transaction.");
}

$data = $result['data'];
if (($data['status'] ?? '') !== 'success') {
    payment_fail("Payment was not successful (status: " . ($data['status'] ?? 'unknown') . ").");
}

// The amount paid (pesewas) must cover the cart total, in GHS
$paid = (int)($data['amount'] ?? 0);
if (($data['currency'] ?? '') !== 'GHS' || $paid < (int)round($expected * 100)) {
    payment_fail("The amount paid does not match your order total.");
}

// ---- Verified: record the order ----
$order_id = $cartController->processPayment($customer_id, $ip, $reference, $paid / 100);
if (!$order_id) {
    payment_fail("Payment was received but the order could not be recorded. Please contact support with reference $reference.");
}

// Optional confirmation email (silently ignored if mail() isn't configured)
$email = $_SESSION['customer_email'] ?? '';
if ($email !== '') {
    $subject = "Your Shoppn order confirmation";
    $body = "Thank you for your order!\n\nOrder #$order_id\nAmount paid: GHS " . number_format($paid / 100, 2)
        . "\nReference: $reference\n";
    @mail($email, $subject, $body, "From: no-reply@shoppn.local");
}

header("Location: ../views/payment_success.php?order_id=" . $order_id);
exit;

