<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $product_title = isset($_POST['product_title']) ? trim($_POST['product_title']) : '';
    $product_cat = isset($_POST['product_cat']) ? intval($_POST['product_cat']) : 0;
    $product_brand = isset($_POST['product_brand']) ? intval($_POST['product_brand']) : 0;
    $product_price = isset($_POST['product_price']) ? floatval($_POST['product_price']) : 0.0;
    $product_desc = isset($_POST['product_desc']) ? trim($_POST['product_desc']) : '';
    $product_keywords = isset($_POST['product_keywords']) ? trim($_POST['product_keywords']) : '';
    $existing_image = isset($_POST['existing_image']) ? trim($_POST['existing_image']) : null;

    if ($product_id <= 0) {
        $_SESSION['error'] = "Invalid product ID.";
        header("Location: ../views/admin/product.php");
        exit;
    }

    if (empty($product_title) || $product_cat <= 0 || $product_brand <= 0 || $product_price <= 0) {
        $_SESSION['error'] = "Please fill in all required fields (Title, Category, Brand, and a valid Price).";
        header("Location: ../views/admin/product.php?edit_id=" . $product_id);
        exit;
    }

    // Default to existing image
    $filename = !empty($existing_image) ? $existing_image : null;

    // Check if new image was uploaded
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        $file_type = $_FILES['product_image']['type'];
        $tmp_path = $_FILES['product_image']['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $real_mime = finfo_file($finfo, $tmp_path);
        finfo_close($finfo);

        if (!in_array($file_type, $allowed) || !in_array($real_mime, $allowed)) {
            $_SESSION['error'] = "Invalid file type. Allowed formats: JPG, PNG, GIF, WEBP.";
            header("Location: ../views/admin/product.php?edit_id=" . $product_id);
            exit;
        }

        if ($_FILES['product_image']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File size exceeds 5MB limit.";
            header("Location: ../views/admin/product.php?edit_id=" . $product_id);
            exit;
        }

        $new_filename = uniqid() . '_' . basename($_FILES['product_image']['name']);
        $upload_dir = __DIR__ . '/../images/products/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $destination = $upload_dir . $new_filename;
        if (move_uploaded_file($tmp_path, $destination)) {
            // Optional: delete old image if exists and not default
            if (!empty($existing_image) && file_exists($upload_dir . $existing_image)) {
                @unlink($upload_dir . $existing_image);
            }
            $filename = $new_filename;
        } else {
            $_SESSION['error'] = "Failed to upload new product image.";
            header("Location: ../views/admin/product.php?edit_id=" . $product_id);
            exit;
        }
    }

    $controller = new ProductController();
    $result = $controller->updateProduct($product_id, $product_cat, $product_brand, $product_title, $product_price, $product_desc, $filename, $product_keywords);

    if ($result) {
        $_SESSION['success'] = "Product updated successfully.";
    } else {
        $_SESSION['error'] = "Failed to update product.";
    }

    header("Location: ../views/admin/product.php");
    exit;
} else {
    header("Location: ../views/admin/product.php");
    exit;
}
