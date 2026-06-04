-- Lead-uri: vizitatori care au deschis WhatsApp / Viber (click pe link).
-- Rulează în phpMyAdmin pe baza site-ului.

CREATE TABLE IF NOT EXISTS chat_contact_leads (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  channel ENUM('whatsapp', 'viber') NOT NULL,
  source VARCHAR(64) NOT NULL DEFAULT 'unknown',
  page_path VARCHAR(500) NOT NULL DEFAULT '',
  product_id INT UNSIGNED NULL,
  product_slug VARCHAR(220) NULL,
  product_name VARCHAR(200) NULL,
  message_preview VARCHAR(500) NULL,
  ip_address VARCHAR(45) NULL,
  user_agent VARCHAR(512) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_channel_created (channel, created_at),
  KEY idx_product (product_id),
  KEY idx_created (created_at)
);
