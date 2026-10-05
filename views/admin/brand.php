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
    <h2 class="mb-4">Brand Management</h2>

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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($brands)): ?>
                            <?php foreach ($brands as $brand): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($brand['brand_id']); ?></td>
                                    <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                    <td><?php echo htmlspecialchars($brand['category_name'] ?: 'None'); ?></td>
                                    <td>
                                        <a href="brand.php?edit_id=<?php echo $brand['brand_id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
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

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
