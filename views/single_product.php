<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/../controllers/ProductController.php';

// Validate ?pro_id GET param
$pro_id = isset($_GET['pro_id']) ? intval($_GET['pro_id']) : 0;
if ($pro_id <= 0) {
    header("Location: " . $ROOT_DIR . "/index.php");
    exit;
}

$productController = new ProductController();
$product = $productController->getProductById($pro_id);

if (!$product) {
    header("Location: " . $ROOT_DIR . "/index.php");
    exit;
}
?>

<div class="container-fluid px-0">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo $ROOT_DIR; ?>/index.php" class="text-decoration-none">Home</a></li>
            <?php if (!empty($product['product_cat'])): ?>
                <li class="breadcrumb-item">
                    <a href="<?php echo $ROOT_DIR; ?>/index.php?cat=<?php echo $product['product_cat']; ?>" class="text-decoration-none">
                        <?php echo htmlspecialchars($product['cat_name']); ?>
                    </a>
                </li>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
        </ol>
    </nav>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden p-4 p-md-5 bg-white">
        <div class="row g-5 align-items-center">
            <!-- Product Image -->
            <div class="col-md-6 text-center">
                <div class="bg-light rounded-4 p-4 d-flex align-items-center justify-content-center" style="min-height: 380px;">
                    <?php if (!empty($product['product_image'])): ?>
                        <img src="<?php echo $ROOT_DIR; ?>/images/products/<?php echo htmlspecialchars($product['product_image']); ?>" 
                             alt="<?php echo htmlspecialchars($product['product_title']); ?>" 
                             class="img-fluid rounded-3" 
                             style="max-height: 360px; object-fit: contain;">
                    <?php else: ?>
                        <div class="text-muted text-center">
                            <i class="bi bi-image" style="font-size: 5rem;"></i>
                            <p class="mt-2 mb-0">No image available</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Product Details -->
            <div class="col-md-6">
                <div class="d-flex gap-2 mb-2">
                    <span class="badge bg-light text-secondary border px-3 py-2">
                        Category: <?php echo htmlspecialchars($product['cat_name'] ?? 'General'); ?>
                    </span>
                    <span class="badge bg-light text-secondary border px-3 py-2">
                        Brand: <?php echo htmlspecialchars($product['brand_name'] ?? 'Unknown'); ?>
                    </span>
                </div>

                <h1 class="fw-bold text-dark mb-3"><?php echo htmlspecialchars($product['product_title']); ?></h1>

                <div class="fs-2 fw-bold mb-4" style="color: var(--primary-color);">
                    $<?php echo number_format($product['product_price'], 2); ?>
                </div>

                <div class="mb-4">
                    <h6 class="text-uppercase text-muted fw-bold small">Description</h6>
                    <p class="text-secondary leading-relaxed" style="line-height: 1.7;">
                        <?php echo nl2br(htmlspecialchars($product['product_desc'] ?: 'No description provided.')); ?>
                    </p>
                </div>

                <?php if (!empty($product['product_keywords'])): ?>
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold small mb-2">Keywords / Tags</h6>
                        <div class="d-flex flex-wrap gap-1">
                            <?php 
                            $tags = explode(',', $product['product_keywords']);
                            foreach ($tags as $tag): 
                                $trimmed = trim($tag);
                                if (!empty($trimmed)):
                            ?>
                                <span class="badge bg-secondary-subtle text-secondary px-2 py-1">
                                    #<?php echo htmlspecialchars($trimmed); ?>
                                </span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="d-flex gap-3 pt-3 border-top">
                    <a href="<?php echo $ROOT_DIR; ?>/actions/add_cart_action.php?add_cart=<?php echo $product['product_id']; ?>" 
                       class="btn-primary-custom text-decoration-none px-4 py-3 d-inline-flex align-items-center gap-2 fs-6">
                        <i class="bi bi-cart-plus-fill fs-5"></i> Add to Cart
                    </a>
                    <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn btn-outline-secondary px-4 py-3 rounded-3 d-inline-flex align-items-center">
                        Back to Shop
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
