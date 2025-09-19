<?php  
require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Category extends Database {

    /**
     * Creates a new Category.
     * @param string $category_name The category name.
     * @param string $description The category description.
     * @return bool Success status.
     */
    public function createCategory($category_name, $description = '') {
        $sql = "INSERT INTO categories (category_name, description) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$category_name, $description]);
    }

    /**
     * Retrieves all active categories.
     * @return array All active categories.
     */
    public function getCategories($active_only = true) {
        if ($active_only) {
            $sql = "SELECT * FROM categories WHERE is_active = 1 ORDER BY category_name ASC";
        } else {
            $sql = "SELECT * FROM categories ORDER BY category_name ASC";
        }
        return $this->executeQuery($sql);
    }

    /**
     * Retrieves a single category by ID.
     * @param int $category_id The category ID.
     * @return array|null The category data or null if not found.
     */
    public function getCategoryById($category_id) {
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        return $this->executeQuerySingle($sql, [$category_id]);
    }

    /**
     * Updates a category.
     * @param int $category_id The category ID.
     * @param string $category_name The new category name.
     * @param string $description The new description.
     * @return bool Success status.
     */
    public function updateCategory($category_id, $category_name, $description = '') {
        $sql = "UPDATE categories SET category_name = ?, description = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_name, $description, $category_id]);
    }

    /**
     * Soft deletes a category (sets is_active to false).
     * @param int $category_id The category ID.
     * @return bool Success status.
     */
    public function deleteCategory($category_id) {
        $sql = "UPDATE categories SET is_active = 0 WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_id]);
    }

    /**
     * Activates a category.
     * @param int $category_id The category ID.
     * @return bool Success status.
     */
    public function activateCategory($category_id) {
        $sql = "UPDATE categories SET is_active = 1 WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$category_id]);
    }

    /**
     * Gets categories with their subcategories.
     * @return array Categories with their subcategories.
     */
    public function getCategoriesWithSubcategories() {
        $sql = "SELECT c.*, s.subcategory_id, s.subcategory_name, s.description as subcategory_description
                FROM categories c 
                LEFT JOIN subcategories s ON c.category_id = s.category_id AND s.is_active = 1
                WHERE c.is_active = 1 
                ORDER BY c.category_name ASC, s.subcategory_name ASC";
        return $this->executeQuery($sql);
    }
}
?>
