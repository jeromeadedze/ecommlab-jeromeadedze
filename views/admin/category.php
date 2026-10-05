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
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="<?php echo $ROOT_DIR; ?>/index.php" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Categories</li>
                </ol>
            </nav>
            <h2 class="mb-0 fw-bold">Category Management</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
            <a href="brand.php" class="btn btn-outline-primary">
                <i class="bi bi-tags"></i> Manage Brands
            </a>
        </div>
    </div>

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
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($categories)): ?>
                            <?php foreach ($categories as $cat): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cat['cat_id']); ?></td>
                                    <td><?php echo htmlspecialchars($cat['cat_name']); ?></td>
                                    <td class="text-end pe-3">
                                        <a href="edit_category.php?id=<?php echo $cat['cat_id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteCatModal"
                                                data-cat-id="<?php echo $cat['cat_id']; ?>"
                                                data-cat-name="<?php echo htmlspecialchars($cat['cat_name']); ?>">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
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

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCatModal" tabindex="-1" aria-labelledby="deleteCatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCatModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-1 fs-6">Are you sure you want to delete <strong id="deleteCatName" class="text-danger"></strong>?</p>
                <small class="text-muted">This action cannot be undone.</small>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteCatBtn" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Yes, Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    const deleteCatModal = document.getElementById('deleteCatModal');
    if (deleteCatModal) {
        deleteCatModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const catId = button.getAttribute('data-cat-id');
            const catName = button.getAttribute('data-cat-name');

            document.getElementById('deleteCatName').textContent = "'" + catName + "'";
            document.getElementById('confirmDeleteCatBtn').href = '../../actions/delete_category_action.php?id=' + catId;
        });
    }
</script>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
