-- ============================================================
-- E Tax Advisors Private Limited - Database Installation Script
-- Run this in phpMyAdmin or via MySQL CLI
-- ============================================================

-- Table: enquiries (for consultation form submissions)
CREATE TABLE IF NOT EXISTS enquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  enquiry_date DATETIME NOT NULL,
  name VARCHAR(120) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(190) NOT NULL,
  organisation VARCHAR(190) DEFAULT NULL,
  service VARCHAR(190) NOT NULL,
  preferred_time VARCHAR(190) DEFAULT NULL,
  message TEXT NOT NULL,
  source_page VARCHAR(190) NOT NULL DEFAULT '',
  ip_address VARCHAR(45) NOT NULL DEFAULT '',
  status ENUM('new','contacted','converted','closed') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: testimonials (client reviews - already created by testimonials.php)
-- This is here for reference; the PHP creates it automatically if it doesn't exist.
-- CREATE TABLE IF NOT EXISTS testimonials (
--   id INT AUTO_INCREMENT PRIMARY KEY,
--   created_at DATETIME NOT NULL,
--   approved_at DATETIME NULL,
--   client_name VARCHAR(120) NOT NULL,
--   company_name VARCHAR(160) NOT NULL,
--   city VARCHAR(120) NOT NULL,
--   mobile VARCHAR(20) NOT NULL,
--   email VARCHAR(190) NOT NULL,
--   service_availed VARCHAR(160) NOT NULL,
--   rating TINYINT NOT NULL,
--   testimonial_text TEXT NOT NULL,
--   publish_permission TINYINT(1) NOT NULL DEFAULT 0,
--   status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
--   admin_notes TEXT NULL,
--   is_spam TINYINT(1) NOT NULL DEFAULT 0,
--   updated_at DATETIME NOT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table: tickets (support ticket system - created by support/install.sql)
-- Reference: see support/install.sql

-- Index for faster lookups on enquiries
ALTER TABLE enquiries ADD INDEX idx_status (status);
ALTER TABLE enquiries ADD INDEX idx_enquiry_date (enquiry_date);
