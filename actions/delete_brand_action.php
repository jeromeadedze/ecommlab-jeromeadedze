<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

$brand_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand_id = isset($_POST['brand_id']) ? intval($_POST['brand_id']) : 0;
} elseif (isset($_GET['id'])) {
    $brand_id = intval($_GET['id']);
}

if ($brand_id <= 0) {
    $_SESSION['error'] = "Invalid brand ID.";
    header("Location: ../views/admin/brand.php");
    exit;
}

$controller = new ProductController();
try {
    $result = $controller->deleteBrand($brand_id);
    if ($result) {
        $_SESSION['success'] = "Brand deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete brand.";
    }
} catch (Exception $e) {
    // If the brand is attached to existing products, a foreign key constraint might trigger
    $_SESSION['error'] = "Cannot delete brand: it may be linked to existing products.";
}

header("Location: ../views/admin/brand.php");
exit;
?>
