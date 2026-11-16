-- =====================================================================
-- EduInfra Tracker - Sumber 'manual' untuk sekolah yang didaftarkan admin.
-- Dijalankan setelah 01c_schools_osm_source.sql (migration 2026_11_10_000004). Idempoten.
-- =====================================================================

ALTER TABLE schools DROP CONSTRAINT IF EXISTS schools_source_chk;

ALTER TABLE schools ADD CONSTRAINT schools_source_chk CHECK (source IN ('dapodik', 'osm', 'manual'));
