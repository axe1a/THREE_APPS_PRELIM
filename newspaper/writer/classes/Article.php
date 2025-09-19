<?php  

require_once 'Database.php';
require_once 'User.php';
/**
 * Class for handling Article-related operations.
 * Inherits CRUD methods from the Database class.
 */
class Article extends Database {
    public function createArticle($title, $content, $author_id, $category_id = null, $image_url = null) {
        $sql = "INSERT INTO articles (title, content, author_id, category_id, image_url, is_active) VALUES (?, ?, ?, ?, ?, 0)";
        return $this->executeNonQuery($sql, [$title, $content, $author_id, $category_id, $image_url]);
    }

    public function addNotification($user_id, $message) {
        $sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$user_id, $message]);
    }

    public function createEditRequest($article_id, $requester_id) {
        $sql = "INSERT INTO edit_requests (article_id, requester_id) VALUES (?, ?)";
        return $this->executeNonQuery($sql, [$article_id, $requester_id]);
    }

    public function updateEditRequestStatus($article_id, $requester_id, $status) {
        $sql = "UPDATE edit_requests SET status = ? WHERE article_id = ? AND requester_id = ?";
        return $this->executeNonQuery($sql, [$status, $article_id, $requester_id]);
    }

    /** Returns 'pending' | 'accepted' | 'rejected' | null if no request */
    public function getEditRequestStatus($article_id, $requester_id) {
        $sql = "SELECT status FROM edit_requests WHERE article_id = ? AND requester_id = ? ORDER BY created_at DESC LIMIT 1";
        $row = $this->executeQuerySingle($sql, [$article_id, $requester_id]);
        return $row ? $row['status'] : null;
    }

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
        $userModel = new User();
        if (!$userModel->isAdmin()) {
            return 0;
        }
        $sql = "UPDATE articles SET is_active = ? WHERE article_id = ?";
        return $this->executeNonQuery($sql, [(int)$is_active, $id]);
    }

    public function deleteArticle($id) {
        $sql = "DELETE FROM articles WHERE article_id = ?";
        return $this->executeNonQuery($sql, [$id]);
    }

    public function getSharedArticlesForUser($user_id) {
        $sql = "SELECT articles.*, school_publication_users.username, school_publication_users.image_url AS user_image_url, categories.name as category_name
                FROM articles
                JOIN edit_requests ON articles.article_id = edit_requests.article_id
                JOIN school_publication_users ON articles.author_id = school_publication_users.user_id
                LEFT JOIN categories ON articles.category_id = categories.category_id
                WHERE edit_requests.requester_id = ? AND edit_requests.status = 'accepted'
                ORDER BY articles.created_at DESC";
        return $this->executeQuery($sql, [$user_id]);
    }
}
?>