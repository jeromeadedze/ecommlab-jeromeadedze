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

    // Delete a category by ID
    public function deleteCategory($id)
    {
        $sql = "DELETE FROM categories WHERE cat_id = ?";
        return $this->execute($sql, [$id]);
    }
}
?>
