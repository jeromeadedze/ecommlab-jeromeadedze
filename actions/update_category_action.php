<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
    $cat_name = isset($_POST['cat_name']) ? trim($_POST['cat_name']) : '';

    if ($cat_id <= 0) {
        $_SESSION['error'] = "Invalid category ID.";
        header("Location: ../views/admin/category.php");
        exit;
    }

    if (empty($cat_name)) {
        $_SESSION['error'] = "Category name is required.";
        header("Location: ../views/admin/category.php?edit_id=" . $cat_id);
        exit;
    }

    $controller = new ProductController();
    $result = $controller->updateCategory($cat_id, $cat_name);

    if ($result) {
        $_SESSION['success'] = "Category updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update category.";
    }

    header("Location: ../views/admin/category.php");
    exit;
} else {
    header("Location: ../views/admin/category.php");
    exit;
}
?>
