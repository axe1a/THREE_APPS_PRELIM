CREATE TABLE fiverr_clone_users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255),
    email VARCHAR(255) UNIQUE NOT NULL,
    password TEXT,
    is_client BOOLEAN,
    bio_description TEXT,
    display_picture TEXT,
    contact_number VARCHAR(255),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE proposals (
    proposal_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    description TEXT,
    image TEXT,
    min_price INT,
    max_price INT,
    view_count INT DEFAULT 0,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES fiverr_clone_users(user_id)
);

CREATE TABLE offers (
    offer_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    description TEXT,
    proposal_id INT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES fiverr_clone_users(user_id),
    FOREIGN KEY (proposal_id) REFERENCES proposals(proposal_id),
    UNIQUE KEY unique_user_proposal (user_id, proposal_id)
);


-- Clean up any existing duplicate offers before adding the constraint
DELETE duplicate_offers FROM offers duplicate_offers
INNER JOIN offers existing_offers 
WHERE duplicate_offers.offer_id > existing_offers.offer_id 
AND duplicate_offers.user_id = existing_offers.user_id 
AND duplicate_offers.proposal_id = existing_offers.proposal_id;

-- Add user roles table
CREATE TABLE user_roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add categories table
CREATE TABLE categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add subcategories table
CREATE TABLE subcategories (
    subcategory_id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    subcategory_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

-- Insert default roles
INSERT INTO user_roles (role_name, description) VALUES 
('client', 'Regular client who can submit offers to proposals'),
('freelancer', 'Freelancer who can create proposals and receive offers'),
('fiverr_administrator', 'Administrator who can manage categories and subcategories');


-- Add role_id column to users table
ALTER TABLE fiverr_clone_users 
ADD COLUMN role_id INT DEFAULT 1,
ADD FOREIGN KEY (role_id) REFERENCES user_roles(role_id);

-- Set all clients
UPDATE fiverr_clone_users SET role_id = 1 WHERE is_client = TRUE;

-- Set all freelancers
UPDATE fiverr_clone_users SET role_id = 2 WHERE is_client = FALSE;

-- Now it's safe to drop the column
ALTER TABLE fiverr_clone_users DROP COLUMN is_client;

-- Add category and subcategory columns to proposals table
ALTER TABLE proposals 
ADD COLUMN category_id INT,
ADD COLUMN subcategory_id INT,
ADD FOREIGN KEY (category_id) REFERENCES categories(category_id),
ADD FOREIGN KEY (subcategory_id) REFERENCES subcategories(subcategory_id);



-- Insert sample categories and subcategories
INSERT INTO categories (category_name, description) VALUES 
('Technology', 'Technology-related services and development'),
('Design', 'Creative design services'),
('Writing & Translation', 'Content creation and translation services'),
('Digital Marketing', 'Online marketing and advertising services'),
('Business', 'Business consulting and administrative services');

-- Insert subcategories for Technology
INSERT INTO subcategories (category_id, subcategory_name, description) VALUES 
(1, 'Web Development', 'Front-end, back-end, and full-stack web development'),
(1, 'Mobile App Development', 'iOS and Android mobile application development'),
(1, 'Game Development', 'Video game design and development'),
(1, 'DevOps', 'Development operations and infrastructure management'),
(1, 'Data Science', 'Data analysis, machine learning, and AI services');

-- Insert subcategories for Design
INSERT INTO subcategories (category_id, subcategory_name, description) VALUES 
(2, 'Logo Design', 'Brand identity and logo creation'),
(2, 'Web Design', 'Website and user interface design'),
(2, 'Graphic Design', 'Print and digital graphic design'),
(2, 'Video Editing', 'Video production and editing services'),
(2, '3D Modeling', '3D design and modeling services');

-- Insert subcategories for Writing & Translation
INSERT INTO subcategories (category_id, subcategory_name, description) VALUES 
(3, 'Content Writing', 'Blog posts, articles, and web content'),
(3, 'Technical Writing', 'Documentation and technical content'),
(3, 'Translation', 'Language translation services'),
(3, 'Copywriting', 'Marketing and advertising copy'),
(3, 'Proofreading', 'Editing and proofreading services');

-- Insert subcategories for Digital Marketing
INSERT INTO subcategories (category_id, subcategory_name, description) VALUES 
(4, 'Social Media Marketing', 'Social media management and advertising'),
(4, 'SEO', 'Search engine optimization services'),
(4, 'PPC Advertising', 'Pay-per-click advertising campaigns'),
(4, 'Email Marketing', 'Email campaign management'),
(4, 'Content Marketing', 'Content strategy and creation');

-- Insert subcategories for Business
INSERT INTO subcategories (category_id, subcategory_name, description) VALUES 
(5, 'Virtual Assistant', 'Administrative and support services'),
(5, 'Business Consulting', 'Strategic business advice and planning'),
(5, 'Accounting', 'Bookkeeping and financial services'),
(5, 'Project Management', 'Project coordination and management'),
(5, 'Customer Service', 'Customer support and service');
