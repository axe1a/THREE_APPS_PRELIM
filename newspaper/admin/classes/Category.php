<?php  

require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Category extends Database {
    
    /**
     * Creates a new category.
     */
    public function createCategory($name, $description = null) {
        $sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$name, $description]);
    }

    /**
     * Retrieves all categories.
     */
    public function getCategories() {
        $sql = "SELECT * FROM categories ORDER BY name ASC";
        return $this->executeQuery($sql);
    }

    /**
     * Retrieves a single category by ID.
     */
    public function getCategory($id) {
        $sql = "SELECT * FROM categories WHERE category_id = ?";
        return $this->executeQuerySingle($sql, [$id]);
    }

    /**
     * Updates a category.
     */
    public function updateCategory($id, $name, $description = null) {
        $sql = "UPDATE categories SET name = ?, description = ? WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$name, $description, $id]);
    }

    /**
     * Deletes a category.
     */
    public function deleteCategory($id) {
        $sql = "DELETE FROM categories WHERE category_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    /**
     * Checks if a category name already exists (excluding current category for updates).
     */
    public function categoryNameExists($name, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT COUNT(*) as count FROM categories WHERE name = ? AND category_id != ?";
            $result = $this->executeQuerySingle($sql, [$name, $excludeId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM categories WHERE name = ?";
            $result = $this->executeQuerySingle($sql, [$name]);
        }
        return $result['count'] > 0;
    }

    /**
     * Gets categories with article count.
     */
    public function getCategoriesWithCount() {
        $sql = "SELECT c.*, COUNT(a.article_id) as article_count 
                FROM categories c 
                LEFT JOIN articles a ON c.category_id = a.category_id 
                GROUP BY c.category_id 
                ORDER BY c.name ASC";
        return $this->executeQuery($sql);
    }
}
?>
