CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    line_user_id VARCHAR(100) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    picture_url VARCHAR(255),
    email VARCHAR(100),
    login_time DATETIME,
    role ENUM('admin', 'user') DEFAULT 'user',
    medicine_alert_time TIME,
    medicine_alert_message VARCHAR(255),
    ocr_scans_text TEXT,
    UNIQUE KEY (line_user_id)
);
