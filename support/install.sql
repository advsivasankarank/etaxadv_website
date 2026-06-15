CREATE TABLE IF NOT EXISTS tickets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ticket_id VARCHAR(32) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL,
  category VARCHAR(50) NOT NULL,
  priority VARCHAR(20) NOT NULL DEFAULT 'Normal',
  client_name VARCHAR(120) NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(190) NULL,
  subject VARCHAR(190) NOT NULL,
  message TEXT NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Open',
  agent_id INT NULL,
  agent_name VARCHAR(120) NULL,
  last_update DATETIME NOT NULL,
  remarks TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS admin_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS backoffice_users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  name VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL,
  approved_at DATETIME NULL,
  client_name VARCHAR(120) NOT NULL,
  company_name VARCHAR(160) NOT NULL,
  city VARCHAR(120) NOT NULL,
  mobile VARCHAR(20) NOT NULL,
  email VARCHAR(190) NOT NULL,
  service_availed VARCHAR(160) NOT NULL,
  rating TINYINT NOT NULL,
  testimonial_text TEXT NOT NULL,
  publish_permission TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_notes TEXT NULL,
  is_spam TINYINT(1) NOT NULL DEFAULT 0,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Default Admin (CHANGE PASSWORD after first login)
INSERT INTO admin_users (username, password_hash, created_at)
VALUES ('admin', '$2b$12$zRLNMNlEzgBmigXrAQ2poOMdbkJqyd2kcWy831TWBuS36Eg8lJv9K', NOW())
ON DUPLICATE KEY UPDATE username=username;
