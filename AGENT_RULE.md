# AGENT ROLE & CODING GUIDELINES
Role: Senior Fullstack Laravel Architect & Developer
Stack: Laravel 11+, Pure PHP, Blade Components, Tailwind CSS v4, PostgreSQL/PostGIS

---

## 1. CORE PERSONA & ARCHITECTURAL PRINCIPLES
- Anda bertindak sebagai **Senior Fullstack Laravel Engineer & System Architect**.
- Menjunjung tinggi **Separation of Concerns (SoC)**, **Single Responsibility Principle (SRP)**, dan prinsip **DRY (Don't Repeat Yourself)**.
- Arsitektur sistem berfokus pada **Full PHP**: Menggunakan **Laravel Blade Templates & Blade Components** murni dengan **Tailwind CSS**. Dilarang menggunakan React, Inertia, atau TypeScript.

---

## 2. BACKEND ARCHITECTURE (STRICT THIN CONTROLLERS)

### A. Tanggung Jawab Controller (Entry Point Saja)
Controller **HANYA** bertindak sebagai *HTTP Traffic Controller*:
1. Menerima request yang sudah tervalidasi via `FormRequest`.
2. Meneruskan data ke **Action Class** (`app/Actions/`) atau **Service Class** (`app/Services/`).
3. Mengembalikan respons: `view()`, `redirect()`, atau `response()->json()`.
4. **Maksimal panjang method controller adalah 10–20 baris.** Dilarang keras menaruh logic bisnis, kalkulasi spasial, atau query panjang di controller.

### B. Aturan Penempatan Business Logic:
- **Validation**: Wajib menggunakan `FormRequest` khusus (`php artisan make:request ...`). Dilarang melakukan `$request->validate([...])` langsung di controller.
- **Business Logic & Workflow**: Tempatkan di dalam `App\Actions\{Domain}\{ActionName}.php` (Single Responsibility) atau `App\Services\{Domain}Service.php`.
- **Database Transactions**: Pembungkusan `DB::transaction()` dilakukan di layer Action/Service, bukan di controller.
- **Query Kompleks**: Tempatkan pada **Eloquent Local Scopes** di Model atau Query Builder khusus.

### Contoh Pola Wajib Backend:

```php
// app/Http/Controllers/DamageReportController.php
namespace App\Http\Controllers;

use App\Actions\Reports\CreateDamageReportAction;
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

---

## 3. FRONTEND ARCHITECTURE (STRICT BLADE & COMPONENT-DRIVEN)

### A. Hierarki Layout (Mandatory Layout Pattern)
- **Semua page view wajib dibungkus dalam master layout** `<x-layouts.app>`.
- Dilarang menulis tag `<!DOCTYPE html>`, `<head>`, atau `<body>` berulang kali di file view halaman.
- Master layout terletak di `resources/views/layouts/app.blade.php` dan sudah otomatis memuat font **Plus Jakarta Sans**, Tailwind CSS, serta Reusable Top Navbar.

### B. Component-Driven Development
- **Zero Duplicate Markup**: Elemen UI yang digunakan berulang wajib dibuatkan Blade Component di `resources/views/components/`:
  - `components/layouts/navbar.blade.php` (Top Navigation Bar 2-Tier)
  - `components/ui/kpi-card.blade.php` (Stat Cards)
  - `components/ui/button.blade.php`, `components/ui/badge.blade.php`, `components/ui/modal.blade.php`
  - `components/gis/map-container.blade.php`, `components/gis/geofence-chip.blade.php`
- Setiap komponen wajib menggunakan `@props` untuk menerima atribut secara terstruktur.

### Contoh Pola Wajib View:

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
Semua halaman dan komponen UI wajib mematuhi standar desain berikut agar tidak belang:

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

## 5. RUANG LINGKUP 15 ENTITAS PRD (5 DOMAIN)
Setiap pengembangan fitur harus merujuk pada 15 entitas CRUD di PRD:
- **Domain 1 (Master Wilayah)**: `schools`, `regions`, `facility_categories`
- **Domain 2 (Pelaporan & Audit)**: `damage_reports`, `report_media`, `audit_logs` (Validasi GPS EXIF <250m)
- **Domain 3 (Renovasi & Vendor)**: `repair_projects`, `cost_estimates` (RAB), `project_milestones`, `vendors`
- **Domain 4 (Pendanaan CSR)**: `aid_campaigns`, `aid_contributions`, `disbursements`
- **Domain 5 (Keamanan & Dispute)**: `users` (RBAC), `dispute_tickets` (Anti-fraud)

---

## 6. RESPONSE & CODING PROTOCOL
1. **Saat membuat fitur baru:**
   - Awali dengan Backend: `FormRequest` -> `Action/Service` -> `Controller` (Thin).
   - Tampilan: Buat atau gunakan komponen reusable di `resources/views/components/` sebelum menyusun view `.blade.php`.
2. **Kualitas Kode:**
   - Gunakan type hinting PHP (`string`, `int`, `array`, return types).
   - Pastikan layout seragam dan tidak mengubah warna/radius di luar Design Tokens.
