<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/../controllers/ProductController.php';

$query = isset($_GET['user_query']) ? trim($_GET['user_query']) : '';

$productController = new ProductController();
$products = [];

if (!empty($query)) {
    $products = $productController->searchProducts($query);
}
?>

<div class="page-head">
    <div>
        <h1 class="page-title">Search results</h1>
        <p class="page-subtitle">
            <?php if (!empty($query)): ?>
                <?php echo count($products) . ' ' . (count($products) == 1 ? 'result' : 'results'); ?> for &ldquo;<?php echo htmlspecialchars($query); ?>&rdquo;
            <?php else: ?>
                Enter a product name, brand or tag in the search bar.
            <?php endif; ?>
        </p>
    </div>
</div>

<?php if (!empty($products)): ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
            <?php include __DIR__ . '/layout/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="empty-state">
        <span class="empty-state-icon" aria-hidden="true"><i class="bi bi-search"></i></span>
        <h2>No matches found</h2>
        <p>
            <?php if (!empty($query)): ?>
                Nothing matched &ldquo;<?php echo htmlspecialchars($query); ?>&rdquo;. Check the spelling or try a broader term, like a brand name.
            <?php else: ?>
                Search by product name, brand or tag.
            <?php endif; ?>
        </p>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Browse all products</a>
    </div>
<?php endif; ?>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
