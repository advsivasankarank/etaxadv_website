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
