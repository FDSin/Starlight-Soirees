CREATE DATABASE IF NOT EXISTS event_management CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE event_management;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE venues (
    venue_id INT AUTO_INCREMENT PRIMARY KEY,
    venue_name VARCHAR(100) NOT NULL,
    max_capacity INT NOT NULL,
    venue_price DECIMAL(10,2) NOT NULL,
    location VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE menus (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    price_per_person DECIMAL(10,2) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE events (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    venue_id INT DEFAULT NULL,
    menu_id INT DEFAULT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    event_date DATE NOT NULL,
    event_time TIME DEFAULT NULL,
    guest_count INT NOT NULL,
    event_status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (venue_id) REFERENCES venues(venue_id),
    FOREIGN KEY (menu_id) REFERENCES menus(menu_id)
) ENGINE=InnoDB;

CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('Bank Transfer', 'Credit Card') DEFAULT NULL,
    payment_status ENUM('Unpaid', 'Pending Approval', 'Paid') DEFAULT 'Unpaid',
    receipt_file VARCHAR(255) DEFAULT NULL,
    payment_date DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(event_id)
) ENGINE=InnoDB;

INSERT INTO menus (package_name, price_per_person, description) VALUES
('Bronze Buffet', 100.00, 'Simple buffet package'),
('Silver Buffet', 150.00, 'Standard buffet package'),
('Gold Buffet', 250.00, 'Premium buffet package');
