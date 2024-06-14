CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    line_user_id VARCHAR(255) NOT NULL,
    display_name VARCHAR(255) NOT NULL,
    picture_url VARCHAR(255),
    email VARCHAR(255),
    login_time DATETIME,
    role ENUM('admin', 'user') DEFAULT 'user',
    UNIQUE KEY(line_user_id)
);
