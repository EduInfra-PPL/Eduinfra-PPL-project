@php
    $defaultDeadline = old('deadline')
        ? \Illuminate\Support\Carbon::parse(old('deadline'))
        : now()->addDays(60);
    $defaultProject = $availableProjects->firstWhere('id', (int) old('project_id'))
        ?? $availableProjects->first();
@endphp

<x-layouts.app active="csr">

    <div 
        x-data="{
            createModalOpen: {{ $errors->any() ? 'true' : 'false' }},
            projectDropdownOpen: false,
            projects: @js($availableProjects->values()),
            selectedProjectId: {{ $defaultProject ? (int) $defaultProject['id'] : 'null' }},
            campaignTitle: @js(old('title', $defaultProject['default_title'] ?? '')),
            get selectedProject() {
                return this.projects.find(p => p.id === Number(this.selectedProjectId)) || null;
            },
            selectProject(project) {
                this.selectedProjectId = project.id;
                this.campaignTitle = project.default_title;
                this.projectDropdownOpen = false;
            },
            formatRupiah(value) {
                return new Intl.NumberFormat('id-ID').format(Number(value || 0));
            }
        }" 
        class="space-y-8 pb-12"
    >

        <!-- FLASH SUCCESS ALERT -->
        @if (session('success'))
            <div class="max-w-4xl mx-auto p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center justify-between gap-3 shadow-xs">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                        </svg>
                    </div>
                    <p class="text-xs sm:text-sm font-semibold text-emerald-900">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- ========================================== -->
        <!-- 1. HERO SECTION: TITLE, SEARCH & QUICK CHIPS -->
        <!-- ========================================== -->
        <div class="max-w-4xl mx-auto text-center space-y-6 pt-4">
            <!-- Header Text -->
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#14171F] tracking-tight">
                    Portal Kemitraan &amp; Matching CSR Sekolah 3T
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Pastikan dana CSR Anda tepat sasaran untuk pendidikan daerah terpencil.
                </p>
            </div>

            <!-- Big Omni Search Box -->
            <form method="GET" action="{{ route('pendanaan.index') }}" class="relative max-w-3xl mx-auto">
                @if (!empty($activeFilter) && $activeFilter !== 'all')
                    <input type="hidden" name="filter" value="{{ $activeFilter }}">
                @endif
                <div class="flex items-center bg-white rounded-full p-1.5 shadow-md border border-slate-200/80 transition-all focus-within:ring-2 focus-within:ring-[#FF5C2A]/30 focus-within:border-[#FF5C2A]">
                    <!-- Search Icon -->
                    <div class="pl-4 pr-2 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                        </svg>
                    </div>

                    <!-- Input Field -->
                    <input 
                        type="text" 
                        name="q"
                        value="{{ $keyword ?? '' }}"
                        placeholder="Masukkan nama sekolah atau daerah..." 
                        class="w-full text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 bg-transparent border-0 focus:outline-none focus:ring-0 py-2.5 px-2"
                    />

                    <!-- Submit Button -->
                    <button type="submit" class="flex items-center gap-2 px-6 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white text-xs sm:text-sm font-bold rounded-full transition-all shadow-sm shrink-0 cursor-pointer">
                        <span>Cari</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- Quick Filter Chips -->
            <div class="flex flex-wrap items-center justify-center gap-2 pt-1 text-xs">
                <span class="text-[11px] font-bold text-slate-400 tracking-wider mr-1">Filter Cepat:</span>

                @php
                    $filterChips = [
                        'all' => 'Semua (' . ($counts['all'] ?? 0) . ')',
                        'rusak_berat' => 'Rusak Berat (' . ($counts['rusak_berat'] ?? 0) . ')',
                        'under_50m' => 'Kebutuhan < Rp 50 Jt',
                        'bantuan_fisik' => 'Bantuan Fisik',
                    ];
                @endphp

                @foreach ($filterChips as $filterKey => $filterLabel)
                    <a 
                        href="{{ route('pendanaan.index', array_filter(['filter' => $filterKey === 'all' ? null : $filterKey, 'q' => $keyword ?? null])) }}"
                        class="{{ ($activeFilter ?? 'all') === $filterKey
                            ? 'px-4 py-1.5 rounded-full bg-[#14171F] text-white font-semibold shadow-xs cursor-pointer'
                            : 'flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-medium border border-slate-200/80 shadow-2xs transition-colors cursor-pointer' }}"
                    >
                        {{ $filterLabel }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. QUICK ACTION TILES (3 HORIZONTAL CARDS) -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Tile 1: Buat Pendanaan Bantuan -->
            <div @click="createModalOpen = true" class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all cursor-pointer group">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-700 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#FF5C2A] transition-colors">
                            Buat Pendanaan Bantuan
                        </h3>
                        <!-- <span class="px-1.5 py-0.2 text-[10px] font-bold bg-slate-100 text-slate-600 rounded">28</span> -->
                    </div>
                </div>
                <div class="text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>

            <!-- Tile 2: Pencairan Anggaran -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all cursor-pointer group">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100/80 flex items-center justify-center text-amber-600 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#FF5C2A] transition-colors">
                            Pencairan Anggaran
                        </h3>
                    </div>
                </div>
                <div class="text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>

            <!-- Tile 3: Wall of Donors & Laporan -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all cursor-pointer group">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 border border-teal-100/80 flex items-center justify-center text-teal-600 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#FF5C2A] transition-colors">
                            Wall of Donors &amp; Laporan
                        </h3>
                    </div>
                </div>
                <div class="text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- 3. SECTION: KAMPANYE PALING MENDESAK PEKAN INI -->
        <!-- ========================================== -->
        <div id="semua-kampanye" class="space-y-4 pt-2">
            
            <!-- Section Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FF5C2A]"></span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Kampanye Paling Mendesak Pekan Ini
                    </h2>
                </div>

                <a href="{{ route('pendanaan.index') }}" class="text-xs sm:text-sm font-bold text-[#FF5C2A] hover:text-[#e04f22] transition-colors">
                    Lihat Semua {{ $counts['all'] ?? 0 }} Kampanye
                </a>
            </div>

            <!-- Campaign Cards Grid (3 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                @forelse ($campaigns as $campaign)
                    @php
                        $isNearGoal = $campaign['progress_pct'] >= 70;
                        $project = $campaign['project'];
                    @endphp
                    <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                        <div>
                            <!-- Image Container with Badges -->
                            <div class="relative h-48 w-full bg-slate-200 overflow-hidden">
                                <img 
                                    src="{{ $project['image_url'] }}" 
                                    alt="{{ $project['school_name'] }}" 
                                    class="w-full h-full object-cover"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-900/10 to-transparent"></div>

                                <!-- Floating Damage Badge (Top Left) -->
                                <div class="absolute top-3.5 left-3.5">
                                    <span class="px-3 py-1 bg-[#14171F]/95 backdrop-blur-xs text-white text-[11px] font-bold rounded-full shadow-sm">
                                        {{ $project['badge_label'] }}
                                    </span>
                                </div>

                                <!-- Location Pill (Bottom Left) -->
                                <div class="absolute bottom-3 left-3.5 flex items-center gap-1 text-[11px] font-medium text-white/95 drop-shadow-xs">
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                    </svg>
                                    <span>{{ $project['region_short'] ?? $project['location'] }}</span>
                                </div>
                            </div>

                            <!-- Card Body -->
                            <div class="p-5 space-y-4">
                                <!-- School Name & Needs -->
                                <div class="space-y-1.5">
                                    <h3 class="text-base font-bold text-slate-900 tracking-tight leading-snug">
                                        {{ $project['school_name'] }}
                                    </h3>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        {{ $campaign['title'] }}
                                    </p>
                                </div>

                                <!-- Target & Progress Box -->
                                <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                    <div class="flex items-center justify-between text-xs font-semibold">
                                        <span class="text-slate-500 text-[11px]">Target RAB SNI:</span>
                                        <span class="text-slate-900 font-extrabold">{{ $campaign['target_formatted'] }}</span>
                                    </div>

                                    <!-- Progress Bar -->
                                    <div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden">
                                        <div class="{{ $isNearGoal ? 'bg-emerald-500' : 'bg-[#FF5C2A]' }} h-2 rounded-full transition-all" style="width: {{ $campaign['progress_pct'] }}%"></div>
                                    </div>

                                    <div class="flex items-center justify-between text-[11px] pt-0.5">
                                        <span class="font-bold {{ $isNearGoal ? 'text-emerald-600' : 'text-[#FF5C2A]' }}">
                                            Terkumpul {{ $campaign['progress_pct'] }}%
                                        </span>
                                        <span class="text-slate-400 font-medium">
                                            Sisa {{ $campaign['remaining_formatted'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Action Footer -->
                        <div class="p-5 pt-0">
                            <button type="button" class="w-full flex items-center justify-center gap-2 py-3 px-4 {{ $isNearGoal ? 'bg-[#14171F] hover:bg-slate-800' : 'bg-[#FF5C2A] hover:bg-[#e04f22]' }} text-white font-bold text-xs sm:text-sm rounded-2xl transition-all shadow-xs cursor-pointer">
                                <span>{{ $isNearGoal ? 'Tutup Sisa Pendanaan' : 'Salurkan Komitmen CSR' }}</span>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-3xl border border-dashed border-slate-300 p-10 text-center space-y-3">
                        <p class="text-sm font-bold text-slate-800">Belum ada kampanye CSR yang sesuai dengan filter saat ini.</p>
                        <p class="text-xs text-slate-500">Klik tombol "Buat Pendanaan Bantuan" di atas untuk merilis proyek terverifikasi, atau reset pencarian.</p>
                        @if (!empty($keyword) || ($activeFilter ?? 'all') !== 'all')
                            <a href="{{ route('pendanaan.index') }}" class="inline-flex items-center px-4 py-2 bg-[#14171F] text-white text-xs font-bold rounded-full">
                                Reset Filter
                            </a>
                        @endif
                    </div>
                @endforelse

            </div>

        </div>

        <!-- ========================================== -->
        <!-- 4. MODAL: BUKA PROGRAM / KAMPANYE CSR BARU -->
        <!-- ========================================== -->
        <div 
            x-show="createModalOpen" 
            x-cloak
            @keydown.escape.window="createModalOpen = false"
            class="relative z-50" 
            aria-labelledby="modal-title" 
            role="dialog" 
            aria-modal="true"
        >
            <!-- Backdrop Overlay -->
            <div 
                x-show="createModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="createModalOpen = false"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            ></div>

            <!-- Modal Dialog Container -->
            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 sm:p-6 text-center">
                    <div 
                        x-show="createModalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        @click.outside="createModalOpen = false"
                        class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-100 p-6 sm:p-7 space-y-6"
                    >
                        <!-- Header -->
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <!-- Orange Document Icon Box -->
                                <div class="w-12 h-12 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FF5C2A] shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                    </svg>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2.5">
                                        <h2 id="modal-title" class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                                            Buka Program / Kampanye CSR Baru
                                        </h2>
                                        
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed">
                                        Hubungkan proyek renovasi fisik sekolah 3T terverifikasi lapangan ke katalog matching CSR.
                                    </p>
                                </div>
                            </div>

                            <!-- Close Button (X) -->
                            <button 
                                type="button" 
                                @click="createModalOpen = false" 
                                class="w-8 h-8 rounded-full flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-400 hover:text-slate-600 transition-colors shrink-0 cursor-pointer"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Form Content -->
                        <form method="POST" action="{{ route('pendanaan.store') }}" class="space-y-5">
                            @csrf

                            @if ($errors->any())
                                <div class="p-3.5 bg-rose-50 border border-rose-200 rounded-2xl text-xs text-rose-700 space-y-1">
                                    <p class="font-bold text-rose-800">Mohon periksa kembali isian formulir:</p>
                                    <ul class="list-disc list-inside space-y-0.5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- 1. Hubungkan Proyek Sasaran Terverifikasi -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-800">
                                        1. Hubungkan Proyek Sasaran Terverifikasi<span class="text-[#FF5C2A] font-mono">*</span>
                                    </label>
                                    <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-600">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                        </svg>
                                        <span>Audit Lapangan Siap Rilis ({{ $availableProjects->count() }} Sekolah)</span>
                                    </span>
                                </div>

                                <input type="hidden" name="project_id" :value="selectedProjectId ?? ''">

                                <!-- Custom Reactive Select Dropdown -->
                                <div class="relative" @click.outside="projectDropdownOpen = false">
                                    <button 
                                        type="button"
                                        @click="if (projects.length > 0) projectDropdownOpen = !projectDropdownOpen"
                                        class="w-full flex items-center justify-between bg-white border border-slate-200/90 rounded-2xl py-3 px-4 text-xs sm:text-sm font-semibold text-slate-800 shadow-2xs cursor-pointer hover:border-slate-300 transition-colors text-left"
                                    >
                                        <span class="truncate" x-text="selectedProject ? `${selectedProject.project_code} — ${selectedProject.school_name} (${selectedProject.summary_tag} - RAB SNI Rp ${formatRupiah(selectedProject.rab_amount)})` : 'Seluruh proyek terverifikasi sudah memiliki kampanye aktif'"></span>
                                        <svg class="w-4 h-4 text-slate-400 shrink-0 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                        </svg>
                                    </button>

                                    <!-- Dropdown List -->
                                    <div 
                                        x-show="projectDropdownOpen" 
                                        x-cloak
                                        x-transition
                                        class="absolute z-40 left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-xl border border-slate-200/90 py-1.5 max-h-56 overflow-y-auto"
                                    >
                                        <template x-for="proj in projects" :key="proj.id">
                                            <button 
                                                type="button" 
                                                @click="selectProject(proj)"
                                                :class="proj.id === Number(selectedProjectId) ? 'bg-orange-50/80 text-[#FF5C2A]' : 'text-slate-700 hover:bg-slate-50'"
                                                class="w-full text-left px-4 py-2.5 text-xs sm:text-sm font-semibold flex items-center justify-between gap-2 transition-colors cursor-pointer"
                                            >
                                                <span class="truncate" x-text="`${proj.project_code} — ${proj.school_name} (${proj.summary_tag})`"></span>
                                                <span class="text-[11px] font-bold shrink-0 text-slate-500" x-text="`Rp ${formatRupiah(proj.rab_amount)}`"></span>
                                            </button>
                                        </template>
                                    </div>
                                </div>

                                <!-- Dynamic Project Preview Box -->
                                <div x-show="selectedProject" class="rounded-2xl border border-slate-200/80 bg-white p-3.5 sm:p-4 shadow-2xs flex flex-col sm:flex-row gap-4 items-start sm:items-center">
                                    <img 
                                        :src="selectedProject?.image_url" 
                                        :alt="selectedProject?.school_name || 'Preview Kondisi Sekolah'" 
                                        class="w-full sm:w-32 h-24 sm:h-20 rounded-xl object-cover shrink-0 bg-slate-100"
                                    />
                                    <div class="flex-1 space-y-1.5 w-full">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold text-[#FF5C2A]">
                                                NPSN: <span x-text="selectedProject?.npsn"></span> • Kategori 3T: <span x-text="selectedProject?.category_3t_label"></span>
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-[#FF5C2A] border border-orange-200/60">
                                                Tingkat Rusak: <span x-text="selectedProject?.damage_pct"></span>% (<span x-text="selectedProject?.severity_label"></span>)
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-relaxed">
                                            Lokasi: <span x-text="selectedProject?.location"></span>. <span x-text="selectedProject?.verifier_note"></span>
                                        </p>
                                        <div class="flex flex-wrap items-center gap-4 text-[11px] pt-0.5">
                                            <span class="text-slate-800 font-bold">
                                                Siswa Terdampak: <span class="font-normal text-slate-600"><span x-text="selectedProject?.affected_students"></span> Anak</span>
                                            </span>
                                            <span class="text-slate-800 font-bold">
                                                RAB Audit PUPR: <span class="font-normal text-slate-600">Rp <span x-text="formatRupiah(selectedProject?.rab_amount)"></span></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 2. Judul Kampanye / Program Bantuan -->
                            <div class="space-y-1.5">
                                <label for="campaign_title" class="block text-xs font-bold text-slate-800">
                                    2. Judul Kampanye / Program Bantuan<span class="text-[#FF5C2A] font-mono">*</span>
                                </label>
                                <input 
                                    id="campaign_title"
                                    name="title"
                                    type="text" 
                                    x-model="campaignTitle"
                                    required
                                    placeholder="Masukkan judul kampanye CSR..."
                                    class="w-full text-xs sm:text-sm font-semibold text-slate-800 bg-white border border-slate-200/90 rounded-2xl py-3 px-4 focus:ring-2 focus:ring-[#FF5C2A]/20 focus:border-[#FF5C2A] focus:outline-none shadow-2xs transition-all"
                                />
                                <p class="text-[11px] text-slate-400">
                                    Judul ini akan menjadi headline utama pada Katalog Matching CSR Korporasi dan laporan publik Ditjen.
                                </p>
                            </div>

                            <!-- 3 & 4. Target Pagu Pendanaan & Batas Waktu Penggalangan -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                
                                <!-- 3. Target Pagu Pendanaan -->
                                <div class="space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-xs font-bold text-slate-800">
                                            3. Target Pagu Pendanaan<span class="text-[#FF5C2A] font-mono">*</span>
                                        </label>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">
                                            Terkunci Sesuai RAB SNI
                                        </span>
                                    </div>
                                    <div class="flex items-center bg-slate-50/70 border border-slate-200/90 rounded-2xl px-4 py-2.5 shadow-2xs h-[48px]">
                                        <span class="text-sm font-bold text-slate-400 mr-2 shrink-0">Rp</span>
                                        <input 
                                            type="text" 
                                            readonly 
                                            :value="formatRupiah(selectedProject?.rab_amount)" 
                                            class="w-full bg-transparent border-0 p-0 text-base sm:text-lg font-black text-slate-900 focus:ring-0 focus:outline-none"
                                        />
                                    </div>
                                    <p class="text-[10px] text-slate-400 leading-relaxed">
                                        Pagu otomatis dikunci sesuai validasi audit insinyur PUPR No: <span x-text="selectedProject?.rab_doc_num || '440/BAP-SARPRAS/2026'"></span>.
                                    </p>
                                </div>

                                <!-- 4. Batas Waktu Penggalangan (Deadline) with Date Picker -->
                                <div 
                                    x-data="{
                                        open: false,
                                        selectedYear: {{ (int) $defaultDeadline->format('Y') }},
                                        selectedMonth: {{ (int) $defaultDeadline->format('n') - 1 }},
                                        selectedDay: {{ (int) $defaultDeadline->format('j') }},
                                        displayDate: '{{ $defaultDeadline->format('m/d/Y') }}',
                                        isoDate: '{{ $defaultDeadline->format('Y-m-d') }}',
                                        durationDays: {{ max(1, (int) now()->startOfDay()->diffInDays($defaultDeadline->copy()->startOfDay(), false)) }},
                                        monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                                        daysInMonth() {
                                            return new Date(this.selectedYear, this.selectedMonth + 1, 0).getDate();
                                        },
                                        firstDayOfWeek() {
                                            return new Date(this.selectedYear, this.selectedMonth, 1).getDay();
                                        },
                                        prevMonth() {
                                            if (this.selectedMonth === 0) {
                                                this.selectedMonth = 11;
                                                this.selectedYear--;
                                            } else {
                                                this.selectedMonth--;
                                            }
                                        },
                                        nextMonth() {
                                            if (this.selectedMonth === 11) {
                                                this.selectedMonth = 0;
                                                this.selectedYear++;
                                            } else {
                                                this.selectedMonth++;
                                            }
                                        },
                                        selectDay(day) {
                                            this.selectedDay = day;
                                            const mm = String(this.selectedMonth + 1).padStart(2, '0');
                                            const dd = String(day).padStart(2, '0');
                                            this.displayDate = `${mm}/${dd}/${this.selectedYear}`;
                                            this.isoDate = `${this.selectedYear}-${mm}-${dd}`;
                                            
                                            const targetDate = new Date(this.selectedYear, this.selectedMonth, day);
                                            const today = new Date();
                                            today.setHours(0, 0, 0, 0);
                                            const diffTime = targetDate.getTime() - today.getTime();
                                            const diffDays = Math.ceil(diffTime / (1000 * 3600 * 24));
                                            this.durationDays = diffDays > 0 ? diffDays : 0;
                                            
                                            this.open = false;
                                        },
                                        isSelected(day) {
                                            return this.selectedDay === day;
                                        }
                                    }"
                                    class="space-y-1.5 relative"
                                    @click.outside="open = false"
                                >
                                    <!-- Header Row -->
                                    <div class="flex items-center justify-between">
                                        <label class="block text-xs font-bold text-slate-800">
                                            4. Batas Waktu Penggalangan (Deadline)<span class="text-[#FF5C2A] font-mono">*</span>
                                        </label>
                                        <span class="text-[10px] font-medium text-slate-400">
                                            Durasi Target: <span class="font-bold text-slate-600" x-text="durationDays + ' Hari'"></span>
                                        </span>
                                    </div>

                                    <!-- Date Picker Trigger Field & Popover Wrapper -->
                                    <div class="relative">
                                        <div 
                                            @click="open = !open" 
                                            class="flex items-center justify-between bg-white border border-slate-200/90 rounded-2xl px-4 py-2.5 shadow-2xs hover:border-[#FF5C2A]/50 focus-within:ring-2 focus-within:ring-[#FF5C2A]/20 focus-within:border-[#FF5C2A] transition-all cursor-pointer h-[48px] group"
                                        >
                                            <span class="text-xs sm:text-sm font-semibold text-slate-800 tracking-wide" x-text="displayDate"></span>
                                            <input type="hidden" name="deadline" :value="isoDate" />
                                            
                                            <!-- Calendar Icon Pill -->
                                            <div class="w-8 h-8 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FF5C2A] group-hover:bg-[#FF5C2A] group-hover:text-white group-hover:border-[#FF5C2A] transition-all shrink-0">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>

                                        <!-- Interactive Calendar Dropdown Popover (Opens upwards to top-left) -->
                                        <div 
                                            x-show="open" 
                                            x-cloak
                                            x-transition:enter="transition ease-out duration-150"
                                            x-transition:enter-start="opacity-0 translate-y-2 scale-98"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave-end="opacity-0 translate-y-2 scale-98"
                                            class="absolute z-50 bottom-full mb-2 right-0 w-72 bg-white rounded-2xl shadow-2xl border border-slate-200/90 p-4 space-y-3"
                                        >
                                            <!-- Calendar Month/Year Header -->
                                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                                <button 
                                                    type="button" 
                                                    @click.stop="prevMonth()" 
                                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                                                    </svg>
                                                </button>

                                                <span class="text-xs font-bold text-slate-900 tracking-tight" x-text="monthNames[selectedMonth] + ' ' + selectedYear"></span>

                                                <button 
                                                    type="button" 
                                                    @click.stop="nextMonth()" 
                                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Day of Week Headers -->
                                            <div class="grid grid-cols-7 gap-1 text-center text-[10px] font-bold text-slate-400">
                                                <span>Min</span>
                                                <span>Sen</span>
                                                <span>Sel</span>
                                                <span>Rab</span>
                                                <span>Kam</span>
                                                <span>Jum</span>
                                                <span>Sab</span>
                                            </div>

                                            <!-- Days Grid -->
                                            <div class="grid grid-cols-7 gap-1 text-center">
                                                <!-- Start Offset Blanks -->
                                                <template x-for="blank in firstDayOfWeek()" :key="'blank-' + blank">
                                                    <span class="w-8 h-8"></span>
                                                </template>

                                                <!-- Days in Month -->
                                                <template x-for="day in daysInMonth()" :key="day">
                                                    <button 
                                                        type="button" 
                                                        @click.stop="selectDay(day)"
                                                        :class="isSelected(day) 
                                                            ? 'bg-[#FF5C2A] text-white font-bold shadow-xs' 
                                                            : 'text-slate-700 hover:bg-orange-50 hover:text-[#FF5C2A] font-medium'"
                                                        class="w-8 h-8 mx-auto rounded-xl flex items-center justify-center text-xs transition-colors cursor-pointer"
                                                        x-text="day"
                                                    ></button>
                                                </template>
                                            </div>

                                            <!-- Footer / Quick Select -->
                                            <div class="flex items-center justify-between pt-2 border-t border-slate-100 text-[11px]">
                                                <button 
                                                    type="button" 
                                                    @click.stop="
                                                        const t = new Date();
                                                        selectedYear = t.getFullYear();
                                                        selectedMonth = t.getMonth();
                                                        selectDay(t.getDate());
                                                    "
                                                    class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                                                >
                                                    Hari Ini
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click.stop="open = false" 
                                                    class="text-slate-400 hover:text-slate-600 font-medium cursor-pointer"
                                                >
                                                    Tutup
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Helper Text -->
                                    <p class="text-[10px] text-slate-400 leading-relaxed">
                                        Jika pagu tidak terpenuhi sampai tenggat, sisa dana dialokasikan via Pooling Buffer Kemendikbud.
                                    </p>
                                </div>

                            </div>

                            <!-- Modal Footer -->
                            <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4 border-t border-slate-100">
                                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                                    <button 
                                        type="button" 
                                        @click="createModalOpen = false" 
                                        class="px-5 py-2.5 rounded-full text-xs sm:text-sm font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors cursor-pointer"
                                    >
                                        Batal
                                    </button>
                                    <button 
                                        type="submit" 
                                        :disabled="!selectedProject"
                                        :class="!selectedProject ? 'opacity-50 cursor-not-allowed' : 'hover:bg-[#e04f22] cursor-pointer'"
                                        class="flex items-center justify-center gap-2 px-6 py-2.5 bg-[#FF5C2A] text-white text-xs sm:text-sm font-bold rounded-full transition-all shadow-sm"
                                    >
                                        <span>Publikasikan Kampanye ke Katalog CSR</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>

