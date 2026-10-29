-- =====================================================================
-- OPSIONAL - Data uji kinerja NFR-01: 50.000 sekolah dummy
-- (query spasial < 150 ms pada 50.000 titik dengan indeks GiST; Subbab 5.3)
-- TIDAK dijalankan otomatis (ada di subfolder optional/).
-- Titik disebar acak di dalam kotak tiap kabupaten/kota hasil seed; bila
-- poligon sudah diganti GeoJSON resmi, sebagian titik bisa jatuh di luar
-- poligon (tidak masalah untuk uji kinerja, tetapi bukan untuk uji geofence).
-- NPSN dummy: 99100001 .. 99150000. Aman dijalankan ulang.
-- =====================================================================

WITH kab AS (
    SELECT id,
           boundary_polygon::geometry AS geom,
           row_number() OVER (ORDER BY id) - 1 AS rn
    FROM regions
    WHERE level = 'kab' AND boundary_polygon IS NOT NULL
),
cnt AS (SELECT count(*)::int AS c FROM kab)
INSERT INTO schools (region_id, npsn, name, educational_level, address, coordinates)
SELECT k.id,
       lpad((99100000 + g)::text, 8, '0'),
       'Sekolah Uji ' || g,
       (ARRAY['SD', 'SMP', 'SMA'])[1 + (g % 3)],
       'Alamat uji ' || g,
       ST_SetSRID(ST_MakePoint(
           ST_XMin(k.geom) + random() * (ST_XMax(k.geom) - ST_XMin(k.geom)),
           ST_YMin(k.geom) + random() * (ST_YMax(k.geom) - ST_YMin(k.geom))
       ), 4326)::geography
FROM generate_series(1, 50000) AS g
CROSS JOIN cnt
JOIN kab k ON k.rn = (g % cnt.c)
ON CONFLICT (npsn) DO NOTHING;

ANALYZE schools;

-- Ukur latensi (Query B proposal: sekolah dalam radius dari posisi pengguna):
-- EXPLAIN (ANALYZE, BUFFERS)
-- SELECT s.id, s.npsn, s.name,
--        ST_Distance(s.coordinates, ST_SetSRID(ST_MakePoint(124.4, -8.3), 4326)::geography) AS m
-- FROM schools s
-- WHERE s.is_active
--   AND ST_DWithin(s.coordinates, ST_SetSRID(ST_MakePoint(124.4, -8.3), 4326)::geography, 15000)
-- ORDER BY m LIMIT 20;
