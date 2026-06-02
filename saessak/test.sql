CREATE DATABASE IF NOT EXISTS saessak DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE saessak;

CREATE TABLE IF NOT EXISTS patients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    login_id VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO patients (name, login_id, password, phone)
VALUES ('환자테스트', 'patient', '1234', '010-1234-5678')
ON DUPLICATE KEY UPDATE name='환자테스트', password='1234', phone='010-1234-5678';
