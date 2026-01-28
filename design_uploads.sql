CREATE TABLE design_uploads (
  design_id INT AUTO_INCREMENT PRIMARY KEY,
  design_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_by VARCHAR(100),
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);