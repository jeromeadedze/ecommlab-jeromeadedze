<?php
// TEMP: show errors instead of a blank 500 page
ini_set('display_errors', 1);
error_reporting(E_ALL);

// This is an "action" file - the endpoint the browser's JavaScript sends
// the registration form to (see js/customer.js -> fetch("../actions/customer_register_action.php")).
// Its job is: read the incoming request, hand the data to the controller,
// and send a response back. It should not contain any SQL itself - that
// belongs in the model (classes/CustomerClass.php).
require_once "../core/core.php";
require_once "../controllers/CustomerController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['customer_name'] ?? '');
    $email = trim($_POST['customer_email'] ?? '');
    $pass = trim($_POST['customer_pass'] ?? '');
    $country = trim($_POST['customer_country'] ?? '');
    $city = trim($_POST['customer_city'] ?? '');
    $contact = trim($_POST['customer_contact'] ?? '');
    $image = $_POST['customer_image'] ?? null;

    if (empty($name) || empty($email) || empty($pass) || empty($country) || empty($city) || empty($contact)) {
        $_SESSION['error'] = 'All fields are required.';
        header('Location: ../views/register.php');
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Invalid email format.';
        header('Location: ../views/register.php');
        exit;
    }

    // Server-side password validation (min 8 chars, 1 uppercase, 1 lowercase, 1 digit, 1 special char)
    $passPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
    if (!preg_match($passPattern, $pass)) {
        $_SESSION['error'] = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&).';
        header('Location: ../views/register.php');
        exit;
    }

    $controller = new CustomerController();
    $result = $controller->register($name, $email, $pass, $country, $city, $contact, $image);

    if ($result['success']) {
        $user = $result['user'];
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['customer_name'] = $user['customer_name'];
        $_SESSION['customer_email'] = $user['customer_email'];
        $_SESSION['user_role'] = (int) $user['user_role'];

        $_SESSION['success'] = 'Welcome, ' . htmlspecialchars($user['customer_name']) . '! Your account has been created.';
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ../views/register.php');
        exit;
    }
}
