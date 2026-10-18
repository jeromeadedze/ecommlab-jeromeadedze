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
}
