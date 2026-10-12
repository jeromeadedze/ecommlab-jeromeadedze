<?php
require_once __DIR__ . '/../../controllers/ProductController.php';

$sidebarController = new ProductController();
$sidebarCategories = $sidebarController->getAllCategories();
$sidebarBrands = $sidebarController->getAllBrands();
$ROOT_DIR = defined('ROOT_DIR') ? ROOT_DIR : '..';

$currentCat = isset($_GET['cat']) ? intval($_GET['cat']) : null;
$currentBrand = isset($_GET['brand']) ? intval($_GET['brand']) : null;
?>
<aside class="custom-sidebar" style="width: 260px; min-width: 240px;">
    <!-- Categories List -->
    <div class="mb-4">
        <h5 class="sidebar-title">Categories</h5>
        <ul class="list-unstyled mb-0">
            <li class="mb-1">
                <a href="<?php echo $ROOT_DIR; ?>/index.php" 
                   class="sidebar-link <?php echo ($currentCat === null && $currentBrand === null && !isset($_GET['user_query'])) ? 'active' : ''; ?>">
                    All Products
                </a>
            </li>
            <?php foreach ($sidebarCategories as $cat): ?>
                <li class="mb-1">
                    <a href="<?php echo $ROOT_DIR; ?>/index.php?cat=<?php echo $cat['cat_id']; ?>" 
                       class="sidebar-link <?php echo ($currentCat === intval($cat['cat_id'])) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($cat['cat_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- Brands List -->
    <div class="mb-4">
        <h5 class="sidebar-title">Brands</h5>
        <ul class="list-unstyled mb-0">
            <?php foreach ($sidebarBrands as $brand): ?>
                <li class="mb-1">
                    <a href="<?php echo $ROOT_DIR; ?>/index.php?brand=<?php echo $brand['brand_id']; ?>" 
                       class="sidebar-link <?php echo ($currentBrand === intval($brand['brand_id'])) ? 'active' : ''; ?>">
                        <?php echo htmlspecialchars($brand['brand_name']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</aside>

<section class="flex-grow-1" style="background-color: var(--bg-color); padding: 30px;">
