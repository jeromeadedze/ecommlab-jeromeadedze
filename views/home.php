<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
?>

<div class="container-fluid py-5 text-center">
    <h2 class="fw-bold mb-4" style="color: #111;">Welcome to the Shop</h2>
    
    <?php if (is_admin()): ?>
        <a href="/views/customers.php" class="btn-light-purple"><i class="bi bi-people"></i> View customers</a>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
