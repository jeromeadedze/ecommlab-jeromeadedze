<?php
require_once __DIR__ . '/layout/header.php';

// Ensure only admins can access this page
require_admin();

require_once __DIR__ . '/../controllers/CustomerController.php';
$controller = new CustomerController();
$customers = $controller->selectAll();
?>

<div class="container-fluid py-5" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0" style="color: #111;">Registered Customers</h2>
        <span class="badge bg-primary rounded-pill px-3 py-2 fs-6"><?php echo count($customers); ?> Total</span>
    </div>

    <div class="card auth-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover table-borderless align-middle mb-0">
                <thead class="table-light text-muted" style="border-bottom: 2px solid #f0f0f0;">
                    <tr>
                        <th class="py-3 px-4 fw-semibold text-uppercase" style="font-size: 0.85rem;">ID</th>
                        <th class="py-3 px-4 fw-semibold text-uppercase" style="font-size: 0.85rem;">Name</th>
                        <th class="py-3 px-4 fw-semibold text-uppercase" style="font-size: 0.85rem;">Email</th>
                        <th class="py-3 px-4 fw-semibold text-uppercase" style="font-size: 0.85rem;">Location</th>
                        <th class="py-3 px-4 fw-semibold text-uppercase" style="font-size: 0.85rem;">Role</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($customers)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No customers found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($customers as $c): ?>
                            <tr style="border-bottom: 1px solid #f8f9fa;">
                                <td class="py-3 px-4 text-muted fw-medium">#<?php echo htmlspecialchars($c['customer_id']); ?></td>
                                <td class="py-3 px-4 fw-semibold" style="color: #333;">
                                    <?php echo htmlspecialchars($c['customer_name']); ?>
                                </td>
                                <td class="py-3 px-4 text-muted">
                                    <a href="mailto:<?php echo htmlspecialchars($c['customer_email']); ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($c['customer_email']); ?>
                                    </a>
                                </td>
                                <td class="py-3 px-4 text-muted">
                                    <i class="bi bi-geo-alt-fill text-black-50 me-1"></i>
                                    <?php echo htmlspecialchars($c['customer_city'] . ', ' . $c['customer_country']); ?>
                                </td>
                                <td class="py-3 px-4">
                                    <?php if ($c['user_role'] == 1): ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Admin</span>
                                    <?php else: ?>
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Customer</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
