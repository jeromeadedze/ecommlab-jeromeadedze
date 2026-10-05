<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate brand_id is a positive integer
    $brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : 0;
    $brand_name = isset($_POST['brand_name']) ? trim($_POST['brand_name']) : '';

    if ($brand_id <= 0) {
        $_SESSION['error'] = "Invalid brand ID.";
        header("Location: ../views/admin/brand.php");
        exit;
    }

    if (empty($brand_name)) {
        $_SESSION['error'] = "Brand name is required.";
        header("Location: ../views/admin/brand.php?edit_id=" . $brand_id);
        exit;
    }

    $controller = new ProductController();

    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $new_category = isset($_POST['new_category']) ? trim($_POST['new_category']) : '';

    // If a new category name was provided, create it and use its ID
    if (!empty($new_category)) {
        $created_cat_id = $controller->addCategory($new_category);
        if ($created_cat_id) {
            $category_id = $created_cat_id;
        }
    }

    $result = $controller->updateBrand($brand_id, $brand_name, $category_id);

    if ($result) {
        $msg = "Brand updated successfully.";
        if (!empty($new_category)) {
            $msg .= " Category '{$new_category}' was also created.";
        }
        $_SESSION['success'] = $msg;
    } else {
        $_SESSION['error'] = "Failed to update brand.";
    }

    header("Location: ../views/admin/brand.php");
    exit;
} else {
    header("Location: ../views/admin/brand.php");
    exit;
}
?>
