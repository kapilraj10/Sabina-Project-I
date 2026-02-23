
CREATE DATABASE IF NOT EXISTS sabina;
USE sabina;

CREATE TABLE users(
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Insert User (Optional Test Data)
INSERT INTO users(name,email,password,role)
VALUES(
'Admin',
'admin@gmail.com',
'$2y$10$examplehashedpassword',
'admin'
);