<?php
require_once __DIR__ . "/../core/db_class.php";

class ProductClass extends Database
{
    // Insert a new brand into the database
    public function addBrand($name, $cat_id = null)
    {
        $sql = "INSERT INTO brands (brand_name, brand_cat) VALUES (?, ?)";
        return $this->execute($sql, [$name, $cat_id ?: null]);
    }

    // Fetch all brands ordered by name ASC (including category details)
    public function getAllBrands()
    {
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name AS category_name
                FROM brands b
                LEFT JOIN categories c ON b.brand_cat = c.cat_id
                ORDER BY b.brand_name ASC";
        return $this->fetchAll($sql);
    }

    // Fetch a single brand by its ID
    public function getBrandById($id)
    {
        $sql = "SELECT b.brand_id, b.brand_name, b.brand_cat, c.cat_name AS category_name
                FROM brands b
                LEFT JOIN categories c ON b.brand_cat = c.cat_id
                WHERE b.brand_id = ?";
        return $this->fetchOne($sql, [$id]);
    }

    // Update an existing brand's name and category
    public function updateBrand($id, $name, $cat_id = null)
    {
        $sql = "UPDATE brands SET brand_name = ?, brand_cat = ? WHERE brand_id = ?";
        return $this->execute($sql, [$name, $cat_id ?: null, $id]);
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
}
?>
