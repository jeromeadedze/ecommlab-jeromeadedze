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

<nav aria-label="Breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?php echo $ROOT_DIR; ?>/index.php">Home</a></li>
        <?php if (!empty($product['product_cat'])): ?>
            <li class="breadcrumb-item">
                <a href="<?php echo $ROOT_DIR; ?>/index.php?cat=<?php echo $product['product_cat']; ?>">
                    <?php echo htmlspecialchars($product['cat_name']); ?>
                </a>
            </li>
        <?php endif; ?>
        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['product_title']); ?></li>
    </ol>
</nav>

<article class="product-detail">
    <!-- Product Image -->
    <div class="product-detail-media">
        <?php if (!empty($product['product_image'])): ?>
            <img src="<?php echo $ROOT_DIR; ?>/images/products/<?php echo htmlspecialchars($product['product_image']); ?>"
                 alt="<?php echo htmlspecialchars($product['product_title']); ?>">
        <?php else: ?>
            <div class="image-placeholder">
                <span class="text-center">
                    <i class="bi bi-image d-block" aria-hidden="true"></i>
                    <span class="fs-6">No image available</span>
                </span>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Details -->
    <div>
        <div class="product-meta product-detail-meta">
            <?php if (!empty($product['product_cat'])): ?>
                <span><a href="<?php echo $ROOT_DIR; ?>/index.php?cat=<?php echo $product['product_cat']; ?>"><?php echo htmlspecialchars($product['cat_name'] ?? 'General'); ?></a></span>
            <?php endif; ?>
            <?php if (!empty($product['product_brand']) && !empty($product['brand_name'])): ?>
                <span><a href="<?php echo $ROOT_DIR; ?>/index.php?brand=<?php echo $product['product_brand']; ?>"><?php echo htmlspecialchars($product['brand_name']); ?></a></span>
            <?php endif; ?>
        </div>

        <h1 class="product-detail-title"><?php echo htmlspecialchars($product['product_title']); ?></h1>

        <span class="price">$<?php echo number_format($product['product_price'], 2); ?></span>

        <div class="detail-actions">
            <a href="<?php echo $ROOT_DIR; ?>/actions/add_cart_action.php?add_cart=<?php echo $product['product_id']; ?>"
               class="btn-primary-custom btn-lg-custom">
                <i class="bi bi-cart-plus" aria-hidden="true"></i> Add to cart
            </a>
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-outline-custom btn-lg-custom">
                Continue shopping
            </a>
        </div>

        <section class="detail-section" aria-labelledby="desc-heading">
            <h2 class="detail-label" id="desc-heading">Description</h2>
            <p class="detail-desc">
                <?php echo nl2br(htmlspecialchars($product['product_desc'] ?: 'No description provided.')); ?>
            </p>
        </section>

        <?php
        $tags = array_filter(array_map('trim', explode(',', $product['product_keywords'] ?? '')));
        if (!empty($tags)):
        ?>
            <section class="detail-section" aria-labelledby="tags-heading">
                <h2 class="detail-label" id="tags-heading">Tags</h2>
                <ul class="tag-list">
                    <?php foreach ($tags as $tag): ?>
                        <li class="tag">#<?php echo htmlspecialchars($tag); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>
    </div>
</article>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
