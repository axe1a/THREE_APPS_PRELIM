<?php  

require_once 'Database.php';

/**
 * Class for handling Category-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Category extends Database {
    
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
}
?>
