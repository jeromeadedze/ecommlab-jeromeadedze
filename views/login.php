<?php
require_once __DIR__ . '/layout/header.php';
?>

<div class="auth-wrap">
    <div class="auth-card" style="max-width: 440px;">
        <h1 class="auth-title">Welcome back</h1>
        <p class="auth-subtitle">Log in to keep shopping and check out faster.</p>

        <!-- Success and error messages are shown by layout/header.php -->
        <form action="../actions/login_action.php" method="POST">
            <div class="mb-3">
                <label for="customer_email" class="form-label">Email address</label>
                <input type="email" name="customer_email" id="customer_email" class="form-control form-control-custom"
                       placeholder="name@example.com" autocomplete="email" required>
            </div>
            <div class="mb-4">
                <label for="customer_pass" class="form-label">Password</label>
                <input type="password" name="customer_pass" id="customer_pass" class="form-control form-control-custom"
                       autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn-primary-custom btn-lg-custom w-100">Log in</button>
        </form>

        <p class="auth-footer">Don't have an account? <a href="register.php">Sign up</a></p>
    </div>
</div>

<?php
require_once __DIR__ . '/layout/footer.php';
?>
