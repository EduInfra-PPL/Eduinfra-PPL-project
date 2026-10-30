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

    <!-- ERROR SUMMARY ALERT (IF VALIDATION FAILS) -->
    @if ($errors->any())
        <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-start gap-3 shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xs font-bold text-rose-800">Terdapat kesalahan pengisian formulir:</h3>
                <ul class="mt-1 list-disc list-inside text-xs text-rose-700 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

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
                <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 2 (PBI-05)</span>
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

    <!-- MAIN FORM WRAPPER -->
    <form method="POST" action="{{ route('reports.store') }}" id="damageReportForm">
        @csrf

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

                <!-- Field 1: Pemilihan Satuan Pendidikan (Sekolah) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="school_id" class="text-xs font-bold text-slate-700">
                            Pencarian NPSN / Satuan Pendidikan <span class="text-rose-500">*</span>
                        </label>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200/60">
                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            Terhubung Dapodik
                        </span>
                    </div>

                    <div class="relative">
                        <select 
                            name="school_id" 
                            id="school_id" 
                            required
                            class="w-full py-2.5 pl-3 pr-8 text-xs font-semibold text-slate-900 bg-[#F8FAFC] border @error('school_id') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-colors"
                        >
                            <option value="" disabled {{ old('school_id') ? '' : 'selected' }}>-- Pilih Satuan Pendidikan Terdaftar --</option>
                            @forelse ($schools as $school)
                                <option 
                                    value="{{ $school->id }}" 
                                    data-npsn="{{ $school->npsn }}"
                                    data-region="{{ $school->region->name ?? 'Wilayah 3T' }}"
                                    data-level="{{ $school->educational_level }}"
                                    data-tier="{{ $school->region->is_3t_tier ?? 1 }}"
                                    data-address="{{ $school->address ?? 'Lokasi Sekolah' }}"
                                    {{ old('school_id', $loop->first ? $school->id : '') == $school->id ? 'selected' : '' }}
                                >
                                    {{ $school->npsn }} &mdash; {{ $school->name }} ({{ $school->region->name ?? 'Nasional' }})
                                </option>
                            @empty
                                <option value="1">10802199 &mdash; SDN 02 Sambas Terluar (Kab. Sambas)</option>
                            @endforelse
                        </select>
                    </div>
                    @error('school_id')
                        <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror

                    <!-- Selected School Info Card (Dynamic Preview) -->
                    <div id="schoolPreviewCard" class="mt-3 p-4 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl flex flex-col gap-2">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-700 shadow-xs shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5"/></svg>
                                </div>
                                <div>
                                    <h3 id="previewSchoolName" class="text-xs font-extrabold text-[#14171F]">
                                        {{ $schools->first()->npsn ?? '10802199' }} &mdash; {{ $schools->first()->name ?? 'SDN 02 Sambas Terluar' }}
                                    </h3>
                                    <p id="previewSchoolAddress" class="text-[11px] text-slate-500 font-medium">
                                        {{ $schools->first()->address ?? 'Desa Temajuk, Kec. Paloh, Kab. Sambas, Kalimantan Barat' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-slate-200/60 text-[10px] font-semibold">
                            <span id="previewSchoolLevel" class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-700">
                                Jenjang: {{ $schools->first()->educational_level ?? 'SD' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-1 font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span id="previewSchoolTier">Wilayah 3T Tier-1 Prioritas Afirmasi</span>
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600 flex items-center gap-1">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Data Pokok Dapodik Aktif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Field 2: Judul Indikasi Kerusakan -->
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-700 mb-1.5">
                        Judul Ringkas Indikasi Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="title" 
                        id="title"
                        required
                        maxlength="200"
                        value="{{ old('title', 'Kerusakan parah pada atap 3 Ruang Kelas Belajar (RKB)') }}"
                        placeholder="Contoh: Atap Ruang Kelas Ambruk Pasca Angin Kencang"
                        class="w-full px-3.5 py-2.5 text-xs font-semibold text-slate-900 bg-[#F8FAFC] border @error('title') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-colors"
                    />
                    @error('title')
                        <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field 3: Kategori Komponen Fasilitas Terdampak -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-700">
                            Kategori Komponen Fasilitas Terdampak <span class="text-rose-500">*</span>
                        </label>
                        <span class="text-[11px] text-slate-400 font-medium">Pilih salah satu komponen utama</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="categoriesContainer">
                        @forelse ($categories as $index => $cat)
                            <label class="category-card relative flex items-center justify-between p-3.5 rounded-2xl border transition-all cursor-pointer shadow-xs {{ old('category_id', $loop->first ? $cat->id : '') == $cat->id ? 'border-2 border-[#FF5C2A] bg-[#FFF8F5]' : 'border-slate-200/80 bg-white hover:bg-slate-50' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 {{ old('category_id', $loop->first ? $cat->id : '') == $cat->id ? 'bg-[#FFF0EB] text-[#FF5C2A]' : 'bg-slate-100 text-slate-500' }}">
                                        @if ($loop->index % 4 === 0)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                        @elseif ($loop->index % 4 === 1)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                                        @elseif ($loop->index % 4 === 2)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="block text-xs font-extrabold text-[#14171F]">{{ $cat->name }}</span>
                                        <span class="block text-[11px] font-medium text-slate-500">Bobot Default: {{ $cat->urgency_weight }} &bull; {{ $cat->sub_category ?: 'Fasilitas Dasar' }}</span>
                                    </div>
                                </div>
                                <input 
                                    type="radio" 
                                    name="category_id" 
                                    value="{{ $cat->id }}" 
                                    class="category-radio sr-only"
                                    {{ old('category_id', $loop->first ? $cat->id : '') == $cat->id ? 'checked' : '' }}
                                    onchange="updateCategorySelection()"
                                />
                            </label>
                        @empty
                            <p class="text-xs text-slate-400">Tidak ada kategori aktif yang tersedia.</p>
                        @endforelse
                    </div>
                    @error('category_id')
                        <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Field 4: Tingkat Urgensi Kerusakan (Weight Scale 1-5) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-700">Tingkat Urgensi Kerusakan (Weight Scale) <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] font-extrabold text-rose-600" id="urgencyAlertText">! Skala 5: Kerusakan Kritis</span>
                    </div>

                    <!-- 5 Pill Radio Options -->
                    <div class="grid grid-cols-5 gap-2 text-center text-xs">
                        @php
                            $labels = [1 => 'Ringan', 2 => 'Sedang', 3 => 'Waspada', 4 => 'Parah', 5 => 'Kritis'];
                            $selectedSeverity = old('severity_level', 5);
                        @endphp
                        @foreach ($labels as $level => $label)
                            <label class="urgency-pill relative py-2.5 px-2 rounded-xl border cursor-pointer transition-all {{ $selectedSeverity == $level ? 'bg-[#FF5C2A] text-white border-[#EA4A18] font-bold shadow-xs' : 'bg-[#F8FAFC] text-slate-600 hover:bg-slate-100 border-slate-200/80 font-medium' }}">
                                <input 
                                    type="radio" 
                                    name="severity_level" 
                                    value="{{ $level }}" 
                                    class="sr-only severity-radio"
                                    {{ $selectedSeverity == $level ? 'checked' : '' }}
                                    onchange="updateSeverityPills(this)"
                                />
                                <span class="block font-extrabold">{{ $level }}</span>
                                <span class="text-[10px] block {{ $selectedSeverity == $level ? 'text-white/90' : 'text-slate-400' }}">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('severity_level')
                        <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror

                    <!-- Alert Box Konsekuensi Bobot 5 -->
                    <div id="urgencyNoticeBox" class="mt-3 p-3.5 bg-[#FFF8F5] border border-[#FFD9CC] rounded-xl flex items-start gap-3">
                        <div class="text-[#FF5C2A] shrink-0 mt-0.5">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-[#FF5C2A]">Tingkat Keparahan Kritis / Ancaman Runtuh</h4>
                            <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                                Struktur berisiko mengancam keselamatan siswa. Memerlukan intervensi tanggap darurat dan prioritas pencocokan CSR 14 hari kerja.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Field 5: Deskripsi Detail Kerusakan Fisik -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="description" class="text-xs font-bold text-slate-700">Deskripsi Detail Kerusakan Fisik <span class="text-rose-500">*</span></label>
                        <span class="text-[11px] font-mono text-slate-400" id="charCounter">Min. 10 &bull; Maks. 1000 Karakter</span>
                    </div>
                    <textarea 
                        name="description" 
                        id="description"
                        rows="4" 
                        required
                        minlength="10"
                        maxlength="1000"
                        placeholder="Jelaskan secara detail bagian bangunan yang rusak, faktor pemicu (cuaca/usia), dan dampaknya terhadap kegiatan belajar mengajar..."
                        class="w-full p-3.5 text-xs text-slate-800 bg-[#F8FAFC] border @error('description') border-rose-400 @else border-slate-200 @enderror rounded-xl focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-all leading-relaxed placeholder-slate-400"
                    >{{ old('description', 'Kerusakan parah pada atap 3 Ruang Kelas Belajar (RKB). Kuda-kuda kayu lapuk pasca cuaca ekstrem, plafon runtuh, air hujan masuk langsung saat KBM berlangsung.') }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
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
                            <span class="px-2 py-0.5 rounded-md font-bold bg-[#14171F] text-white">Zona 3T Terpencil &bull; Perbatasan</span>
                            <span class="px-2 py-0.5 rounded-md font-bold bg-white/90 text-slate-700 shadow-xs">Akurasi GNSS: 99.4%</span>
                        </div>

                        <!-- Radar Buffer Graphics (Mock visual) -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <!-- 250m Buffer Circle Radar -->
                            <div class="relative w-36 h-36 rounded-full border-2 border-dashed border-emerald-500/60 bg-emerald-500/10 flex items-center justify-center">
                                <span class="absolute -top-3 text-[9px] font-bold text-emerald-800 bg-white/90 px-1.5 py-0.2 rounded border border-emerald-200 shadow-xs">
                                    Radius 250m Buffer
                                </span>
                                
                                <!-- Dashed Crosshair line -->
                                <div class="absolute w-full h-[1px] bg-red-400/60 border-t border-dashed border-red-500"></div>

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
                                Geofence 250m Siap di Langkah 2
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
                                    Sensor lokasi perangkat kamera wajib menyala di Langkah 2 (PBI-05) agar koordinat tertanam dalam EXIF.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 text-xs">Batas Deviasi Spasial 250 Meter</span>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5">
                                    Foto di luar toleransi radius 250 meter otomatis diberi status peringatan (Flagged) untuk verifikasi tim ahli.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5 border border-emerald-200">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 text-xs">Privasi &amp; Anti-Fraud ISO/IEC 27001</span>
                                <p class="text-[11px] text-slate-500 leading-relaxed mt-0.5">
                                    Serial number hardware dan IMEI dipangkas otomatis sebelum foto tersimpan ke storage publik.
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
                                Laporan yang valid langsung dipublikasikan ke konsorsium donor CSR tanpa antrian verifikasi manual.
                            </p>
                        </div>
                    </div>

                </div>

            </div>

        </div>

        <!-- FORM BOTTOM ACTIONS -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mt-6 pt-5 border-t border-slate-200/80">
            <a href="{{ route('reports.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-full transition-colors shadow-xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                Kembali ke Direktori Laporan
            </a>

            <div class="flex items-center gap-3">
                <button 
                    type="submit" 
                    id="submitReportBtn"
                    class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#FF5C2A] hover:bg-[#EA4A18] text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition-all group cursor-pointer"
                >
                    <span>Simpan Laporan &amp; Lanjut ke Unggah Bukti</span>
                    <svg class="w-4 h-4 text-white group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </div>
        </div>
    </form>

    <!-- INLINE SCRIPT FOR REACTIVE UI FORM INTERACTION -->
    <script>
        // Reactive school select update
        const schoolSelect = document.getElementById('school_id');
        if (schoolSelect) {
            schoolSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    const npsn = opt.dataset.npsn || '';
                    const name = opt.text.split('—')[1]?.trim() || opt.text;
                    const address = opt.dataset.address || '';
                    const level = opt.dataset.level || 'SD';
                    const tier = opt.dataset.tier || '1';

                    document.getElementById('previewSchoolName').textContent = `${npsn} — ${name}`;
                    document.getElementById('previewSchoolAddress').textContent = address;
                    document.getElementById('previewSchoolLevel').textContent = `Jenjang: ${level}`;
                    document.getElementById('previewSchoolTier').textContent = `Wilayah 3T Tier-${tier} Prioritas Afirmasi`;
                }
            });
        }

        // Reactive category selection styling
        function updateCategorySelection() {
            const cards = document.querySelectorAll('.category-card');
            cards.forEach(card => {
                const radio = card.querySelector('.category-radio');
                const iconBox = card.querySelector('div > div:first-child');
                if (radio && radio.checked) {
                    card.classList.add('border-2', 'border-[#FF5C2A]', 'bg-[#FFF8F5]');
                    card.classList.remove('border-slate-200/80', 'bg-white');
                    if (iconBox) {
                        iconBox.classList.add('bg-[#FFF0EB]', 'text-[#FF5C2A]');
                        iconBox.classList.remove('bg-slate-100', 'text-slate-500');
                    }
                } else {
                    card.classList.remove('border-2', 'border-[#FF5C2A]', 'bg-[#FFF8F5]');
                    card.classList.add('border-slate-200/80', 'bg-white');
                    if (iconBox) {
                        iconBox.classList.remove('bg-[#FFF0EB]', 'text-[#FF5C2A]');
                        iconBox.classList.add('bg-slate-100', 'text-slate-500');
                    }
                }
            });
        }

        // Reactive severity level pills styling
        function updateSeverityPills(input) {
            const pills = document.querySelectorAll('.urgency-pill');
            const alertText = document.getElementById('urgencyAlertText');
            const noticeBox = document.getElementById('urgencyNoticeBox');
            const val = parseInt(input.value);

            pills.forEach(pill => {
                const radio = pill.querySelector('.severity-radio');
                const subtext = pill.querySelector('span:last-child');
                if (radio && radio.checked) {
                    pill.classList.add('bg-[#FF5C2A]', 'text-white', 'border-[#EA4A18]', 'font-bold', 'shadow-xs');
                    pill.classList.remove('bg-[#F8FAFC]', 'text-slate-600', 'border-slate-200/80', 'font-medium');
                    if (subtext) subtext.className = 'text-[10px] block text-white/90';
                } else {
                    pill.classList.remove('bg-[#FF5C2A]', 'text-white', 'border-[#EA4A18]', 'font-bold', 'shadow-xs');
                    pill.classList.add('bg-[#F8FAFC]', 'text-slate-600', 'border-slate-200/80', 'font-medium');
                    if (subtext) subtext.className = 'text-[10px] block text-slate-400';
                }
            });

            if (val === 5) {
                alertText.textContent = '! Skala 5: Kerusakan Kritis';
                alertText.className = 'text-[11px] font-extrabold text-rose-600';
            } else if (val >= 3) {
                alertText.textContent = `Skala ${val}: Kerusakan Sedang / Waspada`;
                alertText.className = 'text-[11px] font-extrabold text-amber-600';
            } else {
                alertText.textContent = `Skala ${val}: Kerusakan Ringan`;
                alertText.className = 'text-[11px] font-extrabold text-emerald-600';
            }
        }
    </script>

</x-layouts.app>
