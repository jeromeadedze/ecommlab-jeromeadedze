<?php
// Expects $p (product row) and $ROOT_DIR from the including view.
$detailUrl = $ROOT_DIR . '/views/single_product.php?pro_id=' . $p['product_id'];
?>
<article class="product-card">
    <!-- Duplicate of the title link, so it stays out of the tab order and accessibility tree -->
    <a href="<?php echo $detailUrl; ?>" class="product-card-media" tabindex="-1" aria-hidden="true">
        <?php if (!empty($p['product_image'])): ?>
            <img src="<?php echo $ROOT_DIR; ?>/images/products/<?php echo htmlspecialchars($p['product_image']); ?>" alt="" loading="lazy">
        <?php else: ?>
            <span class="image-placeholder"><i class="bi bi-image"></i></span>
        <?php endif; ?>
    </a>
    <div class="product-card-body">
        <div class="product-meta">
            <span><?php echo htmlspecialchars($p['cat_name'] ?? 'General'); ?></span>
            <?php if (!empty($p['brand_name'])): ?>
                <span><?php echo htmlspecialchars($p['brand_name']); ?></span>
            <?php endif; ?>
        </div>
        <h2 class="product-card-title">
            <a href="<?php echo $detailUrl; ?>"><?php echo htmlspecialchars($p['product_title']); ?></a>
        </h2>
        <p class="product-card-desc"><?php echo htmlspecialchars($p['product_desc'] ?? ''); ?></p>
        <div class="product-card-footer">
            <span class="price">$<?php echo number_format($p['product_price'], 2); ?></span>
            <?php if (is_admin()): ?>
                <a href="<?php echo $ROOT_DIR; ?>/views/admin/product.php?edit=<?php echo $p['product_id']; ?>"
                   class="btn-outline-custom btn-sm-custom">
                    <i class="bi bi-pencil" aria-hidden="true"></i>
                    <span>Edit</span>
                </a>
            <?php else: ?>
                <a href="<?php echo $ROOT_DIR; ?>/actions/add_cart_action.php?add_cart=<?php echo $p['product_id']; ?>"
                   class="btn-primary-custom btn-sm-custom">
                    <i class="bi bi-cart-plus" aria-hidden="true"></i>
                    <span>Add<span class="visually-hidden"> <?php echo htmlspecialchars($p['product_title']); ?></span> to cart</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
