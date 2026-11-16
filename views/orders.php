<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';

require_login();

$orders = $cartCtrl->getOrdersByCustomer(get_user_id());
?>
<div class="page-head">
    <div>
        <h1 class="page-title">My orders</h1>
        <p class="page-subtitle"><?php echo count($orders) . ' ' . (count($orders) == 1 ? 'order' : 'orders'); ?></p>
    </div>
</div>

<?php if (empty($orders)): ?>
    <div class="empty-state">
        <h2>No orders yet</h2>
        <p>Your receipts will show up here after you pay.</p>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Start shopping</a>
    </div>
<?php else: ?>
    <div class="surface-card overflow-hidden">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th class="d-none d-sm-table-cell">Date</th>
                        <th class="text-center d-none d-sm-table-cell">Items</th>
                        <th>Status</th>
                        <th class="text-end">Total</th>
                        <th class="text-end d-none d-sm-table-cell"><span class="visually-hidden">Receipt</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $o): ?>
                        <?php $receiptUrl = $ROOT_DIR . '/views/payment_success.php?order_id=' . (int)$o['order_id']; ?>
                        <tr>
                            <td>
                                <!-- On phones the invoice number is the receipt link, and the date sits under it -->
                                <a href="<?php echo $receiptUrl; ?>" class="d-sm-none fw-semibold">#<?php echo (int)$o['invoice_no']; ?></a>
                                <span class="d-none d-sm-inline">#<?php echo (int)$o['invoice_no']; ?></span>
                                <span class="d-block d-sm-none small text-secondary"><?php echo htmlspecialchars(substr($o['order_date'], 0, 10)); ?></span>
                            </td>
                            <td class="d-none d-sm-table-cell"><?php echo htmlspecialchars($o['order_date']); ?></td>
                            <td class="text-center d-none d-sm-table-cell"><?php echo (int)$o['item_count']; ?></td>
                            <td><?php echo htmlspecialchars($o['order_status']); ?></td>
                            <td class="text-end text-nowrap">GHS <?php echo number_format($o['total'], 2); ?></td>
                            <td class="text-end d-none d-sm-table-cell">
                                <a href="<?php echo $receiptUrl; ?>">View receipt</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/footer.php'; ?>
