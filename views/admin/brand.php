<?php
define('ROOT_DIR', '../..');
require_once __DIR__ . "/../../core/core.php";
require_once __DIR__ . "/../../controllers/ProductController.php";

// Restrict access to admin
require_admin();

$controller = new ProductController();
$brands = $controller->getAllBrands();
$categories = $controller->getAllCategories();

// Check if we are in edit mode
$editBrand = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($edit_id > 0) {
        $editBrand = $controller->getBrandById($edit_id);
    }
}

// Include layout header
require_once __DIR__ . "/../layout/header.php";
?>

<div class="container py-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="<?php echo $ROOT_DIR; ?>/index.php" class="text-decoration-none">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Brands</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-3">Brand Management</h2>
        <div class="d-flex gap-2">
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
            <a href="category.php" class="btn btn-outline-primary">
                <i class="bi bi-grid"></i> Manage Categories
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

    <!-- Add / Edit Brand Form -->
    <div class="card mb-5 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="card-title mb-0"><?php echo $editBrand ? 'Edit Brand' : 'Add New Brand'; ?></h5>
        </div>
        <div class="card-body">
            <form action="../../actions/<?php echo $editBrand ? 'update_brand_action.php' : 'add_brand_action.php'; ?>" method="POST">
                <?php if ($editBrand): ?>
                    <input type="hidden" name="brand_id" value="<?php echo htmlspecialchars($editBrand['brand_id']); ?>">
                <?php endif; ?>
                <div class="mb-3">
                    <label for="brand_name" class="form-label">Brand Name</label>
                    <input type="text" class="form-control" id="brand_name" name="brand_name" 
                           placeholder="Enter brand name" required
                           value="<?php echo $editBrand ? htmlspecialchars($editBrand['brand_name']) : ''; ?>">
                </div>

                <!-- Category Selection -->
                <div class="mb-3">
                    <label for="category_id" class="form-label">Assign Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">-- Select a Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['cat_id']; ?>" 
                                <?php echo ($editBrand && $editBrand['brand_cat'] == $cat['cat_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['cat_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Quick Add New Category -->
                <div class="mb-3">
                    <label for="new_category" class="form-label text-muted">Or add a new category</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="new_category" name="new_category" placeholder="New category name">
                        <span class="input-group-text bg-light"><i class="bi bi-plus-circle"></i></span>
                    </div>
                    <small class="form-text text-muted">If filled, a new category will be created automatically.</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <?php echo $editBrand ? 'Update Brand' : 'Add Brand'; ?>
                </button>
                <?php if ($editBrand): ?>
                    <a href="brand.php" class="btn btn-secondary ms-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Brands List Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title mb-0">Existing Brands</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Brand Name</th>
                            <th>Category</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($brands)): ?>
                            <?php foreach ($brands as $brand): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($brand['brand_id']); ?></td>
                                    <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                    <td><?php echo htmlspecialchars($brand['category_name'] ?: 'None'); ?></td>
                                    <td class="text-end pe-3">
                                        <a href="brand.php?edit_id=<?php echo $brand['brand_id']; ?>" class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteBrandModal"
                                                data-brand-id="<?php echo $brand['brand_id']; ?>"
                                                data-brand-name="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                                            <i class="bi bi-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-3 text-muted">No brands found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteBrandModal" tabindex="-1" aria-labelledby="deleteBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteBrandModalLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Confirm Delete
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-1 fs-6">Are you sure you want to delete <strong id="deleteBrandName" class="text-danger"></strong>?</p>
                <small class="text-muted">This action cannot be undone.</small>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="bi bi-trash"></i> Yes, Delete
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    const deleteBrandModal = document.getElementById('deleteBrandModal');
    if (deleteBrandModal) {
        deleteBrandModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const brandId = button.getAttribute('data-brand-id');
            const brandName = button.getAttribute('data-brand-name');

            document.getElementById('deleteBrandName').textContent = "'" + brandName + "'";
            document.getElementById('confirmDeleteBtn').href = '../../actions/delete_brand_action.php?id=' + brandId;
        });
    }
</script>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
