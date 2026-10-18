<?php
require_once __DIR__ . "/../core/db_class.php";

class ProductClass extends Database
{
    // Insert a new brand into the database
    public function addBrand($name, $cat_id = null)
    {
        try {
            $sql = "INSERT INTO brands (brand_name, brand_cat) VALUES (?, ?)";
            return $this->execute($sql, [$name, $cat_id ?: null]);
        } catch (Exception $e) {
            $sql = "INSERT INTO brands (brand_name) VALUES (?)";
            return $this->execute($sql, [$name]);
        }
    }

    // Fetch all brands ordered by name ASC (including category details)
    public function getAllBrands()
    {
        try {
            $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name AS category_name
                    FROM brands b
                    LEFT JOIN categories c ON b.brand_cat = c.cat_id
                    ORDER BY b.brand_name ASC";
            return $this->fetchAll($sql);
        } catch (Exception $e) {
            $sql = "SELECT brand_id, brand_name, NULL AS brand_cat, NULL AS category_name
                    FROM brands
                    ORDER BY brand_name ASC";
            return $this->fetchAll($sql);
        }
    }

    // Fetch a single brand by its ID
    public function getBrandById($id)
    {
        try {
            $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name AS category_name
                    FROM brands b
                    LEFT JOIN categories c ON b.brand_cat = c.cat_id
                    WHERE b.brand_id = ?";
            return $this->fetchOne($sql, [$id]);
        } catch (Exception $e) {
            $sql = "SELECT brand_id, brand_name, NULL AS brand_cat, NULL AS category_name
                    FROM brands
                    WHERE brand_id = ?";
            return $this->fetchOne($sql, [$id]);
        }
    }

    // Update an existing brand's name and category
    public function updateBrand($id, $name, $cat_id = null)
    {
        try {
            $sql = "UPDATE brands SET brand_name = ?, brand_cat = ? WHERE brand_id = ?";
            return $this->execute($sql, [$name, $cat_id ?: null, $id]);
        } catch (Exception $e) {
            $sql = "UPDATE brands SET brand_name = ? WHERE brand_id = ?";
            return $this->execute($sql, [$name, $id]);
        }
    }

    // Delete a brand by ID
    public function deleteBrand($id)
    {
        $sql = "DELETE FROM brands WHERE brand_id = ?";
        return $this->execute($sql, [$id]);
    }

    // Insert a new category into the database (returns new category ID on success)
    public function addCategory($name)
    {
        $sql = "INSERT INTO categories (cat_name) VALUES (?)";
        $success = $this->execute($sql, [$name]);
        return $success ? $this->getLastInsertId() : false;
    }

    // Fetch all categories ordered by name ASC
    public function getAllCategories()
    {
        $sql = "SELECT * FROM categories ORDER BY cat_name ASC";
        return $this->fetchAll($sql);
    }

    // Fetch a single category by ID
    public function getCategoryById($id)
    {
        $sql = "SELECT * FROM categories WHERE cat_id = ?";
        return $this->fetchOne($sql, [$id]);
    }

    // Update an existing category
    public function updateCategory($id, $name)
    {
        $sql = "UPDATE categories SET cat_name = ? WHERE cat_id = ?";
        return $this->execute($sql, [$name, $id]);
    }

    // Delete a category by ID
    public function deleteCategory($id)
    {
        $sql = "DELETE FROM categories WHERE cat_id = ?";
        return $this->execute($sql, [$id]);
    }

    // ============================================================
    // Product Methods (Task 9 & Task 10)
    // ============================================================

    // Insert a new product
    public function addProduct($cat, $brand, $title, $price, $desc, $image_filename, $keywords)
    {
        $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        return $this->execute($sql, [$cat, $brand, $title, $price, $desc, $image_filename, $keywords]);
    }

    // Update an existing product
    public function updateProduct($id, $cat, $brand, $title, $price, $desc, $image_filename, $keywords)
    {
        $sql = "UPDATE products 
                SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_image = ?, product_keywords = ? 
                WHERE product_id = ?";
        return $this->execute($sql, [$cat, $brand, $title, $price, $desc, $image_filename, $keywords, $id]);
    }

    // Get a single product by ID with category and brand names joined
    public function getProductById($id)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id = ?";
        return $this->fetchOne($sql, [$id]);
    }

    // Get all products (useful for admin listing)
    public function getAllProducts()
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC";
        $result = $this->fetchAll($sql);
        return is_array($result) ? $result : [];
    }

    // Get featured products with limit (stable order: latest first)
    public function getFeaturedProducts($limit = 6)
    {
        $limit = intval($limit);
        if ($limit <= 0) {
            $limit = 6;
        }
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC LIMIT " . $limit;
        $result = $this->fetchAll($sql);
        return is_array($result) ? $result : [];
    }

    // Get products filtered by category
    public function getProductsByCategory($cat_id)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_cat = ?";
        $result = $this->fetchAll($sql, [$cat_id]);
        return is_array($result) ? $result : [];
    }

    // Get products filtered by brand
    public function getProductsByBrand($brand_id)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_brand = ?";
        $result = $this->fetchAll($sql, [$brand_id]);
        return is_array($result) ? $result : [];
    }

    // Search products by title, keywords, brand, or category
    public function searchProducts($query)
    {
        $sql = "SELECT p.*, c.cat_name, b.brand_name 
                FROM products p 
                LEFT JOIN categories c ON p.product_cat = c.cat_id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_title LIKE ? 
                   OR p.product_keywords LIKE ? 
                   OR b.brand_name LIKE ? 
                   OR c.cat_name LIKE ?";
        $searchTerm = '%' . $query . '%';
        $result = $this->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return is_array($result) ? $result : [];
    }
}
?>
