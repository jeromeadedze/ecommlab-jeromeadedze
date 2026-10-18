<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarController = new ProductController();
$sidebarCategories = $sidebarController->getAllCategories();
$sidebarBrands = $sidebarController->getAllBrands();
$ROOT_DIR = defined('ROOT_DIR') ? ROOT_DIR : '..';

$currentCat = isset($_GET['cat']) ? intval($_GET['cat']) : null;
$currentBrand = isset($_GET['brand']) ? intval($_GET['brand']) : null;

// Tells footer.php to close the shop layout wrappers opened below
define('SHOP_LAYOUT', true);
?>
<div class="shop-layout">
<aside class="custom-sidebar" aria-label="Browse">
    <!-- Categories List -->
    <nav class="sidebar-group" aria-labelledby="sidebar-categories">
        <h2 class="sidebar-title" id="sidebar-categories">Categories</h2>
        <ul class="sidebar-list">
            <?php $allActive = $currentCat === null && $currentBrand === null && !isset($_GET['user_query']); ?>
            <li>
                <a href="<?php echo $ROOT_DIR; ?>/index.php"
                   class="sidebar-link <?php echo $allActive ? 'active' : ''; ?>"
                   <?php echo $allActive ? 'aria-current="page"' : ''; ?>>
                    All products
                </a>
            </li>
            <?php foreach ($sidebarCategories as $cat): ?>
                <?php $isActive = $currentCat === intval($cat['cat_id']); ?>
                <li>
                    <a href="<?php echo $ROOT_DIR; ?>/index.php?cat=<?php echo $cat['cat_id']; ?>"
                       class="sidebar-link <?php echo $isActive ? 'active' : ''; ?>"
                       <?php echo $isActive ? 'aria-current="page"' : ''; ?>>
                        <?php echo htmlspecialchars($cat['cat_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- Brands List -->
    <nav class="sidebar-group" aria-labelledby="sidebar-brands">
        <h2 class="sidebar-title" id="sidebar-brands">Brands</h2>
        <ul class="sidebar-list">
            <?php foreach ($sidebarBrands as $brand): ?>
                <?php $isActive = $currentBrand === intval($brand['brand_id']); ?>
                <li>
                    <a href="<?php echo $ROOT_DIR; ?>/index.php?brand=<?php echo $brand['brand_id']; ?>"
                       class="sidebar-link <?php echo $isActive ? 'active' : ''; ?>"
                       <?php echo $isActive ? 'aria-current="page"' : ''; ?>>
                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>
</aside>

<section class="shop-content">
