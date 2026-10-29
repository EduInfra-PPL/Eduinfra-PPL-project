<x-layouts.app active="audit" title="Input Laporan Baru - EduInfra Tracker">

    <!-- BREADCRUMB -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-3 pt-2">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Beranda</a>
        <span>&rsaquo;</span>
        <a href="{{ route('reports.index') }}" class="hover:text-slate-700 transition-colors">Pelaporan Fasilitas Sekolah</a>
        <span>&rsaquo;</span>
        <span class="text-slate-800 font-semibold">Input Laporan Baru</span>
    </nav>

    <!-- PAGE TITLE & PROTOCOL BADGE -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#14171F] tracking-tight">Laporan Kerusakan Fasilitas Sekolah</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">
                Audit fasilitas 3T terstandarisasi dengan verifikasi foto geolokasi dan proteksi anti-fraud EXIF.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200/80 rounded-full shadow-xs text-xs font-semibold text-slate-700 shrink-0">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <span>Protokol EXIF v4.2</span>
            <span class="text-slate-300">|</span>
            <span class="text-slate-500 font-medium">Radius Maks 250m</span>
        </div>
    </div>

    <!-- WIZARD STEPPER (3 STEPS) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
        
        <!-- Step 1: Active -->
        <div class="flex items-center gap-3 p-3 bg-[#14171F] text-white rounded-full shadow-xs">
            <span class="w-7 h-7 rounded-full bg-[#FF5C2A] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                1
            </span>
            <div class="truncate">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-300">Langkah Aktif</span>
                <span class="block text-xs font-bold text-white truncate">1. Identitas &amp; Kategori</span>
            </div>
        </div>

        <!-- Step 2: Upcoming -->
        <div class="flex items-center gap-3 p-3 bg-white border border-slate-200/80 text-slate-400 rounded-full">
            <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0">
                2
            </span>
            <div class="truncate">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 2</span>
                <span class="block text-xs font-semibold text-slate-600 truncate">2. Unggah Bukti &amp; Geotagging</span>
            </div>
        </div>

        <!-- Step 3: Upcoming -->
        <div class="flex items-center gap-3 p-3 bg-white border border-slate-200/80 text-slate-400 rounded-full">
            <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0">
                3
            </span>
            <div class="truncate">
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 3</span>
                <span class="block text-xs font-semibold text-slate-600 truncate">3. Verifikasi &amp; Finalisasi</span>
            </div>
        </div>

    </div>

    <!-- MAIN FORM GRID (2 COLUMNS: LEFT 7 COLS, RIGHT 5 COLS) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT COLUMN: IDENTIFIKASI SATUAN PENDIDIKAN & FASILITAS (7 COLS) -->
        <div class="lg:col-span-7 bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-xs border border-slate-100 flex flex-col gap-5">
            
            <!-- Section Header -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-[#14171F]">Identifikasi Satuan Pendidikan &amp; Fasilitas</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Identifikasi satuan pendidikan dan tingkat keparahan fisik.</p>
                </div>
                <div class="w-9 h-9 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FF5C2A]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21"/></svg>
                </div>
            </div>

            <!-- Field 1: Pencarian NPSN / Nama Sekolah -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-slate-700">Pencarian NPSN / Nama Sekolah <span class="text-rose-500">*</span></label>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        Terhubung Dapodik
                    </span>
                </div>
                <div class="relative flex items-center">
                    <div class="absolute left-3.5 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/></svg>
                    </div>
                    <input 
                        type="text" 
                        value="SDN 02 Sambas Terluar" 
                        readonly
                        class="w-full pl-10 pr-24 py-2.5 text-xs font-bold text-slate-900 bg-[#F8FAFC] border border-slate-200 rounded-xl focus:outline-none"
                    />
                    <button type="button" class="absolute right-2 px-3 py-1 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-lg transition-colors shadow-xs">
                        Ubah Data
                    </button>
                </div>

                <!-- Selected School Info Card -->
                <div class="mt-3 p-4 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-xs shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                            </div>
                            <div>
                                <h3 class="text-xs font-extrabold text-[#14171F]">10802199 — SDN 02 Sambas Terluar</h3>
                                <p class="text-[11px] text-slate-500 font-medium">Desa Temajuk, Kec. Paloh, Kab. Sambas, Kalimantan Barat</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200/60 text-[10px] font-semibold">
                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700">Jenjang: SD</span>
                        <span class="px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-1 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Wilayah 3T Tier-1 Perbatasan RI-Malaysia
                        </span>
                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600 flex items-center gap-1">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                            142 Siswa Aktif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Field 2: Kategori Komponen Fasilitas Terdampak -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-slate-700">Kategori Komponen Fasilitas Terdampak <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] text-slate-400 font-medium">Pilih salah satu komponen utama</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    
                    <!-- Option 1: Selected RKB -->
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border-2 border-[#FF5C2A] bg-[#FFF8F5] cursor-pointer transition-all shadow-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#FFF0EB] text-[#FF5C2A] flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <span class="block text-xs font-extrabold text-[#14171F]">Ruang Kelas Belajar (RKB)</span>
                                <span class="block text-[11px] font-semibold text-[#FF5C2A]">3 Unit RKB Ambruk</span>
                            </div>
                        </div>
                        <div class="w-5 h-5 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center shadow-xs">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </div>
                        <input type="radio" name="facility_category" value="rkb" checked class="sr-only">
                    </label>

                    <!-- Option 2: Sanitasi Terpisah & MCK -->
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border border-slate-200/80 bg-white hover:bg-slate-50 cursor-pointer transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Sanitasi Terpisah &amp; MCK</span>
                                <span class="block text-[11px] text-slate-400 font-medium">Toilet Siswa &amp; Air Bersih</span>
                            </div>
                        </div>
                        <div class="w-4 h-4 rounded-full border-2 border-slate-300"></div>
                        <input type="radio" name="facility_category" value="mck" class="sr-only">
                    </label>

                    <!-- Option 3: Laboratorium IPA / Komputer -->
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border border-slate-200/80 bg-white hover:bg-slate-50 cursor-pointer transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Laboratorium IPA / Komputer</span>
                                <span class="block text-[11px] text-slate-400 font-medium">Ruang Praktik Digital</span>
                            </div>
                        </div>
                        <div class="w-4 h-4 rounded-full border-2 border-slate-300"></div>
                        <input type="radio" name="facility_category" value="lab" class="sr-only">
                    </label>

                    <!-- Option 4: Perpustakaan & Arsip -->
                    <label class="relative flex items-center justify-between p-3.5 rounded-2xl border border-slate-200/80 bg-white hover:bg-slate-50 cursor-pointer transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Perpustakaan &amp; Arsip</span>
                                <span class="block text-[11px] text-slate-400 font-medium">Ruang Sumber Belajar</span>
                            </div>
                        </div>
                        <div class="w-4 h-4 rounded-full border-2 border-slate-300"></div>
                        <input type="radio" name="facility_category" value="perpus" class="sr-only">
                    </label>

                </div>
            </div>

            <!-- Field 3: Tingkat Urgensi Kerusakan (Weight Scale 1-5) -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-bold text-slate-700">Tingkat Urgensi Kerusakan (Weight Scale) <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] font-extrabold text-rose-600">! Skala 5: Kerusakan Kritis</span>
                </div>

                <!-- 5 Pill Buttons -->
                <div class="grid grid-cols-5 gap-2 text-center text-xs">
                    <button type="button" class="py-2.5 px-2 font-medium text-slate-600 bg-[#F8FAFC] hover:bg-slate-100 rounded-xl border border-slate-200/80 transition-colors">
                        <span class="block font-bold">1</span>
                        <span class="text-[10px] text-slate-400">Ringan</span>
                    </button>
                    <button type="button" class="py-2.5 px-2 font-medium text-slate-600 bg-[#F8FAFC] hover:bg-slate-100 rounded-xl border border-slate-200/80 transition-colors">
                        <span class="block font-bold">2</span>
                        <span class="text-[10px] text-slate-400">Sedang</span>
                    </button>
                    <button type="button" class="py-2.5 px-2 font-medium text-slate-600 bg-[#F8FAFC] hover:bg-slate-100 rounded-xl border border-slate-200/80 transition-colors">
                        <span class="block font-bold">3</span>
                        <span class="text-[10px] text-slate-400">Waspada</span>
                    </button>
                    <button type="button" class="py-2.5 px-2 font-medium text-slate-600 bg-[#F8FAFC] hover:bg-slate-100 rounded-xl border border-slate-200/80 transition-colors">
                        <span class="block font-bold">4</span>
                        <span class="text-[10px] text-slate-400">Parah</span>
                    </button>
                    <button type="button" class="py-2.5 px-2 font-bold text-white bg-[#FF5C2A] rounded-xl shadow-xs border border-[#EA4A18]">
                        <span class="block font-extrabold">5</span>
                        <span class="text-[10px] text-white/90">Kritis</span>
                    </button>
                </div>

                <!-- Alert Box Konsekuensi Bobot 5 -->
                <div class="mt-3 p-3.5 bg-[#FFF8F5] border border-[#FFD9CC] rounded-xl flex items-start gap-3">
                    <div class="text-[#FF5C2A] shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-[#FF5C2A]">Bobot 5: Kerusakan Kritis / Ancaman Runtuh</h4>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                            Struktur berisiko mengancam keselamatan siswa. Memerlukan intervensi tanggap darurat dan prioritas pencocokan CSR 14 hari kerja.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Field 4: Deskripsi Detail Kerusakan Fisik -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="text-xs font-bold text-slate-700">Deskripsi Detail Kerusakan Fisik <span class="text-rose-500">*</span></label>
                    <span class="text-[11px] font-mono text-slate-400">182 / 1000 Karakter</span>
                </div>
                <textarea 
                    rows="4" 
                    placeholder="Jelaskan secara detail bagian bangunan yang rusak, faktor pemicu (cuaca/usia), dan dampaknya terhadap kegiatan belajar mengajar..."
                    class="w-full p-3.5 text-xs text-slate-800 bg-[#F8FAFC] border border-slate-200 rounded-xl focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-all leading-relaxed placeholder-slate-400"
                >Kerusakan parah pada atap 3 Ruang Kelas Belajar (RKB). Kuda-kuda kayu lapuk pasca cuaca ekstrem, plafon runtuh, air hujan masuk langsung saat KBM berlangsung.</textarea>
                <p class="text-[11px] text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01"/></svg>
                    Uraian ini akan digunakan tim kurator Kemendikbud &amp; calon mitra CSR untuk kalkulasi RAB darurat.
                </p>
            </div>

        </div>

        <!-- RIGHT COLUMN: INTEGRITAS SPASIAL & TITIK MASTER SEKOLAH (5 COLS) -->
        <div class="lg:col-span-5 flex flex-col gap-4">
            
            <div class="bg-white rounded-2xl lg:rounded-3xl p-5 shadow-xs border border-slate-100 flex flex-col gap-4">
                
                <!-- Card Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Integritas Spasial &amp; Titik Master Sekolah</span>
                        <h3 class="text-sm font-extrabold text-[#14171F] mt-0.5">Titik Master GPS Sekolah</h3>
                        <p class="text-[11px] text-slate-500">Basis data koordinat resmi Kemendikbud WGS84.</p>
                    </div>
                    <span class="px-2.5 py-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200/80 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        WGS84 Terkunci
                    </span>
                </div>

                <!-- Coordinates Value Box -->
                <div class="grid grid-cols-2 gap-3 p-3 bg-[#F8FAFC] rounded-2xl border border-slate-200/70">
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Latitude (Garis Lintang)</span>
                        <span class="text-xs font-mono font-bold text-slate-900 mt-0.5 block">1.1892&deg; N</span>
                    </div>
                    <div>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase">Longitude (Garis Bujur)</span>
                        <span class="text-xs font-mono font-bold text-slate-900 mt-0.5 block">109.3218&deg; E</span>
                    </div>
                </div>

                <!-- Map Visualizer Mock Radar Widget -->
                <div class="relative w-full h-44 rounded-2xl bg-[#EAF0F6] overflow-hidden border border-slate-200 flex flex-col justify-between p-3 select-none">
                    
                    <!-- Top Info overlay -->
                    <div class="flex items-center justify-between z-10 text-[10px]">
                        <span class="px-2 py-0.5 rounded-md font-bold bg-[#14171F] text-white">Desa Temajuk &bull; Zona Pesisir Sambas</span>
                        <span class="px-2 py-0.5 rounded-md font-bold bg-white/90 text-slate-700 shadow-xs">Akurasi GNSS Master: 99.4%</span>
                    </div>

                    <!-- Radar Buffer Graphics (Mock visual) -->
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <!-- 250m Buffer Circle Radar -->
                        <div class="relative w-36 h-36 rounded-full border-2 border-dashed border-emerald-500/60 bg-emerald-500/10 flex items-center justify-center">
                            <span class="absolute -top-3 text-[9px] font-bold text-emerald-800 bg-white/90 px-1.5 py-0.2 rounded border border-emerald-200 shadow-xs">
                                Radius 250m Buffer
                            </span>
                            
                            <!-- Dashed Border RI - Malaysia line hint -->
                            <div class="absolute w-full h-[1px] bg-red-400/60 border-t border-dashed border-red-500"></div>
                            <span class="absolute top-4 right-1 text-[8px] font-bold text-red-600 uppercase tracking-widest">Batas Negara RI - Malaysia</span>

                            <!-- School Center Pin -->
                            <div class="w-8 h-8 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center shadow-lg border-2 border-white z-10">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A48.243 48.243 0 0012 9.75c-2.551 0-5.056.2-7.5.583V21"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Bottom Tag Bar -->
                    <div class="flex items-center justify-between z-10 text-[10px]">
                        <span class="px-2 py-0.5 rounded-full font-bold bg-[#14171F] text-white flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Radius Buffer Geofence 250m Siap Divalidasi di Langkah 2
                        </span>
                        <span class="px-2 py-0.5 rounded-md font-mono font-bold bg-white/90 text-slate-700">Alt: 18m dpl</span>
                    </div>

                </div>

                <!-- 3 Checkpoints Protocols -->
                <div class="flex flex-col gap-3 pt-2 text-xs">
                    
                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 text-xs">Wajib Geotagging GPS Kamera</span>
                            <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5">
                                Kamera perangkat wajib mengaktifkan sensor lokasi saat pemotretan fisik di Langkah 2 agar koordinat tertanam dalam EXIF.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 text-xs">Batas Toleransi Spasial 250 Meter</span>
                            <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5">
                                Foto yang diambil dengan deviasi &gt; 250 meter dari titik master sekolah otomatis ditandai peringatan (Flagged).
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5">
                        <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 text-xs">Proteksi Privasi (Privacy Stripping)</span>
                            <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5">
                                Nomor seri hardware dan IMEI perangkat dienkripsi dan dihapus sesuai ISO/IEC 27001 sebelum data disimpan ke cloud.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- Dark Banner Promo CSR -->
                <div class="p-4 bg-[#14171F] text-white rounded-2xl flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#FF5C2A] text-white flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-white">Jalur Prioritas CSR 3x Lebih Cepat</h4>
                        <p class="text-[11px] text-slate-300 leading-relaxed mt-0.5">
                            Laporan dengan metadata valid langsung dipublikasikan ke konsorsium donor CSR tanpa antrian verifikasi manual.
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- FORM BOTTOM ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6 pt-5 border-t border-slate-200/80">
        <button type="button" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-full transition-colors shadow-xs">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0z"/></svg>
            Simpan Draft Lokal
        </button>

        <a href="#langkah-2" 
           class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#FF5C2A] hover:bg-[#EA4A18] text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition-all group">
            <span>Lanjut ke Unggah Bukti (Langkah 2)</span>
            <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
        </a>
    </div>

</x-layouts.app>

