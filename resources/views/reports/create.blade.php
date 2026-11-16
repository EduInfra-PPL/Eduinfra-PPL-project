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
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
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

    <!-- WIZARD STEPPER (3 STEPS INTERAKTIF) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6" id="wizardStepper">
        
        <!-- Step 1 Indicator -->
        <button type="button" onclick="goToStep(1)" id="stepTab1" class="flex items-center gap-3 p-3 bg-[#14171F] text-white rounded-full shadow-xs text-left cursor-pointer transition-all">
            <span id="stepIcon1" class="w-7 h-7 rounded-full bg-[#FF5C2A] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                1
            </span>
            <div class="truncate">
                <span id="stepSub1" class="block text-[10px] font-bold uppercase tracking-wider text-slate-300">Langkah 1</span>
                <span id="stepTitle1" class="block text-xs font-bold text-white truncate">1. Identitas &amp; Kategori</span>
            </div>
        </button>

        <!-- Step 2 Indicator (PBI-05 Unggah Bukti & Geotagging) -->
        <button type="button" onclick="goToStep(2)" id="stepTab2" class="flex items-center gap-3 p-3 bg-white border border-slate-200/80 text-slate-400 rounded-full shadow-xs text-left cursor-pointer transition-all">
            <span id="stepIcon2" class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0">
                2
            </span>
            <div class="truncate">
                <span id="stepSub2" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 2 (PBI-05)</span>
                <span id="stepTitle2" class="block text-xs font-semibold text-slate-600 truncate">2. Unggah Bukti &amp; EXIF</span>
            </div>
        </button>

        <!-- Step 3 Indicator -->
        <button type="button" onclick="goToStep(3)" id="stepTab3" class="flex items-center gap-3 p-3 bg-white border border-slate-200/80 text-slate-400 rounded-full shadow-xs text-left cursor-pointer transition-all">
            <span id="stepIcon3" class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0">
                3
            </span>
            <div class="truncate">
                <span id="stepSub3" class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Langkah 3</span>
                <span id="stepTitle3" class="block text-xs font-semibold text-slate-600 truncate">3. Verifikasi &amp; Terbitkan</span>
            </div>
        </button>

    </div>

    <!-- MAIN FORM WRAPPER (MULTIPART FORM FOR STEP 1, STEP 2 & STEP 3) -->
    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" id="damageReportForm">
        @csrf

        <!-- MAIN FORM GRID (2 COLUMNS: LEFT 7 COLS, RIGHT 5 COLS) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: WIZARD STEPS CONTENT (7 COLS) -->
            <div class="lg:col-span-7 flex flex-col gap-5">
                
                <!-- ========================================================= -->
                <!-- STEP 1 PANEL: IDENTIFIKASI SATUAN PENDIDIKAN & FASILITAS  -->
                <!-- ========================================================= -->
                <div id="stepPanel1" class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-xs border border-slate-100 flex flex-col gap-5">
                    
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
                                        data-name="{{ $school->name }}"
                                        data-region="{{ $school->region->name ?? 'Wilayah 3T' }}"
                                        data-level="{{ $school->educational_level }}"
                                        data-tier="{{ $school->region->is_3t_tier ?? 1 }}"
                                        data-address="{{ $school->address ?? 'Lokasi Sekolah' }}"
                                        data-lat="{{ $school->latitude ?? ($school->getCoordinatesLatLng()['latitude'] ?? 1.1892) }}"
                                        data-lng="{{ $school->longitude ?? ($school->getCoordinatesLatLng()['longitude'] ?? 109.3218) }}"
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
                                        data-name="{{ $cat->name }}"
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
                                        data-label="{{ $label }}"
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
                    </div>

                    <!-- Step 1 Nav Button -->
                    <div class="pt-3 border-t border-slate-100 flex justify-end">
                        <button type="button" onclick="goToStep(2)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#14171F] hover:bg-slate-800 text-white text-xs font-bold rounded-full transition-colors shadow-xs cursor-pointer">
                            <span>Lanjut ke Langkah 2: Unggah Bukti &amp; Geotagging</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>

                </div>

                <!-- ========================================================= -->
                <!-- STEP 2 PANEL: UNGGAH BUKTI MEDIA & VALIDASI EXIF GPS (PBI-05) -->
                <!-- ========================================================= -->
                <div id="stepPanel2" class="hidden bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-xs border border-slate-100 flex flex-col gap-5">
                    
                    <!-- Section Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-extrabold text-[#14171F]">Unggah Bukti Fisik &amp; Ekstraksi EXIF GPS</h2>
                                <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded-md border border-emerald-200">PBI-05 Aktif</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Unggah foto kerusakan asli dari kamera perangkat. Sistem akan mengekstrak koordinat GPS otomatis.</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z"/><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z"/></svg>
                        </div>
                    </div>

                    <!-- Protocol Alert Banner -->
                    <div class="p-3.5 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl flex items-start gap-3">
                        <div class="w-6 h-6 rounded-lg bg-orange-100 text-[#FF5C2A] flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 18.75h.007v.008H12v-.008z"/></svg>
                        </div>
                        <div class="text-xs text-slate-600 leading-relaxed">
                            <strong class="text-slate-800">Petunjuk Pemotretan Fisik:</strong> Pastikan sensor lokasi/GPS kamera ponsel aktif saat memotret fasilitas. Foto berjarak <span class="font-bold text-emerald-700">&le; 250m</span> dari titik sekolah otomatis berstatus <strong>Verified</strong> dan diprioritaskan untuk pendanaan CSR.
                        </div>
                    </div>

                    <!-- DROPZONE UPLOAD AREA INTERAKTIF -->
                    <div>
                        <input 
                            type="file" 
                            name="photos[]" 
                            id="photoInput" 
                            multiple 
                            accept="image/jpeg,image/png,image/webp" 
                            class="hidden" 
                            onchange="handleFileSelect(this.files)"
                        />

                        <div 
                            id="dropzoneContainer"
                            onclick="document.getElementById('photoInput').click()"
                            ondragover="handleDragOver(event)"
                            ondragleave="handleDragLeave(event)"
                            ondrop="handleDrop(event)"
                            class="border-2 border-dashed border-slate-300 hover:border-[#FF5C2A] bg-[#F8FAFC] hover:bg-[#FFF8F5] rounded-2xl p-6 text-center cursor-pointer transition-all flex flex-col items-center justify-center gap-2 group"
                        >
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 group-hover:border-orange-200 text-slate-400 group-hover:text-[#FF5C2A] flex items-center justify-center shadow-xs transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z"/></svg>
                            </div>
                            <h3 class="text-xs font-extrabold text-slate-800 group-hover:text-[#FF5C2A] transition-colors">
                                Tarik &amp; Lepas Berkas Foto di Sini, atau <span class="underline">Klik untuk Memilih</span>
                            </h3>
                            <p class="text-[11px] text-slate-400 font-medium">
                                Mendukung format JPEG, JPG, PNG, atau WebP &bull; Maksimal 10 MB per foto &bull; Hingga 5 foto
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-full bg-white border border-slate-200 text-[10px] font-semibold text-slate-600">
                                    Anti-Fraud Stripping ISO 27001
                                </span>
                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-[10px] font-bold text-emerald-700">
                                    Geofence 250m Buffer
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- THUMBNAIL GALLERY / UPLOADED MEDIA QUEUE -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-slate-700">Daftar Foto Bukti Fisik (<span id="photoCount">0</span>/5)</span>
                            <span class="text-[11px] text-slate-400" id="photoStatusSummary">Belum ada foto yang dipilih</span>
                        </div>

                        <!-- Grid Galeri Kartu Foto -->
                        <div id="photoPreviewGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Kartu foto dinamis akan disisipkan di sini oleh JavaScript -->
                        </div>

                        <!-- Empty State Box -->
                        <div id="emptyPhotosBox" class="p-5 text-center bg-[#F8FAFC] border border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center gap-1.5">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                            </div>
                            <span class="text-xs font-bold text-slate-700">Belum Ada Foto Bukti</span>
                            <p class="text-[11px] text-slate-400 max-w-xs">
                                Silakan pilih minimal 1 foto fisik bangunan yang rusak agar laporan memiliki nilai verifikasi tinggi.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 Navigation Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" onclick="goToStep(1)" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-[#F8FAFC] hover:bg-slate-100 border border-slate-200 rounded-full transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                            <span>Kembali ke Langkah 1</span>
                        </button>
                        <button type="button" onclick="goToStep(3)" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#14171F] hover:bg-slate-800 text-white text-xs font-bold rounded-full transition-colors shadow-xs cursor-pointer">
                            <span>Lanjut ke Langkah 3: Verifikasi &amp; Terbitkan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                        </button>
                    </div>

                </div>

                <!-- ========================================================= -->
                <!-- STEP 3 PANEL: VERIFIKASI RINGKASAN & TERBITKAN TIKET     -->
                <!-- ========================================================= -->
                <div id="stepPanel3" class="hidden bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-xs border border-slate-100 flex flex-col gap-5">
                    
                    <!-- Section Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-extrabold text-[#14171F]">Verifikasi Ringkasan &amp; Terbitkan Tiket</h2>
                            <p class="text-xs text-slate-500 mt-0.5">Tinjau seluruh data sebelum menerbitkan nomor tiket resmi pelaporan.</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <!-- Review Card: Sekolah & Lokasi -->
                    <div class="p-4 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl flex flex-col gap-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Satuan Pendidikan Terpilih</span>
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-extrabold text-[#14171F]" id="reviewSchool">10802199 — SDN 02 Sambas Terluar</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 rounded border border-emerald-200" id="reviewTier">Tier-1 Afirmasi</span>
                        </div>
                        <p class="text-[11px] text-slate-500" id="reviewAddress">Desa Temajuk, Kec. Paloh, Kab. Sambas, Kalimantan Barat</p>
                    </div>

                    <!-- Review Grid: Komponen & Urgensi -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3.5 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl">
                            <span class="block text-[10px] font-bold uppercase text-slate-400">Komponen Fasilitas</span>
                            <span class="text-xs font-extrabold text-[#14171F] mt-1 block" id="reviewCategory">Ruang Kelas Belajar (RKB)</span>
                        </div>
                        <div class="p-3.5 bg-[#FFF8F5] border border-[#FFD9CC] rounded-2xl">
                            <span class="block text-[10px] font-bold uppercase text-[#FF5C2A]">Tingkat Urgensi</span>
                            <span class="text-xs font-extrabold text-[#FF5C2A] mt-1 block" id="reviewSeverity">Skala 5 &ndash; Kritis</span>
                        </div>
                    </div>

                    <!-- Review Description -->
                    <div class="p-3.5 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl">
                        <span class="block text-[10px] font-bold uppercase text-slate-400 mb-1">Deskripsi Kerusakan</span>
                        <p class="text-xs text-slate-700 leading-relaxed font-medium" id="reviewDescription">-</p>
                    </div>

                    <!-- Review Photos Status -->
                    <div class="p-4 bg-emerald-50/60 border border-emerald-200/80 rounded-2xl flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-emerald-900" id="reviewPhotoSummaryTitle">Rekapitulasi Bukti Foto Media (PBI-05)</h4>
                            <p class="text-[11px] text-emerald-800 mt-0.5 leading-relaxed" id="reviewPhotoSummaryDetail">
                                Foto yang diunggah akan otomatis diproses dengan konversi koordinat desimal WGS84, perhitungan jarak Haversine, dan pembersihan nomor seri kamera (ISO/IEC 27001).
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 Navigation Buttons -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <button type="button" onclick="goToStep(2)" class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-slate-600 hover:text-slate-900 bg-[#F8FAFC] hover:bg-slate-100 border border-slate-200 rounded-full transition-colors cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                            <span>Kembali ke Langkah 2</span>
                        </button>

                        <button 
                            type="submit" 
                            id="finalSubmitBtn"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-[#FF5C2A] hover:bg-[#EA4A18] text-white text-xs font-bold rounded-full shadow-sm hover:shadow transition-all group cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                            <span>Terbitkan Tiket Laporan Kerusakan</span>
                        </button>
                    </div>

                </div>

            </div>

            <!-- RIGHT COLUMN: INTEGRITAS SPASIAL & TITIK MASTER SEKOLAH (5 COLS) -->
            <div class="lg:col-span-5 flex flex-col gap-4">
                
                <div class="bg-white rounded-2xl lg:rounded-3xl p-5 shadow-xs border border-slate-100 flex flex-col gap-4 sticky top-6">
                    
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
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Latitude (Lintang)</span>
                            <span class="text-xs font-mono font-bold text-slate-900 mt-0.5 block" id="masterLatDisplay">
                                {{ number_format($schools->first()->latitude ?? ($schools->first()->getCoordinatesLatLng()['latitude'] ?? 1.1892), 4) }}&deg;
                            </span>
                        </div>
                        <div>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Longitude (Bujur)</span>
                            <span class="text-xs font-mono font-bold text-slate-900 mt-0.5 block" id="masterLngDisplay">
                                {{ number_format($schools->first()->longitude ?? ($schools->first()->getCoordinatesLatLng()['longitude'] ?? 109.3218), 4) }}&deg;
                            </span>
                        </div>
                    </div>

                    <!-- Map Visualizer Interactive Radar Widget (Radius 250m Geofence) -->
                    <div class="relative w-full h-48 rounded-2xl bg-[#EAF0F6] overflow-hidden border border-slate-200 flex flex-col justify-between p-3 select-none">
                        
                        <!-- Top Info overlay -->
                        <div class="flex items-center justify-between z-10 text-[10px]">
                            <span class="px-2 py-0.5 rounded-md font-bold bg-[#14171F] text-white" id="radarRegionBadge">
                                {{ $schools->first()->region->name ?? 'Wilayah 3T' }}
                            </span>
                            <span class="px-2 py-0.5 rounded-md font-bold bg-white/90 text-slate-700 shadow-xs" id="radarGnssAccuracy">
                                GNSS WGS84: 99.4%
                            </span>
                        </div>

                        <!-- Radar Buffer Graphics -->
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none" id="radarViewport">
                            <!-- 250m Buffer Circle Radar -->
                            <div class="relative w-36 h-36 rounded-full border-2 border-dashed border-emerald-500/60 bg-emerald-500/10 flex items-center justify-center shadow-inner">
                                <span class="absolute -top-3 text-[9px] font-bold text-emerald-800 bg-white/90 px-1.5 py-0.2 rounded border border-emerald-200 shadow-xs">
                                    Radius 250m Buffer
                                </span>
                                
                                <!-- Dashed Crosshair line -->
                                <div class="absolute w-full h-[1px] bg-slate-400/40 border-t border-dashed border-slate-400"></div>
                                <div class="absolute h-full w-[1px] bg-slate-400/40 border-l border-dashed border-slate-400"></div>

                                <!-- School Center Pin (Oranye) -->
                                <div class="w-7 h-7 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center shadow-lg border-2 border-white z-10" title="Titik Master Sekolah">
                                    <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.333A48.243 48.243 0 0012 9.75c-2.551 0-5.056.2-7.5.583V21"/></svg>
                                </div>

                                <!-- Dynamic Photo GPS Pin (Ditempatkan oleh JavaScript) -->
                                <div id="radarPhotoPin" class="hidden absolute w-5 h-5 rounded-full bg-emerald-600 border-2 border-white shadow-md flex items-center justify-center z-20 transition-all duration-500" style="top: 25%; left: 65%;">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Tag Bar -->
                        <div class="flex items-center justify-between z-10 text-[10px]">
                            <span class="px-2 py-0.5 rounded-full font-bold bg-[#14171F] text-white flex items-center gap-1" id="radarStatusText">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                Geofence 250m Siap Divalidasi
                            </span>
                            <span class="px-2 py-0.5 rounded-md font-mono font-bold bg-white/90 text-slate-700" id="radarDistanceLabel">
                                Deviasi: -
                            </span>
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

    </form>

    <!-- INLINE SCRIPT FOR REACTIVE WIZARD & EXIF GEOFENCE PARSER (VANILLA JS) -->
    <script>
        // State Wizard
        let currentStep = 1;
        let selectedFiles = []; // Array of { file: File, previewUrl: string, exif: object|null, distance: number|null, status: string }

        // Koordinat Master Sekolah saat ini (default: opsi pertama)
        let currentMasterCoords = {
            lat: 1.1892,
            lng: 109.3218
        };

        // Inisialisasi koordinat dari sekolah yang terpilih
        document.addEventListener('DOMContentLoaded', function() {
            updateMasterCoordsFromSelect();
        });

        // Update koordinat ketika sekolah diubah
        const schoolSelect = document.getElementById('school_id');
        if (schoolSelect) {
            schoolSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (opt && opt.value) {
                    const npsn = opt.dataset.npsn || '';
                    const name = opt.dataset.name || opt.text.split('—')[1]?.trim() || opt.text;
                    const address = opt.dataset.address || '';
                    const level = opt.dataset.level || 'SD';
                    const tier = opt.dataset.tier || '1';
                    const region = opt.dataset.region || 'Wilayah 3T';

                    document.getElementById('previewSchoolName').textContent = `${npsn} — ${name}`;
                    document.getElementById('previewSchoolAddress').textContent = address;
                    document.getElementById('previewSchoolLevel').textContent = `Jenjang: ${level}`;
                    document.getElementById('previewSchoolTier').textContent = `Wilayah 3T Tier-${tier} Prioritas Afirmasi`;
                    document.getElementById('radarRegionBadge').textContent = region;

                    updateMasterCoordsFromSelect();

                    // Re-kalkulasi jarak untuk semua foto yang sudah dipilih
                    recalculateAllPhotosDistance();
                }
            });
        }

        function updateMasterCoordsFromSelect() {
            if (!schoolSelect) return;
            const opt = schoolSelect.options[schoolSelect.selectedIndex];
            if (opt) {
                const lat = parseFloat(opt.dataset.lat);
                const lng = parseFloat(opt.dataset.lng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    currentMasterCoords.lat = lat;
                    currentMasterCoords.lng = lng;

                    document.getElementById('masterLatDisplay').textContent = `${lat.toFixed(4)}°`;
                    document.getElementById('masterLngDisplay').textContent = `${lng.toFixed(4)}°`;
                }
            }
        }

        // Navigasi Antar Langkah Wizard (Step 1, Step 2, Step 3)
        function goToStep(step) {
            if (step < 1 || step > 3) return;

            // Validasi Step 1 jika ingin ke Step 2 atau 3
            if (step > 1) {
                const titleInput = document.getElementById('title');
                const descInput = document.getElementById('description');
                if (!titleInput.value.trim() || !descInput.value.trim()) {
                    alert('Harap lengkapi Judul dan Deskripsi kerusakan pada Langkah 1 terlebih dahulu.');
                    return;
                }
            }

            // Sembunyikan semua panel
            document.getElementById('stepPanel1').classList.add('hidden');
            document.getElementById('stepPanel2').classList.add('hidden');
            document.getElementById('stepPanel3').classList.add('hidden');

            // Tampilkan panel target
            document.getElementById(`stepPanel${step}`).classList.remove('hidden');
            currentStep = step;

            // Update Tampilan Stepper Header
            updateStepperUI(step);

            // Jika masuk ke Step 3, sinkronkan data ringkasan
            if (step === 3) {
                populateStep3Summary();
            }

            window.scrollTo({ top: 120, behavior: 'smooth' });
        }

        function updateStepperUI(activeStep) {
            for (let i = 1; i <= 3; i++) {
                const tab = document.getElementById(`stepTab${i}`);
                const icon = document.getElementById(`stepIcon${i}`);
                const sub = document.getElementById(`stepSub${i}`);
                const title = document.getElementById(`stepTitle${i}`);

                if (i === activeStep) {
                    tab.className = "flex items-center gap-3 p-3 bg-[#14171F] text-white rounded-full shadow-xs text-left cursor-pointer transition-all";
                    icon.className = "w-7 h-7 rounded-full bg-[#FF5C2A] text-white font-extrabold text-xs flex items-center justify-center shrink-0";
                    sub.className = "block text-[10px] font-bold uppercase tracking-wider text-slate-300";
                    title.className = "block text-xs font-bold text-white truncate";
                } else if (i < activeStep) {
                    tab.className = "flex items-center gap-3 p-3 bg-white border border-emerald-200 text-emerald-700 rounded-full shadow-xs text-left cursor-pointer transition-all";
                    icon.className = "w-7 h-7 rounded-full bg-emerald-100 text-emerald-700 font-extrabold text-xs flex items-center justify-center shrink-0";
                    sub.className = "block text-[10px] font-bold uppercase tracking-wider text-emerald-600";
                    title.className = "block text-xs font-semibold text-slate-700 truncate";
                } else {
                    tab.className = "flex items-center gap-3 p-3 bg-white border border-slate-200/80 text-slate-400 rounded-full shadow-xs text-left cursor-pointer transition-all";
                    icon.className = "w-7 h-7 rounded-full bg-slate-100 text-slate-500 font-bold text-xs flex items-center justify-center shrink-0";
                    sub.className = "block text-[10px] font-bold uppercase tracking-wider text-slate-400";
                    title.className = "block text-xs font-semibold text-slate-600 truncate";
                }
            }
        }

        // Category selection styling
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

        // Severity pills styling
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
                noticeBox.classList.remove('hidden');
            } else if (val >= 3) {
                alertText.textContent = `Skala ${val}: Kerusakan Sedang / Waspada`;
                alertText.className = 'text-[11px] font-extrabold text-amber-600';
                noticeBox.classList.remove('hidden');
            } else {
                alertText.textContent = `Skala ${val}: Kerusakan Ringan`;
                alertText.className = 'text-[11px] font-extrabold text-emerald-600';
                noticeBox.classList.add('hidden');
            }
        }

        // =========================================================
        // DROPZONE & DRAG-AND-DROP FILE UPLOAD LOGIC (PBI-05)
        // =========================================================
        function handleDragOver(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('dropzoneContainer').classList.add('border-[#FF5C2A]', 'bg-[#FFF8F5]');
        }

        function handleDragLeave(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('dropzoneContainer').classList.remove('border-[#FF5C2A]', 'bg-[#FFF8F5]');
        }

        function handleDrop(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('dropzoneContainer').classList.remove('border-[#FF5C2A]', 'bg-[#FFF8F5]');
            if (e.dataTransfer && e.dataTransfer.files) {
                handleFileSelect(e.dataTransfer.files);
            }
        }

        function handleFileSelect(files) {
            if (!files || files.length === 0) return;

            const remainingSlots = 5 - selectedFiles.length;
            if (remainingSlots <= 0) {
                alert('Maksimal 5 foto bukti yang dapat diunggah.');
                return;
            }

            const filesToAdd = Array.from(files).slice(0, remainingSlots);

            filesToAdd.forEach(file => {
                // Validasi ukuran < 10MB
                if (file.size > 10 * 1024 * 1024) {
                    alert(`Berkas "${file.name}" melebihi batas 10 MB.`);
                    return;
                }

                const previewUrl = URL.createObjectURL(file);
                const fileItem = {
                    id: 'file_' + Math.random().toString(36).substr(2, 9),
                    file: file,
                    name: file.name,
                    sizeFormatted: (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                    previewUrl: previewUrl,
                    hasGps: false,
                    distance: null,
                    isVerifiedLocation: false,
                    statusLabel: 'Mengekstrak EXIF...'
                };

                selectedFiles.push(fileItem);
                parseFileExif(fileItem);
            });

            renderPhotoGallery();
            syncFilesToFormInput();
        }

        // Sinkronisasi data selectedFiles ke elemen input asli <input type="file" name="photos[]">
        function syncFilesToFormInput() {
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(item => {
                dataTransfer.items.add(item.file);
            });
            const input = document.getElementById('photoInput');
            if (input) {
                input.files = dataTransfer.files;
            }
        }

        // Hapus foto dari antrean
        function removePhoto(id) {
            selectedFiles = selectedFiles.filter(item => item.id !== id);
            renderPhotoGallery();
            syncFilesToFormInput();
            recalculateAllPhotosDistance();
        }

        // Render Galeri Thumbnail Foto
        function renderPhotoGallery() {
            const grid = document.getElementById('photoPreviewGrid');
            const emptyBox = document.getElementById('emptyPhotosBox');
            const countLabel = document.getElementById('photoCount');
            const statusSummary = document.getElementById('photoStatusSummary');

            countLabel.textContent = selectedFiles.length;

            if (selectedFiles.length === 0) {
                grid.innerHTML = '';
                emptyBox.classList.remove('hidden');
                statusSummary.textContent = 'Belum ada foto yang dipilih';
                resetRadarIndicator();
                return;
            }

            emptyBox.classList.add('hidden');
            grid.innerHTML = '';

            let verifiedCount = 0;

            selectedFiles.forEach(item => {
                if (item.isVerifiedLocation) verifiedCount++;

                let badgeHtml = '';
                if (!item.hasGps) {
                    badgeHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Tanpa Metadata GPS
                    </span>`;
                } else if (item.isVerifiedLocation) {
                    badgeHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Valid Geofence (${item.distance}m)
                    </span>`;
                } else {
                    badgeHtml = `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold text-rose-700 bg-rose-50 border border-rose-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Flagged: Deviasi ${item.distance}m (>250m)
                    </span>`;
                }

                const card = document.createElement('div');
                card.className = "p-3 bg-[#F8FAFC] border border-slate-200/80 rounded-2xl flex items-center justify-between gap-3 shadow-xs hover:border-slate-300 transition-all";
                card.innerHTML = `
                    <div class="flex items-center gap-3 truncate">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 overflow-hidden shrink-0 border border-slate-200">
                            <img src="${item.previewUrl}" alt="Preview" class="w-full h-full object-cover">
                        </div>
                        <div class="truncate">
                            <h4 class="text-xs font-extrabold text-slate-800 truncate" title="${item.name}">${item.name}</h4>
                            <span class="text-[10px] text-slate-400 block">${item.sizeFormatted}</span>
                            <div class="mt-1">${badgeHtml}</div>
                        </div>
                    </div>
                    <button type="button" onclick="removePhoto('${item.id}')" class="w-8 h-8 rounded-lg bg-white hover:bg-rose-50 text-slate-400 hover:text-rose-600 border border-slate-200 hover:border-rose-200 flex items-center justify-center shrink-0 transition-colors shadow-xs" title="Hapus Foto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </button>
                `;
                grid.appendChild(card);
            });

            statusSummary.textContent = `${selectedFiles.length} foto dipilih (${verifiedCount} terverifikasi geofence)`;
        }

        // =========================================================
        // CLIENT-SIDE EXIF PARSER & HAVERSINE DISTANCE ENGINE
        // =========================================================
        function parseFileExif(item) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const buffer = e.target.result;
                const coords = extractGpsFromJpegBuffer(buffer);

                if (coords) {
                    item.hasGps = true;
                    item.gpsLat = coords.lat;
                    item.gpsLng = coords.lng;
                    calculateItemDistance(item);
                } else {
                    item.hasGps = false;
                    item.statusLabel = 'Tanpa Metadata GPS';
                }

                renderPhotoGallery();
                updateRadarWithPhoto(item);
            };
            reader.readAsArrayBuffer(item.file);
        }

        function calculateItemDistance(item) {
            if (!item.hasGps) return;
            const dist = calculateHaversineMeters(item.gpsLat, item.gpsLng, currentMasterCoords.lat, currentMasterCoords.lng);
            item.distance = dist;
            item.isVerifiedLocation = (dist <= 250);
            item.statusLabel = item.isVerifiedLocation ? `Valid Geofence (${dist}m)` : `Flagged: Deviasi ${dist}m (>250m)`;
        }

        function recalculateAllPhotosDistance() {
            selectedFiles.forEach(item => {
                if (item.hasGps) {
                    calculateItemDistance(item);
                }
            });
            renderPhotoGallery();
            if (selectedFiles.length > 0) {
                updateRadarWithPhoto(selectedFiles[selectedFiles.length - 1]);
            }
        }

        // Rumus Geodesik Haversine (Bola Bumi R = 6.371.000m)
        function calculateHaversineMeters(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return Math.round(R * c);
        }

        // Parser EXIF JPEG Binary Ringan
        function extractGpsFromJpegBuffer(buffer) {
            try {
                const view = new DataView(buffer);
                if (view.getUint16(0, false) !== 0xFFD8) return null; // Bukan JPEG

                let offset = 2;
                const length = view.byteLength;

                while (offset < length) {
                    const marker = view.getUint16(offset, false);
                    offset += 2;

                    if (marker === 0xFFE1) { // APP1 EXIF Marker
                        const exifLength = view.getUint16(offset, false);
                        const exifHeader = view.getUint32(offset + 2, false);
                        if (exifHeader === 0x45786966) { // 'Exif'
                            const tiffOffset = offset + 8;
                            const isLittleEndian = (view.getUint16(tiffOffset, false) === 0x4949);

                            // Temukan GPS IFD offset
                            const ifd0Offset = tiffOffset + view.getUint32(tiffOffset + 4, isLittleEndian);
                            const numEntries = view.getUint16(ifd0Offset, isLittleEndian);

                            let gpsOffset = 0;
                            for (let i = 0; i < numEntries; i++) {
                                const entryOffset = ifd0Offset + 2 + (i * 12);
                                const tag = view.getUint16(entryOffset, isLittleEndian);
                                if (tag === 0x8825) { // GPS Info IFD tag
                                    gpsOffset = tiffOffset + view.getUint32(entryOffset + 8, isLittleEndian);
                                    break;
                                }
                            }

                            if (gpsOffset > 0) {
                                return parseGpsIfd(view, gpsOffset, tiffOffset, isLittleEndian);
                            }
                        }
                        offset += exifLength;
                    } else if ((marker & 0xFF00) === 0xFF00) {
                        offset += view.getUint16(offset, false);
                    } else {
                        break;
                    }
                }
            } catch (err) {
                // Abaikan jika berkas tidak terformat sempurna
            }
            return null;
        }

        function parseGpsIfd(view, gpsOffset, tiffOffset, isLittleEndian) {
            const numGpsEntries = view.getUint16(gpsOffset, isLittleEndian);
            let latDms = null, latRef = 'N', lngDms = null, lngRef = 'E';

            for (let i = 0; i < numGpsEntries; i++) {
                const entry = gpsOffset + 2 + (i * 12);
                const tag = view.getUint16(entry, isLittleEndian);
                const valOffset = tiffOffset + view.getUint32(entry + 8, isLittleEndian);

                if (tag === 1) { // Latitude Ref (N/S)
                    latRef = String.fromCharCode(view.getUint8(entry + 8));
                } else if (tag === 2) { // Latitude (DMS)
                    latDms = readDms(view, valOffset, isLittleEndian);
                } else if (tag === 3) { // Longitude Ref (E/W)
                    lngRef = String.fromCharCode(view.getUint8(entry + 8));
                } else if (tag === 4) { // Longitude (DMS)
                    lngDms = readDms(view, valOffset, isLittleEndian);
                }
            }

            if (latDms && lngDms) {
                let lat = latDms[0] + (latDms[1]/60) + (latDms[2]/3600);
                let lng = lngDms[0] + (lngDms[1]/60) + (lngDms[2]/3600);
                if (latRef === 'S') lat = -lat;
                if (lngRef === 'W') lng = -lng;
                return { lat: parseFloat(lat.toFixed(7)), lng: parseFloat(lng.toFixed(7)) };
            }
            return null;
        }

        function readDms(view, offset, isLittleEndian) {
            const denomD = view.getUint32(offset + 4, isLittleEndian);
            const denomM = view.getUint32(offset + 12, isLittleEndian);
            const denomS = view.getUint32(offset + 20, isLittleEndian);
            const d = denomD ? view.getUint32(offset, isLittleEndian) / denomD : 0;
            const m = denomM ? view.getUint32(offset + 8, isLittleEndian) / denomM : 0;
            const s = denomS ? view.getUint32(offset + 16, isLittleEndian) / denomS : 0;
            return [d, m, s];
        }

        // =========================================================
        // VISUALIZER RADAR SPASIAL SYNC
        // =========================================================
        function updateRadarWithPhoto(item) {
            const pin = document.getElementById('radarPhotoPin');
            const statusText = document.getElementById('radarStatusText');
            const distanceLabel = document.getElementById('radarDistanceLabel');

            if (!item || !item.hasGps || item.distance === null) {
                resetRadarIndicator();
                return;
            }

            pin.classList.remove('hidden');

            // Kalkulasi posisi proporsional relatif terhadap radius 250m
            // 250m = 50% radius lingkaran radar
            const scale = Math.min(item.distance / 250, 1.25);
            const angle = Math.random() * Math.PI * 2; // Representasi radial
            const rPx = scale * 48; // px offset dari pusat

            const topPercent = 50 - (Math.cos(angle) * rPx / 72 * 50);
            const leftPercent = 50 + (Math.sin(angle) * rPx / 72 * 50);

            pin.style.top = `${Math.max(10, Math.min(90, topPercent))}%`;
            pin.style.left = `${Math.max(10, Math.min(90, leftPercent))}%`;

            if (item.isVerifiedLocation) {
                pin.className = "absolute w-5 h-5 rounded-full bg-emerald-600 border-2 border-white shadow-md flex items-center justify-center z-20 transition-all duration-500 animate-bounce";
                statusText.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Foto Valid Geofence (< 250m)`;
                distanceLabel.textContent = `Deviasi: ${item.distance}m`;
                distanceLabel.className = "px-2 py-0.5 rounded-md font-mono font-bold bg-emerald-100 text-emerald-800";
            } else {
                pin.className = "absolute w-5 h-5 rounded-full bg-rose-600 border-2 border-white shadow-md flex items-center justify-center z-20 transition-all duration-500 animate-pulse";
                statusText.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Deviasi Melebihi Batas (>250m)`;
                distanceLabel.textContent = `Deviasi: ${item.distance}m`;
                distanceLabel.className = "px-2 py-0.5 rounded-md font-mono font-bold bg-rose-100 text-rose-800";
            }
        }

        function resetRadarIndicator() {
            const pin = document.getElementById('radarPhotoPin');
            const statusText = document.getElementById('radarStatusText');
            const distanceLabel = document.getElementById('radarDistanceLabel');

            if (pin) pin.classList.add('hidden');
            if (statusText) statusText.innerHTML = `<span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Geofence 250m Siap Divalidasi`;
            if (distanceLabel) {
                distanceLabel.textContent = 'Deviasi: -';
                distanceLabel.className = 'px-2 py-0.5 rounded-md font-mono font-bold bg-white/90 text-slate-700';
            }
        }

        // =========================================================
        // STEP 3 SUMMARY POPULATOR
        // =========================================================
        function populateStep3Summary() {
            const opt = schoolSelect ? schoolSelect.options[schoolSelect.selectedIndex] : null;
            if (opt) {
                document.getElementById('reviewSchool').textContent = `${opt.dataset.npsn || ''} — ${opt.dataset.name || opt.text}`;
                document.getElementById('reviewTier').textContent = `Wilayah 3T Tier-${opt.dataset.tier || '1'}`;
                document.getElementById('reviewAddress').textContent = opt.dataset.address || 'Alamat sekolah';
            }

            const selectedCat = document.querySelector('.category-radio:checked');
            if (selectedCat) {
                document.getElementById('reviewCategory').textContent = selectedCat.dataset.name || 'Komponen Fasilitas';
            }

            const selectedSev = document.querySelector('.severity-radio:checked');
            if (selectedSev) {
                document.getElementById('reviewSeverity').textContent = `Skala ${selectedSev.value} — ${selectedSev.dataset.label || 'Kritis'}`;
            }

            const desc = document.getElementById('description');
            document.getElementById('reviewDescription').textContent = desc ? desc.value : '-';

            const photoCount = selectedFiles.length;
            const verifiedCount = selectedFiles.filter(item => item.isVerifiedLocation).length;
            const flaggedCount = selectedFiles.filter(item => item.hasGps && !item.isVerifiedLocation).length;
            const noGpsCount = selectedFiles.filter(item => !item.hasGps).length;

            document.getElementById('reviewPhotoSummaryTitle').textContent = `Total ${photoCount} Foto Bukti Fisik Dilampirkan (PBI-05)`;
            document.getElementById('reviewPhotoSummaryDetail').textContent = 
                `${verifiedCount} foto terverifikasi dalam geofence 250m, ${flaggedCount} foto berjarak >250m (flagged), dan ${noGpsCount} foto tanpa GPS. Nomor tiket resmi RPT-${new Date().getFullYear()}-XXXX akan digenerate otomatis.`;
        }
    </script>

</x-layouts.app>
