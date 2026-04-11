-- Rulează o dată în phpMyAdmin pe baza ta existentă (după schema inițială).

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image_url VARCHAR(800) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  KEY idx_product_sort (product_id, sort_order)
);

-- Coloane pentru „Produse noi” pe homepage
ALTER TABLE products
  ADD COLUMN featured_on_home TINYINT(1) NOT NULL DEFAULT 0 AFTER image,
  ADD COLUMN home_sort INT NOT NULL DEFAULT 0 AFTER featured_on_home;

-- Copiază imaginea principală existentă în galerie (dacă tabelul e gol)
INSERT INTO product_images (product_id, image_url, sort_order)
SELECT p.id, p.image, 0 FROM products p
WHERE NOT EXISTS (
  SELECT 1 FROM product_images pi WHERE pi.product_id = p.id
);
