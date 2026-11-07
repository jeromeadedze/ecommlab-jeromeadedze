<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';

$error = $_SESSION['payment_error'] ?? 'Something went wrong with your payment.';
unset($_SESSION['payment_error']);
?>
<div class="empty-state">
    <h2>Payment failed</h2>
    <p><?php echo htmlspecialchars($error); ?></p>
    <a href="<?php echo $ROOT_DIR; ?>/views/payment.php" class="btn-primary-custom">Try again</a>
    <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="continue-link">Back to cart</a>
</div>

<?php require_once __DIR__ . '/layout/footer.php'; ?>

