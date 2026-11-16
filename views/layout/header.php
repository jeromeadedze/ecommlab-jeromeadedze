<?php
// Ensure core is included if not already (header might be included directly by some views)
require_once __DIR__ . '/../../core/core.php';
require_once __DIR__ . '/../../controllers/CartController.php';

$ROOT_DIR = defined('ROOT_DIR') ? ROOT_DIR : '..';

$cartCtrl = new CartController();
$cartIp = get_ip();
$totalItems = $cartCtrl->getCartCount($cartIp);
$totalPrice = $cartCtrl->getCartTotal($cartIp);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shoppn</title>
    <!-- Blank favicon (no tab icon) -->
    <link rel="icon" href="data:,">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo $ROOT_DIR; ?>/css/style.css?v=<?php echo filemtime(__DIR__ . '/../../css/style.css'); ?>">
</head>
<body>
    <a href="#main" class="skip-link">Skip to content</a>

    <header class="custom-header">
        <!-- Logo -->
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="brand-logo" aria-label="Shoppn home">
            <span class="brand-name">Shopp<span class="brand-accent">n</span></span>
        </a>

        <!-- Search Bar -->
        <form action="<?php echo $ROOT_DIR; ?>/views/search_results.php" method="GET" class="search-bar-container" role="search">
            <i class="bi bi-search" aria-hidden="true"></i>
            <label for="site-search" class="visually-hidden">Search products</label>
            <input type="search" id="site-search" name="user_query" placeholder="Search products, brands, tags…" required value="<?php echo htmlspecialchars($_GET['user_query'] ?? ''); ?>">
            <button type="submit" class="search-submit">Search</button>
        </form>

        <!-- Cart Summary & Navigation Actions -->
        <nav class="header-actions" aria-label="Account">
            <?php if (!is_admin()): ?>
                <?php $itemWord = $totalItems == 1 ? 'item' : 'items'; ?>
                <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="cart-pill">
                    <i class="bi bi-cart3" aria-hidden="true"></i>
                    <span class="cart-count" aria-hidden="true"><?php echo $totalItems; ?></span>
                    <span class="cart-total" aria-hidden="true">GH₵<?php echo number_format($totalPrice, 2); ?></span>
                    <span class="visually-hidden">Cart, <?php echo $totalItems . ' ' . $itemWord; ?>, GH₵<?php echo number_format($totalPrice, 2); ?></span>
                </a>
            <?php endif; ?>

            <?php if (is_logged_in() && !is_admin()): ?>
                <a href="<?php echo $ROOT_DIR; ?>/views/orders.php" class="btn-ghost"><i class="bi bi-receipt" aria-hidden="true"></i> <span class="header-btn-label">My orders</span></a>
            <?php endif; ?>

            <?php if (is_logged_in()): ?>
                <span class="header-greeting">Hi, <?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'there'); ?></span>
                <a href="<?php echo $ROOT_DIR; ?>/logout.php" class="btn-ghost">Log out</a>
            <?php else: ?>
                <a href="<?php echo $ROOT_DIR; ?>/views/login.php" class="btn-ghost">Log in</a>
                <a href="<?php echo $ROOT_DIR; ?>/views/register.php" class="btn-primary-custom">Sign up</a>
            <?php endif; ?>
        </nav>
    </header>
    <script>
        // The header wraps to two rows at some widths; keep --header-height equal to its real height
        // so the sticky sidebar, scroll padding and toasts never sit underneath it.
        (function () {
            var header = document.querySelector('.custom-header');
            if (!header || !window.ResizeObserver) return;
            new ResizeObserver(function () {
                document.documentElement.style.setProperty('--header-height', header.offsetHeight + 'px');
            }).observe(header);
        })();
    </script>

    <?php if (isset($_SESSION['warning']) || isset($_SESSION['success']) || isset($_SESSION['error'])): ?>
        <div class="toast-popup-container" aria-live="polite" aria-atomic="true">
            <?php if (isset($_SESSION['warning'])): ?>
                <div class="toast-popup toast-warning">
                    <div class="toast-icon">
                        <i class="bi bi-exclamation-circle-fill"></i>
                    </div>
                    <div class="toast-content">
                        <p class="toast-message"><?php echo htmlspecialchars($_SESSION['warning']); unset($_SESSION['warning']); ?></p>
                        <div class="toast-actions">
                            <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="toast-btn toast-btn-primary">
                                <i class="bi bi-cart-fill me-1"></i> Go to cart
                            </a>
                            <button type="button" class="toast-btn toast-btn-ghost" onclick="this.closest('.toast-popup').remove();">
                                Dismiss
                            </button>
                        </div>
                    </div>
                    <button type="button" class="toast-close" onclick="this.closest('.toast-popup').remove();" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <?php 
                $succMsg = $_SESSION['success'];
                unset($_SESSION['success']);
                $isCartSuccess = (stripos($succMsg, 'cart') !== false);
                ?>
                <div class="toast-popup toast-success">
                    <div class="toast-icon">
                        <i class="bi bi-check-circle-fill"></i>
                    </div>
                    <div class="toast-content">
                        <p class="toast-message <?php echo !$isCartSuccess ? 'mb-0' : ''; ?>"><?php echo htmlspecialchars($succMsg); ?></p>
                        <?php if ($isCartSuccess && !is_admin()): ?>
                            <div class="toast-actions">
                                <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="toast-btn toast-btn-primary">
                                    <i class="bi bi-cart-fill me-1"></i> View cart
                                </a>
                                <button type="button" class="toast-btn toast-btn-ghost" onclick="this.closest('.toast-popup').remove();">
                                    Continue
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="toast-close" onclick="this.closest('.toast-popup').remove();" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="toast-popup toast-error">
                    <div class="toast-icon">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div class="toast-content">
                        <p class="toast-message"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></p>
                    </div>
                    <button type="button" class="toast-close" onclick="this.closest('.toast-popup').remove();" aria-label="Close">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    
    <main id="main" class="app-main" tabindex="-1">
