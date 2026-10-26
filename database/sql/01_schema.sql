-- =====================================================================
-- EduInfra Tracker - Domain 1: Master Wilayah dan Kelembagaan Pendidikan
-- PBI-01 schools | PBI-02 regions | PBI-03 facility_categories
-- Acuan: Proposal PPL Kelompok C - ERD (Gambar 7), Tabel 16, FR-01..FR-03
-- PostgreSQL 16 + PostGIS 3.x. Idempoten: aman dijalankan berulang.
-- Sumber tunggal skema: dipakai docker init DAN migration Laravel.
-- =====================================================================

CREATE EXTENSION IF NOT EXISTS postgis;

-- Trigger umum untuk mengisi updated_at (juga dipakai domain lain)
CREATE OR REPLACE FUNCTION set_updated_at() RETURNS trigger AS $$
BEGIN
    NEW.updated_at = now();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- ---------------------------------------------------------------------
-- regions (PBI-02 / FR-02)
-- Tabel 16: id, code, name, level (prov/kab/kec), boundary_polygon, is_3t_tier
-- is_3t_tier: 0 = bukan wilayah 3T, 1 = Tier-1 (prioritas lebih tinggi), 2 = Tier-2
-- boundary_polygon bertipe MultiPolygon agar wilayah kepulauan dapat disimpan;
-- GeoJSON bertipe Polygon dinormalkan saat impor (ST_Multi).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS regions (
    id                BIGSERIAL    PRIMARY KEY,
    code              VARCHAR(20)  NOT NULL UNIQUE,
    name              VARCHAR(150) NOT NULL,
    level             VARCHAR(4)   NOT NULL CHECK (level IN ('prov', 'kab', 'kec')),
    boundary_polygon  geography(MultiPolygon, 4326),
    is_3t_tier        SMALLINT     NOT NULL DEFAULT 0 CHECK (is_3t_tier IN (0, 1, 2)),
    created_at        TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at        TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_regions_level       ON regions (level);
CREATE INDEX IF NOT EXISTS idx_regions_code_prefix ON regions (code varchar_pattern_ops);
CREATE INDEX IF NOT EXISTS idx_regions_3t          ON regions (is_3t_tier) WHERE is_3t_tier > 0;
CREATE INDEX IF NOT EXISTS gist_regions_boundary   ON regions USING GIST (boundary_polygon);

DROP TRIGGER IF EXISTS trg_regions_updated_at ON regions;
CREATE TRIGGER trg_regions_updated_at BEFORE UPDATE ON regions
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ---------------------------------------------------------------------
-- facility_categories (PBI-03 / FR-03)
-- Tabel 16: id, name, sub_category, urgency_weight (1-5), default_unit, is_enabled
-- Hapus = nonaktifkan (is_enabled = false)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS facility_categories (
    id               BIGSERIAL    PRIMARY KEY,
    name             VARCHAR(100) NOT NULL,
    sub_category     VARCHAR(100) NOT NULL DEFAULT '',
    urgency_weight   SMALLINT     NOT NULL CHECK (urgency_weight BETWEEN 1 AND 5),
    default_unit     VARCHAR(30),
    is_enabled       BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at       TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at       TIMESTAMPTZ  NOT NULL DEFAULT now(),
    CONSTRAINT uq_facility_category UNIQUE (name, sub_category)
);

DROP TRIGGER IF EXISTS trg_facility_categories_updated_at ON facility_categories;
CREATE TRIGGER trg_facility_categories_updated_at BEFORE UPDATE ON facility_categories
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- ---------------------------------------------------------------------
-- schools (PBI-01 / FR-01)
-- Tabel 16: id, npsn (unique), name, educational_level, coordinates (Point),
--           address, is_active (+ FK region_id pada ERD)
-- Tambahan dari FR-01 ("mengubah kontak/akreditasi"): accreditation,
-- contact_phone, contact_email. Hapus = soft delete lewat is_active.
-- coordinates = titik master untuk geofence 250 m (Domain 2).
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS schools (
    id                 BIGSERIAL    PRIMARY KEY,
    region_id          BIGINT       NOT NULL REFERENCES regions(id) ON DELETE RESTRICT,
    npsn               CHAR(8)      NOT NULL UNIQUE CHECK (npsn ~ '^[0-9]{8}$'),
    name               VARCHAR(200) NOT NULL,
    educational_level  VARCHAR(10)  NOT NULL CHECK (educational_level IN ('SD', 'SMP', 'SMA')),
    address            TEXT,
    coordinates        geography(Point, 4326) NOT NULL,
    accreditation      CHAR(1)      CHECK (accreditation IN ('A', 'B', 'C')),
    contact_phone      VARCHAR(30),
    contact_email      VARCHAR(150),
    is_active          BOOLEAN      NOT NULL DEFAULT TRUE,
    created_at         TIMESTAMPTZ  NOT NULL DEFAULT now(),
    updated_at         TIMESTAMPTZ  NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_schools_region ON schools (region_id);
CREATE INDEX IF NOT EXISTS idx_schools_level  ON schools (educational_level);
CREATE INDEX IF NOT EXISTS gist_schools_coordinates ON schools USING GIST (coordinates);

DROP TRIGGER IF EXISTS trg_schools_updated_at ON schools;
CREATE TRIGGER trg_schools_updated_at BEFORE UPDATE ON schools
    FOR EACH ROW EXECUTE FUNCTION set_updated_at();
