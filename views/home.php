<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$productController = new ProductController();

$pageTitle = "Featured Products";
$products = [];

if (isset($_GET['cat'])) {
    $cat_id = intval($_GET['cat']);
    $products = $productController->getProductsByCategory($cat_id);
    $selectedCategory = $productController->getCategoryById($cat_id);
    $pageTitle = $selectedCategory ? "Category: " . htmlspecialchars($selectedCategory['cat_name']) : "Products by Category";
} elseif (isset($_GET['brand'])) {
    $brand_id = intval($_GET['brand']);
    $products = $productController->getProductsByBrand($brand_id);
    $selectedBrand = $productController->getBrandById($brand_id);
    $pageTitle = $selectedBrand ? "Brand: " . htmlspecialchars($selectedBrand['brand_name']) : "Products by Brand";
} else {
    $products = $productController->getFeaturedProducts(6);
    $pageTitle = "Featured Products";
}
?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h2 class="fw-bold mb-0 text-dark"><?php echo $pageTitle; ?></h2>
        
        <?php if (is_admin()): ?>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-dark-subtle text-dark border px-2 py-2 fw-semibold">
                    <i class="bi bi-gear-fill me-1"></i> Admin Controls
                </span>
                <a href="<?php echo $ROOT_DIR; ?>/views/admin/product.php" class="btn-light-purple"><i class="bi bi-box-seam"></i> Products</a>
                <a href="<?php echo $ROOT_DIR; ?>/views/admin/brand.php" class="btn-light-purple"><i class="bi bi-tags"></i> Brands</a>
                <a href="<?php echo $ROOT_DIR; ?>/views/admin/category.php" class="btn-light-purple"><i class="bi bi-grid"></i> Categories</a>
                <a href="<?php echo $ROOT_DIR; ?>/views/customers.php" class="btn-light-purple"><i class="bi bi-people"></i> Customers</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Grid -->
    <?php if (!empty($products)): ?>
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 g-4">
            <?php foreach ($products as $p): ?>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative">
                        <div class="position-relative bg-white text-center p-3" style="height: 220px;">
                            <?php if (!empty($p['product_image'])): ?>
                                <img src="<?php echo $ROOT_DIR; ?>/images/products/<?php echo htmlspecialchars($p['product_image']); ?>" 
                                     class="w-100 h-100" 
                                     style="object-fit: contain;" 
                                     alt="<?php echo htmlspecialchars($p['product_title']); ?>">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light rounded text-muted">
                                    <i class="bi bi-image fs-1"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column p-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="badge bg-light text-secondary border">
                                    <?php echo htmlspecialchars($p['cat_name'] ?? 'General'); ?>
                                </span>
                                <?php if (!empty($p['brand_name'])): ?>
                                    <span class="text-muted small fw-medium">
                                        <?php echo htmlspecialchars($p['brand_name']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title fw-bold text-dark mt-2 mb-2 text-truncate" title="<?php echo htmlspecialchars($p['product_title']); ?>">
                                <?php echo htmlspecialchars($p['product_title']); ?>
                            </h5>
                            <p class="text-muted small flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?php echo htmlspecialchars($p['product_desc'] ?? ''); ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mt-2">
                                <span class="fs-5 fw-bold" style="color: var(--primary-color);">
                                    $<?php echo number_format($p['product_price'], 2); ?>
                                </span>
                                <div class="d-flex gap-2">
                                    <a href="<?php echo $ROOT_DIR; ?>/views/single_product.php?pro_id=<?php echo $p['product_id']; ?>" 
                                       class="btn btn-outline-secondary btn-sm rounded-3 px-3">
                                        Details
                                    </a>
                                    <a href="<?php echo $ROOT_DIR; ?>/actions/add_cart_action.php?add_cart=<?php echo $p['product_id']; ?>" 
                                       class="btn-primary-custom text-decoration-none btn-sm px-3 d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-cart-plus"></i> Add
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm p-5">
            <i class="bi bi-box2 text-muted" style="font-size: 3.5rem;"></i>
            <h4 class="fw-bold mt-3 text-dark">No products found</h4>
            <p class="text-muted">There are no products matching this criteria at the moment.</p>
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom text-decoration-none px-4 py-2 mt-2">
                Browse All Products
            </a>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
