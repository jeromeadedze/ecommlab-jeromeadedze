<?php

require_once __DIR__ . "/../classes/CartClass.php";

/**
 * CartController
 * Acts as the intermediary between views/actions and the CartClass model.
 */
class CartController
{
    private $cartModel;

    public function __construct()
    {
        $this->cartModel = new CartClass();
    }

    /**
     * Check if item is already in cart
     */
    public function isInCart($product_id, $ip)
    {
        return $this->cartModel->isInCart($product_id, $ip);
    }

    /**
     * Add an item to cart
     */
    public function addToCart($product_id, $qty, $ip, $customer_id = null)
    {
        return $this->cartModel->addToCart($product_id, $qty, $ip, $customer_id);
    }

    /**
     * Retrieve cart items
     */
    public function getCartItems($ip)
    {
        return $this->cartModel->getCartItems($ip);
    }

    /**
     * Get number of items in cart
     */
    public function getCartCount($ip)
    {
        return $this->cartModel->getCartCount($ip);
    }

    /**
     * Get monetary total of cart
     */
    public function getCartTotal($ip)
    {
        return $this->cartModel->getCartTotal($ip);
    }

    /**
     * Remove an item from the cart
     */
    public function removeFromCart($product_id, $ip)
    {
        return $this->cartModel->removeFromCart($product_id, $ip);
    }

    /**
     * Update quantity of an item
     */
    public function updateQty($product_id, $new_qty, $ip)
    {
        return $this->cartModel->updateQty($product_id, $new_qty, $ip);
    }
}
