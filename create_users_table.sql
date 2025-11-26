CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    gender ENUM('male', 'female', 'other', 'prefer_not_to_say'),
    dob DATE NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    family_size INT NULL DEFAULT 0,
    vehicle_number VARCHAR(50) NULL,
    profile_image_url VARCHAR(255) NULL,
    profile_image_public_id VARCHAR(255) NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);