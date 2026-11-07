<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/../core/paystack_config.php';
require_login();
require_once __DIR__ . '/layout/sidebar.php';

$items = $cartCtrl->getCartItems($cartIp);
$total = $cartCtrl->getCartTotal($cartIp);
?>
<div class="page-head">
    <div>
        <h1 class="page-title">Payment</h1>
        <p class="page-subtitle">Review your order and pay securely with Paystack</p>
    </div>
</div>

<?php if (empty($items)): ?>
    <div class="empty-state">
        <h2>Your cart is empty</h2>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Start shopping</a>
    </div>
<?php else: ?>
    <div class="surface-card overflow-hidden">
        <div class="table-responsive">
            <table class="table cart-table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Unit price</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['product_title']); ?></td>
                            <td class="text-end">GHS <?php echo number_format($item['product_price'], 2); ?></td>
                            <td class="text-center"><?php echo (int)$item['qty']; ?></td>
                            <td class="text-end fw-bold">GHS <?php echo number_format($item['qty'] * (float)$item['product_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" class="text-end">Total</th>
                        <th class="text-end">GHS <?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="p-3 d-flex gap-2 justify-content-end">
            <a href="<?php echo $ROOT_DIR; ?>/views/cart.php" class="btn-outline-custom">Back to cart</a>
            <button type="button" class="btn-primary-custom" onclick="payWithPaystack()">
                Pay GHS <?php echo number_format($total, 2); ?>
            </button>
        </div>
    </div>

    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        function payWithPaystack() {
            var handler = PaystackPop.setup({
                key: '<?php echo PAYSTACK_PUBLIC_KEY; ?>',
                email: '<?php echo htmlspecialchars($_SESSION["customer_email"] ?? "", ENT_QUOTES); ?>',
                amount: <?php echo (int)round($total * 100); ?>,
                currency: 'GHS',
                onSuccess: function (response) {
                    window.location = '<?php echo $ROOT_DIR; ?>/actions/process_payment_action.php?ref=' + encodeURIComponent(response.reference);
                },
                onCancel: function () { alert('Payment cancelled.'); }
            });
            handler.openIframe();
        }
    </script>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>

