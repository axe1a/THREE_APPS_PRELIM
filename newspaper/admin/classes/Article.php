<?php  

require_once 'Database.php';
require_once 'User.php';
/**
 * Class for handling Article-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Article extends Database {
    /**
     * Creates a new article.
     */
    public function createArticle($title, $content, $author_id, $category_id = null, $image_url = null) {
        $sql = "INSERT INTO articles (title, content, author_id, category_id, image_url, is_active) VALUES (?, ?, ?, ?, ?, 1)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $category_id, $image_url]);
    }

    /** Adds a notification for a user. */
    public function addNotification($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$user_id, $message]);
    }

    /** Create an edit request. */
    public function createEditRequest($article_id, $requester_id) {
        $sql = "INSERT INTO edit_requests (article_id, requester_id) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $requester_id]);
    }

    /** Update an edit request's status. */
    public function updateEditRequestStatus($article_id, $requester_id, $status) {
        $sql = "UPDATE edit_requests SET status = ? WHERE article_id = ? AND requester_id = ?";
        return $this->executeNonQuery($sql, [$status, $article_id, $requester_id]);
    }

    /** Returns pending edit requests joined with article and requester info. */
    public function getPendingEditRequests() {
        $sql = "SELECT er.article_id, er.requester_id, er.status, er.created_at,
                       a.title, a.author_id,
                       u.username AS requester_name
                FROM edit_requests er
                JOIN articles a ON er.article_id = a.article_id
                JOIN school_publication_users u ON er.requester_id = u.user_id
                WHERE er.status = 'pending'
                ORDER BY er.created_at DESC";
        return $this->executeQuery($sql);
    }

    /** Retrieves articles (joined with user info and category). */
    public function getArticles($id = null) {
        if ($id) {
            $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, school_publication_users.image_url AS user_image_url, categories.name as category_name
                    FROM articles
                    JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                    LEFT JOIN categories ON articles.category_id = categories.category_id
                    WHERE articles.article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, school_publication_users.image_url AS user_image_url, categories.name as category_name
                FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id 
                LEFT JOIN categories ON articles.category_id = categories.category_id
                ORDER BY articles.created_at DESC";

        return $this->executeQuery($sql);
    }

    public function getActiveArticles($id = null) {
        if ($id) {
            $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, school_publication_users.image_url AS user_image_url, categories.name as category_name
                    FROM articles
                    JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                    LEFT JOIN categories ON articles.category_id = categories.category_id
                    WHERE articles.article_id = ?";
            return $this->executeQuerySingle($sql, [$id]);
        }
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, school_publication_users.image_url AS user_image_url, categories.name as category_name
                FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id 
                LEFT JOIN categories ON articles.category_id = categories.category_id
                WHERE articles.is_active = 1 ORDER BY articles.created_at DESC";
                
        return $this->executeQuery($sql);
    }

    public function getArticlesByUserID($user_id) {
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.is_admin, school_publication_users.image_url AS user_image_url, categories.name as category_name
                FROM articles
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                LEFT JOIN categories ON articles.category_id = categories.category_id
                WHERE articles.author_id = ? ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }

    public function updateArticle($id, $title, $content, $category_id = null) {
        $sql = "UPDATE articles SET title = ?, content = ?, category_id = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$title, $content, $category_id, $id]);
    }
    
    public function updateArticleVisibility($id, $is_active) {
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$is_active, $id]);
    }

    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }
}
?>