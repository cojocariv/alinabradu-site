-- Rulează o dată pe baza existentă: ascunde produsele fără stoc de pe site.

ALTER TABLE products
  ADD COLUMN in_stock TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=afișat pe site, 0=ascuns' AFTER home_sort;
