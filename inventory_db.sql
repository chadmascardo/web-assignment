CREATE DATABASE inventory_db;
USE inventory_db;

CREATE TABLE items (
    product_id VARCHAR(9) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Manager', 'Staff') NOT NULL DEFAULT 'Staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email)
);

INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@example.com', 
'$argon2i$v=19$m=65536,t=4,p=1$ZW5abTVkaXRIOGRJb2RGNw$gY7vGLzxQXHmvVbBTqF8qw5eVvpVe5CnLl1dGLqQbBs',
 'System Administrator', 'Admin');
 
 select * from users;