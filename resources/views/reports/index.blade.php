<x-layouts.app active="audit" title="Daftar Laporan Fasilitas Sekolah - EduInfra Tracker">

    <!-- BREADCRUMB -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-3 pt-2">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Beranda</a>
        <span>&rsaquo;</span>
        <span class="text-slate-800 font-semibold">Laporan Fasilitas Sekolah</span>
    </nav>

    <!-- PAGE HEADER & PRIMARY CTA BUTTON -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-extrabold text-[#14171F] tracking-tight">Daftar Laporan Fasilitas Sekolah</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">
                Pusat monitoring tiket kerusakan fisik, verifikasi lapangan 3T, dan status intervensi CSR.
            </p>
        </div>

        <a href="{{ route('reports.create') }}" 
           class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#FF5C2A] hover:bg-[#EA4A18] text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition-all group shrink-0">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            <span>Buat Laporan Kerusakan Baru</span>
            <svg class="w-3.5 h-3.5 text-white/80 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
            </svg>
        </a>
    </div>

    <!-- KPI STATS CARDS (4 COLUMNS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Card 1: Total Tiket Masuk -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-slate-500">Total Tiket Masuk</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200/60">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                    +12% bulan ini
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#14171F] tracking-tight">1,420</span>
                    <span class="text-sm font-semibold text-slate-500">Laporan</span>
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Agregat nasional & wilayah 3T</p>
            </div>
        </div>

        <!-- Card 2: Menunggu Visitasi -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-slate-500">Menunggu Visitasi</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-amber-700 bg-amber-50 rounded-full border border-amber-200/60">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/></svg>
                    SLA 3 Hari
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#14171F] tracking-tight">128</span>
                    <span class="text-sm font-semibold text-slate-500">Antrean</span>
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Menunggu verifikator lapangan</p>
            </div>
        </div>

        <!-- Card 3: Terverifikasi Lapangan -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-slate-500">Terverifikasi Lapangan</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200/60">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    Siap CSR
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#14171F] tracking-tight">842</span>
                    <span class="text-sm font-semibold text-slate-500">Sekolah</span>
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Integritas data geospasial 98.4%</p>
            </div>
        </div>

        <!-- Card 4: Deviasi Geofence EXIF (Urgent Alert Highlight) -->
        <div class="bg-[#FFF8F5] rounded-2xl p-5 shadow-xs border-2 border-[#FFD9CC] flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-[#FF5C2A]">Deviasi Geofence EXIF</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-bold text-white bg-[#FF5C2A] rounded-full shadow-xs">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18.75h.007v.008H12v-.008z"/></svg>
                    Perlu Review Spasial
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#FF5C2A] tracking-tight">14</span>
                    <span class="text-sm font-bold text-[#FF5C2A]">Tiket Flagged</span>
                </div>
                <p class="text-[11px] font-medium text-slate-500 mt-1">Offset radius &gt; 250m dari titik master</p>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER UTILITY CARD -->
    <div class="bg-white rounded-2xl lg:rounded-3xl p-4 lg:p-5 shadow-xs border border-slate-100 mb-6 flex flex-col gap-4">
        
        <!-- Search Bar & Action Buttons Row -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            
            <!-- Search Input Omni with Kbd shortcut -->
            <div class="relative flex-1">
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="Cari No. Tiket, NPSN, nama sekolah, atau kabupaten..." 
                    class="w-full pl-10 pr-12 py-2.5 text-xs font-medium text-slate-800 bg-[#F8FAFC] border border-slate-200/70 rounded-full focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-all placeholder-slate-400 shadow-xs"
                />
                <kbd class="absolute right-3.5 top-1/2 -translate-y-1/2 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded-md">⌘K</kbd>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-[#F8FAFC] hover:bg-slate-100 border border-slate-200/80 rounded-full transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                    Filter Wilayah 3T
                </button>
                <button type="button" class="inline-flex items-center gap-2 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-[#F8FAFC] hover:bg-slate-100 border border-slate-200/80 rounded-full transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Export Excel / CSV
                </button>
            </div>
        </div>

        <!-- Filter Segmented Pills Rows -->
        <div class="flex flex-col gap-2.5 pt-2 border-t border-slate-100 text-xs">
            
            <!-- Row 1: Status Filter -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-slate-400 font-semibold w-16 text-[11px]">Status:</span>
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" class="px-3 py-1 text-xs font-bold rounded-full bg-[#14171F] text-white shadow-xs">Semua Status</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">Pending Visitasi</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">Verified Lapangan</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">Dalam Renovasi</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">Flagged EXIF</button>
                </div>

                <div class="hidden lg:flex items-center gap-2 ml-auto">
                    <span class="text-slate-400 font-semibold text-[11px]">Jenjang:</span>
                    <button type="button" class="px-3 py-1 text-xs font-bold rounded-full bg-[#14171F] text-white shadow-xs">Semua Jenjang</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">SD</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">SMP</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">SMA/SMK</button>
                </div>
            </div>

            <!-- Row 2: Urgency Filter -->
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-slate-400 font-semibold w-16 text-[11px]">Urgensi:</span>
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" class="px-3 py-1 text-xs font-bold rounded-full bg-[#14171F] text-white shadow-xs">Semua Urgensi</button>
                    <button type="button" class="px-3 py-1 text-xs font-semibold rounded-full bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition-colors">Kritis (Level 5)</button>
                    <button type="button" class="px-3 py-1 text-xs font-semibold rounded-full bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 transition-colors">Sedang (Level 3–4)</button>
                    <button type="button" class="px-3 py-1 text-xs font-medium rounded-full bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 transition-colors">Ringan (Level 1–2)</button>
                </div>
            </div>

        </div>

    </div>

    <!-- MAIN DATA TABLE CARD -->
    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xs border border-slate-100 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-slate-100 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">No. Tiket</th>
                        <th class="py-3.5 px-4">Satuan Pendidikan & Wilayah</th>
                        <th class="py-3.5 px-4">Komponen Fasilitas</th>
                        <th class="py-3.5 px-4">Validasi EXIF</th>
                        <th class="py-3.5 px-4">Status Laporan</th>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    
                    <!-- ROW 1: SDN 02 SAMBAS TERLUAR (KRITIS) -->
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono text-slate-700 bg-slate-100 rounded-lg">
                                #REP-2026-0891
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-slate-900 leading-tight">SDN 02 Sambas Terluar</div>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                <span>NPSN: 10802199</span>
                                <span>&bull;</span>
                                <span>Kab. Sambas, Kalbar</span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 rounded">3T Tier-1</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-800">Ruang Kelas Belajar (RKB)</div>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-full">
                                <svg class="w-2.5 h-2.5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Level 5 – Kritis
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Valid &lt; 250m (42m)
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200/80 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                Pending Visitasi Lapangan
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">08 Okt 2026</div>
                            <div class="text-[11px] text-slate-400">09:14 WIB</div>
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-center">
                            <button type="button" class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-white bg-white hover:bg-[#14171F] border border-slate-300 rounded-full transition-colors shadow-xs">
                                Detail &amp; Verifikasi
                            </button>
                        </td>
                    </tr>

                    <!-- ROW 2: SMP SATAP 01 AGATS -->
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono text-slate-700 bg-slate-100 rounded-lg">
                                #REP-2026-0840
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-slate-900 leading-tight">SMP Satap 01 Agats</div>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                <span>NPSN: 60301412</span>
                                <span>&bull;</span>
                                <span>Kab. Asmat, Papua Selatan</span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 rounded">3T Tier-1</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-800">Pondasi &amp; MCK Siswa</div>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-full">
                                <svg class="w-2.5 h-2.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Level 4 – Rusak Berat
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Valid &lt; 250m (68m)
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Verified Auditor
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">05 Okt 2026</div>
                            <div class="text-[11px] text-slate-400">14:20 WIB</div>
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-center">
                            <button type="button" class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-white bg-white hover:bg-[#14171F] border border-slate-300 rounded-full transition-colors shadow-xs">
                                Lihat RAB &amp; CSR
                            </button>
                        </td>
                    </tr>

                    <!-- ROW 3: MIS AL-FALAH PANTAR (FLAGGED DEVIASI 310M) -->
                    <tr class="bg-rose-50/20 hover:bg-rose-50/40 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono text-rose-800 bg-rose-100 rounded-lg">
                                #REP-2026-0812
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-slate-900 leading-tight">MIS Al-Falah Pantar</div>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                <span>NPSN: 50308821</span>
                                <span>&bull;</span>
                                <span>Kab. Alor, NTT</span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 rounded">3T Tier-2</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-800">Plafon &amp; Sanitasi Bersih</div>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[10px] font-semibold text-slate-700 bg-slate-100 rounded-full">
                                <svg class="w-2.5 h-2.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><polyline points="12 6 12 12 16 14"/></svg>
                                Level 3 – Sedang
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-rose-700 bg-rose-50 border border-rose-300 rounded-full">
                                <svg class="w-3 h-3 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Flagged: Deviasi 310m
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-rose-800 bg-rose-50 border border-rose-200/80 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                                Investigasi Fraud
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">02 Okt 2026</div>
                            <div class="text-[11px] text-slate-400">11:05 WIB</div>
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-center">
                            <button type="button" class="px-3.5 py-1.5 text-xs font-bold text-rose-700 hover:text-white bg-rose-50 hover:bg-rose-600 border border-rose-300 rounded-full transition-colors shadow-xs">
                                Tinjau Deviasi
                            </button>
                        </td>
                    </tr>

                    <!-- ROW 4: SDN 14 MAHAKAM ULU -->
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono text-slate-700 bg-slate-100 rounded-lg">
                                #REP-2026-0798
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-slate-900 leading-tight">SDN 14 Mahakam Ulu</div>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                <span>NPSN: 30405118</span>
                                <span>&bull;</span>
                                <span>Kab. Mahakam Ulu, Kaltim</span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 rounded">3T Tier-1</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-800">Ruang Guru &amp; Laboratorium</div>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-full">
                                <svg class="w-2.5 h-2.5 text-amber-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                Level 4 – Rusak Berat
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Valid &lt; 250m (18m)
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-indigo-800 bg-indigo-50 border border-indigo-200/80 rounded-full">
                                <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                                Sponsorship Active
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">28 Sep 2026</div>
                            <div class="text-[11px] text-slate-400">10:45 WIB</div>
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-center">
                            <button type="button" class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-white bg-white hover:bg-[#14171F] border border-slate-300 rounded-full transition-colors shadow-xs">
                                Lihat Progres CSR
                            </button>
                        </td>
                    </tr>

                    <!-- ROW 5: SMKN 1 NUNUKAN PERBATASAN -->
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-4 sm:px-6">
                            <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono text-slate-700 bg-slate-100 rounded-lg">
                                #REP-2026-0775
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-slate-900 leading-tight">SMKN 1 Nunukan Perbatasan</div>
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                <span>NPSN: 40201994</span>
                                <span>&bull;</span>
                                <span>Kab. Nunukan, Kaltara</span>
                                <span>&bull;</span>
                                <span class="px-1.5 py-0.5 text-[10px] font-bold text-slate-700 bg-slate-100 rounded">3T Tier-1</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-semibold text-slate-800">Bengkel Praktik Vokasi</div>
                            <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 text-[10px] font-semibold text-sky-800 bg-sky-50 border border-sky-200 rounded-full">
                                <svg class="w-2.5 h-2.5 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.162 1.157-.107 1.668.175.76.42 1.242 1.207 1.242 2.083 0 .546-.184 1.066-.525 1.488"/></svg>
                                Level 2 – Ringan
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                Valid &lt; 250m (95m)
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Renovasi Selesai
                            </span>
                        </td>
                        <td class="py-4 px-4 whitespace-nowrap">
                            <div class="font-semibold text-slate-800">20 Sep 2026</div>
                            <div class="text-[11px] text-slate-400">16:10 WIB</div>
                        </td>
                        <td class="py-4 px-4 sm:px-6 text-center">
                            <button type="button" class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-white bg-white hover:bg-[#14171F] border border-slate-300 rounded-full transition-colors shadow-xs">
                                Arsip &amp; Laporan
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <!-- PAGINATION BAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 sm:px-6 bg-white border-t border-slate-100 text-xs">
            <span class="text-slate-500 font-medium">
                Menampilkan <span class="font-bold text-slate-800">1 – 5</span> dari <span class="font-bold text-slate-800">1,420</span> laporan fasilitas (Halaman 1)
            </span>

            <div class="flex items-center gap-1.5">
                <button type="button" class="px-3 py-1.5 text-xs font-medium text-slate-400 bg-slate-50 rounded-full cursor-not-allowed border border-slate-200/60" disabled>
                    &larr; Sebelumnya
                </button>
                <button type="button" class="w-8 h-8 flex items-center justify-center text-xs font-bold text-white bg-[#14171F] rounded-full shadow-xs">
                    1
                </button>
                <button type="button" class="w-8 h-8 flex items-center justify-center text-xs font-medium text-slate-700 hover:bg-slate-100 rounded-full transition-colors">
                    2
                </button>
                <button type="button" class="w-8 h-8 flex items-center justify-center text-xs font-medium text-slate-700 hover:bg-slate-100 rounded-full transition-colors">
                    3
                </button>
                <span class="px-1 text-slate-400 font-bold">&hellip;</span>
                <button type="button" class="w-8 h-8 flex items-center justify-center text-xs font-medium text-slate-700 hover:bg-slate-100 rounded-full transition-colors">
                    142
                </button>
                <button type="button" class="px-3 py-1.5 text-xs font-bold text-slate-700 hover:bg-slate-100 rounded-full border border-slate-200/80 transition-colors shadow-xs">
                    Selanjutnya &rarr;
                </button>
            </div>
        </div>

    </div>

</x-layouts.app>

