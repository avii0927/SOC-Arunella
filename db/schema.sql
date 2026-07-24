-- Arunella Database Schema
-- CSC 313 1.5 Service Oriented Computing Final Project
-- University of Sri Jayewardenepura

CREATE DATABASE IF NOT EXISTS `arunella`;
USE `arunella`;

-- --------------------------------------------------------
-- Table: ADMIN
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ADMIN` (
  `admin_id` INT AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: FARMER
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `FARMER` (
  `user_id` INT AUTO_INCREMENT,
  `admin_id` INT DEFAULT NULL,
  `role` VARCHAR(20) DEFAULT 'Farmer',
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nic` VARCHAR(20) NOT NULL UNIQUE,
  `contact_no` VARCHAR(15) NOT NULL,
  `district` VARCHAR(50) NOT NULL,
  `rating` DECIMAL(3,2) DEFAULT 5.00,
  `location` VARCHAR(255) NOT NULL,
  `wallet` DECIMAL(10,2) DEFAULT 0.00,
  `bank_ac_no` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`admin_id`) REFERENCES `ADMIN` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: BUYER
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `BUYER` (
  `user_id` INT AUTO_INCREMENT,
  `admin_id` INT DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nic` VARCHAR(20) NOT NULL UNIQUE,
  `contact_no` VARCHAR(15) NOT NULL,
  `district` VARCHAR(50) NOT NULL,
  `rating` DECIMAL(3,2) DEFAULT 5.00,
  `role` VARCHAR(20) DEFAULT 'Buyer',
  `business_reg_no` VARCHAR(50) NOT NULL,
  `market_location` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`admin_id`) REFERENCES `ADMIN` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: TRANSPORTER
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `TRANSPORTER` (
  `user_id` INT AUTO_INCREMENT,
  `admin_id` INT DEFAULT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nic` VARCHAR(20) NOT NULL UNIQUE,
  `contact_no` VARCHAR(15) NOT NULL,
  `district` VARCHAR(50) NOT NULL,
  `rating` DECIMAL(3,2) DEFAULT 5.00,
  `role` VARCHAR(20) DEFAULT 'Transporter',
  `vehicle_plate_no` VARCHAR(20) NOT NULL UNIQUE,
  `max_capacity` DECIMAL(10,2) NOT NULL, -- in kg
  PRIMARY KEY (`user_id`),
  FOREIGN KEY (`admin_id`) REFERENCES `ADMIN` (`admin_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: CROP
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `CROP` (
  `crop_id` INT AUTO_INCREMENT,
  `user_id` INT NOT NULL, -- Farmer
  `crop_name` VARCHAR(100) NOT NULL,
  `price_per_kg` DECIMAL(10,2) NOT NULL,
  `stock` DECIMAL(10,2) NOT NULL, -- in kg
  `status` VARCHAR(20) DEFAULT 'Available', -- Available, Out of Stock, Flagged
  `uploaded_date` DATE NOT NULL,
  `exp_date` DATE NOT NULL,
  `min_price` DECIMAL(10,2) NOT NULL, -- Minimum bidding or negotiation price
  `description` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`crop_id`),
  FOREIGN KEY (`user_id`) REFERENCES `FARMER` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: ORDER
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `ORDER` (
  `order_id` INT AUTO_INCREMENT,
  `user_id` INT NOT NULL, -- Buyer
  `price` DECIMAL(10,2) NOT NULL, -- Total Order Price
  `quantity` DECIMAL(10,2) NOT NULL, -- Total Order Quantity
  `date` DATE NOT NULL,
  `status` VARCHAR(20) DEFAULT 'Pending', -- Pending, Accepted, Shipped, Delivered, Cancelled
  PRIMARY KEY (`order_id`),
  FOREIGN KEY (`user_id`) REFERENCES `BUYER` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: DELIVERY
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `DELIVERY` (
  `delivery_id` INT AUTO_INCREMENT,
  `user_id` INT DEFAULT NULL, -- Transporter (NULL if not assigned yet)
  `order_id` INT NOT NULL,
  `pickup_location` VARCHAR(255) NOT NULL,
  `delivery_location` VARCHAR(255) NOT NULL,
  `status` VARCHAR(20) DEFAULT 'Pending', -- Pending, Assigned, Picked Up, In Transit, Delivered
  `confirmation_img` VARCHAR(255) DEFAULT NULL,
  `date` DATE NOT NULL,
  PRIMARY KEY (`delivery_id`),
  FOREIGN KEY (`user_id`) REFERENCES `TRANSPORTER` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  FOREIGN KEY (`order_id`) REFERENCES `ORDER` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: HAS (Junction table linking Orders to Crops)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `HAS` (
  `order_id` INT NOT NULL,
  `crop_id` INT NOT NULL,
  PRIMARY KEY (`order_id`, `crop_id`),
  FOREIGN KEY (`order_id`) REFERENCES `ORDER` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`crop_id`) REFERENCES `CROP` (`crop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ========================================================
-- SEED DATA (All passwords are hashed value of 'password123')
-- Hash: $2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6
-- ========================================================

-- Seed ADMIN
INSERT INTO `ADMIN` (`admin_id`, `name`, `email`, `password`) VALUES
(1, 'System Administrator', 'admin@arunella.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6');

-- Seed FARMER
INSERT INTO `FARMER` (`user_id`, `admin_id`, `role`, `name`, `email`, `password`, `nic`, `contact_no`, `district`, `rating`, `location`, `wallet`, `bank_ac_no`) VALUES
(1, 1, 'Farmer', 'Bandara Dissanayake', 'bandara@farmer.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6', '197523456789', '0771234567', 'Nuwara Eliya', 4.8, 'Keppetipola Road, Welimada', 15000.00, 'BOC-1002345678'),
(2, 1, 'Farmer', 'Kaveesha Perera', 'kaveesha@farmer.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6', '198823456780', '0719876543', 'Badulla', 4.9, 'Spring Valley Road, Badulla', 8500.00, 'PeopleBank-88776655');

-- Seed BUYER
INSERT INTO `BUYER` (`user_id`, `admin_id`, `name`, `email`, `password`, `nic`, `contact_no`, `district`, `rating`, `role`, `business_reg_no`, `market_location`) VALUES
(1, 1, 'Keells Supermarket', 'procure@keells.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6', '199045678912', '0112300100', 'Colombo', 4.7, 'Buyer', 'PV-88772', 'Glenn Abery Street, Colombo 02'),
(2, 1, 'Saman Silva (Wholesaler)', 'saman@buyer.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6', '198245678913', '0751122334', 'Gampaha', 4.5, 'Buyer', 'BR-55441', 'Manning Market, Peliyagoda');

-- Seed TRANSPORTER
INSERT INTO `TRANSPORTER` (`user_id`, `admin_id`, `name`, `email`, `password`, `nic`, `contact_no`, `district`, `rating`, `role`, `vehicle_plate_no`, `max_capacity`) VALUES
(1, 1, 'Ruwan Kumara', 'ruwan@transporter.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6', '198534567890', '0723456789', 'Nuwara Eliya', 4.6, 'Transporter', 'WP-LY-4567', 1500.00),
(2, 1, 'Tuan Saleem', 'tuan@transporter.lk', '$2y$10$STq008iyIeyuApU09HUFnOO5zbnNkaEPP3A44KDv9HXMD9EGRnXy6', '199134567891', '0769876543', 'Kandy', 4.9, 'Transporter', 'CP-DA-8899', 800.00);

-- Seed CROPS
INSERT INTO `CROP` (`crop_id`, `user_id`, `crop_name`, `price_per_kg`, `stock`, `status`, `uploaded_date`, `exp_date`, `min_price`, `description`, `image`) VALUES
(1, 1, 'Fresh Nuwara Eliya Carrots', 280.00, 500.00, 'Available', '2026-07-01', '2026-07-15', 250.00, 'Premium quality farm-fresh carrots harvested from Welimada hills. Cleaned and packed in 50kg bags.', 'carrot.jpg'),
(2, 1, 'Organic Potatoes', 320.00, 350.00, 'Available', '2026-07-02', '2026-07-20', 300.00, 'Red potatoes grown with minimal chemical use. High starch quality and long shelf life.', 'potato.jpg'),
(3, 2, 'Green Leeks', 180.00, 200.00, 'Available', '2026-07-03', '2026-07-10', 160.00, 'Freshly harvested leeks, washed and trimmed. Ready for immediate shipping.', 'leeks.jpg'),
(4, 2, 'Big Onions (Lunu)', 220.00, 1000.00, 'Available', '2026-07-04', '2026-08-01', 200.00, 'Dry red onions. Good quality, medium size, cured and ready for wholesale.', 'onion.jpg');

-- Seed ORDERS (re-naming ORDER table internally to avoid conflicts or quote it as `ORDER`)
INSERT INTO `ORDER` (`order_id`, `user_id`, `price`, `quantity`, `date`, `status`) VALUES
(1, 1, 56000.00, 200.00, '2026-07-05', 'Delivered'),
(2, 2, 32000.00, 100.00, '2026-07-06', 'Pending');

-- Seed HAS
INSERT INTO `HAS` (`order_id`, `crop_id`) VALUES
(1, 1),
(2, 2);

-- Seed DELIVERY
INSERT INTO `DELIVERY` (`delivery_id`, `user_id`, `order_id`, `pickup_location`, `delivery_location`, `status`, `confirmation_img`, `date`) VALUES
(1, 1, 1, 'Keppetipola Road, Welimada', 'Glenn Abery Street, Colombo 02', 'Delivered', 'delivered_sign.jpg', '2026-07-06'),
(2, NULL, 2, 'Spring Valley Road, Badulla', 'Manning Market, Peliyagoda', 'Pending', NULL, '2026-07-07');
