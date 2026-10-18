<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$productController = new ProductController();

$pageTitle = "Featured products";
$products = [];

if (isset($_GET['cat'])) {
    $cat_id = intval($_GET['cat']);
    $products = $productController->getProductsByCategory($cat_id);
    $selectedCategory = $productController->getCategoryById($cat_id);
    $pageTitle = $selectedCategory ? $selectedCategory['cat_name'] : "Products by category";
} elseif (isset($_GET['brand'])) {
    $brand_id = intval($_GET['brand']);
    $products = $productController->getProductsByBrand($brand_id);
    $selectedBrand = $productController->getBrandById($brand_id);
    $pageTitle = $selectedBrand ? $selectedBrand['brand_name'] : "Products by brand";
} else {
    $products = $productController->getFeaturedProducts(6);
    $pageTitle = "Featured products";
}
?>

<div class="page-head">
    <div>
        <h1 class="page-title"><?php echo htmlspecialchars($pageTitle); ?></h1>
        <p class="page-subtitle"><?php echo count($products) . ' ' . (count($products) == 1 ? 'product' : 'products'); ?></p>
    </div>

    <?php if (is_admin()): ?>
        <nav class="admin-toolbar" aria-label="Admin">
            <span class="admin-toolbar-label">Admin</span>
            <a href="<?php echo $ROOT_DIR; ?>/views/admin/product.php" class="btn-light-purple btn-sm-custom"><i class="bi bi-box-seam" aria-hidden="true"></i> Products</a>
            <a href="<?php echo $ROOT_DIR; ?>/views/admin/brand.php" class="btn-light-purple btn-sm-custom"><i class="bi bi-tags" aria-hidden="true"></i> Brands</a>
            <a href="<?php echo $ROOT_DIR; ?>/views/admin/category.php" class="btn-light-purple btn-sm-custom"><i class="bi bi-grid" aria-hidden="true"></i> Categories</a>
            <a href="<?php echo $ROOT_DIR; ?>/views/customers.php" class="btn-light-purple btn-sm-custom"><i class="bi bi-people" aria-hidden="true"></i> Customers</a>
        </nav>
    <?php endif; ?>
</div>

<?php if (!empty($products)): ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <?php include __DIR__ . '/layout/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <span class="empty-state-icon" aria-hidden="true"><i class="bi bi-box2"></i></span>
        <h2>No products here yet</h2>
        <p>Nothing is listed under this filter right now. Try another category or brand.</p>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Browse all products</a>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
