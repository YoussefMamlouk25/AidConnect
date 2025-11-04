-- AidConnect Database Schema
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'volunteer', 'beneficiary') DEFAULT 'volunteer',
    avatar VARCHAR(255) DEFAULT 'default_profile.png',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
