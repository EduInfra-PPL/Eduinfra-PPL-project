-- =====================================================================
-- EduInfra Tracker - Kategori 3T pada regions (Tertinggal, Terdepan, Terluar)
-- Dijalankan setelah 01_schema.sql, baik oleh migration Laravel
-- (2026_11_10_000002_add_3t_categories_to_regions) maupun oleh docker-entrypoint
-- (urutan nama file: 01_schema < 01b_* < 02_seed). Idempoten.
--
-- Satu wilayah boleh memegang lebih dari satu kategori.
-- is_3t adalah kolom turunan (STORED) agar filter "wilayah 3T" tidak mengulang logika OR.
-- =====================================================================

ALTER TABLE regions
    ADD COLUMN IF NOT EXISTS is_tertinggal BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN IF NOT EXISTS is_terdepan   BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN IF NOT EXISTS is_terluar    BOOLEAN NOT NULL DEFAULT FALSE,
    ADD COLUMN IF NOT EXISTS is_3t BOOLEAN GENERATED ALWAYS AS
        (is_3t_tier > 0 OR is_tertinggal OR is_terdepan OR is_terluar) STORED;

-- Tier-1 (prioritas 3T) didefinisikan sebagai daerah tertinggal (Perpres 63/2020).
UPDATE regions SET is_tertinggal = TRUE WHERE is_3t_tier > 0 AND is_tertinggal = FALSE;

CREATE INDEX IF NOT EXISTS idx_regions_is_3t ON regions (is_3t) WHERE is_3t;
