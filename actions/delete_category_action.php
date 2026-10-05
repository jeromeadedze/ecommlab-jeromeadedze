<?php
require_once __DIR__ . "/../core/core.php";
require_once __DIR__ . "/../controllers/ProductController.php";

// Protect route: restrict to admin users
require_admin();

$cat_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cat_id = isset($_POST['cat_id']) ? intval($_POST['cat_id']) : 0;
} elseif (isset($_GET['id'])) {
    $cat_id = intval($_GET['id']);
}

if ($cat_id <= 0) {
    $_SESSION['error'] = "Invalid category ID.";
    header("Location: ../views/admin/category.php");
    exit;
}

$controller = new ProductController();
try {
    $result = $controller->deleteCategory($cat_id);
    if ($result) {
        $_SESSION['success'] = "Category deleted successfully.";
    } else {
        $_SESSION['error'] = "Failed to delete category.";
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Cannot delete category: it may be linked to existing products or brands.";
}

header("Location: ../views/admin/category.php");
exit;
?>
