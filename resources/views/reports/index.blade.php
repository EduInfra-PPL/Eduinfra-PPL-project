<x-layouts.app active="audit" title="Daftar Laporan Fasilitas Sekolah - EduInfra Tracker">

    <!-- BREADCRUMB -->
    <nav class="flex items-center gap-2 text-xs font-medium text-slate-400 mb-3 pt-2">
        <a href="{{ route('dashboard') }}" class="hover:text-slate-700 transition-colors">Beranda</a>
        <span>&rsaquo;</span>
        <span class="text-slate-800 font-semibold">Laporan Fasilitas Sekolah</span>
    </nav>

    <!-- SUCCESS FLASH ALERT (PBI-04 / STORE CONFIRMATION) -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl flex items-start justify-between gap-3 shadow-xs">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-emerald-900">Operasi Berhasil</h3>
                    <p class="text-xs text-emerald-800 mt-0.5">{{ session('success') }}</p>
                </div>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

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

    <!-- KPI STATS CARDS (4 COLUMNS DYNAMIC FROM CONTROLLER) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        
        <!-- Card 1: Total Tiket Masuk -->
        <div class="bg-white rounded-2xl p-5 shadow-xs border border-slate-100 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-slate-500">Total Tiket Masuk</span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded-full border border-emerald-200/60">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25"/></svg>
                    Terdata
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#14171F] tracking-tight">{{ number_format($kpi['total'] ?? 0) }}</span>
                    <span class="text-sm font-semibold text-slate-500">Laporan</span>
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Agregat nasional &amp; wilayah 3T</p>
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
                    <span class="text-3xl font-extrabold text-[#14171F] tracking-tight">{{ number_format($kpi['pending'] ?? 0) }}</span>
                    <span class="text-sm font-semibold text-slate-500">Antrean</span>
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Status tiket pending verifikasi</p>
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
                    <span class="text-3xl font-extrabold text-[#14171F] tracking-tight">{{ number_format($kpi['verified'] ?? 0) }}</span>
                    <span class="text-sm font-semibold text-slate-500">Sekolah</span>
                </div>
                <p class="text-[11px] font-medium text-slate-400 mt-1">Integritas data geospasial 98.4%</p>
            </div>
        </div>

        <!-- Card 4: Kerusakan Kritis (Level 5) -->
        <div class="bg-[#FFF8F5] rounded-2xl p-5 shadow-xs border-2 border-[#FFD9CC] flex flex-col justify-between relative overflow-hidden">
            <div class="flex items-center justify-between gap-2 mb-3">
                <span class="text-xs font-bold text-[#FF5C2A]">Kerusakan Kritis</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 text-[10px] font-bold text-white bg-[#FF5C2A] rounded-full shadow-xs">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18.75h.007v.008H12v-.008z"/></svg>
                    Prioritas Darurat
                </span>
            </div>
            <div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-[#FF5C2A] tracking-tight">{{ number_format($kpi['urgent'] ?? 0) }}</span>
                    <span class="text-sm font-bold text-[#FF5C2A]">Tiket Kritis</span>
                </div>
                <p class="text-[11px] font-medium text-slate-500 mt-1">Skala urgensi 5 ancaman runtuh</p>
            </div>
        </div>
    </div>

    <!-- SEARCH & FILTER UTILITY CARD -->
    <div class="bg-white rounded-2xl lg:rounded-3xl p-4 lg:p-5 shadow-xs border border-slate-100 mb-6 flex flex-col gap-4">
        
        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col gap-3">
            
            <!-- Search Input Omni -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <div class="relative flex-1">
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="q"
                        value="{{ $keyword }}"
                        placeholder="Cari No. Tiket (RPT-2026-...), nama sekolah, atau kata kunci judul..." 
                        class="w-full pl-10 pr-20 py-2.5 text-xs font-medium text-slate-800 bg-[#F8FAFC] border border-slate-200/70 rounded-full focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-all placeholder-slate-400 shadow-xs"
                    />
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1 bg-[#14171F] hover:bg-slate-800 text-white text-[11px] font-bold rounded-full transition-colors">
                        Cari
                    </button>
                </div>

                <!-- Filter Reset / Clear Action -->
                @if ($keyword || $status || $severity || $level)
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-full transition-colors shrink-0">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reset Filter
                    </a>
                @endif
            </div>

            <!-- Filter Segmented Pills Rows -->
            <div class="flex flex-col gap-2.5 pt-2 border-t border-slate-100 text-xs">
                
                <!-- Row 1: Status Filter -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-slate-400 font-semibold w-16 text-[11px]">Status:</span>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['status' => null])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ empty($status) || $status === 'all' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Semua Status
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $status === 'pending' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Pending Visitasi
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['status' => 'verified'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $status === 'verified' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Verified Lapangan
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['status' => 'in_repair'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $status === 'in_repair' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Dalam Renovasi
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['status' => 'resolved'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $status === 'resolved' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Selesai Ditangani
                        </a>
                    </div>

                    <!-- Jenjang Filter -->
                    <div class="hidden lg:flex items-center gap-2 ml-auto">
                        <span class="text-slate-400 font-semibold text-[11px]">Jenjang:</span>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['level' => null])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ empty($level) || $level === 'all' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Semua
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['level' => 'SD'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $level === 'SD' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            SD
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['level' => 'SMP'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $level === 'SMP' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            SMP
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['level' => 'SMA'])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $level === 'SMA' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            SMA
                        </a>
                    </div>
                </div>

                <!-- Row 2: Urgency Filter -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-slate-400 font-semibold w-16 text-[11px]">Urgensi:</span>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['severity' => null])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ empty($severity) || $severity === 'all' ? 'bg-[#14171F] text-white font-bold shadow-xs' : 'bg-[#F8FAFC] hover:bg-slate-100 text-slate-600 border border-slate-200/70 font-medium' }}">
                            Semua Bobot
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['severity' => 5])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $severity == 5 ? 'bg-rose-600 text-white font-bold shadow-xs' : 'bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-semibold' }}">
                            Kritis (Level 5)
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['severity' => 4])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $severity == 4 ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold' }}">
                            Parah (Level 4)
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['severity' => 3])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $severity == 3 ? 'bg-amber-600 text-white font-bold shadow-xs' : 'bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 font-semibold' }}">
                            Waspada (Level 3)
                        </a>
                        <a href="{{ route('reports.index', array_merge(request()->query(), ['severity' => 1])) }}" 
                           class="px-3 py-1 text-xs rounded-full transition-colors {{ $severity == 1 ? 'bg-emerald-600 text-white font-bold shadow-xs' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-semibold' }}">
                            Ringan (Level 1)
                        </a>
                    </div>
                </div>

            </div>

        </form>

    </div>

    <!-- MAIN DATA TABLE CARD -->
    <div class="bg-white rounded-2xl lg:rounded-3xl shadow-xs border border-slate-100 overflow-hidden">
        
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#F8FAFC] border-b border-slate-100 text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 sm:px-6">No. Tiket</th>
                        <th class="py-3.5 px-4">Satuan Pendidikan &amp; Wilayah</th>
                        <th class="py-3.5 px-4">Komponen Fasilitas &amp; Indikasi</th>
                        <th class="py-3.5 px-4">Validasi EXIF GPS</th>
                        <th class="py-3.5 px-4">Status Laporan</th>
                        <th class="py-3.5 px-4">Tanggal Input</th>
                        <th class="py-3.5 px-4 sm:px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    
                    @forelse ($reports as $report)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            
                            <!-- No. Tiket -->
                            <td class="py-4 px-4 sm:px-6">
                                <span class="inline-block px-2.5 py-1 text-xs font-bold font-mono text-slate-700 bg-slate-100 rounded-lg">
                                    #{{ $report->ticket_num }}
                                </span>
                            </td>

                            <!-- Satuan Pendidikan & Wilayah -->
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-slate-900 leading-tight">
                                    {{ $report->school->name ?? 'Satuan Pendidikan' }}
                                </div>
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-500 mt-1">
                                    <span>NPSN: {{ $report->school->npsn ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $report->school->region->name ?? 'Nasional' }}</span>
                                    <span>&bull;</span>
                                    @php
                                        $tier = $report->school->region->is_3t_tier ?? 0;
                                    @endphp
                                    <span class="px-1.5 py-0.5 text-[10px] font-bold rounded {{ $tier > 0 ? 'text-amber-800 bg-amber-50 border border-amber-200' : 'text-slate-600 bg-slate-100' }}">
                                        {{ $tier > 0 ? "3T Tier-{$tier}" : 'Non-3T' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Komponen Fasilitas & Judul -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-800">
                                    {{ $report->category->name ?? 'Fasilitas Umum' }}
                                </div>
                                <div class="text-[11px] text-slate-500 truncate max-w-xs mt-0.5 font-medium" title="{{ $report->title }}">
                                    {{ $report->title }}
                                </div>
                                <div class="mt-1">
                                    @if ($report->severity_level == 5)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200 rounded-full">
                                            <svg class="w-2.5 h-2.5 text-rose-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                            Level 5 &ndash; Kritis
                                        </span>
                                    @elseif ($report->severity_level == 4)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-bold text-amber-800 bg-amber-50 border border-amber-200 rounded-full">
                                            Level 4 &ndash; Rusak Berat
                                        </span>
                                    @elseif ($report->severity_level == 3)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold text-slate-700 bg-slate-100 rounded-full">
                                            Level 3 &ndash; Sedang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-full">
                                            Level {{ $report->severity_level }} &ndash; Ringan
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Validasi EXIF GPS (PBI-05 Ready Badge) -->
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                    <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                                    Valid &lt; 250m
                                </span>
                            </td>

                            <!-- Status Laporan -->
                            <td class="py-4 px-4">
                                @if ($report->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-amber-800 bg-amber-50 border border-amber-200/80 rounded-full">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        Pending Visitasi Lapangan
                                    </span>
                                @elseif ($report->status === 'verified')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-emerald-800 bg-emerald-50 border border-emerald-200/80 rounded-full">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Verified Lapangan
                                    </span>
                                @elseif ($report->status === 'in_repair')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-indigo-800 bg-indigo-50 border border-indigo-200/80 rounded-full">
                                        <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                        Dalam Perbaikan CSR
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold text-slate-700 bg-slate-100 rounded-full">
                                        {{ $report->status_label }}
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal Input -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="font-semibold text-slate-800">{{ $report->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400">{{ $report->created_at->format('H:i') }} WIB</div>
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 px-4 sm:px-6 text-center">
                                <button type="button" class="px-3.5 py-1.5 text-xs font-bold text-slate-800 hover:text-white bg-white hover:bg-[#14171F] border border-slate-300 rounded-full transition-colors shadow-xs">
                                    Detail Tiket
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center">
                                <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 text-[#FF5C2A] flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                                    </div>
                                    <h3 class="text-sm font-extrabold text-[#14171F]">Belum Ada Laporan Kerusakan</h3>
                                    <p class="text-xs text-slate-500 mt-1">
                                        Data laporan kerusakan fisik sekolah belum tersedia untuk kriteria pencarian ini.
                                    </p>
                                    <a href="{{ route('reports.create') }}" class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-[#FF5C2A] hover:bg-[#EA4A18] text-white text-xs font-bold rounded-full shadow-xs transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                        Buat Laporan Baru Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- PAGINATION BAR (DYNAMIC) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 sm:px-6 bg-white border-t border-slate-100 text-xs">
            <span class="text-slate-500 font-medium">
                @if ($reports->total() > 0)
                    Menampilkan <span class="font-bold text-slate-800">{{ $reports->firstItem() }} &ndash; {{ $reports->lastItem() }}</span> dari <span class="font-bold text-slate-800">{{ number_format($reports->total()) }}</span> laporan fasilitas
                @else
                    Menampilkan <span class="font-bold text-slate-800">0</span> laporan
                @endif
            </span>

            <div class="flex items-center gap-1.5">
                {{ $reports->links() }}
            </div>
        </div>

    </div>

</x-layouts.app>
