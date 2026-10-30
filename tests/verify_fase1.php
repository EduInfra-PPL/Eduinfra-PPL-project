<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

echo "========================================================\n";
echo "   DEEP AUTOMATED VERIFICATION: FASE 1 & FRONTEND UI    \n";
echo "========================================================\n\n";

$passed = 0;
$failed = 0;

function assertTest(string $description, bool $condition, ?string $detail = null): void {
    global $passed, $failed;
    if ($condition) {
        echo "  [PASS] {$description}\n";
        $passed++;
    } else {
        echo "  [FAIL] {$description}\n";
        if ($detail) {
            echo "         Detail: {$detail}\n";
        }
        $failed++;
    }
}

// -------------------------------------------------------------
// 1. TESTING ROUTES & FRONTEND UI RENDERING
// -------------------------------------------------------------
echo "[1] PENGUJIAN ROUTE & FRONTEND BLADE UI:\n";

// Test Route 1: /reports (Index)
$request1 = \Illuminate\Http\Request::create('/reports', 'GET');
$response1 = $kernel->handle($request1);
$content1 = $response1->getContent();

assertTest(
    "HTTP GET /reports mengembalikan status 200 OK",
    $response1->getStatusCode() === 200,
    "Status code: " . $response1->getStatusCode()
);
assertTest(
    "Halaman Index memuat judul 'Daftar Laporan Fasilitas Sekolah'",
    str_contains($content1, 'Daftar Laporan Fasilitas Sekolah')
);
assertTest(
    "Halaman Index memuat komponen tabel data laporan atau status kosong",
    str_contains($content1, 'No. Tiket') || str_contains($content1, 'Belum Ada Laporan Kerusakan')
);
assertTest(
    "Halaman Index memuat kartu KPI ringkasan tiket ('Total Tiket Masuk')",
    str_contains($content1, 'Total Tiket Masuk')
);
assertTest(
    "Halaman Index memuat tombol CTA '+ Buat Laporan Kerusakan Baru'",
    str_contains($content1, 'Buat Laporan Kerusakan Baru')
);

// Test Route 2: /reports/create (Form Wizard)
$request2 = \Illuminate\Http\Request::create('/reports/create', 'GET');
$response2 = $kernel->handle($request2);
$content2 = $response2->getContent();

assertTest(
    "HTTP GET /reports/create mengembalikan status 200 OK",
    $response2->getStatusCode() === 200,
    "Status code: " . $response2->getStatusCode()
);
assertTest(
    "Formulir Create memuat judul 'Laporan Kerusakan Fasilitas Sekolah'",
    str_contains($content2, 'Laporan Kerusakan Fasilitas Sekolah')
);
assertTest(
    "Formulir Create memuat badge 'Protokol EXIF v4.2'",
    str_contains($content2, 'Protokol EXIF v4.2')
);
assertTest(
    "Formulir Create memuat Step 1 Wizard '1. Identitas & Kategori'",
    str_contains($content2, '1. Identitas &amp; Kategori') || str_contains($content2, '1. Identitas & Kategori')
);
assertTest(
    "Formulir Create memuat panel 'Titik Master GPS Sekolah' (WGS84)",
    str_contains($content2, 'Titik Master GPS Sekolah')
);
assertTest(
    "Formulir Create memuat visualizer 'Radius 250m Buffer'",
    str_contains($content2, 'Radius 250m Buffer')
);

echo "\n";

// -------------------------------------------------------------
// 2. TESTING MODEL ELOQUENT & ACCESSORS (FASE 1)
// -------------------------------------------------------------
echo "[2] PENGUJIAN MODEL ELOQUENT (DamageReport):\n";

$report = new \App\Models\DamageReport([
    'ticket_num' => 'RPT-2026-TEST',
    'school_id' => 1,
    'category_id' => 2,
    'title' => 'Kerusakan Atap RKB',
    'description' => 'Kayu penyangga atap rapuh dan bocor deras',
    'severity_level' => 5,
    'status' => 'pending',
]);

assertTest(
    "Model DamageReport dapat di-instantiate dengan atribut fillable",
    $report->ticket_num === 'RPT-2026-TEST' && $report->severity_level === 5
);

assertTest(
    "Accessor severity_label menghasilkan format 'Level 5 – Kritis'",
    $report->severity_label === 'Level 5 – Kritis',
    "Actual: " . $report->severity_label
);

$report->severity_level = 3;
assertTest(
    "Accessor severity_label menghasilkan format 'Level 3 – Sedang'",
    $report->severity_label === 'Level 3 – Sedang',
    "Actual: " . $report->severity_label
);

$report->severity_level = 1;
assertTest(
    "Accessor severity_label menghasilkan format 'Level 1 – Ringan'",
    $report->severity_label === 'Level 1 – Ringan',
    "Actual: " . $report->severity_label
);

assertTest(
    "Accessor status_label menghasilkan 'Pending Visitasi Lapangan'",
    $report->status_label === 'Pending Visitasi Lapangan',
    "Actual: " . $report->status_label
);

$report->status = 'verified';
assertTest(
    "Accessor status_label menghasilkan 'Verified Auditor'",
    $report->status_label === 'Verified Auditor',
    "Actual: " . $report->status_label
);

$report->status = 'in_repair';
assertTest(
    "Accessor status_label menghasilkan 'Dalam Renovasi'",
    $report->status_label === 'Dalam Renovasi',
    "Actual: " . $report->status_label
);

assertTest(
    "Relasi school() didefinisikan dengan benar (BelongsTo)",
    $report->school() instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo
);

assertTest(
    "Relasi category() didefinisikan dengan benar (BelongsTo)",
    $report->category() instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo
);

assertTest(
    "Relasi reporter() didefinisikan dengan benar (BelongsTo)",
    $report->reporter() instanceof \Illuminate\Database\Eloquent\Relations\BelongsTo
);

// Inverse relationships
$school = new \App\Models\School();
assertTest(
    "Model School memiliki relasi balik damageReports() (HasMany)",
    $school->damageReports() instanceof \Illuminate\Database\Eloquent\Relations\HasMany
);

$category = new \App\Models\FacilityCategory();
assertTest(
    "Model FacilityCategory memiliki relasi balik damageReports() (HasMany)",
    $category->damageReports() instanceof \Illuminate\Database\Eloquent\Relations\HasMany
);

echo "\n";

// -------------------------------------------------------------
// 3. TESTING FILE MIGRASI DATABASE (FASE 1)
// -------------------------------------------------------------
echo "[3] PENGUJIAN FILE MIGRASI (damage_reports):\n";

$migrationPath = __DIR__ . '/../database/migrations/2026_10_29_000001_create_damage_reports_table.php';
assertTest(
    "Berkas migrasi 2026_10_29_000001_create_damage_reports_table.php ada",
    file_exists($migrationPath)
);

$migration = require $migrationPath;
assertTest(
    "Berkas migrasi mengembalikan anonymous class migration yang valid",
    $migration instanceof \Illuminate\Database\Migrations\Migration
);
assertTest(
    "Metode up() dan down() tersedia pada migrasi",
    method_exists($migration, 'up') && method_exists($migration, 'down')
);

echo "\n========================================================\n";
echo "   HASIL AKHIR: {$passed} BERHASIL, {$failed} GAGAL      \n";
echo "========================================================\n";

exit($failed > 0 ? 1 : 0);
