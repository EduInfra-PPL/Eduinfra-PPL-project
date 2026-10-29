# AGENT ROLE & ARCHITECTURAL GUIDELINES (EDUINFRA TRACKER)
Role: Senior Fullstack Laravel Architect & Developer
Stack: Laravel 11+, Pure PHP, Blade Components, Tailwind CSS v4, PostgreSQL/PostGIS (Local fallback: SQLite/MySQL), Alpine.js, MapTiler SDK

---

## 1. MANDATORY 3-STAGE WORKFLOW (BACA ──► VALIDASI ──► EKSEKUSI)
Setiap kali agen menerima instruksi atau hendak membuat/memperbarui kode, agen **WAJIB** melalui 3 tahapan terstruktur berikut:

```
┌───────────────────────────────────────────────────────────────────────────────────┐
│                        TAHAP 1: BACA RULES, PRD & ERD                             │
│  - Baca AGENT_RULE.md (Baseline arsitektur & konvensi kode)                       │
│  - Baca Dokumen PRD (dokumen/..._new.pdf atau katalog PRD di Section 5)           │
│  - Baca Diagram ERD (dokumen/ERD.png sebagai anchor visual relasi data)           │
└────────────────────────────────────────┬──────────────────────────────────────────┘
                                         │
┌────────────────────────────────────────▼──────────────────────────────────────────┐
│                   TAHAP 2: VALIDASI SEBELUM EKSEKUSI                              │
│         (The Reflection & Living Baseline Re-mapping Gate)                        │
│  - Cek keselarasan fitur terhadap PRD & ERD                                       │
│  - Jika aturan dirasa kaku / kurang sesuai / developer clueless:                  │
│    🛑 Lakukan PEMETAAN ULANG (RE-MAPPING) & ajukan penyesuaian ke pengguna        │
│  - Tunggu konfirmasi sebelum menyentuh file kode                                  │
└────────────────────────────────────────┬──────────────────────────────────────────┘
                                         │
┌────────────────────────────────────────▼──────────────────────────────────────────┐
│                    TAHAP 3: EKSEKUSI BERTAHAP & VERIFIKASI                        │
│  - Backend First: Migration & Model ──► FormRequest ──► Action ──► Controller     │
│  - Frontend: Reusable Component (Blade) ──► Page View (<x-layouts.app>)           │
│  - Verifikasi: npm.cmd run build & artisan route:list                             │
│  - Git Protocol: Berikan REKOMENDASI commit (DILARANG commit otomatis)            │
└───────────────────────────────────────────────────────────────────────────────────┘
```

### A. TAHAP 1: BACA RULES, PRD & ERD (Context Loading)
Sebelum menyentuh kode apa pun, agen wajib mengumpulkan konteks komprehensif:
1. **`AGENT_RULE.md`**: Sebagai *single baseline* arsitektur dan gaya penulisan kode.
2. **Dokumen PRD & Spesifikasi Teknis** (`dokumen/Dokumen PRD & Spesifikasi Teknis 15 CRUD Platform Audit Fasilitas Sekolah_new.pdf`):
   - Memetakan proses bisnis, 4 persona RBAC, dan validasi anti-fraud geofencing (<250m).
3. **Diagram ERD Visual** (`dokumen/ERD.png`):
   - Membaca diagram *Gambar 7 Entity Relationship Diagram EduInfra Tracker* sebagai panduan relasi visual (PK, FK, dan kardinalitas 1:N / polymorphic).

### B. TAHAP 2: VALIDASI SEBELUM EKSEKUSI (Living Baseline & Re-mapping Gate)
Tahap ini adalah gerbang refleksi dan evaluasi sebelum coding:
1. **Prinsip Living Baseline (Non-Strict Anchor)**:
   - `AGENT_RULE.md` dan `dokumen/ERD.png` adalah **pegangan acuan awal (anchor)** saat developer/agen ragu atau *clueless*, **BUKAN dogma kaku yang tidak bisa diubah**.
   - Sistem bersifat adaptif terhadap kebutuhan teknis riil di lapangan (misalnya: penambahan kolom audit timestamps, penyesuaian tipe data spatial lokal, simplifikasi query, atau refactoring komponen UI).
2. **Protokol Pemetaan Ulang (Re-mapping Protocol)**:
   - Jika agen menemukan bahwa aturan baseline dirasa **kurang pas, over-engineered, usang, atau tidak efisien**:
     - 🛑 **AGEN WAJIB MEMBUAT PEMETAAN ULANG (RE-MAPPING)**.
     - Paparkan alasan teknis secara objektif dan tawarkan alternatif solusi yang lebih bersih/efisien kepada pengguna.
     - Konfirmasi dengan pengguna sebelum mengeksekusi perubahan.
     - Setelah disepakati, agen mengeksekusi kode dan mendokumentasikan penyesuaian tersebut ke dalam `AGENT_RULE.md` (evolusi aturan berkelanjutan).

### C. TAHAP 3: EKSEKUSI BERTAHAP & VERIFIKASI (Execution & Quality Check)
1. **Urutan Pengerjaan Terstruktur**:
   - **Backend First**: Migration & Model (ERD) ──► `FormRequest` ──► `Action`/`Service` ──► `Controller` (Thin) ──► Route.
   - **Frontend**: Component Reusable (`resources/views/components/`) ──► Page View (`<x-layouts.app>`).
2. **Verifikasi Kualitas**:
   - Jalankan `npm.cmd run build` untuk memastikan kompilasi aset Vite & CSS Tailwind v4 bersih.
   - Jalankan `php artisan route:list` atau command artisan terkait untuk memeriksa integritas route.
3. **Strict Git Protocol**:
   - ⛔ **DILARANG KERAS** menjalankan `git commit` otomatis di latar belakang!
   - Agen hanya menyusun rekomendasi commit message terstruktur (*Conventional Commits*) untuk dieksekusi manual oleh pengguna.

---

## 2. BACKEND ARCHITECTURE (STRICT THIN CONTROLLERS)

### A. Tanggung Jawab Controller (HTTP Entry Point Saja)
Controller **HANYA** bertindak sebagai *HTTP Traffic Controller*:
1. Menerima request yang sudah tervalidasi via `FormRequest`.
2. Meneruskan data ke **Action Class** (`app/Actions/{Domain}/{ActionName}.php`) atau **Service Class** (`app/Services/{Domain}Service.php`).
3. Mengembalikan respons: `view()`, `redirect()`, atau `response()->json()`.
4. **Maksimal panjang method controller adalah 10–20 baris.** Dilarang keras menaruh logic bisnis, kalkulasi spasial, atau query panjang di controller.

### B. Aturan Penempatan Business Logic:
- **Validation**: Wajib menggunakan `FormRequest` khusus (`php artisan make:request ...`). Dilarang melakukan `$request->validate([...])` langsung di controller.
- **Business Logic & Workflow**: Tempatkan di dalam `App\Actions\{Domain}\{ActionName}.php` (Single Responsibility) atau `App\Services\{Domain}Service.php`.
- **Database Transactions**: Pembungkusan `DB::transaction()` dilakukan di layer Action/Service, bukan di controller.
- **Query Kompleks & Filter**: Tempatkan pada **Eloquent Local Scopes** di Model atau Query Builder class.
- **Spatial Anti-Fraud Logic**: Validasi deviasi jarak koordinat foto terhadap koordinat sekolah (`< 250m`) dikalkulasikan di layer Action atau Service khusus.

### Contoh Pola Backend Wajib:
```php
// app/Http/Controllers/Reports/DamageReportController.php
namespace App\Http\Controllers\Reports;

use App\Actions\Reports\CreateDamageReportAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Reports\StoreDamageReportRequest;
use Illuminate\Http\RedirectResponse;

class DamageReportController extends Controller
{
    public function store(StoreDamageReportRequest $request, CreateDamageReportAction $action): RedirectResponse
    {
        $report = $action->execute($request->validated());

        return redirect()->route('reports.show', $report)
            ->with('success', 'Laporan kerusakan berhasil diverifikasi dan disimpan.');
    }
}
```

### C. Pengecualian Tercatat (Re-mapping)
- Modul **Domain 1** (`SchoolController`, `RegionController`, `FacilityCategoryController`) masih memakai `$request->validate()` dan `DB::transaction()` langsung di controller, serta logic query di dalamnya. Ini **belum** sesuai aturan di atas.
- Diterima sebagai pengecualian sementara saat integrasi awal. Refactor ke `FormRequest` + `Action` + thin controller dijadwalkan sebagai langkah terpisah.

---

## 3. FRONTEND ARCHITECTURE (BLADE COMPONENT-DRIVEN)

### A. Hierarki Layout (Mandatory Layout Pattern)
- **Semua page view wajib dibungkus dalam master layout** `<x-layouts.app>`.
- Dilarang menulis tag `<!DOCTYPE html>`, `<head>`, atau `<body>` berulang kali di file view halaman.
- Master layout terletak di `resources/views/components/layouts/app.blade.php` dan memuat font **Plus Jakarta Sans**, Tailwind CSS v4, top navbar, serta footer.

### B. Component-Driven Development
- **Zero Duplicate Markup**: Elemen UI yang digunakan berulang wajib dibuatkan Blade Component di `resources/views/components/`:
  - `components/layouts/navbar.blade.php` (Top Navigation Bar 2-Tier: Brand + Tabs, Search + User Capsule)
  - `components/layouts/footer.blade.php` (Institutional Footer Kemendikbud)
  - `components/ui/kpi-card.blade.php` (Stat / KPI Cards)
  - `components/ui/button.blade.php`, `components/ui/badge.blade.php`, `components/ui/modal.blade.php`, `components/ui/alert.blade.php`, `components/ui/dropdown.blade.php`
  - `components/gis/map-container.blade.php`, `components/gis/geofence-chip.blade.php`
- Setiap komponen wajib menggunakan `@props` untuk menerima atribut secara terstruktur dan aman.

### Contoh Pola View Wajib:
```html
<!-- resources/views/reports/index.blade.php -->
<x-layouts.app active="audit" title="Audit Fasilitas Sekolah 3T">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-xl font-extrabold text-[#14171F]">Verifikasi Lapangan</h1>
        <x-ui.button variant="primary" icon="plus">Tambah Laporan</x-ui.button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-ui.kpi-card 
            title="Laporan Masuk" 
            value="142" 
            unit="Tiket" 
            icon="building" 
            badgeText="Hari Ini" 
            badgeVariant="orange-pill" 
        />
    </div>
</x-layouts.app>
```

---

## 4. DESIGN TOKENS & VISUAL RULES (EDUINFRA TRACKER)
Semua halaman dan komponen UI wajib mematuhi standar desain agar tampilan konsisten:

| Token | Nilai Hex / Tailwind | Deskripsi & Penggunaan |
| :--- | :--- | :--- |
| **Primary Accent** | `#FF5C2A` / `#EA4A18` | Tombol CTA utama, badge urgensi krisis, indikator radar geofence |
| **Dark Control** | `#14171F` / `#1E2229` | Tab navigasi aktif, header teks utama, tombol sekunder solid |
| **Canvas Background** | `#F3F5F8` | Latar belakang seluruh halaman/body aplikasi |
| **Card Surface** | `#FFFFFF` | Latar kartu modul, modal, navbar melayang |
| **Border Color** | `border-slate-100` / `border-slate-200/80` | Garis pembatas halus antar modul |
| **Font Family** | `'Plus Jakarta Sans', sans-serif` | Tipografi utama seluruh aplikasi |
| **Radius Scale** | `rounded-full` (pills) untuk tombol & status; `rounded-2xl` / `rounded-3xl` untuk kartu modul | Sudut membulat tebal konsisten |

---

## 5. KATALOG SPESIFIKASI PRD & SKEMA RELASI ERD (15 ENTITAS DI 5 DOMAIN)

Acuan visual utama bersumber dari **`dokumen/ERD.png`** (*Gambar 7 Entity Relationship Diagram EduInfra Tracker*) dan dokumen PRD. Sistem memiliki 15 entitas CRUD fungsional yang terbagi dalam 5 domain:

### Matriks Relasi Foreign Key (Anchor dari `dokumen/ERD.png`):
| Tabel Sumber | Foreign Key | Terhubung ke Tabel (PK) | Catatan Relasi di ERD |
| :--- | :--- | :--- | :--- |
| **`schools`** | `FK region_id` | `regions.id` | Sekolah berada dalam wilayah administratif |
| **`vendors`** | `FK service_region_id` | `regions.id` | Wilayah cakupan operasional vendor rekanan |
| **`damage_reports`** | `FK school_id`<br>`FK reporter_id`<br>`FK category_id` | `schools.id`<br>`users.id`<br>`facility_categories.id` | Laporan kerusakan terikat pada sekolah, pelapor, dan taksonomi fasilitas |
| **`report_media`** | `FK report_id` | `damage_reports.id` | Foto bukti kerusakan (EXIF & validasi <250m) |
| **`audit_logs`** | `FK report_id`<br>`FK auditor_id` | `damage_reports.id`<br>`users.id` | Lembar verifikasi fisik oleh auditor resmi |
| **`repair_projects`** | `FK report_id`<br>`FK vendor_id` | `damage_reports.id`<br>`vendors.id` | Proyek perbaikan hasil verifikasi laporan |
| **`cost_estimates`** | `FK project_id` | `repair_projects.id` | Baris rincian anggaran biaya (RAB) |
| **`project_milestones`**| `FK project_id` | `repair_projects.id` | Target tahapan fisik dan verifikasi pengerjaan |
| **`aid_campaigns`** | `FK project_id` | `repair_projects.id` | Kampanye penggalangan bantuan CSR proyek |
| **`aid_contributions`**| `FK campaign_id`<br>`FK donor_id` | `aid_campaigns.id`<br>`users.id` | Donasi dana/material dari mitra CSR |
| **`disbursements`** | `FK project_id`<br>`FK authorized_by` | `repair_projects.id`<br>`users.id` | Pencairan dana termin disetujui dinas |
| **`dispute_tickets`** | `FK investigator_id`<br>`reference_id` | `users.id`<br>Polimorfik (`damage_reports` / dll) | Tiket eskalasi dugaan fraud/penyimpangan spesifikasi |

---

### Rincian 15 Entitas per Domain:

#### Domain 1: Master Wilayah & Kelembagaan Pendidikan
1. **`schools`**
   - Atribut: `PK id`, `FK region_id`, `npsn` (unique), `name`, `educational_level` (SD/SMP/SMA/SMK), `coordinates` (`Point` / lat, lng), `address`, `is_active`.
   - Relasi: belongsTo `regions`, hasMany `damage_reports`.
   - Operasi: C: Registrasi sekolah; R: Detail & titik peta; U: Update kontak/akreditasi; D: Soft delete.
2. **`regions`**
   - Atribut: `PK id`, `code`, `name`, `level` (prov/kab/kec), `boundary_polygon` (`Polygon` GeoJSON), `is_3t_tier` (boolean prioritas 3T).
   - Relasi: hasMany `schools`, hasMany `vendors`.
   - Operasi: C: Import GeoJSON; R: Poligon wilayah; U: Perbarui prioritas 3T; D: Hapus wilayah.
3. **`facility_categories`**
   - Atribut: `PK id`, `name`, `sub_category`, `urgency_weight` (1–5), `default_unit`, `is_enabled`.
   - Relasi: hasMany `damage_reports`.
   - Operasi: C: Tambah kategori; R: Taksonomi fasilitas; U: Update bobot urgensi; D: Nonaktifkan kategori.

#### Domain 2: Pelaporan Kerusakan & Verifikasi Lapangan
4. **`damage_reports`**
   - Atribut: `PK id`, `ticket_num` (unique auto-gen), `FK school_id`, `FK reporter_id`, `FK category_id`, `severity_level` (Ringan/Sedang/Berat/Kritis), `status` (Pending/Verified/Rejected/In_Repair/Resolved).
   - Relasi: belongsTo `schools`, belongsTo `users` (reporter), belongsTo `facility_categories`, hasMany `report_media`, hasMany `audit_logs`, hasOne `repair_projects`.
   - Operasi: C: Submit laporan; R: Detail laporan publik; U: Koreksi deskripsi; D: Batalkan tiket draft.
5. **`report_media`**
   - Atribut: `PK id`, `FK report_id`, `media_url`, `exif_lat`, `exif_lng`, `captured_at`, `is_verified_location` (boolean).
   - Relasi: belongsTo `damage_reports`.
   - **Aturan Khusus**: Deviasi koordinat foto terhadap koordinat sekolah wajib `< 250 meter`. Jika deviasi > 250m, status otomatis *flagged*. Identitas seri perangkat wajib di-strip.
6. **`audit_logs`**
   - Atribut: `PK id`, `FK report_id`, `FK auditor_id`, `inspection_date`, `physical_score` (skala 0.0 - 5.0), `audit_status` (Approved/Needs_Review/Rejected), `notes`.
   - Relasi: belongsTo `damage_reports`, belongsTo `users` (auditor).
   - Operasi: C: Input hasil inspeksi; R: Lembar verifikasi resmi; U: Koreksi catatan; D: Anulir audit keliru.

#### Domain 3: Manajemen Renovasi, RAB & Rekanan Vendor
7. **`repair_projects`**
   - Atribut: `PK id`, `project_code` (unique), `FK report_id`, `FK vendor_id`, `target_start`, `target_end`, `progress_pct` (0 - 100).
   - Relasi: belongsTo `damage_reports`, belongsTo `vendors`, hasMany `cost_estimates`, hasMany `project_milestones`, hasOne `aid_campaigns`, hasMany `disbursements`.
   - Operasi: C: Buka proyek renovasi; R: Timeline pengerjaan; U: Perbarui progres fisik; D: Batalkan proyek.
8. **`cost_estimates` (RAB)**
   - Atribut: `PK id`, `FK project_id`, `item_name`, `quantity`, `unit_price`, `total_price`, `approval_status` (Draft/Approved/Revised).
   - Relasi: belongsTo `repair_projects`.
   - Operasi: C: Susun baris anggaran RAB; R: Rekapitulasi biaya; U: Penyesuaian harga satuan; D: Hapus item.
9. **`project_milestones`**
   - Atribut: `PK id`, `FK project_id`, `title`, `due_date`, `completed_at`, `proof_media_url`, `is_approved`.
   - Relasi: belongsTo `repair_projects`.
   - Operasi: C: Tambah tahapan proyek; R: Tracking target berkala; U: Verifikasi tahapan selesai; D: Hapus target.
10. **`vendors`**
    - Atribut: `PK id`, `company_name`, `license_num`, `contact_phone`, `FK service_region_id`, `rating` (0.0 - 5.0), `is_active`.
    - Relasi: belongsTo `regions`, hasMany `repair_projects`.
    - Operasi: C: Registrasi kontraktor; R: Direktori rekanan; U: Update kontak/dokumen; D: Blacklist vendor.

#### Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran
11. **`aid_campaigns`**
    - Atribut: `PK id`, `FK project_id`, `title`, `target_amount`, `collected_amount`, `deadline`, `status` (Open/Funded/Closed).
    - Relasi: belongsTo `repair_projects`, hasMany `aid_contributions`.
    - Operasi: C: Buka program CSR; R: Katalog kampanye; U: Perpanjang batas waktu; D: Tutup kampanye.
12. **`aid_contributions`**
    - Atribut: `PK id`, `FK campaign_id`, `FK donor_id`, `amount_or_goods`, `payment_proof`, `status` (Pending/Verified/Cancelled).
    - Relasi: belongsTo `aid_campaigns`, belongsTo `users` (donor).
    - Operasi: C: Submit donasi; R: Riwayat kontribusi; U: Verifikasi serah terima; D: Batalkan donasi kadaluarsa.
13. **`disbursements`**
    - Atribut: `PK id`, `FK project_id`, `stage_num`, `amount`, `receipt_file`, `disbursed_at`, `FK authorized_by`.
    - Relasi: belongsTo `repair_projects`, belongsTo `users` (authorizer).
    - Operasi: C: Catat termin pencairan ke vendor; R: Laporan mutasi; U: Lampirkan faktur susulan; D: Anulir pencairan salah.

#### Domain 5: Tata Kelola Akun, Keamanan & Eskalasi Pengaduan
14. **`users` (Matriks RBAC 4 Persona)**
    - Atribut: `PK id`, `name`, `email`, `password_hash`, `phone`, `role_enum`, `organization`, `is_verified`.
    - **Matriks Wewenang Persona**:
      - `pelapor_guru`: Laporan indikasi kerusakan awal di sekolahnya (`Create/Read Damage Report`, `Upload Media`, `Read School`).
      - `auditor_relawan`: Inspeksi visitasi fisik dan mengukur kelayakan (`Create/Update Audit Log`, `Draft RAB`).
      - `mitra_csr`: Menyalurkan bantuan dana/material CSR (`Create/Update Aid Contribution`, `Read Public Dashboard`).
      - `admin_dinas`: Mengelola master data, validasi RAB proyek, otorisasi pencairan dana (`Full CRUD Region/Category`, `Project Approval`, `Disbursement`).
15. **`dispute_tickets` (Anti-Fraud)**
    - Atribut: `PK id`, `reference_type`, `reference_id`, `claim_detail`, `FK investigator_id`, `verdict`, `status` (Open/Under_Investigation/Resolved/Dismissed).
    - Relasi: polimorfik / reference, belongsTo `users` (investigator).
    - Operasi: C: Laporkan dugaan fraud/korupsi/markup; R: Tiket investigasi; U: Catat tindak lanjut; D: Tutup tiket selesai.

---

## 6. PROTOKOL GIT & COMMIT (STRICT USER-CONTROLLED)
1. ⛔ **DILARANG KERAS** menjalankan `git commit` secara otomatis di latar belakang dalam keadaan apa pun!
2. Agen hanya boleh menyajikan rekomendasi perintah git yang terstruktur untuk dieksekusi secara manual oleh pengguna di terminal:
   ```bash
   git add <daftar-file-yang-diubah>
   git commit -m "<tipe>(<lingkup>): <pesan-ringkas>"
   ```
3. Jangan pernah melakukan `git push` atau `git merge` kecuali diminta secara eksplisit oleh pengguna.
