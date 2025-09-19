<?php  
require_once 'Database.php';

/**
 * Class for handling Subcategory-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Subcategory extends Database {

    /**
     * Creates a new Subcategory.
     * @param int $category_id The parent category ID.
     * @param string $subcategory_name The subcategory name.
     * @param string $description The subcategory description.
     * @return bool Success status.
     */
    public function createSubcategory($category_id, $subcategory_name, $description = '') {
        $sql = "INSERT INTO subcategories (category_id, subcategory_name, description) VALUES (?, ?, ?)";
        return $this->executeNonQuery($sql, [$category_id, $subcategory_name, $description]);
    }

    /**
     * Retrieves all active subcategories for a specific category.
     * @param int $category_id The category ID.
     * @return array Subcategories for the category.
     */
    public function getSubcategoriesByCategory($category_id, $active_only = true) {
        if ($active_only) {
            $sql = "SELECT * FROM subcategories WHERE category_id = ? AND is_active = 1 ORDER BY subcategory_name ASC";
        } else {
            $sql = "SELECT * FROM subcategories WHERE category_id = ? ORDER BY subcategory_name ASC";
        }
        return $this->executeQuery($sql, [$category_id]);
    }

    /**
     * Retrieves all active subcategories.
     * @return array All active subcategories.
     */
    public function getAllSubcategories($active_only = true) {
        if ($active_only) {
            $sql = "SELECT s.*, c.category_name 
                    FROM subcategories s 
                    JOIN categories c ON s.category_id = c.category_id 
                    WHERE s.is_active = 1 
                    ORDER BY c.category_name ASC, s.subcategory_name ASC";
        } else {
            $sql = "SELECT s.*, c.category_name 
                    FROM subcategories s 
                    JOIN categories c ON s.category_id = c.category_id 
                    ORDER BY c.category_name ASC, s.subcategory_name ASC";
        }
        return $this->executeQuery($sql);
    }

    /**
     * Retrieves a single subcategory by ID.
     * @param int $subcategory_id The subcategory ID.
     * @return array|null The subcategory data or null if not found.
     */
    public function getSubcategoryById($subcategory_id) {
        $sql = "SELECT s.*, c.category_name 
                FROM subcategories s 
                JOIN categories c ON s.category_id = c.category_id 
                WHERE s.subcategory_id = ?";
        return $this->executeQuerySingle($sql, [$subcategory_id]);
    }

    /**
     * Updates a subcategory.
     * @param int $subcategory_id The subcategory ID.
     * @param int $category_id The parent category ID.
     * @param string $subcategory_name The new subcategory name.
     * @param string $description The new description.
     * @return bool Success status.
     */
    public function updateSubcategory($subcategory_id, $category_id, $subcategory_name, $description = '') {
        $sql = "UPDATE subcategories SET category_id = ?, subcategory_name = ?, description = ? WHERE subcategory_id = ?";
        return $this->executeNonQuery($sql, [$category_id, $subcategory_name, $description, $subcategory_id]);
    }

    /**
     * Soft deletes a subcategory (sets is_active to false).
     * @param int $subcategory_id The subcategory ID.
     * @return bool Success status.
     */
    public function deleteSubcategory($subcategory_id) {
        $sql = "UPDATE subcategories SET is_active = 0 WHERE subcategory_id = ?";
        return $this->executeNonQuery($sql, [$subcategory_id]);
    }

    /**
     * Activates a subcategory.
     * @param int $subcategory_id The subcategory ID.
     * @return bool Success status.
     */
    public function activateSubcategory($subcategory_id) {
        $sql = "UPDATE subcategories SET is_active = 1 WHERE subcategory_id = ?";
        return $this->executeNonQuery($sql, [$subcategory_id]);
    }
}
?>
