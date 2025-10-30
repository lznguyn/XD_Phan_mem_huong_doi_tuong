CREATE DATABASE IF NOT EXISTS musicdb;
USE musicdb;

CREATE TABLE users (
    id INT(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    email VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    password VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    user_type ENUM('user', 'coordinator', 'admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'user' NULL,
    token_expiry DATETIME DEFAULT NULL NULL,
    reset_token VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL,
    reset_token_expiry DATETIME DEFAULT NULL NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE services (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255)
);

CREATE TABLE orders (
    id INT(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(100) NOT NULL,
    name VARCHAR(100) NOT NULL,
    number VARCHAR(12) NOT NULL,
    email VARCHAR(100) NOT NULL,
    method VARCHAR(50) NOT NULL,
    total_products VARCHAR(1000),
    total_price INT(100),
    placed_on VARCHAR(50),
    payment_status VARCHAR(20)
);

CREATE TABLE notifications (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    message TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    status ENUM('unread', 'read') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'unread' NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NULL
);

CREATE TABLE music_submissions (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    file_name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    note TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL,
    status ENUM('pending', 'completed', 'rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'pending' NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NULL
);

CREATE TABLE experts (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    role VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Chuyên gia' NULL,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL NULL
);

CREATE TABLE edit_requests (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    submission_id INT(11) NOT NULL,
    user_id INT(11) NOT NULL,
    feedback TEXT NOT NULL,
    file_name VARCHAR(255) DEFAULT NULL NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP NULL
);

CREATE TABLE cart (
    id INT(100) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(100) NOT NULL,
    name VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    price INT(100) NOT NULL,
    quantity INT(100) NOT NULL,
    image VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
);

CREATE TABLE bookings (
    id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT(10) UNSIGNED NOT NULL,
    expert_id INT(10) UNSIGNED NOT NULL,
    date DATE NOT NULL,
    time_slot VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
    priority ENUM('Thấp', 'Trung bình', 'Cao') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Trung bình' NULL
);