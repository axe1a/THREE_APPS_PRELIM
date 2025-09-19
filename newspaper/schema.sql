-- Users table
CREATE TABLE IF NOT EXISTS school_publication_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin TINYINT(1) NOT NULL DEFAULT 0,
    image_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Articles table
CREATE TABLE IF NOT EXISTS articles (
    article_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_articles_author FOREIGN KEY (author_id)
        REFERENCES school_publication_users(user_id) ON DELETE CASCADE
);

-- Notifications table
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id)
        REFERENCES school_publication_users(user_id) ON DELETE CASCADE
);

-- Edit requests table
CREATE TABLE IF NOT EXISTS edit_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    requester_id INT NOT NULL,
    status ENUM('pending','accepted','rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_edit_article FOREIGN KEY (article_id)
        REFERENCES articles(article_id) ON DELETE CASCADE,
    CONSTRAINT fk_edit_requester FOREIGN KEY (requester_id)
        REFERENCES school_publication_users(user_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


ALTER TABLE articles 
ADD COLUMN category_id INT DEFAULT NULL AFTER author_id;


ALTER TABLE articles 
ADD CONSTRAINT fk_articles_category 
FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE SET NULL;


INSERT INTO categories (name, description) VALUES 
    ('News Reports', 'Factual reporting on current events and news'),
    ('Editorials', 'Opinion pieces expressing the publication\'s stance'),
    ('Opinion', 'Personal opinions and commentary from writers'),
    ('Features', 'In-depth articles on specific topics'),
    ('Sports', 'Sports-related news and commentary'),
    ('Entertainment', 'Entertainment news, reviews, and features'),
    ('Technology', 'Technology news, reviews, and analysis'),
    ('Lifestyle', 'Lifestyle, culture, and human interest stories')