<?php

require_once __DIR__ . "/../core/db_class.php";

/**
 * CartClass (Model)
 * Handles all database operations for the shopping cart table.
 */
class CartClass extends Database
{
    /**
     * Check if a product is already in the cart for a given IP address.
     *
     * @param int $product_id
     * @param string $ip
     * @return bool
     */
    public function isInCart($product_id, $ip)
    {
        $sql = "SELECT p_id FROM cart WHERE p_id = ? AND ip_add = ?";
        $row = $this->fetchOne($sql, [$product_id, $ip]);
        return $row ? true : false;
    }

    /**
     * Add a product to the cart.
     *
     * @param int $product_id
     * @param int $qty
     * @param string $ip
     * @param int|null $customer_id
     * @return bool
     */
    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        $sql = "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)";
        return $this->execute($sql, [$product_id, $ip, $customer_id, $qty]);
    }

    /**
     * Retrieve all cart items for a given IP address, joined with product details.
     *
     * @param string $ip
     * @return array
     */
    public function getCartItems($ip)
    {
        $sql = "SELECT cart.*, products.product_title, products.product_price, products.product_image 
                FROM cart 
                JOIN products ON cart.p_id = products.product_id 
                WHERE cart.ip_add = ?";
        return $this->fetchAll($sql, [$ip]);
    }

    /**
     * Get the total number of items in the cart for a given IP.
     *
     * @param string $ip
     * @return int
     */
    public function getCartCount($ip)
    {
        $sql = "SELECT COUNT(*) as item_count FROM cart WHERE ip_add = ?";
        $row = $this->fetchOne($sql, [$ip]);
        return $row ? (int)$row['item_count'] : 0;
    }

    /**
     * Calculate the total monetary value of items in the cart for a given IP.
     *
     * @param string $ip
     * @return float
     */
    public function getCartTotal($ip)
    {
        $items = $this->getCartItems($ip);
        $total = 0.0;
        foreach ($items as $item) {
            $total += ($item['qty'] * (float)$item['product_price']);
        }
        return $total;
    }

    /**
     * Remove an item from the cart.
     *
     * @param int $product_id
     * @param string $ip
     * @return bool
     */
    public function removeFromCart($product_id, $ip)
    {
        $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ?";
        return $this->execute($sql, [$product_id, $ip]);
    }

    /**
     * Update quantity of an item in the cart.
     *
     * @param int $product_id
     * @param int $new_qty
     * @param string $ip
     * @return bool
     */
    public function updateQty($product_id, $new_qty, $ip)
    {
        if ($new_qty < 1) {
            return false;
        }
        $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ?";
        return $this->execute($sql, [$new_qty, $product_id, $ip]);
    }

    /**
     * Turn the cart for an IP into a paid order, inside one DB transaction.
     * 1. generate invoice number  2. insert order  3. copy cart rows into
     * orderdetails  4. record payment  5. empty cart  6. return order_id.
     *
     * @return int|false new order_id, or false on failure
     */
    public function processPayment($customer_id, $ip, $reference, $amount)
    {
        $items = $this->getCartItems($ip);
        if (empty($items)) {
            return false;
        }

        $pdo = $this->getConnection();
        try {
            $pdo->beginTransaction();

            $invoice_no = mt_rand(100000, 999999);

            // Support both standard shoppn schema and shared schema with drop_location
            $hasDropLoc = (bool)$this->fetchOne("SHOW COLUMNS FROM orders LIKE 'drop_location'");
            if ($hasDropLoc) {
                $this->execute(
                    "INSERT INTO orders (customer_id, invoice_no, order_date, order_status, drop_location) VALUES (?, ?, CURDATE(), 'paid', 'Online Order')",
                    [$customer_id, $invoice_no]
                );
            } else {
                $this->execute(
                    "INSERT INTO orders (customer_id, invoice_no, order_date, order_status) VALUES (?, ?, CURDATE(), 'paid')",
                    [$customer_id, $invoice_no]
                );
            }
            $order_id = (int)$this->getLastInsertId();

            $this->execute(
                "INSERT INTO orderdetails (order_id, product_id, qty) SELECT ?, p_id, qty FROM cart WHERE ip_add = ?",
                [$order_id, $ip]
            );

            $this->execute(
                "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date) VALUES (?, ?, ?, 'GHS', CURDATE())",
                [$amount, $customer_id, $order_id]
            );

            $this->execute("DELETE FROM cart WHERE ip_add = ?", [$ip]);

            $pdo->commit();
            return $order_id;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            if (session_status() === PHP_SESSION_ACTIVE) {
                $_SESSION['db_error'] = $e->getMessage();
            }
            error_log("processPayment failed (ref $reference): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch an order with its line items (joined with product names/prices).
     * Only returns rows belonging to $customer_id when one is supplied.
     *
     * @return array rows (empty if not found)
     */
    public function getOrderById($order_id, $customer_id = null)
    {
        $sql = "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status, o.customer_id,
                       od.product_id, od.qty, p.product_title, p.product_price
                FROM orders o
                JOIN orderdetails od ON od.order_id = o.order_id
                JOIN products p ON p.product_id = od.product_id
                WHERE o.order_id = ?";
        $params = [$order_id];
        if ($customer_id !== null) {
            $sql .= " AND o.customer_id = ?";
            $params[] = $customer_id;
        }
        return $this->fetchAll($sql, $params);
    }

    /**
     * List all orders for a customer with their totals, newest first.
     */
    public function getOrdersByCustomer($customer_id)
    {
        $sql = "SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
                       SUM(od.qty) AS item_count, SUM(od.qty * p.product_price) AS total
                FROM orders o
                JOIN orderdetails od ON od.order_id = o.order_id
                JOIN products p ON p.product_id = od.product_id
                WHERE o.customer_id = ?
                GROUP BY o.order_id, o.invoice_no, o.order_date, o.order_status
                ORDER BY o.order_id DESC";
        return $this->fetchAll($sql, [$customer_id]);
    }

}
