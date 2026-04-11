-- Import în phpMyAdmin: selectează mai întâi baza creată în Plesk (stânga), apoi Import → acest fișier.
-- NU rula CREATE DATABASE pe hosting shared (eroare #1044).

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
  featured_on_home TINYINT(1) NOT NULL DEFAULT 0,
  home_sort INT NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image_url VARCHAR(800) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  KEY idx_product_sort (product_id, sort_order)
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
('Bluză', 'bluze'),
('Fustă', 'fuste'),
('Home decor', 'home-decor'),
('Rochie', 'rochii')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO products (name, slug, description, price, category, category_slug, subcategory, subcategory_slug, size, image) VALUES
('Rochie Colecția Dor', 'rochie-colectia-dor', 'Rochie elegantă cu broderie tradițională moldovenească.', 1190.00, 'Rochie', 'rochii', 'Colecția Dor', 'colectia-dor', 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png'),
('Bluză Floral Heritage', 'bluza-floral-heritage', 'Bluză tradițională premium, țesătură fină cu motive etnice.', 490.00, 'Bluză', 'bluze', NULL, NULL, 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png'),
('Fustă Atelier Gold', 'fusta-atelier-gold', 'Fustă feminină cu accente aurii și croi modern.', 560.00, 'Fustă', 'fuste', NULL, NULL, 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png'),
('Rochie Colecția Mireasă', 'rochie-colectia-mireasa', 'Rochie mireasă tradițională reinterpretată pentru evenimente speciale.', 1890.00, 'Rochie', 'rochii', 'Colecția Mireasă', 'colectia-mireasa', 'XS,S,M,L,XL', 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO product_images (product_id, image_url, sort_order)
SELECT p.id, p.image, 0 FROM products p
WHERE NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.product_id = p.id);
