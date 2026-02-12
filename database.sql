-- ============================================
-- Car Rental Management System - Database Schema
-- ============================================

-- Create database
CREATE DATABASE IF NOT EXISTS car_rental_system;
USE car_rental_system;

-- ============================================
-- Table 1: users
-- Stores both customers and agencies
-- ============================================
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_type ENUM('customer', 'agency') NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20),
    company_name VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_user_type (user_type)
);

-- ============================================
-- Table 2: cars
-- Stores car inventory managed by agencies
-- ============================================
CREATE TABLE cars (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agency_id INT NOT NULL,
    vehicle_model VARCHAR(255) NOT NULL,
    vehicle_number VARCHAR(50) UNIQUE NOT NULL,
    seating_capacity INT NOT NULL,
    rent_per_day DECIMAL(10, 2) NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (agency_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_agency_id (agency_id),
    INDEX idx_is_available (is_available)
);

-- ============================================
-- Table 3: bookings
-- Stores rental bookings by customers
-- ============================================
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    car_id INT NOT NULL,
    customer_id INT NOT NULL,
    start_date DATE NOT NULL,
    number_of_days INT NOT NULL,
    end_date DATE NOT NULL,
    total_cost DECIMAL(10, 2) NOT NULL,
    booking_status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_car_id (car_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_booking_status (booking_status)
);

-- ============================================
-- Sample Data
-- All passwords below are: password123
-- Hashed with password_hash('password123', PASSWORD_DEFAULT)
-- ============================================

-- Sample Users: 2 agencies, 3 customers
INSERT INTO users (user_type, email, password, full_name, phone_number, company_name) VALUES
('agency', 'agency1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rahul Sharma', '9876543210', 'Sharma Car Rentals'),
('agency', 'agency2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Priya Patel', '9876543211', 'Patel Auto Rentals'),
('customer', 'customer1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Amit Kumar', '9876543212', NULL),
('customer', 'customer2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sneha Gupta', '9876543213', NULL),
('customer', 'customer3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Vikram Singh', '9876543214', NULL);

-- Sample Cars: 5 cars (3 from agency1, 2 from agency2)
INSERT INTO cars (agency_id, vehicle_model, vehicle_number, seating_capacity, rent_per_day, is_available) VALUES
(1, 'Maruti Swift', 'MH01AB1234', 5, 1500.00, TRUE),
(1, 'Hyundai Creta', 'MH01CD5678', 5, 2500.00, TRUE),
(1, 'Toyota Innova', 'MH01EF9012', 7, 3500.00, FALSE),
(2, 'Honda City', 'DL02GH3456', 5, 2000.00, TRUE),
(2, 'Tata Nexon', 'DL02IJ7890', 5, 1800.00, TRUE);

-- Sample Bookings: 3 bookings
INSERT INTO bookings (car_id, customer_id, start_date, number_of_days, end_date, total_cost, booking_status) VALUES
(3, 3, '2026-02-10', 5, '2026-02-15', 17500.00, 'active'),
(1, 4, '2026-01-20', 3, '2026-01-23', 4500.00, 'completed'),
(2, 5, '2026-01-25', 2, '2026-01-27', 5000.00, 'completed');
