<?php
require_once __DIR__ . '/layout/header.php';
?>

<div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 80px);">
    <div class="auth-card w-100" style="max-width: 450px;">
        <h2 class="auth-title text-center">Log In</h2>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<div class='alert alert-danger border-0 rounded-3'>" . $_SESSION['error'] . "</div>";
            unset($_SESSION['error']);
        }
        if (isset($_SESSION['success'])) {
            echo "<div class='alert alert-success border-0 rounded-3'>" . $_SESSION['success'] . "</div>";
            unset($_SESSION['success']);
        }
        ?>

        <form action="../actions/login_action.php" method="POST">
            <div class="mb-3">
                <label class="form-label text-muted fw-semibold">Email Address</label>
                <input type="email" name="customer_email" class="form-control form-control-custom" placeholder="name@example.com" required>
            </div>
            <div class="mb-4">
                <label class="form-label text-muted fw-semibold">Password</label>
                <input type="password" name="customer_pass" class="form-control form-control-custom" placeholder="••••••••" required>
            </div>
            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary-custom py-2 fs-5">Login</button>
            </div>
        </form>

        <p class="text-center text-muted">Don't have an account? <a href="register.php" class="text-primary text-decoration-none fw-bold">Register here</a></p>
    </div>
</div>

</main>
<?php
require_once __DIR__ . '/layout/footer.php';
?>
