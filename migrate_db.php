<?php
require_once __DIR__ . '/core/db_class.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Check if brand_cat column already exists
    $check = $conn->query("SHOW COLUMNS FROM brands LIKE 'brand_cat'")->fetch();

    if (!$check) {
        // Add brand_cat column
        $conn->exec("ALTER TABLE brands ADD COLUMN brand_cat INT NULL");
        
        // Try adding foreign key constraint (if categories table exists)
        try {
            $conn->exec("ALTER TABLE brands ADD CONSTRAINT fk_brand_cat FOREIGN KEY (brand_cat) REFERENCES categories(cat_id) ON DELETE SET NULL");
        } catch (Exception $e) {
            // Foreign key might fail if constraint name exists or engine mismatch, ignore
        }
        echo "<h2 style='color:green;'>✅ Success: 'brand_cat' column added to 'brands' table on this server!</h2>";
    } else {
        echo "<h2 style='color:blue;'>ℹ️ 'brand_cat' column already exists on this server.</h2>";
    }

    echo "<p><a href='views/admin/brand.php'>Go to Brand Management</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ Migration Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
?>
