<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
$error = $_GET['error'] ?? 'Something went wrong with your payment.';
?>
<div class="empty-state">
    <h2>Payment failed</h2>
    <p><?php echo htmlspecialchars($error); ?></p>
    <div class="action-row justify-content-center">
        <a href="<?php echo $ROOT_DIR; ?>/views/payment.php" class="btn-primary-custom btn-lg-custom">Try again</a>
        <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="btn-outline-custom btn-lg-custom">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to cart
        </a>
    </div>
</div>
<?php require_once __DIR__ . '/layout/footer.php'; ?>
