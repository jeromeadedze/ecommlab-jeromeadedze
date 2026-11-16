<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/../core/paystack_config.php';

require_login();

$cartItems = $cartCtrl->getCartItems($cartIp);
$total = $cartCtrl->getCartTotal($cartIp);
?>
<div class="page-head">
    <h1 class="page-title">Payment</h1>
</div>

<?php if (empty($cartItems)): ?>
    <div class="empty-state">
        <h2>Your cart is empty</h2>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Start shopping</a>
    </div>
<?php else: ?>
    <div class="surface-card overflow-hidden mb-3">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end d-none d-sm-table-cell">Unit price</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                            <td class="text-center"><?php echo (int)$item['qty']; ?></td>
                            <td class="text-end text-nowrap d-none d-sm-table-cell">GHS <?php echo number_format($item['product_price'], 2); ?></td>
                            <td class="text-end text-nowrap">GHS <?php echo number_format($item['qty'] * $item['product_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total</th>
                        <td class="d-none d-sm-table-cell"></td>
                        <th class="text-end text-nowrap">GHS <?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="action-row">
        <button type="button" class="btn-primary-custom btn-lg-custom" onclick="payWithPaystack()">
            <i class="bi bi-lock-fill" aria-hidden="true"></i> Pay <span class="text-nowrap">GHS <?php echo number_format($total, 2); ?></span> with Paystack
        </button>
        <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="btn-outline-custom btn-lg-custom">
            <i class="bi bi-arrow-left" aria-hidden="true"></i> Back to cart
        </a>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack() {
            var handler = PaystackPop.setup({
                key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
                email: '<?php echo htmlspecialchars($_SESSION["customer_email"], ENT_QUOTES); ?>',
                amount: <?php echo (int)round($total * 100); ?>,
                currency: 'GHS',
                callback: function (response) {
                    window.location = '<?php echo $ROOT_DIR; ?>/actions/process_payment_action.php?ref=' + encodeURIComponent(response.reference);
                },
                onClose: function () { alert('Payment cancelled.'); }
            });
            handler.openIframe();
        }
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
