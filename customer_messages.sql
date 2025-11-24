CREATE TABLE customer_messages (
  message_id INT AUTO_INCREMENT PRIMARY KEY,
  customer_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(255),
  message TEXT NOT NULL,
  status ENUM('unread', 'read') DEFAULT 'unread',
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);