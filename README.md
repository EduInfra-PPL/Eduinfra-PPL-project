# 🏫 EduInfra Tracker (EduBuild)
> **Platform Crowdsourcing Audit Fasilitas Sekolah & Matching CSR Wilayah 3T Berbasis GIS Berakurasi Tinggi**  
> *Mendukung Tujuan Pembangunan Berkelanjutan (SDG 4: Quality Education)*

---

## 📌 Ringkasan Proyek
**EduInfra Tracker** adalah platform terintegrasi yang dirancang untuk memetakan kesenjangan sarana dan prasarana fisik pendidikan (ruang kelas rusak berat, minimnya sanitasi terpisah, dan kelangkaan laboratorium) secara transparan dan akuntabel di wilayah 3T (Tertinggal, Terdepan, dan Terluar). 

Sistem ini menggabungkan mekanisme pelaporan publik berakurasi tinggi dengan verifikasi lapangan resmi oleh auditor, visualisasi geospasial interaktif (GIS multi-layer), serta penyaluran bantuan CSR terukur dari mitra BUMN/swasta hingga serah terima fasilitas.

---

## 🚨 Urgensi
1. **Kesenjangan Sarana Fisik di Daerah 3T**: Ribuan sekolah di daerah pelosok mengalami kerusakan struktural berat tanpa terdata secara tepat waktu, menghambat mutu pembelajaran dan membahayakan keselamatan murid.
2. **Tingginya Risiko Pemalsuan Data (Fraud)**: Kerap terjadi laporan fiktif atau penggunaan foto lama yang tidak sesuai kondisi lapangan saat pengajuan bantuan fisik.
3. **Penyaluran CSR yang Tidak Tepat Sasaran**: Mitra donatur dan perusahaan CSR kerap kesulitan memverifikasi sekolah mana yang paling mendesak membutuhkan intervensi akibat ketiadaan data spasial dan estimasi Rencana Anggaran Biaya (RAB) yang transparan.
4. **Keterbatasan Validasi Lapangan**: Minimnya integrasi data antara pelaporan awal masyarakat dengan bukti visitasi fisik resmi pengawas/auditor pendidikan.

---

## 🎯 Tujuan Platform
* **Memetakan Kesenjangan Fasilitas secara Real-Time**: Menyediakan basis data geospasial terpusat untuk kondisi fisik sekolah di seluruh Indonesia, khususnya wilayah 3T.
* **Mencegah Fraud dengan Validasi Spasial Ketat**: Mengimplementasikan ekstraksi metadata biner EXIF GPS (`GPSLatitude`, `GPSLongitude`, `DateTimeOriginal`) dan validasi *geofencing* radius **< 250 meter** terhadap titik master sekolah.
* **Transparansi Audit Visitasi**: Memfasilitasi auditor resmi untuk menginput lembar audit fisik terstandar dengan bobot keparahan terkalibrasi.
* **Akselerasi Bantuan Melalui Matching CSR**: Mempertemukan proyek renovasi sekolah prioritas darurat dengan donatur/mitra CSR melalui katalog kampanye dan pencatatan termin anggaran yang akuntabel.
* **Visualisasi Geospasial Cerdas (GIS)**: Menghadirkan visualisasi *choropleth map* wilayah 3T, *dynamic marker clustering*, dan pelapisan batas GeoJSON administrasi menggunakan MapTiler SDK dan PostGIS.

---

## 👥 Penerima Manfaat
1. **Siswa & Tenaga Pendidik di Wilayah 3T**: Memperoleh fasilitas belajar yang aman, nyaman, dan layak (ruang kelas kokoh, toilet sanitasi higienis, dan laboratorium).
2. **Pelapor Publik & Komunitas Sekolah**: Memiliki kanal pelaporan kerusakan fasilitas yang cepat, transparan, dan dapat dipantau perkembangannya.
3. **Auditor & Relawan Lapangan (Kemendikbud/Dinas)**: Memiliki instrumen verifikasi visitasi fisik yang terstandarisasi dengan proteksi data lokasi anti-manipulasi.
4. **Mitra CSR, Donatur, BUMN & Swasta**: Mendapatkan kepastian penyaluran bantuan yang tepat sasaran dengan rincian RAB, linimasa pengerjaan vendor, dan bukti milestone pengerjaan nyata.
5. **Dinas Pendidikan & Pemerintah Daerah**: Memperoleh alat bantu pengambilan keputusan strategis (*decision support*) dalam mengalokasikan anggaran renovasi sarpras secara adil.

---

## 👨‍💻 Tim Pengembang (Kelompok A-C - PPL)

| No | NIM / Nama | Peran | Modul |
|:--:|:---|:---|:---|
| 1 | 102022580031 / **Abdullah Amaaniy** | Full-stack Developer | A. Master Wilayah dan Kelembagaan Pendidikan |
| 2 | 102022580027 / **Dimas Rizky Prayoga** | Scrum Master dan Full-stack Developer | B. Pelaporan Kerusakan dan Verifikasi Lapangan |
| 3 | 102022580039 / **Fakhira Nur Aini** | Full-stack Developer | C. Manajemen Renovasi, RAB dan Rekanan Vendor |
| 4 | 102022580018 / **I Made Lanang Ary Wisana** | Product Owner dan Full-stack Developer | D. Pendanaan Bantuan, CSR dan Pencairan Anggaran |
| 5 | 102022580030 / **Nazwa Abdullah Nasution** | Full-stack Developer | E. Tata Kelola Akun, Keamanan dan Eskalasi Pengaduan |

---

## 🏗️ Ruang Lingkup Arsitektur (5 Domain & 15 Entitas CRUD)

Berdasarkan Dokumen Spesifikasi Teknis & PRD:
1. **Domain 1: Master Wilayah & Kelembagaan Pendidikan**: `schools`, `regions`, `facility_categories`
2. **Domain 2: Pelaporan Kerusakan & Verifikasi Lapangan**: `damage_reports`, `report_media` (EXIF Geofence <250m), `audit_logs`
3. **Domain 3: Manajemen Renovasi, RAB & Rekanan Vendor**: `repair_projects`, `cost_estimates` (RAB), `project_milestones`, `vendors`
4. **Domain 4: Pendanaan Bantuan, CSR & Pencairan Anggaran**: `aid_campaigns`, `aid_contributions`, `disbursements`
5. **Domain 5: Tata Kelola Akun, Keamanan & Eskalasi Pengaduan**: `users` (4 Persona RBAC: Pelapor, Auditor, Mitra CSR, Admin Dinas), `dispute_tickets`

---

## 💻 Tech Stack
* **Backend**: Laravel 11 (Pure PHP, Thin Controllers, Action/Service-Driven Pattern)
* **Frontend**: Laravel Blade Components, Tailwind CSS v4, Plus Jakarta Sans Font, Alpine.js
* **Pemetaan Spasial (GIS)**: MapTiler SDK (`@maptiler/sdk`), OpenStreetMap Layer, GeoJSON Multi-Layer
* **Database & Spasial**: PostgreSQL 16 + PostGIS 3.4 (dengan GiST Indexing & spatial queries) / Docker Container
* **Build Tooling**: Vite 6, Composer, PHP 8.2+

---

## 🚀 Panduan Menjalankan Aplikasi di Lokal

### 1. Kloning & Dependensi
```bash
# Clone repository
git clone https://github.com/EduInfra-PPL/Eduinfra-PPL-project.git
cd Eduinfra-PPL-project

# Install dependensi PHP & Node
composer install
npm install
```

### 2. Konfigurasi Lingkungan (.env)
Salin `.env.example` menjadi `.env` dan masukkan API Key MapTiler:
```env
APP_NAME="EduInfra Tracker"
APP_ENV=local
APP_KEY=...

# Database PostgreSQL (Docker)
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=eduinfra
DB_USERNAME=....
DB_PASSWORD=....

# MapTiler API Key (https://cloud.maptiler.com)
VITE_MAPTILER_API_KEY=YOUR_MAPTILER_API_KEY_HERE
```

### 3. Jalankan Database & Migrasi (Docker)
```bash
# Nyalakan PostgreSQL + PostGIS via Docker
docker compose up -d

# Jalankan migrasi dan seeder awal
php artisan migrate
php artisan db:seed --class=Domain1Seeder
```

### 4. Jalankan Server Pengembangan
Buka 2 terminal terpisah:
```bash
# Terminal 1: Laravel Backend
php artisan serve

```
