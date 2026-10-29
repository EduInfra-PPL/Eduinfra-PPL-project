-- =====================================================================
-- EduInfra Tracker - Seed Domain 1 (DATA DUMMY untuk dev/staging)
-- Jalankan setelah 01_schema.sql. Aman dijalankan ulang (ON CONFLICT DO NOTHING).
--
-- * Poligon wilayah (yang ada) = kotak perkiraan, BUKAN batas resmi; sebagian wilayah
--   sengaja boundary NULL. Ganti dengan GeoJSON
--   resmi lewat POST /api/v1/regions/import (upsert berdasarkan code).
-- * is_3t_tier = 1 untuk kabupaten yang termasuk 62 daerah tertinggal
--   Perpres 63/2020; kriteria Tier-1 vs Tier-2 belum didefinisikan di proposal
--   (Tier-2 belum dipakai), perlu disepakati dengan pemangku kepentingan.
-- * Sekolah = data dummy (NPSN rentang 99000001+, bukan NPSN asli).
-- * Bobot urgensi kategori = nilai awal, dapat diubah Admin (FR-03).
-- =====================================================================

-- 1. regions: provinsi (4 dengan kotak perkiraan, sisanya boundary NULL)
INSERT INTO regions (code, name, level, boundary_polygon, is_3t_tier)
SELECT v.code, v.name, 'prov',
       ST_Multi(ST_MakeEnvelope(v.minx, v.miny, v.maxx, v.maxy, 4326))::geography, 0
FROM (VALUES
    ('53', 'Nusa Tenggara Timur', 118.9, -11.2, 125.3, -8.0),
    ('93', 'Papua Selatan', 137.6, -9.2, 141.1, -4.8),
    ('95', 'Papua Pegunungan', 137.9, -5.4, 141.1, -3.6),
    ('32', 'Jawa Barat', 106.3, -7.9, 108.9, -5.9)
) AS v(code, name, minx, miny, maxx, maxy)
ON CONFLICT (code) DO NOTHING;

INSERT INTO regions (code, name, level, is_3t_tier) VALUES
    ('12', 'Sumatera Utara', 'prov', 0),
    ('13', 'Sumatera Barat', 'prov', 0),
    ('16', 'Sumatera Selatan', 'prov', 0),
    ('18', 'Lampung', 'prov', 0),
    ('52', 'Nusa Tenggara Barat', 'prov', 0),
    ('72', 'Sulawesi Tengah', 'prov', 0),
    ('81', 'Maluku', 'prov', 0),
    ('82', 'Maluku Utara', 'prov', 0),
    ('91', 'Papua', 'prov', 0),
    ('92', 'Papua Barat', 'prov', 0),
    ('96', 'Papua Barat Daya', 'prov', 0),
    ('94', 'Papua Tengah', 'prov', 0)
ON CONFLICT (code) DO NOTHING;

-- 2. regions: kabupaten/kota
-- 2a. dengan kotak perkiraan (demo, dipakai sekolah dummy)
INSERT INTO regions (code, name, level, boundary_polygon, is_3t_tier)
SELECT v.code, v.name, 'kab',
       ST_Multi(ST_MakeEnvelope(v.minx, v.miny, v.maxx, v.maxy, 4326))::geography, v.tier
FROM (VALUES
    ('53.05', 'Kabupaten Alor', 123.9, -8.6, 124.9, -8.1, 1),
    ('53.02', 'Kabupaten Timor Tengah Selatan', 123.9, -10.2, 124.8, -9.4, 1),
    ('53.71', 'Kota Kupang', 123.5, -10.25, 123.7, -10.1, 0),
    ('93.04', 'Kabupaten Asmat', 137.7, -6.4, 139.2, -5.0, 1),
    ('95.02', 'Kabupaten Pegunungan Bintang', 139.9, -5.2, 141.0, -3.9, 1),
    ('95.08', 'Kabupaten Nduga', 137.9, -4.8, 138.6, -3.9, 1),
    ('95.01', 'Kabupaten Jayawijaya', 138.7, -4.5, 139.4, -3.7, 1),
    ('32.04', 'Kabupaten Bandung', 107.3, -7.35, 107.9, -6.98, 0),
    ('32.73', 'Kota Bandung', 107.55, -6.97, 107.72, -6.84, 0)
) AS v(code, name, minx, miny, maxx, maxy, tier)
ON CONFLICT (code) DO NOTHING;

-- 2b. 62 kabupaten tertinggal (Perpres 63/2020) tanpa poligon, is_3t_tier = 1
--     (poligon diisi lewat POST /api/v1/regions/import; yang sudah ada di 2a dilewati)
INSERT INTO regions (code, name, level, is_3t_tier) VALUES
    ('12.04', 'Kabupaten Nias', 'kab', 1),
    ('12.14', 'Kabupaten Nias Selatan', 'kab', 1),
    ('12.24', 'Kabupaten Nias Utara', 'kab', 1),
    ('12.25', 'Kabupaten Nias Barat', 'kab', 1),
    ('13.09', 'Kabupaten Kepulauan Mentawai', 'kab', 1),
    ('16.13', 'Kabupaten Musi Rawas Utara', 'kab', 1),
    ('18.13', 'Kabupaten Pesisir Barat', 'kab', 1),
    ('52.08', 'Kabupaten Lombok Utara', 'kab', 1),
    ('53.12', 'Kabupaten Sumba Barat', 'kab', 1),
    ('53.11', 'Kabupaten Sumba Timur', 'kab', 1),
    ('53.01', 'Kabupaten Kupang', 'kab', 1),
    ('53.04', 'Kabupaten Belu', 'kab', 1),
    ('53.13', 'Kabupaten Lembata', 'kab', 1),
    ('53.14', 'Kabupaten Rote Ndao', 'kab', 1),
    ('53.17', 'Kabupaten Sumba Tengah', 'kab', 1),
    ('53.18', 'Kabupaten Sumba Barat Daya', 'kab', 1),
    ('53.19', 'Kabupaten Manggarai Timur', 'kab', 1),
    ('53.20', 'Kabupaten Sabu Raijua', 'kab', 1),
    ('53.21', 'Kabupaten Malaka', 'kab', 1),
    ('72.03', 'Kabupaten Donggala', 'kab', 1),
    ('72.09', 'Kabupaten Tojo Una-Una', 'kab', 1),
    ('72.10', 'Kabupaten Sigi', 'kab', 1),
    ('81.03', 'Kabupaten Maluku Tenggara Barat (Kepulauan Tanimbar)', 'kab', 1),
    ('81.07', 'Kabupaten Kepulauan Aru', 'kab', 1),
    ('81.06', 'Kabupaten Seram Bagian Barat', 'kab', 1),
    ('81.05', 'Kabupaten Seram Bagian Timur', 'kab', 1),
    ('81.08', 'Kabupaten Maluku Barat Daya', 'kab', 1),
    ('81.09', 'Kabupaten Buru Selatan', 'kab', 1),
    ('82.05', 'Kabupaten Kepulauan Sula', 'kab', 1),
    ('82.08', 'Kabupaten Pulau Taliabu', 'kab', 1),
    ('92.07', 'Kabupaten Teluk Wondama', 'kab', 1),
    ('92.06', 'Kabupaten Teluk Bintuni', 'kab', 1),
    ('96.02', 'Kabupaten Sorong Selatan', 'kab', 1),
    ('96.01', 'Kabupaten Sorong', 'kab', 1),
    ('96.04', 'Kabupaten Tambrauw', 'kab', 1),
    ('96.05', 'Kabupaten Maybrat', 'kab', 1),
    ('92.11', 'Kabupaten Manokwari Selatan', 'kab', 1),
    ('92.12', 'Kabupaten Pegunungan Arfak', 'kab', 1),
    ('94.01', 'Kabupaten Nabire', 'kab', 1),
    ('94.03', 'Kabupaten Paniai', 'kab', 1),
    ('94.02', 'Kabupaten Puncak Jaya', 'kab', 1),
    ('93.02', 'Kabupaten Boven Digoel', 'kab', 1),
    ('93.03', 'Kabupaten Mappi', 'kab', 1),
    ('95.03', 'Kabupaten Yahukimo', 'kab', 1),
    ('95.04', 'Kabupaten Tolikara', 'kab', 1),
    ('91.11', 'Kabupaten Keerom', 'kab', 1),
    ('91.15', 'Kabupaten Waropen', 'kab', 1),
    ('91.19', 'Kabupaten Supiori', 'kab', 1),
    ('91.20', 'Kabupaten Mamberamo Raya', 'kab', 1),
    ('95.07', 'Kabupaten Lanny Jaya', 'kab', 1),
    ('95.05', 'Kabupaten Mamberamo Tengah', 'kab', 1),
    ('95.06', 'Kabupaten Yalimo', 'kab', 1),
    ('94.05', 'Kabupaten Puncak', 'kab', 1),
    ('94.06', 'Kabupaten Dogiyai', 'kab', 1),
    ('94.07', 'Kabupaten Intan Jaya', 'kab', 1),
    ('94.08', 'Kabupaten Deiyai', 'kab', 1)
ON CONFLICT (code) DO NOTHING;

-- 3. facility_categories (nilai awal; Admin dapat mengubah bobot 1-5)
INSERT INTO facility_categories (name, sub_category, urgency_weight, default_unit) VALUES
    ('Ruang Kelas',  'Struktur dan Atap',      5, 'ruang'),
    ('Ruang Kelas',  'Lantai dan Dinding',     4, 'ruang'),
    ('Ruang Kelas',  'Plafon',                 3, 'ruang'),
    ('Ruang Kelas',  'Mebel',                  2, 'set'),
    ('Sanitasi',     'Toilet Siswa',           4, 'unit'),
    ('Sanitasi',     'Air Minum',              5, 'titik'),
    ('Sanitasi',     'Fasilitas Cuci Tangan',  3, 'unit'),
    ('Laboratorium', 'Laboratorium IPA',       3, 'ruang'),
    ('Laboratorium', 'Laboratorium Komputer',  3, 'ruang'),
    ('Perpustakaan', 'Ruang dan Rak Buku',     2, 'ruang'),
    ('Kelistrikan',  'Instalasi Listrik',      4, 'paket')
ON CONFLICT (name, sub_category) DO NOTHING;

-- 4. schools (dummy): 3 sekolah per kabupaten/kota = SD, SMP, SMA
INSERT INTO schools (region_id, npsn, name, educational_level, address,
                     coordinates, accreditation, contact_phone, contact_email)
SELECT r.id, v.npsn, v.name, v.level, v.address,
       ST_SetSRID(ST_MakePoint(v.lng, v.lat), 4326)::geography,
       v.accreditation, v.phone, v.email
FROM (VALUES
    ('53.05', '99000001', 'SDN Dummy 01 Alor', 'SD', 'Alamat dummy, Kabupaten Alor', 124.25, -8.35, 'B', '0812-0000-0001', 'dummy01@example.org'),
    ('53.05', '99000002', 'SMPN Dummy 01 Alor', 'SMP', 'Alamat dummy, Kabupaten Alor', 124.4, -8.425, 'C', '0812-0000-0002', 'dummy02@example.org'),
    ('53.05', '99000003', 'SMAN Dummy 01 Alor', 'SMA', 'Alamat dummy, Kabupaten Alor', 124.52, -8.31, NULL, '0812-0000-0003', 'dummy03@example.org'),
    ('53.02', '99000004', 'SDN Dummy 01 TTS', 'SD', 'Alamat dummy, Kabupaten Timor Tengah Selatan', 124.215, -9.8, 'A', '0812-0000-0004', 'dummy04@example.org'),
    ('53.02', '99000005', 'SMPN Dummy 01 TTS', 'SMP', 'Alamat dummy, Kabupaten Timor Tengah Selatan', 124.35, -9.92, 'B', '0812-0000-0005', 'dummy05@example.org'),
    ('53.02', '99000006', 'SMAN Dummy 01 TTS', 'SMA', 'Alamat dummy, Kabupaten Timor Tengah Selatan', 124.458, -9.736, 'C', '0812-0000-0006', 'dummy06@example.org'),
    ('53.71', '99000007', 'SDN Dummy 01 Kota Kupang', 'SD', 'Alamat dummy, Kota Kupang', 123.57, -10.175, NULL, '0812-0000-0007', 'dummy07@example.org'),
    ('53.71', '99000008', 'SMPN Dummy 01 Kota Kupang', 'SMP', 'Alamat dummy, Kota Kupang', 123.6, -10.1975, 'A', '0812-0000-0008', 'dummy08@example.org'),
    ('53.71', '99000009', 'SMAN Dummy 01 Kota Kupang', 'SMA', 'Alamat dummy, Kota Kupang', 123.624, -10.163, 'B', '0812-0000-0009', 'dummy09@example.org'),
    ('93.04', '99000010', 'SDN Dummy 01 Asmat', 'SD', 'Alamat dummy, Kabupaten Asmat', 138.225, -5.7, 'C', '0812-0000-0010', 'dummy10@example.org'),
    ('93.04', '99000011', 'SMPN Dummy 01 Asmat', 'SMP', 'Alamat dummy, Kabupaten Asmat', 138.45, -5.91, NULL, '0812-0000-0011', 'dummy11@example.org'),
    ('93.04', '99000012', 'SMAN Dummy 01 Asmat', 'SMA', 'Alamat dummy, Kabupaten Asmat', 138.63, -5.588, 'A', '0812-0000-0012', 'dummy12@example.org'),
    ('95.02', '99000013', 'SDN Dummy 01 Pegunungan Bintang', 'SD', 'Alamat dummy, Kabupaten Pegunungan Bintang', 140.285, -4.55, 'B', '0812-0000-0013', 'dummy13@example.org'),
    ('95.02', '99000014', 'SMPN Dummy 01 Pegunungan Bintang', 'SMP', 'Alamat dummy, Kabupaten Pegunungan Bintang', 140.45, -4.745, 'C', '0812-0000-0014', 'dummy14@example.org'),
    ('95.02', '99000015', 'SMAN Dummy 01 Pegunungan Bintang', 'SMA', 'Alamat dummy, Kabupaten Pegunungan Bintang', 140.582, -4.446, NULL, '0812-0000-0015', 'dummy15@example.org'),
    ('95.08', '99000016', 'SDN Dummy 01 Nduga', 'SD', 'Alamat dummy, Kabupaten Nduga', 138.145, -4.35, 'A', '0812-0000-0016', 'dummy16@example.org'),
    ('95.08', '99000017', 'SMPN Dummy 01 Nduga', 'SMP', 'Alamat dummy, Kabupaten Nduga', 138.25, -4.485, 'B', '0812-0000-0017', 'dummy17@example.org'),
    ('95.08', '99000018', 'SMAN Dummy 01 Nduga', 'SMA', 'Alamat dummy, Kabupaten Nduga', 138.334, -4.278, 'C', '0812-0000-0018', 'dummy18@example.org'),
    ('95.01', '99000019', 'SDN Dummy 01 Jayawijaya', 'SD', 'Alamat dummy, Kabupaten Jayawijaya', 138.945, -4.1, NULL, '0812-0000-0019', 'dummy19@example.org'),
    ('95.01', '99000020', 'SMPN Dummy 01 Jayawijaya', 'SMP', 'Alamat dummy, Kabupaten Jayawijaya', 139.05, -4.22, 'A', '0812-0000-0020', 'dummy20@example.org'),
    ('95.01', '99000021', 'SMAN Dummy 01 Jayawijaya', 'SMA', 'Alamat dummy, Kabupaten Jayawijaya', 139.134, -4.036, 'B', '0812-0000-0021', 'dummy21@example.org'),
    ('32.04', '99000022', 'SDN Dummy 01 Kab. Bandung', 'SD', 'Alamat dummy, Kabupaten Bandung', 107.51, -7.165, 'C', '0812-0000-0022', 'dummy22@example.org'),
    ('32.04', '99000023', 'SMPN Dummy 01 Kab. Bandung', 'SMP', 'Alamat dummy, Kabupaten Bandung', 107.6, -7.2205, NULL, '0812-0000-0023', 'dummy23@example.org'),
    ('32.04', '99000024', 'SMAN Dummy 01 Kab. Bandung', 'SMA', 'Alamat dummy, Kabupaten Bandung', 107.672, -7.1354, 'A', '0812-0000-0024', 'dummy24@example.org'),
    ('32.73', '99000025', 'SDN Dummy 01 Kota Bandung', 'SD', 'Alamat dummy, Kota Bandung', 107.6095, -6.905, 'B', '0812-0000-0025', 'dummy25@example.org'),
    ('32.73', '99000026', 'SMPN Dummy 01 Kota Bandung', 'SMP', 'Alamat dummy, Kota Bandung', 107.635, -6.9245, 'C', '0812-0000-0026', 'dummy26@example.org'),
    ('32.73', '99000027', 'SMAN Dummy 01 Kota Bandung', 'SMA', 'Alamat dummy, Kota Bandung', 107.6554, -6.8946, NULL, '0812-0000-0027', 'dummy27@example.org')
) AS v(region_code, npsn, name, level, address, lng, lat, accreditation, phone, email)
JOIN regions r ON r.code = v.region_code
ON CONFLICT (npsn) DO NOTHING;
