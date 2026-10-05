<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
?>

<div class="container-fluid py-5 text-center">
    <h2 class="fw-bold mb-4" style="color: #111;">Welcome to the Shop</h2>
    
    <?php if (is_admin()): ?>
        <a href="<?php echo $ROOT_DIR; ?>/views/customers.php" class="btn-light-purple"><i class="bi bi-people"></i> View customers</a>
        <a href="<?php echo $ROOT_DIR; ?>/views/admin/brand.php" class="btn-light-purple"><i class="bi bi-tags"></i> Brands</a>
        <a href="<?php echo $ROOT_DIR; ?>/views/admin/category.php" class="btn-light-purple"><i class="bi bi-grid"></i> Categories</a>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
