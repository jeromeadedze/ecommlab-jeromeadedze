<?php
require_once __DIR__ . '/layout/header.php';
?>
<!--
	This is the "view" for registering a new customer.
-->
<div class="container-fluid d-flex justify-content-center py-5">
    <div class="auth-card w-100" style="max-width: 600px;">
        <h2 class="auth-title text-center">Create an Account</h2>

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

        <form id="registerForm" action="../actions/register_action.php" method="POST" novalidate>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-semibold">Full Name</label>
                    <input type="text" name="customer_name" id="customer_name" class="form-control form-control-custom" placeholder="John Doe">
                    <div id="name_error" class="text-danger mt-1 small" style="display: none;"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-semibold">Email Address</label>
                    <input type="email" name="customer_email" id="customer_email" class="form-control form-control-custom" placeholder="name@example.com">
                    <div id="email_error" class="text-danger mt-1 small" style="display: none;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-semibold">Password</label>
                    <input type="password" name="customer_pass" id="customer_pass" class="form-control form-control-custom" placeholder="••••••••">
                    <div id="pass_error" class="text-danger mt-1 small" style="display: none;"></div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-semibold">Contact Number</label>
                    <input type="text" name="customer_contact" id="customer_contact" class="form-control form-control-custom" placeholder="+123456789">
                    <div id="contact_error" class="text-danger mt-1 small" style="display: none;"></div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label text-muted fw-semibold">Country</label>
                    <select name="customer_country" id="customer_country" class="form-select form-control-custom">
                        <option value="">-- Select Country --</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="United States">United States</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="Kenya">Kenya</option>
                        <option value="South Africa">South Africa</option>
                        <option value="Canada">Canada</option>
                    </select>
                    <div id="country_error" class="text-danger mt-1 small" style="display: none;"></div>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label text-muted fw-semibold">City</label>
                    <select name="customer_city" id="customer_city" class="form-select form-control-custom">
                        <option value="">-- Select Country First --</option>
                    </select>
                    <div id="city_error" class="text-danger mt-1 small" style="display: none;"></div>
                </div>
            </div>

            <!-- Optional Image -->
            <div class="mb-4 d-none">
                <label class="form-label text-muted fw-semibold">Image URL (optional)</label>
                <input type="text" name="customer_image" id="customer_image" class="form-control form-control-custom">
            </div>

            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary-custom py-2 fs-5">Register</button>
            </div>
        </form>

        <p class="text-center text-muted">Already have an account? <a href="login.php" class="text-primary text-decoration-none fw-bold">Login here</a></p>

    </div>
</div>

<script src="../js/validate.js"></script>

</main>
<?php
require_once __DIR__ . '/layout/footer.php';
?>
