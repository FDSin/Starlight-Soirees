-- Run this file once on the existing event_management database.
-- It keeps the old records and updates Modules 2 to 5 to the new structure.

USE event_management;

ALTER TABLE venues
    CHANGE name venue_name VARCHAR(100) NOT NULL,
    CHANGE capacity max_capacity INT NOT NULL DEFAULT 1,
    CHANGE address location VARCHAR(255) DEFAULT NULL,
    ADD venue_price DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER max_capacity;

CREATE TABLE menus (
    menu_id INT AUTO_INCREMENT PRIMARY KEY,
    package_name VARCHAR(100) NOT NULL,
    price_per_person DECIMAL(10,2) NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO menus (package_name, price_per_person, description)
SELECT DISTINCT item_name, COALESCE(price, 0), description
FROM food_catering;

INSERT INTO menus (package_name, price_per_person, description)
SELECT 'Bronze Buffet', 100.00, 'Simple buffet package'
WHERE NOT EXISTS (SELECT 1 FROM menus WHERE package_name = 'Bronze Buffet');

INSERT INTO menus (package_name, price_per_person, description)
SELECT 'Silver Buffet', 150.00, 'Standard buffet package'
WHERE NOT EXISTS (SELECT 1 FROM menus WHERE package_name = 'Silver Buffet');

INSERT INTO menus (package_name, price_per_person, description)
SELECT 'Gold Buffet', 250.00, 'Premium buffet package'
WHERE NOT EXISTS (SELECT 1 FROM menus WHERE package_name = 'Gold Buffet');

ALTER TABLE events
    ADD user_id INT NULL AFTER event_id,
    ADD menu_id INT NULL AFTER venue_id,
    ADD guest_count INT NOT NULL DEFAULT 1 AFTER event_time,
    ADD event_status ENUM('Pending', 'Confirmed', 'Completed', 'Cancelled') NOT NULL DEFAULT 'Pending' AFTER guest_count;

UPDATE events
SET user_id = (SELECT MIN(user_id) FROM users)
WHERE user_id IS NULL;

UPDATE events
SET event_status = CASE status
    WHEN 'confirmed' THEN 'Confirmed'
    WHEN 'completed' THEN 'Completed'
    WHEN 'cancelled' THEN 'Cancelled'
    ELSE 'Pending'
END;

UPDATE events e
JOIN food_catering f ON f.event_id = e.event_id
JOIN menus m ON m.package_name = f.item_name
SET e.menu_id = m.menu_id
WHERE e.menu_id IS NULL;

ALTER TABLE events
    MODIFY user_id INT NOT NULL,
    MODIFY event_date DATE NOT NULL,
    DROP COLUMN status,
    ADD CONSTRAINT fk_events_user FOREIGN KEY (user_id) REFERENCES users(user_id),
    ADD CONSTRAINT fk_events_menu FOREIGN KEY (menu_id) REFERENCES menus(menu_id);

ALTER TABLE payments
    CHANGE amount total_amount DECIMAL(10,2) NOT NULL,
    MODIFY event_id INT NOT NULL,
    MODIFY payment_method ENUM('Bank Transfer', 'Credit Card') DEFAULT NULL,
    ADD payment_status ENUM('Unpaid', 'Pending Approval', 'Paid') NOT NULL DEFAULT 'Unpaid' AFTER payment_method,
    ADD receipt_file VARCHAR(255) DEFAULT NULL AFTER payment_status,
    MODIFY payment_date DATETIME DEFAULT NULL;

UPDATE payments
SET payment_status = CASE status
    WHEN 'completed' THEN 'Paid'
    WHEN 'pending' THEN 'Pending Approval'
    ELSE 'Unpaid'
END;

ALTER TABLE payments DROP COLUMN status;

-- food_catering is intentionally retained as a legacy backup table.
