<?php
define('ROOT_DIR', '../..');
require_once __DIR__ . "/../../core/core.php";
require_once __DIR__ . "/../../controllers/ProductController.php";

// Restrict access to admin
require_admin();

$controller = new ProductController();
$categories = $controller->getAllCategories();
$brands = $controller->getAllBrands();
$products = $controller->getAllProducts();

// Check if we are in edit mode
$editProduct = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    if ($edit_id > 0) {
        $editProduct = $controller->getProductById($edit_id);
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
                <li class="breadcrumb-item active" aria-current="page">Products</li>
            </ol>
        </nav>
        <h2 class="fw-bold mb-3">Product Management</h2>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?php echo $ROOT_DIR; ?>/index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Home
            </a>
            <a href="brand.php" class="btn-light-purple">
                <i class="bi bi-tags"></i> Manage Brands
            </a>
            <a href="category.php" class="btn-light-purple">
                <i class="bi bi-grid"></i> Manage Categories
            </a>
            <a href="<?php echo $ROOT_DIR; ?>/views/customers.php" class="btn-light-purple">
                <i class="bi bi-people"></i> Customers
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
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

    <!-- Add / Edit Product Form -->
    <div class="card mb-5 shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header card-header-violet py-3">
            <h5 class="card-title mb-0 fw-bold"><?php echo $editProduct ? 'Edit Product' : 'Add New Product'; ?></h5>
        </div>
        <div class="card-body">
            <form id="productForm" 
                  action="../../actions/<?php echo $editProduct ? 'update_product_action.php' : 'add_product_action.php'; ?>" 
                  method="POST" 
                  enctype="multipart/form-data">

                <?php if ($editProduct): ?>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($editProduct['product_id']); ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($editProduct['product_image'] ?? ''); ?>">
                <?php endif; ?>

                <div class="row">
                    <!-- Title -->
                    <div class="col-md-6 mb-3">
                        <label for="product_title" class="form-label">Product Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="product_title" name="product_title" 
                               placeholder="e.g. Wireless Noise-Cancelling Headphones" required
                               value="<?php echo $editProduct ? htmlspecialchars($editProduct['product_title']) : ''; ?>">
                    </div>

                    <!-- Price -->
                    <div class="col-md-6 mb-3">
                        <label for="product_price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" step="0.01" min="0.01" class="form-control" id="product_price" name="product_price" 
                                   placeholder="0.00" required
                                   value="<?php echo $editProduct ? htmlspecialchars($editProduct['product_price']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Category Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label for="product_cat" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_cat" name="product_cat" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['cat_id']; ?>" 
                                    <?php echo ($editProduct && $editProduct['product_cat'] == $cat['cat_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['cat_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Brand Dropdown -->
                    <div class="col-md-6 mb-3">
                        <label for="product_brand" class="form-label">Brand <span class="text-danger">*</span></label>
                        <select class="form-select" id="product_brand" name="product_brand" required>
                            <option value="">-- Select Brand --</option>
                            <?php foreach ($brands as $brand): ?>
                                <option value="<?php echo $brand['brand_id']; ?>" 
                                    <?php echo ($editProduct && $editProduct['product_brand'] == $brand['brand_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($brand['brand_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="product_desc" class="form-label">Description</label>
                    <textarea class="form-control" id="product_desc" name="product_desc" rows="3" 
                              placeholder="Enter product description..."><?php echo $editProduct ? htmlspecialchars($editProduct['product_desc'] ?? '') : ''; ?></textarea>
                </div>

                <!-- Keywords -->
                <div class="mb-3">
                    <label for="product_keywords" class="form-label">Keywords</label>
                    <input type="text" class="form-control" id="product_keywords" name="product_keywords" 
                           placeholder="e.g. audio, wireless, electronics"
                           value="<?php echo $editProduct ? htmlspecialchars($editProduct['product_keywords'] ?? '') : ''; ?>">
                    <small class="form-text text-muted">Comma-separated tags or search terms.</small>
                </div>

                <!-- Image Upload with Preview -->
                <div class="mb-4">
                    <label for="product_image" class="form-label">
                        Product Image <?php echo $editProduct ? '(leave empty to keep current)' : ''; ?>
                    </label>
                    <input type="file" class="form-control" id="product_image" name="product_image" 
                           accept="image/jpeg,image/png,image/gif,image/webp">
                    <small class="form-text text-muted">Allowed formats: JPG, PNG, GIF, WEBP. Max size: 5MB.</small>
                    <div id="imageClientError" class="text-danger small mt-1 d-none"></div>

                    <!-- Image Preview Area -->
                    <div class="mt-3">
                        <?php if ($editProduct && !empty($editProduct['product_image'])): ?>
                            <div class="mb-2" id="existingPreviewContainer">
                                <span class="text-muted small d-block mb-1">Current Image:</span>
                                <img src="../../images/products/<?php echo htmlspecialchars($editProduct['product_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($editProduct['product_title']); ?>" 
                                     class="rounded border shadow-sm" 
                                     style="max-width: 140px; max-height: 140px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <div id="newImagePreviewContainer" class="d-none">
                            <span class="text-muted small d-block mb-1">Selected Image Preview:</span>
                            <img id="newImagePreview" src="#" alt="Preview" 
                                 class="rounded border shadow-sm" 
                                 style="max-width: 140px; max-height: 140px; object-fit: cover;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary px-4">
                    <?php echo $editProduct ? 'Update Product' : 'Add Product'; ?>
                </button>
                <?php if ($editProduct): ?>
                    <a href="product.php" class="btn btn-secondary ms-2">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Existing Products Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="card-title mb-0">Existing Products</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th class="text-end pe-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['product_id']); ?></td>
                                    <td>
                                        <?php if (!empty($p['product_image'])): ?>
                                            <img src="../../images/products/<?php echo htmlspecialchars($p['product_image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($p['product_title']); ?>" 
                                                 class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" 
                                                 style="width: 48px; height: 48px; font-size: 0.8rem;">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($p['product_title']); ?></td>
                                    <td><?php echo htmlspecialchars($p['cat_name'] ?? 'None'); ?></td>
                                    <td><?php echo htmlspecialchars($p['brand_name'] ?? 'None'); ?></td>
                                    <td>$<?php echo number_format($p['product_price'], 2); ?></td>
                                    <td class="text-end pe-3">
                                        <a href="product.php?edit_id=<?php echo $p['product_id']; ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No products found. Add your first product above.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Client-side validation & preview
const imageInput = document.getElementById('product_image');
const previewContainer = document.getElementById('newImagePreviewContainer');
const previewImage = document.getElementById('newImagePreview');
const clientError = document.getElementById('imageClientError');
const productForm = document.getElementById('productForm');

const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
const maxSize = 5 * 1024 * 1024; // 5MB

if (imageInput) {
    imageInput.addEventListener('change', function () {
        clientError.classList.add('d-none');
        clientError.textContent = '';

        const file = this.files[0];
        if (!file) {
            previewContainer.classList.add('d-none');
            return;
        }

        // Validate type
        if (!allowedTypes.includes(file.type)) {
            clientError.textContent = 'Invalid file type. Please select a JPG, PNG, GIF, or WEBP image.';
            clientError.classList.remove('d-none');
            this.value = '';
            previewContainer.classList.add('d-none');
            return;
        }

        // Validate size
        if (file.size > maxSize) {
            clientError.textContent = 'File is too large. Maximum allowed size is 5MB.';
            clientError.classList.remove('d-none');
            this.value = '';
            previewContainer.classList.add('d-none');
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = function (e) {
            previewImage.src = e.target.result;
            previewContainer.classList.remove('d-none');
        };
        reader.readAsDataURL(file);
    });
}

if (productForm) {
    productForm.addEventListener('submit', function (e) {
        const file = imageInput.files[0];
        if (file) {
            if (!allowedTypes.includes(file.type) || file.size > maxSize) {
                e.preventDefault();
                clientError.textContent = 'Please provide a valid image (JPG, PNG, GIF, WEBP) under 5MB.';
                clientError.classList.remove('d-none');
            }
        }
    });
}
</script>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
