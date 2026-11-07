<?php
require_once __DIR__ . '/layout/header.php';
require_login();
require_once __DIR__ . '/layout/sidebar.php';

$order_id = (int)($_GET['order_id'] ?? 0);
$order = $order_id ? $cartCtrl->getOrderById($order_id) : null;

// A customer may only view their own invoice
if ($order && (int)$order['order']['customer_id'] !== (int)get_user_id()) {
    $order = null;
}
?>
<?php if (!$order): ?>
    <div class="empty-state">
        <h2>Order not found</h2>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Back to shop</a>
    </div>
<?php else: ?>
    <div class="page-head">
        <div>
            <h1 class="page-title">Payment successful</h1>
            <p class="page-subtitle">Thank you! Your invoice is below.</p>
        </div>
    </div>

    <div class="surface-card overflow-hidden">
        <div class="p-3">
            <div><strong>Order #:</strong> <?php echo (int)$order['order']['order_id']; ?></div>
            <div><strong>Invoice #:</strong> <?php echo (int)$order['order']['invoice_no']; ?></div>
            <div><strong>Date:</strong> <?php echo htmlspecialchars($order['order']['order_date']); ?></div>
            <div><strong>Status:</strong> <?php echo htmlspecialchars($order['order']['order_status']); ?></div>
        </div>
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
                    <?php foreach ($order['items'] as $item): ?>
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
                        <th class="text-end">GHS <?php echo number_format($order['total'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="p-3 text-end">
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Continue shopping</a>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>

