<x-layouts.app active="renovasi" title="EduInfra Tracker - Direktori Kontraktor LPJK & Rekanan 3T">

    @if(session('success'))
        <div role="status" class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-center justify-between text-xs font-bold shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-700 text-white flex items-center justify-center shrink-0 text-[11px]">✓</div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" aria-label="Tutup notifikasi" class="cursor-pointer text-emerald-700 hover:text-emerald-950 text-base font-black leading-none">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div role="alert" class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs font-semibold shadow-2xs">
            <div class="font-bold mb-1">Periksa kembali data registrasi rekanan yang diinputkan:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- HEADER SECTION (SCREEN DIREKTORI KONTRAKTOR LPJK) -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-xs font-semibold text-slate-600 mb-2">
                <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition-colors">Beranda</a>
                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <a href="{{ route('renovasi.index') }}" class="hover:text-slate-900 transition-colors">Renovasi &amp; RAB</a>
                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span class="text-[#C83E13] font-bold" aria-current="page">Direktori Kontraktor LPJK</span>
            </nav>

            <h1 class="text-2xl font-black text-[#14171F] tracking-tight">Manajemen Renovasi &amp; Kontraktor 3T</h1>
            <p class="text-xs text-slate-600 mt-0.5 max-w-[65ch]">Registrasi rekanan pelaksana LPJK, verifikasi masa berlaku SBU, pemetaan jangkauan wilayah 3T, dan penugasan proyek.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="button"
                    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-tambah-vendor' }))"
                    class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#C83E13] hover:bg-[#A9320D] text-white text-xs font-bold shadow-xs transition-all active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>Registrasi Rekanan LPJK</span>
            </button>

            <a href="{{ route('renovasi.index') }}"
               class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold shadow-2xs transition-colors">
                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                <span>Daftar Proyek Renovasi</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI CARDS (FIGMA SCREEN DIREKTORI KONTRAKTOR LPJK) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.kpi-card
            title="Total Rekanan Terdaftar"
            :value="$kpis['total_vendors']['val'] ?? '0'"
            :unit="$kpis['total_vendors']['unit'] ?? 'Kontraktor'"
            icon="building"
            :badgeText="$kpis['total_vendors']['badge'] ?? '0 Aktif'"
            badgeVariant="orange-pill"
        />

        <x-ui.kpi-card
            title="Rating Performa Konstruksi"
            :value="$kpis['avg_rating']['val'] ?? '0.00'"
            :unit="$kpis['avg_rating']['unit'] ?? '/ 5.0'"
            icon="alert"
            :badgeText="$kpis['avg_rating']['badge'] ?? 'Ketepatan BAST'"
            badgeVariant="success"
        />

        <x-ui.kpi-card
            title="Kapasitas Proyek Berjalan"
            :value="$kpis['active_capacity']['val'] ?? '0'"
            :unit="$kpis['active_capacity']['unit'] ?? 'Proyek'"
            icon="link"
            :badgeText="$kpis['active_capacity']['badge'] ?? 'Siap Ditugaskan'"
            badgeVariant="success"
        />

        <x-ui.kpi-card
            title="Wilayah Jangkauan 3T"
            :value="$kpis['region_coverage']['val'] ?? '0'"
            :unit="$kpis['region_coverage']['unit'] ?? 'Kab/Kota'"
            icon="building"
            :badgeText="$kpis['region_coverage']['badge'] ?? 'Klaster Wilayah'"
            badgeVariant="orange-pill"
        />
    </div>

    <x-renovasi.tabs-nav activeTab="vendors" :vendorCount="$counts['all'] ?? 0" />

    <!-- FILTER BAR & PILLS (FIGMA SCREEN DIREKTORI KONTRAKTOR) -->
    <div class="space-y-3 mb-6">
        <form method="GET" action="{{ route('renovasi.vendors') }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-lg flex items-center gap-2">
                <div class="relative flex-1">
                    <label for="vendor-search" class="sr-only">Cari kontraktor, nomor SBU, atau NIB</label>
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="vendor-search"
                           type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari nama kontraktor (PT/CV), nomor SBU/NIB, atau wilayah..."
                           class="w-full pl-10 pr-4 py-2 text-xs font-medium rounded-full bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-[#C83E13] focus:outline-hidden transition-colors">
                </div>
                <button type="submit" class="cursor-pointer px-4 py-2 rounded-full bg-[#14171F] hover:bg-slate-800 text-white text-xs font-bold transition-colors shrink-0">
                    Cari
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <label for="filter-vendor-grade" class="sr-only">Filter Kualifikasi Grade</label>
                <select id="filter-vendor-grade"
                        name="grade"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800">
                    <option value="">Kualifikasi: Semua Grade</option>
                    <option value="small" @selected($grade === 'small')>Grade K1-K3 / Kecil</option>
                    <option value="medium" @selected($grade === 'medium')>Grade M1-M2 / Menengah</option>
                    <option value="large" @selected($grade === 'large')>Grade B1 / Besar</option>
                </select>

                <label for="filter-vendor-region" class="sr-only">Filter Wilayah 3T</label>
                <select id="filter-vendor-region"
                        name="region"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800 max-w-[215px]">
                    <option value="">Wilayah 3T: Semua Klaster</option>
                    <option value="3t" @selected($region === '3t')>Seluruh Klaster 3T</option>
                    <option value="tier1" @selected($region === 'tier1')>3T Tier-1 (Perbatasan/Kepulauan)</option>
                    <option value="tier2" @selected($region === 'tier2')>3T Tier-2 (Pedalaman/Terisolir)</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}" @selected((string) $region === (string) $reg->id)>
                            {{ $reg->name }}
                        </option>
                    @endforeach
                </select>

                <label for="filter-vendor-status" class="sr-only">Filter Status Rekanan</label>
                <select id="filter-vendor-status"
                        name="status"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800">
                    <option value="">Status: Semua Rekanan</option>
                    <option value="active" @selected($status === 'active')>Aktif Terverifikasi LPJK</option>
                    <option value="sbu_review" @selected($status === 'sbu_review')>Perlu Perpanjangan SBU</option>
                    <option value="blacklist" @selected($status === 'blacklist')>Blacklist / Ditangguhkan</option>
                </select>

                <label for="filter-vendor-specialty" class="sr-only">Filter Spesialisasi</label>
                <select id="filter-vendor-specialty"
                        name="specialty"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800">
                    <option value="">Spesialis: Semua Tipe</option>
                    <option value="Atap" @selected($specialty === 'Atap')>Spesialis Atap &amp; Baja Ringan</option>
                    <option value="Sanitasi" @selected($specialty === 'Sanitasi')>Spesialis Sanitasi &amp; Air Bersih</option>
                    <option value="Struktur" @selected($specialty === 'Struktur')>Struktur RKB &amp; Pondasi</option>
                    <option value="Laboratorium" @selected($specialty === 'Laboratorium')>Laboratorium &amp; Kelistrikan</option>
                </select>

                @if($search || $grade || $region || $status || $specialty)
                    <a href="{{ route('renovasi.vendors') }}" class="px-3 py-2 text-xs font-bold text-[#C83E13] hover:underline">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Filter Cepat Pills -->
        <div class="flex flex-wrap items-center gap-2 text-xs">
            <span class="text-slate-600 font-semibold mr-1">Filter Cepat:</span>
            <a href="{{ route('renovasi.vendors') }}"
               class="px-3.5 py-1.5 rounded-full font-bold transition-colors {{ !$grade && !$status ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Semua Rekanan ({{ $counts['all'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.vendors', ['grade' => 'small']) }}"
               class="px-3.5 py-1.5 rounded-full font-bold transition-colors {{ $grade === 'small' ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Grade K1-K3 / Kecil ({{ $counts['small'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.vendors', ['grade' => 'medium']) }}"
               class="px-3.5 py-1.5 rounded-full font-bold transition-colors {{ $grade === 'medium' ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Grade M1-M2 / Menengah ({{ $counts['medium'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.vendors', ['grade' => 'large']) }}"
               class="px-3.5 py-1.5 rounded-full font-bold transition-colors {{ $grade === 'large' ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Grade B1 / Besar ({{ $counts['large'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.vendors', ['status' => 'sbu_review']) }}"
               class="px-3.5 py-1.5 rounded-full font-bold transition-colors {{ $status === 'sbu_review' ? 'bg-amber-800 text-white' : 'bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200' }}">
                Perlu Perpanjangan SBU ({{ $counts['sbu_review'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.vendors', ['status' => 'blacklist']) }}"
               class="px-3.5 py-1.5 rounded-full font-bold transition-colors {{ $status === 'blacklist' ? 'bg-rose-800 text-white' : 'bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200' }}">
                Blacklist / Nonaktif ({{ $counts['blacklist'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- VENDOR DIRECTORY TABLE (FIGMA SCREEN DIREKTORI KONTRAKTOR LPJK) -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                        <th scope="col" class="py-3.5 px-4">NAMA PERUSAHAAN &amp; LEGALITAS LPJK</th>
                        <th scope="col" class="py-3.5 px-4">KUALIFIKASI &amp; PAGU MAKS</th>
                        <th scope="col" class="py-3.5 px-4">CAKUPAN WILAYAH 3T &amp; LOGISTIK</th>
                        <th scope="col" class="py-3.5 px-4">PERFORMA &amp; REKAM JEJAK</th>
                        <th scope="col" class="py-3.5 px-4 text-right">STATUS &amp; TINDAKAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($vendors as $vendor)
                    @php
                        $activeProj = $vendor->repairProjects->firstWhere('status', 'in_progress') ?? $vendor->repairProjects->first();
                        $activeProjCount = $vendor->repairProjects->where('status', 'in_progress')->count();
                        $completedProjCount = max($vendor->review_count, $vendor->repairProjects->where('status', 'completed')->count());
                        $vendorJson = [
                            'id' => $vendor->id,
                            'company_name' => $vendor->company_name,
                            'entity_type' => $vendor->entity_type,
                            'license_num' => $vendor->license_num,
                            'nib' => $vendor->nib,
                            'contact_phone' => $vendor->contact_phone,
                            'service_region_id' => $vendor->service_region_id,
                            'grade' => $vendor->grade,
                            'qualification_label' => $vendor->qualification_label,
                            'max_contract_limit' => (float) $vendor->max_contract_limit,
                            'sbu_status' => $vendor->sbu_status,
                            'sbu_valid_until' => $vendor->sbu_valid_until?->format('Y-m-d'),
                            'specialty' => $vendor->specialty,
                            'logistics_note' => $vendor->logistics_note,
                            'lead_engineer' => $vendor->lead_engineer,
                            'engineer_role' => $vendor->engineer_role,
                            'rating' => (float) $vendor->rating,
                            'is_active' => (bool) $vendor->is_active,
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors {{ !$vendor->is_active ? 'bg-rose-50/20' : '' }}">

                        <!-- Col 1: Nama Perusahaan & Legalitas LPJK -->
                        <td class="py-4 px-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-black text-xs shrink-0 border {{ !$vendor->is_active ? 'bg-rose-100 text-rose-900 border-rose-200' : ($vendor->grade === 'B1' ? 'bg-slate-900 text-white border-slate-800' : 'bg-orange-50 text-[#C83E13] border-orange-200') }}">
                                    {{ $vendor->initials }}
                                </div>
                                <div>
                                    <div class="flex flex-wrap items-center gap-1.5">
                                        <span class="font-extrabold text-sm text-[#14171F]">{{ $vendor->company_name }}</span>
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $vendor->entity_type }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 mt-0.5 font-mono">
                                        NIB: {{ $vendor->nib ?: '-' }} &bull; <strong class="text-blue-800">{{ $vendor->license_num }}</strong>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                        @if(!$vendor->is_active)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-900 border border-rose-200">
                                                Izin Ditangguhkan (Blacklist)
                                            </span>
                                        @elseif($vendor->sbu_status === 'review')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-200">
                                                SBU Masa Review
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                                ✓ LPJK Terverifikasi
                                            </span>
                                        @endif

                                        @if($vendor->specialty)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                                {{ $vendor->specialty }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Col 2: Kualifikasi & Pagu Maks -->
                        <td class="py-4 px-4">
                            <div class="flex items-start gap-2">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-black bg-orange-50 text-[#C83E13] border border-orange-200 shrink-0">
                                    {{ $vendor->grade }}
                                </span>
                                <div>
                                    <span class="font-bold text-slate-900 block leading-snug">{{ $vendor->qualification_label }}</span>
                                    <span class="text-[11px] text-slate-600 block mt-0.5">{{ $vendor->formatted_max_limit }}</span>
                                    <span class="text-[10px] font-bold block mt-0.5 {{ !$vendor->is_active ? 'text-rose-800' : ($vendor->sbu_status === 'review' ? 'text-amber-800' : 'text-emerald-800') }}">
                                        {{ $vendor->sbu_validity_text }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Col 3: Cakupan Wilayah 3T & Logistik -->
                        <td class="py-4 px-4 max-w-[240px]">
                            <div class="flex items-start gap-1.5">
                                <svg class="w-3.5 h-3.5 text-[#C83E13] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <span class="font-bold text-slate-900 block">{{ $vendor->region_name }}</span>
                                    <span class="text-[10px] text-slate-600 block">{{ $vendor->region_cluster_label }}</span>
                                    @if($vendor->logistics_note)
                                        <span class="text-[10px] text-blue-800 font-semibold block mt-1 leading-snug">
                                            {{ $vendor->logistics_note }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- Col 4: Performa & Rekam Jejak -->
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-1.5 mb-1">
                                <span class="text-amber-500 font-black text-xs" aria-hidden="true">★★★★★</span>
                                <span class="font-black text-slate-900 text-xs">{{ number_format((float) $vendor->rating, 2, '.', '') }}</span>
                                <span class="text-[10px] text-slate-600">({{ $vendor->review_count }} Ulasan)</span>
                            </div>
                            <div class="text-[11px] text-slate-700">
                                <strong>{{ $completedProjCount }} Selesai</strong> &bull;
                                <span>{{ $activeProjCount }} Berjalan</span>
                                @if($activeProj)
                                    : <a href="{{ route('renovasi.projects.show', $activeProj->project_code) }}" class="font-bold text-blue-800 hover:underline">{{ \Illuminate\Support\Str::limit($activeProj->school?->name ?? $activeProj->project_code, 22) }}</a>
                                @endif
                            </div>
                            <div class="w-36 bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200 mt-1.5">
                                <div class="h-full {{ $vendor->on_time_pct >= 95 ? 'bg-emerald-600' : 'bg-amber-600' }} rounded-full" style="width: {{ min(100, $vendor->on_time_pct) }}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-600 block mt-0.5">Ketepatan BAST: {{ number_format((float) $vendor->on_time_pct, 1) }}%</span>
                        </td>

                        <!-- Col 5: Status & Tindakan -->
                        <td class="py-4 px-4 text-right">
                            <div class="mb-2">
                                <span class="font-bold text-slate-900 block">{{ $vendor->lead_engineer ?: 'Tim Teknis Rekanan' }}</span>
                                <span class="text-[10px] text-slate-600 block">{{ $vendor->engineer_role ?: 'PJBU Konstruksi' }} &bull; {{ $vendor->contact_phone }}</span>
                            </div>

                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <button type="button"
                                        onclick='window.dispatchEvent(new CustomEvent("open-vendor-modal", { detail: { mode: "edit", vendor: @json($vendorJson) } }))'
                                        class="cursor-pointer px-3 py-1.5 rounded-full bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 text-xs font-bold transition-colors">
                                    Detail / Ubah
                                </button>

                                @if($vendor->is_active)
                                    <button type="button"
                                            onclick='window.dispatchEvent(new CustomEvent("open-vendor-modal", { detail: { mode: "assign", vendor: @json($vendorJson) } }))'
                                            class="cursor-pointer px-3 py-1.5 rounded-full {{ $vendor->sbu_status === 'review' ? 'bg-slate-200 hover:bg-slate-300 text-slate-900' : 'bg-[#C83E13] hover:bg-[#A9320D] text-white' }} text-xs font-bold transition-colors shadow-2xs">
                                        {{ $vendor->sbu_status === 'review' ? 'Perbarui SBU' : 'Tugaskan' }}
                                    </button>
                                @endif

                                <form action="{{ route('renovasi.vendors.blacklist', $vendor->id) }}" method="POST" class="inline m-0"
                                      onsubmit="return confirm('Ubah status izin operasional rekanan {{ addslashes($vendor->company_name) }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            title="{{ $vendor->is_active ? 'Tangguhkan / Blacklist Rekanan' : 'Pulihkan Status Aktif Rekanan' }}"
                                            class="cursor-pointer px-2.5 py-1.5 rounded-full {{ $vendor->is_active ? 'bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200' }} text-[11px] font-bold transition-colors">
                                        {{ $vendor->is_active ? 'Blacklist' : 'Pulihkan' }}
                                    </button>
                                </form>

                                <form action="{{ route('renovasi.vendors.destroy', $vendor->id) }}" method="POST" class="inline m-0"
                                      onsubmit="return confirm('Hapus permanen data rekanan {{ addslashes($vendor->company_name) }} dari direktori?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            title="Hapus Rekanan dari Direktori"
                                            aria-label="Hapus {{ $vendor->company_name }}"
                                            class="cursor-pointer p-1.5 rounded-full bg-white hover:bg-rose-50 text-slate-600 hover:text-rose-800 border border-slate-200 transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 px-4 text-center">
                            <p class="text-sm font-bold text-slate-900">Tidak Ada Rekanan Kontraktor yang Sesuai Kriteria</p>
                            <p class="text-xs text-slate-600 mt-1">Ubah filter pencarian atau registrasikan kontraktor LPJK baru ke dalam direktori.</p>
                            <button type="button"
                                    onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-tambah-vendor' }))"
                                    class="cursor-pointer mt-3 px-4 py-2 rounded-full bg-[#C83E13] hover:bg-[#A9320D] text-white text-xs font-bold">
                                Registrasi Rekanan LPJK Baru
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGINATION FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4 px-2 text-xs text-slate-600 mb-6">
        <div>
            Menampilkan <strong class="text-slate-900">{{ $vendors->firstItem() ?? 0 }}-{{ $vendors->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-900">{{ $vendors->total() }}</strong> kontraktor terakreditasi LPJK
        </div>
        @if($vendors->hasPages())
            <div class="flex items-center gap-1.5">
                @if($vendors->onFirstPage())
                    <span class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center font-bold" aria-disabled="true">&lt;</span>
                @else
                    <a href="{{ $vendors->previousPageUrl() }}" class="w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center font-bold" aria-label="Halaman sebelumnya">&lt;</a>
                @endif

                @foreach($vendors->getUrlRange(1, $vendors->lastPage()) as $page => $url)
                    @if($page == $vendors->currentPage())
                        <span aria-current="page" class="w-8 h-8 rounded-full bg-[#C83E13] text-white flex items-center justify-center font-black shadow-2xs">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center font-bold">{{ $page }}</a>
                    @endif
                @endforeach

                @if($vendors->hasMorePages())
                    <a href="{{ $vendors->nextPageUrl() }}" class="px-3 h-8 rounded-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center font-bold">Selanjutnya &gt;</a>
                @else
                    <span class="px-3 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center font-bold" aria-disabled="true">Selanjutnya &gt;</span>
                @endif
            </div>
        @endif
    </div>

    <!-- BOTTOM COMPLIANCE BANNER (FIGMA SCREEN DIREKTORI KONTRAKTOR LPJK) -->
    <div class="p-5 bg-white rounded-3xl border border-slate-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Jaminan Kepatuhan Izin &amp; Integritas Rekanan LPJK</h2>
                <p class="text-xs text-slate-600 mt-0.5 max-w-[65ch] leading-relaxed">
                    Data izin usaha (NIB OSS RBA) dan Sertifikat Badan Usaha (SBU LPJK) diverifikasi terhadap cakupan wilayah Master Data 3T. Rekanan dengan status Blacklist dibekukan otomatis dari penugasan proyek renovasi baru.
                </p>
            </div>
        </div>
        <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-tambah-vendor' }))"
                class="cursor-pointer px-4 py-2.5 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-300 text-xs font-bold whitespace-nowrap transition-colors">
            + Tambah Rekanan Terverifikasi
        </button>
    </div>

    <!-- MODAL CREATE VENDOR (C) -->
    <x-renovasi.modals.create-vendor-modal :regions="$regions" :projects="$projects" />

    <!-- MODAL EDIT / ASSIGN VENDOR (U) -->
    <x-renovasi.modals.edit-vendor-modal :regions="$regions" :projects="$projects" />

</x-layouts.app>
