<?php
require_once "../core/core.php";
require_once "../controllers/CustomerController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['customer_email'] ?? '');
    $pass = trim($_POST['customer_pass'] ?? '');

    if (empty($email) || empty($pass)) {
        $_SESSION['error'] = 'Please enter email and password.';
        header('Location: ../views/login.php');
        exit;
    }

    $controller = new CustomerController();
    $result = $controller->login($email, $pass);

    if ($result['success']) {
        $user = $result['user'];
        $_SESSION['customer_id'] = $user['customer_id'];
        $_SESSION['customer_name'] = $user['customer_name'];
        $_SESSION['customer_email'] = $user['customer_email'];
        $_SESSION['user_role'] = (int) $user['user_role'];
        
        header('Location: ../index.php');
        exit;
    } else {
        $_SESSION['error'] = $result['error'];
        header('Location: ../views/login.php');
        exit;
    }
}
