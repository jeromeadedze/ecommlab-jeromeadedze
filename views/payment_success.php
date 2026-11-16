<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';

require_login();

$order_id = intval($_GET['order_id'] ?? 0);
$rows = $order_id ? $cartCtrl->getOrderById($order_id, get_user_id()) : [];

if (empty($rows)): ?>
    <div class="empty-state">
        <h2>Order not found</h2>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Back to shop</a>
    </div>
<?php else:
    $order = $rows[0];
    $total = 0;
    foreach ($rows as $r) {
        $total += $r['qty'] * $r['product_price'];
    }

    // Send confirmation email once per order (best effort; needs a configured mail server)
    if (empty($_SESSION['emailed_order_' . $order_id])) {
        $_SESSION['emailed_order_' . $order_id] = true;
        $body = "Thank you for your order!\n\nInvoice #" . $order['invoice_no'] . "\nOrder #" . $order_id . "\n\n";
        foreach ($rows as $r) {
            $body .= $r['product_title'] . " x" . $r['qty'] . " - GHS " . number_format($r['qty'] * $r['product_price'], 2) . "\n";
        }
        $body .= "\nTotal: GHS " . number_format($total, 2) . "\n";
        @mail($_SESSION['customer_email'], "Your Shoppn order confirmation (#" . $order['invoice_no'] . ")", $body, "From: no-reply@shoppn.local");
    }
?>
    <div class="page-head">
        <h1 class="page-title">Payment successful</h1>
        <p class="page-subtitle">Thank you! A confirmation email has been sent to <?php echo htmlspecialchars($_SESSION['customer_email']); ?>.</p>
    </div>

    <div class="surface-card p-3">
        <h2>Invoice #<?php echo (int)$order['invoice_no']; ?></h2>
        <p>Order #<?php echo (int)$order_id; ?> &middot; <?php echo htmlspecialchars($order['order_date']); ?> &middot; <?php echo htmlspecialchars($order['order_status']); ?></p>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr><th>Product</th><th class="text-center">Qty</th><th class="text-end d-none d-sm-table-cell">Price</th><th class="text-end">Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['product_title']); ?></td>
                            <td class="text-center"><?php echo (int)$r['qty']; ?></td>
                            <td class="text-end text-nowrap d-none d-sm-table-cell">GHS <?php echo number_format($r['product_price'], 2); ?></td>
                            <td class="text-end text-nowrap">GHS <?php echo number_format($r['qty'] * $r['product_price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr><th colspan="2" class="text-end">Total</th><td class="d-none d-sm-table-cell"></td><th class="text-end text-nowrap">GHS <?php echo number_format($total, 2); ?></th></tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="action-row">
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-outline-custom btn-lg-custom">
            <i class="bi bi-bag" aria-hidden="true"></i> Continue shopping
        </a>
        <a href="<?php echo $ROOT_DIR; ?>/views/orders.php" class="btn-primary-custom btn-lg-custom">
            <i class="bi bi-receipt" aria-hidden="true"></i> View all my orders
        </a>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
