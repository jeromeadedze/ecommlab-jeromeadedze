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
     * Turn the cart into a paid order, inside a single DB transaction:
     *   1. generate an invoice number
     *   2. insert the order
     *   3. copy cart rows into orderdetails
     *   4. record the payment
     *   5. empty the cart
     * If any step fails, everything is rolled back.
     *
     * @param int    $customer_id
     * @param string $ip
     * @param string $reference Paystack transaction reference
     * @param float  $amount    Amount paid (in cedis, not pesewas)
     * @return int|false The new order_id, or false on failure
     */
    public function processPayment($customer_id, $ip, $reference, $amount)
    {
        $conn = $this->getConnection();

        try {
            $conn->beginTransaction();

            $cartItems = $this->getCartItems($ip);
            if (empty($cartItems)) {
                throw new Exception("Cart is empty.");
            }

            // 1. Invoice number
            $invoice_no = mt_rand(100000, 999999);

            // 2. Order
            $this->execute(
                "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                 VALUES (?, ?, CURDATE(), 'paid')",
                [$customer_id, $invoice_no]
            );
            $order_id = (int)$this->getLastInsertId();

            // 3. Copy cart rows into orderdetails
            foreach ($cartItems as $item) {
                $this->execute(
                    "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)",
                    [$order_id, $item['p_id'], $item['qty']]
                );
            }

            // Record the payment against the order
            $this->execute(
                "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
                 VALUES (?, ?, ?, 'GHS', CURDATE())",
                [$amount, $customer_id, $order_id]
            );

            // 4. Empty the cart
            $this->execute("DELETE FROM cart WHERE ip_add = ?", [$ip]);

            $conn->commit();

            // 5. Return the new order id
            return $order_id;
        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("processPayment failed (ref $reference): " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fetch an order with its line items and product names.
     *
     * @param int $order_id
     * @return array|null ['order' => row, 'items' => rows, 'total' => float]
     */
    public function getOrderById($order_id)
    {
        $order = $this->fetchOne(
            "SELECT o.order_id, o.customer_id, o.invoice_no, o.order_date, o.order_status
             FROM orders o WHERE o.order_id = ?",
            [$order_id]
        );
        if (!$order) {
            return null;
        }

        $items = $this->fetchAll(
            "SELECT od.product_id, od.qty, p.product_title, p.product_price
             FROM orderdetails od
             JOIN products p ON od.product_id = p.product_id
             WHERE od.order_id = ?",
            [$order_id]
        );

        $total = 0.0;
        foreach ($items as $item) {
            $total += $item['qty'] * (float)$item['product_price'];
        }

        return ['order' => $order, 'items' => $items, 'total' => $total];
    }
}
