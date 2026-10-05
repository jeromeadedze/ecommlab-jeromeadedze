<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';

    if (empty($cat_name)) {
        $_SESSION['error'] = "Category name is required.";
        header("Location: ../views/admin/category.php");
        exit;
    }

    $controller = new ProductController();
    $result = $controller->addCategory($cat_name);

    if ($result) {
        $_SESSION['success'] = "Category added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add category.";
    }

    header("Location: ../views/admin/category.php");
    exit;
} else {
    header("Location: ../views/admin/category.php");
    exit;
}
?>
