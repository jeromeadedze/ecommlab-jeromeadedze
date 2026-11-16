<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/../controllers/CartController.php';

$cartController = new CartController();
$ip = get_ip();
$cartItems = $cartController->getCartItems($ip);
$cartTotal = $cartController->getCartTotal($ip);
?>

<?php
$itemCount = count($cartItems);
$unitCount = array_sum(array_map(fn($i) => (int)$i['qty'], $cartItems));
?>
<div class="page-head">
    <div>
        <h1 class="page-title">Your cart</h1>
        <?php if (!empty($cartItems)): ?>
            <p class="page-subtitle"><?php echo $itemCount . ' ' . ($itemCount == 1 ? 'product' : 'products'); ?></p>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($cartItems)): ?>
    <div class="empty-state">
        <span class="empty-state-icon" aria-hidden="true"><i class="bi bi-cart"></i></span>
        <h2>Your cart is empty</h2>
        <p>Items you add to your cart will show up here.</p>
        <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-primary-custom">Start shopping</a>
    </div>
<?php else: ?>
    <div class="cart-layout">
        <form id="cartForm" method="POST" class="surface-card overflow-hidden">
            <div class="table-responsive">
                <table class="table cart-table align-middle">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 56px;"><span class="visually-hidden">Select</span></th>
                            <th scope="col">Product</th>
                            <th scope="col" class="text-end d-none d-md-table-cell">Price</th>
                            <th scope="col" class="text-center">Quantity</th>
                            <th scope="col" class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <?php
                            $itemSubtotal = $item['qty'] * (float)$item['product_price'];
                            $titleSafe = htmlspecialchars($item['product_title']);
                            ?>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox" name="remove[]"
                                           id="remove-<?php echo $item['p_id']; ?>" value="<?php echo $item['p_id']; ?>">
                                    <label class="visually-hidden" for="remove-<?php echo $item['p_id']; ?>">Select <?php echo $titleSafe; ?></label>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="cart-thumb d-none d-sm-block">
                                            <?php if (!empty($item['product_image'])): ?>
                                                <img src="<?php echo $ROOT_DIR; ?>/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" alt="">
                                            <?php else: ?>
                                                <span class="image-placeholder fs-5"><i class="bi bi-image" aria-hidden="true"></i></span>
                                            <?php endif; ?>
                                        </div>
                                        <a href="<?php echo $ROOT_DIR; ?>/views/single_product.php?pro_id=<?php echo $item['p_id']; ?>" class="cart-item-title">
                                            <?php echo $titleSafe; ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-end text-secondary tabular d-none d-md-table-cell">
                                    GH₵<?php echo number_format($item['product_price'], 2); ?>
                                </td>
                                <td class="text-center">
                                    <input type="hidden" name="update[]" value="<?php echo $item['p_id']; ?>">
                                    <label class="visually-hidden" for="qty-<?php echo $item['p_id']; ?>">Quantity of <?php echo $titleSafe; ?></label>
                                    <input type="number" name="qty[]" id="qty-<?php echo $item['p_id']; ?>"
                                           value="<?php echo (int)$item['qty']; ?>" min="1" inputmode="numeric" data-price="<?php echo (float)$item['product_price']; ?>"
                                           class="form-control qty-input mx-auto">
                                </td>
                                <td class="text-end fw-bold tabular js-subtotal">
                                    GH₵<?php echo number_format($itemSubtotal, 2); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-table-actions">
                <button type="submit"
                        formaction="<?php echo $ROOT_DIR; ?>/actions/update_qty_action.php"
                        class="btn-outline-custom btn-sm-custom">
                    <i class="bi bi-arrow-repeat" aria-hidden="true"></i> Update quantities
                </button>
                <button type="submit"
                        formaction="<?php echo $ROOT_DIR; ?>/actions/remove_from_cart_action.php"
                        class="btn-ghost btn-danger-ghost btn-sm-custom">
                    <i class="bi bi-trash" aria-hidden="true"></i> Remove selected
                </button>
            </div>
        </form>

        <aside class="surface-card summary-card" aria-labelledby="summary-heading">
            <h2 id="summary-heading">Order summary</h2>
            <div class="summary-row">
                <span>Items (<span id="summary-units"><?php echo $unitCount; ?></span>)</span>
                <span id="summary-items-total">GH₵<?php echo number_format($cartTotal, 2); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping</span>
                <span>Calculated at checkout</span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="summary-total">GH₵<?php echo number_format($cartTotal, 2); ?></span>
            </div>
            <a href="<?php echo $ROOT_DIR; ?>/views/payment.php" class="btn-primary-custom btn-lg-custom icon-end">
                Checkout <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn-outline-custom btn-lg-custom w-100 mt-2"><i class="bi bi-bag" aria-hidden="true"></i> Continue shopping</a>
        </aside>
    </div>
    <script>
    (function () {
        var form = document.getElementById('cartForm');
        if (!form) return;
        var updateBtn = form.querySelector('button[formaction*="update_qty_action"]');
        var inputs = form.querySelectorAll('.qty-input');
        var fmt = function (n) {
            return 'GH₵' + n.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        };

        // Recalculate row subtotals and the order summary as the quantity changes
        function recalc() {
            var total = 0, units = 0;
            inputs.forEach(function (input) {
                var qty = Math.max(1, parseInt(input.value, 10) || 1);
                var sub = qty * parseFloat(input.dataset.price);
                input.closest('tr').querySelector('.js-subtotal').textContent = fmt(sub);
                total += sub;
                units += qty;
            });
            document.getElementById('summary-units').textContent = units;
            document.getElementById('summary-items-total').textContent = fmt(total);
            document.getElementById('summary-total').textContent = fmt(total);
        }

        inputs.forEach(function (input) {
            input.addEventListener('input', recalc);
            // Save to the cart once editing is done, so payment uses the new quantity
            input.addEventListener('change', function () {
                if (!(parseInt(input.value, 10) >= 1)) input.value = 1;
                recalc();
                form.requestSubmit(updateBtn);
            });
        });
    })();
    </script>
<?php endif; ?>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
