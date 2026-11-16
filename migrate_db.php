<?php
require_once __DIR__ . '/core/db_class.php';

try {
    $db = new Database();
    $conn = $db->getConnection();

    echo "<h1>Shoppn Database Migration</h1>";

    // 1. Ensure brand_cat exists on brands
    $check = $conn->query("SHOW COLUMNS FROM brands LIKE 'brand_cat'")->fetch();
    if (!$check) {
        $conn->exec("ALTER TABLE brands ADD COLUMN brand_cat INT NULL");
        try {
            $conn->exec("ALTER TABLE brands ADD CONSTRAINT fk_brand_cat FOREIGN KEY (brand_cat) REFERENCES categories(cat_id) ON DELETE SET NULL");
        } catch (Exception $e) {}
        echo "<h3 style='color:green;'>✅ Added 'brand_cat' column to 'brands'.</h3>";
    } else {
        echo "<h3 style='color:blue;'>ℹ️ 'brand_cat' column already exists in 'brands'.</h3>";
    }

    // 2. Ensure 'orders' table exists
    $conn->exec("CREATE TABLE IF NOT EXISTS `orders` (
      `order_id` int(11) NOT NULL AUTO_INCREMENT,
      `customer_id` int(11) NOT NULL,
      `invoice_no` int(11) NOT NULL,
      `order_date` date NOT NULL,
      `order_status` varchar(100) NOT NULL DEFAULT 'paid',
      PRIMARY KEY (`order_id`),
      KEY `customer_id` (`customer_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<h3 style='color:green;'>✅ Verified 'orders' table.</h3>";

    // 3. If orders has drop_location (from previous setups), make it optional
    try {
        $orderCheck = $conn->query("SHOW COLUMNS FROM orders LIKE 'drop_location'")->fetch();
        if ($orderCheck) {
            $conn->exec("ALTER TABLE orders MODIFY COLUMN drop_location VARCHAR(150) NULL DEFAULT 'Online Order'");
            echo "<h3 style='color:green;'>✅ 'drop_location' column in 'orders' made optional with default.</h3>";
        }
    } catch (Exception $e) {}

    // 4. Ensure 'orderdetails' table exists
    $conn->exec("CREATE TABLE IF NOT EXISTS `orderdetails` (
      `order_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `qty` int(11) NOT NULL,
      KEY `order_id` (`order_id`),
      KEY `product_id` (`product_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<h3 style='color:green;'>✅ Verified 'orderdetails' table.</h3>";

    // 5. Ensure 'payment' table exists
    $conn->exec("CREATE TABLE IF NOT EXISTS `payment` (
      `pay_id` int(11) NOT NULL AUTO_INCREMENT,
      `amt` double NOT NULL,
      `customer_id` int(11) NOT NULL,
      `order_id` int(11) NOT NULL,
      `currency` text NOT NULL,
      `payment_date` date NOT NULL,
      PRIMARY KEY (`pay_id`),
      KEY `customer_id` (`customer_id`),
      KEY `order_id` (`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    echo "<h3 style='color:green;'>✅ Verified 'payment' table.</h3>";

    echo "<hr><p><a href='index.php'>Go to Shop Home</a> | <a href='views/cart.php'>Go to Cart</a></p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>❌ Migration Error: " . htmlspecialchars($e->getMessage()) . "</h2>";
}
