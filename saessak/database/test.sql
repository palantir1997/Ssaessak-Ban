CREATE DATABASE IF NOT EXISTS saessak;
USE saessak;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 테스트용 어드민 계정
INSERT INTO admin_users (user_id, password, role) VALUES ('admin', '1234', 'admin');