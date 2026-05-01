-- Rulează o dată pe baza existentă pentru galeria animată din pagina Despre noi.

CREATE TABLE IF NOT EXISTS about_gallery_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  image_url VARCHAR(800) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO about_gallery_images (image_url, sort_order, is_active)
SELECT * FROM (
  SELECT 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-1-5.png' AS image_url, 0 AS sort_order, 1 AS is_active
  UNION ALL SELECT 'https://alinabradupozestorage.blob.core.windows.net/poze/Rectangle-11-1-400x500.png', 1, 1
  UNION ALL SELECT 'https://alinabradupozestorage.blob.core.windows.net/poze/image-2-1.png', 2, 1
) seed
WHERE NOT EXISTS (SELECT 1 FROM about_gallery_images);
