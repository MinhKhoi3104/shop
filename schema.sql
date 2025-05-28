-- MySQL schema for simple shop (guest checkout)

CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255),
  category VARCHAR(100),
  discount DECIMAL(5,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vouchers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(50) NOT NULL UNIQUE,
  discount_percent INT NOT NULL,
  valid_from DATE,
  valid_to DATE,
  usage_limit INT DEFAULT 1,
  used_count INT DEFAULT 0
);

CREATE TABLE cart_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(64) NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id VARCHAR(64) NOT NULL,
  customer_name VARCHAR(255) NOT NULL,
  email VARCHAR(255),
  phone VARCHAR(50),
  address VARCHAR(255),
  delivery_method VARCHAR(50),
  payment_method VARCHAR(50),
  note TEXT,
  voucher_code VARCHAR(50),
  total DECIMAL(10,2) NOT NULL,
  status VARCHAR(50) DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id),
  FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Sample data
INSERT INTO products (name, description, price, image, category, discount) VALUES
('Táo Fuji', 'Táo Fuji nhập khẩu tươi ngon', 50000, 'apple.jpg', 'Trái cây', 10),
('Cam Sành', 'Cam Sành ngọt lịm', 40000, 'orange.jpg', 'Trái cây', 0),
('Nho Mỹ', 'Nho Mỹ không hạt', 120000, 'grape.jpg', 'Trái cây', 15);

INSERT INTO vouchers (code, discount_percent, valid_from, valid_to, usage_limit) VALUES
('SALE10', 10, '2024-01-01', '2024-12-31', 100); 