<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_title = isset($_POST['product_title']) ? trim($_POST['product_title']) : '';
    $product_cat = isset($_POST['product_cat']) ? intval($_POST['product_cat']) : 0;
    $product_brand = isset($_POST['product_brand']) ? intval($_POST['product_brand']) : 0;
    $product_price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0.0;
    $product_desc = isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '';
    $product_keywords = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';

    // Basic Validation
    if (empty($product_title) || $product_cat <= 0 || $product_brand <= 0 || $product_price <= 0) {
        $_SESSION['error'] = "Please fill in all required fields (Title, Category, Brand, and a valid Price).";
        header("Location: ../views/admin/product.php");
        exit;
    }

    // Handle image upload
    $filename = null;
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        // PHP-side security checks: MIME type verification using finfo and uploaded type
        $file_type = $_FILES['product_image']['type'];
        $tmp_path = $_FILES['product_image']['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = finfo_file($finfo, $tmp_path);
        finfo_close($finfo);

        if (!in_array($file_type, $allowed) || !in_array($real_mime, $allowed)) {
            $_SESSION['error'] = "Invalid file type. Allowed formats: JPG, PNG, GIF, WEBP.";
            header("Location: ../views/admin/product.php");
            exit;
        }

        // Limit size (e.g. 5MB)
        if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File size exceeds 5MB limit.";
            header("Location: ../views/admin/product.php");
            exit;
        }

        // Generate unique filename
        $filename = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $upload_dir = __DIR__ . '/../images/products/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $destination = $upload_dir . $filename;
        if (!move_uploaded_file($tmp_path, $destination)) {
            $_SESSION['error'] = "Failed to upload product image.";
            header("Location: ../views/admin/product.php");
            exit;
        }
    }

    $controller = new ProductController();
    $result = $controller->addProduct($product_cat, $product_brand, $product_title, $product_price, $product_desc, $filename, $product_keywords);

    if ($result) {
        $_SESSION['success'] = "Product added successfully.";
    } else {
        $_SESSION['error'] = "Failed to add product to database.";
    }

    header("Location: ../views/admin/product.php");
    exit;
} else {
    header("Location: ../views/admin/product.php");
    exit;
}
