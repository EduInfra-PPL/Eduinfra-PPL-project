-- =====================================================================
-- EduInfra Tracker - Sekolah dari sumber OpenStreetMap (tanpa NPSN)
-- Dijalankan setelah 01_schema.sql dan 01b_*, baik oleh migration Laravel
-- (2026_11_10_000003_allow_osm_schools) maupun oleh docker-entrypoint.
-- Idempoten.
--
-- npsn menjadi nullable, sekolah wajib punya salah satu: npsn (Dapodik) atau osm_id (OSM).
-- source mencatat asal baris ('dapodik' atau 'osm').
-- =====================================================================

ALTER TABLE schools ALTER COLUMN npsn DROP NOT NULL;

ALTER TABLE schools
    ADD COLUMN IF NOT EXISTS osm_id VARCHAR(30),
    ADD COLUMN IF NOT EXISTS source VARCHAR(20) NOT NULL DEFAULT 'dapodik';

CREATE UNIQUE INDEX IF NOT EXISTS uq_schools_osm_id ON schools (osm_id);

DO $$
BEGIN
    ALTER TABLE schools ADD CONSTRAINT schools_identity_chk
        CHECK (npsn IS NOT NULL OR osm_id IS NOT NULL);
EXCEPTION WHEN duplicate_object THEN NULL;
END $$;

DO $$
BEGIN
    ALTER TABLE schools ADD CONSTRAINT schools_source_chk CHECK (source IN ('dapodik', 'osm'));
EXCEPTION WHEN duplicate_object THEN NULL;
END $$;
