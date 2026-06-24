-- Locație aproximativă (oraș, țară) pentru lead-uri chat.
-- Rulează în phpMyAdmin după migration_chat_contacts.sql.

ALTER TABLE chat_contact_leads
  ADD COLUMN geo_city VARCHAR(120) NULL DEFAULT NULL AFTER ip_address,
  ADD COLUMN geo_country VARCHAR(120) NULL DEFAULT NULL AFTER geo_city,
  ADD COLUMN geo_region VARCHAR(120) NULL DEFAULT NULL AFTER geo_country;
