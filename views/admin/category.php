<?php
define('ROOT_DIR', '../..');
require_once __DIR__ . "/../../core/core.php";
require_once __DIR__ . "/../../controllers/ProductController.php";

// Restrict access to admin
require_admin();

$controller = new ProductController();
$categories = $controller->getAllCategories();

// Include layout header
require_once __DIR__ . "/../layout/header.php";
?>

<div class="container py-4">
    <h2 class="mb-4">Category Management</h2>

    <!-- Display Flash Success / Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Add Category Form -->
    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0">Add New Category</h5>
        </div>
        <div class="card-body">
            <form action="../../actions/add_category_action.php" method="POST">
                <div class="mb-3">
                    <label for="cat_name" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="cat_name" name="cat_name" placeholder="Enter category name" required>
                </div>
                <button type="submit" class="btn btn-primary">Add Category</button>
            </form>
        </div>
    </div>

    <!-- Categories List Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title mb-0">Existing Categories</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['cat_id']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['cat_name']); ?></td>
                                    <td>
                                        <a href="edit_category.php?id=<?php echo $cat['cat_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center py-3 text-muted">No categories found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
