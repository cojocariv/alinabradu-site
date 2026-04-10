CREATE DATABASE IF NOT EXISTS ab_db_ CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ab_db_;

CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  category VARCHAR(100) NOT NULL,
  category_slug VARCHAR(120) NOT NULL,
  subcategory VARCHAR(120) NULL,
  subcategory_slug VARCHAR(120) NULL,
  size VARCHAR(50) NOT NULL COMMENT 'Comma separated sizes',
  image VARCHAR(500) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(200) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  address TEXT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  product_name VARCHAR(200) NOT NULL,
  size VARCHAR(10) NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

INSERT INTO categories (name, slug) VALUES
('Bluza', 'bluze'),
('Fusta', 'fuste'),
('Home decor', 'home-decor'),
('Rochie', 'rochii')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO products (name, slug, description, price, category, category_slug, subcategory, subcategory_slug, size, image) VALUES
('Rochie Colectia Dor', 'rochie-colectia-dor', 'Rochie eleganta cu broderie traditionala moldoveneasca.', 1190.00, 'Rochie', 'rochii', 'Colectia Dor', 'colectia-dor', 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png'),
('Bluza Floral Heritage', 'bluza-floral-heritage', 'Bluza traditionala premium, tesatura fina cu motive etnice.', 490.00, 'Bluza', 'bluze', NULL, NULL, 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png'),
('Fusta Atelier Gold', 'fusta-atelier-gold', 'Fusta feminina cu accente aurii si croi modern.', 560.00, 'Fusta', 'fuste', NULL, NULL, 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png'),
('Rochie Colectia Mireasa', 'rochie-colectia-mireasa', 'Rochie mireasa traditionala reinterpretata pentru evenimente speciale.', 1890.00, 'Rochie', 'rochii', 'Colectia Mireasa', 'colectia-mireasa', 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png')
ON DUPLICATE KEY UPDATE name = VALUES(name);
