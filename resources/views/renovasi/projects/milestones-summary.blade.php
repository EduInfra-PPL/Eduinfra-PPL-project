<x-layouts.app active="renovasi" title="EduInfra Tracker - Monitoring Tahapan & Milestone">

    <x-renovasi.header subTitle="Monitoring Tahapan &amp; Milestone" />

    <!-- 4 KPI CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.kpi-card
            title="Total Tahapan Aktif"
            :value="$kpis['total_milestones']['val'] ?? '0'"
            :unit="$kpis['total_milestones']['unit'] ?? 'Milestone'"
            icon="building"
            :badgeText="$kpis['total_milestones']['badge'] ?? '0 Selesai'"
            badgeVariant="orange-pill"
        />

        <x-ui.kpi-card
            title="Deviasi Kurva S Rata-rata"
            :value="$kpis['deviasi_kurva']['val'] ?? '0%'"
            unit=""
            icon="alert"
            :badgeText="$kpis['deviasi_kurva']['badge'] ?? 'Sesuai Jadwal'"
            badgeVariant="success"
        />

        <x-ui.kpi-card
            title="Estimasi Termin Siap Rilis"
            :value="$kpis['escrow_cair']['val'] ?? 'Rp 0'"
            :unit="$kpis['escrow_cair']['unit'] ?? ''"
            icon="link"
            :badgeText="$kpis['escrow_cair']['badge'] ?? 'Proyek Memenuhi Target'"
            badgeVariant="warning-pill"
        />

        <x-ui.kpi-card
            title="Penyelesaian Komponen RAB"
            :value="$kpis['geotag_compliance']['val'] ?? '0%'"
            unit=""
            icon="building"
            :badgeText="$kpis['geotag_compliance']['badge'] ?? 'Item RAB Tuntas'"
            badgeVariant="success"
        />
    </div>

    <x-renovasi.tabs-nav activeTab="milestone" :projectCount="$counts['all'] ?? 0" />

    <!-- FILTER BAR & PILLS -->
    <div class="space-y-3 mb-6">
        <form method="GET" action="{{ route('renovasi.milestones') }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
            <div class="relative flex-1 max-w-lg flex items-center gap-2">
                <div class="relative flex-1">
                    <label for="milestone-search" class="sr-only">Cari proyek atau sekolah</label>
                    <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="milestone-search"
                           type="text"
                           name="search"
                           value="{{ $search }}"
                           placeholder="Cari kode proyek (PRI), nama sekolah, NPSN, atau kontraktor..."
                           class="w-full pl-10 pr-4 py-2 text-xs font-medium rounded-full bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-[#C83E13] focus:outline-hidden transition-colors">
                </div>
                <button type="submit" class="cursor-pointer px-4 py-2 rounded-full bg-[#14171F] hover:bg-slate-800 text-white text-xs font-bold transition-colors shrink-0">
                    Cari
                </button>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <label for="filter-termin" class="sr-only">Filter Tahapan Termin</label>
                <select id="filter-termin"
                        name="termin"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800">
                    <option value="">Semua Tahapan Termin</option>
                    <option value="termin1" @selected($termin === 'termin1')>Termin 1 (Pondasi &amp; Awal &lt;30%)</option>
                    <option value="termin2" @selected($termin === 'termin2')>Termin 2 (Struktur &amp; Fisik 30-89%)</option>
                    <option value="termin3" @selected($termin === 'termin3')>Termin 3 (Finishing &amp; BAST &ge;90%)</option>
                    <option value="escrow_ready" @selected($termin === 'escrow_ready')>Siap Otorisasi Escrow</option>
                    <option value="delayed" @selected($termin === 'delayed')>Deviasi / Terlambat</option>
                </select>

                <label for="filter-curve" class="sr-only">Filter Kurva S</label>
                <select id="filter-curve"
                        name="curve"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800">
                    <option value="">Semua Kurva S</option>
                    <option value="ahead" @selected($curve === 'ahead')>Ahead of Schedule</option>
                    <option value="ontrack" @selected($curve === 'ontrack')>On Track</option>
                    <option value="delay" @selected($curve === 'delay')>Perlu Akselerasi</option>
                </select>

                <label for="filter-milestone-region" class="sr-only">Filter Wilayah</label>
                <select id="filter-milestone-region"
                        name="region"
                        onchange="this.form.submit()"
                        class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800 max-w-[220px]">
                    <option value="">Semua Wilayah (Master Data)</option>
                    <option value="3t" @selected($region === '3t')>Seluruh Wilayah 3T</option>
                    <option value="tier1" @selected($region === 'tier1')>3T Tier-1</option>
                    <option value="tier2" @selected($region === 'tier2')>3T Tier-2</option>
                    <option value="non3t" @selected($region === 'non3t')>Reguler Non-3T</option>
                    @foreach($regions as $reg)
                        <option value="{{ $reg->id }}" @selected((string) $region === (string) $reg->id)>
                            {{ $reg->name }}
                        </option>
                    @endforeach
                </select>

                @if($search || $termin || $curve || $region)
                    <a href="{{ route('renovasi.milestones') }}" class="px-3 py-2 text-xs font-bold text-[#C83E13] hover:underline">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Filter Cepat Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('renovasi.milestones') }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ !$termin ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Semua Proyek ({{ $counts['all'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.milestones', ['termin' => 'termin1']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ $termin === 'termin1' ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Termin 1: Pondasi &amp; Awal ({{ $counts['termin1'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.milestones', ['termin' => 'termin2']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ $termin === 'termin2' ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Termin 2: Rangka &amp; Fisik ({{ $counts['termin2'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.milestones', ['termin' => 'termin3']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ $termin === 'termin3' ? 'bg-[#14171F] text-white shadow-2xs' : 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-300' }}">
                Termin 3: Finishing &amp; BAST ({{ $counts['termin3'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.milestones', ['termin' => 'escrow_ready']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ $termin === 'escrow_ready' ? 'bg-amber-800 text-white' : 'bg-amber-50 hover:bg-amber-100 text-amber-900 border border-amber-200' }}">
                Siap Otorisasi Escrow ({{ $counts['escrow_ready'] ?? 0 }})
            </a>
            <a href="{{ route('renovasi.milestones', ['termin' => 'delayed']) }}"
               class="px-3.5 py-1.5 rounded-full text-xs font-bold transition-colors {{ $termin === 'delayed' ? 'bg-rose-800 text-white' : 'bg-rose-50 hover:bg-rose-100 text-rose-900 border border-rose-200' }}">
                Deviasi / Terlambat ({{ $counts['delayed'] ?? 0 }})
            </a>
        </div>
    </div>

    <!-- MILESTONE DATA TABLE -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden mb-6">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                        <th scope="col" class="py-3.5 px-4">KODE PROYEK &amp; ZONASI</th>
                        <th scope="col" class="py-3.5 px-4">SEKOLAH &amp; LOKASI (MASTER DATA)</th>
                        <th scope="col" class="py-3.5 px-4">TAHAPAN AKTIF &amp; LINGKUP</th>
                        <th scope="col" class="py-3.5 px-4">PROGRES RIIL VS KURVA S</th>
                        <th scope="col" class="py-3.5 px-4">KOMPONEN RAB &amp; VERIFIKATOR</th>
                        <th scope="col" class="py-3.5 px-4">STATUS ESCROW</th>
                        <th scope="col" class="py-3.5 px-4 text-center">AKSI TERPADU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($projects as $project)
                    @php
                        $delta = round($project['progress_pct'] - $project['target_pct'], 1);
                        $completedRab = $project->costEstimates->where('is_completed', true)->count();
                        $totalRab = $project->costEstimates->count();
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-4 font-mono font-bold text-slate-800">
                            {{ $project['code'] }}
                            <span class="block text-[10px] text-slate-600 font-sans font-semibold mt-0.5">{{ $project['zonasi'] }}</span>
                            <span class="text-[10px] text-slate-600 font-sans block">{{ $project['vendor_name'] }}</span>
                        </td>

                        <td class="py-4 px-4">
                            <a href="{{ route('renovasi.projects.show', $project['project_code'] ?? $project['id']) }}" class="font-extrabold text-[#14171F] hover:text-[#C83E13] transition-colors">
                                {{ $project['school_name'] }}
                            </a>
                            <span class="block text-[11px] text-slate-600">{{ $project['location'] }}</span>
                            <span class="text-[10px] font-mono text-slate-600">NPSN: {{ $project['npsn'] }}</span>
                        </td>

                        <td class="py-4 px-4 max-w-[230px]">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-900 border border-orange-200 block w-fit mb-1">
                                {{ $project['current_stage'] }}
                            </span>
                            <span class="text-[11px] text-slate-700 line-clamp-2">{{ $project['scope'] }}</span>
                        </td>

                        <td class="py-4 px-4">
                            <div class="flex items-baseline gap-1.5 mb-1">
                                <span class="font-black {{ $delta >= 0 ? 'text-emerald-700' : 'text-amber-800' }} text-sm">{{ round($project['progress_pct'], 1) }}%</span>
                                <span class="text-[10px] font-bold {{ $delta >= 0 ? 'text-emerald-800 bg-emerald-50 border border-emerald-200' : 'text-amber-900 bg-amber-50 border border-amber-200' }} px-1.5 py-0.5 rounded-md">
                                    {{ $delta >= 0 ? '+' . $delta . '% Ahead' : $delta . '% Deviasi' }}
                                </span>
                            </div>
                            <div class="w-24 bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200">
                                <div class="h-full {{ $delta >= 0 ? 'bg-emerald-600' : 'bg-[#C83E13]' }} rounded-full" style="width: {{ min(100, $project['progress_pct']) }}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-600 mt-0.5 block">Target Kurva S: {{ round($project['target_pct'], 1) }}%</span>
                        </td>

                        <td class="py-4 px-4">
                            <div class="flex items-center gap-1.5 text-emerald-800 font-bold text-[11px]">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>{{ $completedRab }}/{{ $totalRab }} Item RAB Selesai</span>
                            </div>
                            <span class="text-[10px] text-slate-600 block mt-0.5">{{ $project['lpjk_surveyor'] }}</span>
                        </td>

                        <td class="py-4 px-4">
                            <span class="font-bold text-slate-900 block text-xs">{{ $project['escrow_locked'] }}</span>
                            <span class="inline-block mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $project['escrow_ready'] ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                {{ $project['escrow_ready'] ? 'Siap Otorisasi' : 'Escrow Terkunci' }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center">
                            <a href="{{ route('renovasi.projects.show', $project['project_code'] ?? $project['id']) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#14171F] hover:bg-[#C83E13] text-white text-xs font-bold transition-all shadow-2xs">
                                <span>Monitoring Detail</span>
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-10 px-4 text-center text-xs text-slate-600">
                            Tidak ada tahapan proyek yang sesuai dengan filter pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 3 BOTTOM FEATURE BANNERS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-orange-100 text-[#C83E13] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900">Verifikasi Titik Koordinat Sekolah</h3>
                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                    Seluruh tahapan konstruksi mengacu pada koordinat geospasial dan NPSN sekolah yang terdaftar di Master Data.
                </p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900">Integrasi Komponen RAB &amp; Pencairan</h3>
                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                    Nilai pagu proyek dihitung langsung dari rincian <code class="font-mono">cost_estimates</code> yang telah disetujui untuk pencairan bertahap.
                </p>
            </div>
        </div>

        <div class="p-4 bg-white rounded-2xl border border-slate-200 shadow-xs flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </div>
            <div>
                <h3 class="text-xs font-bold text-slate-900">Sinkronisasi Status Laporan Publik</h3>
                <p class="text-[11px] text-slate-600 mt-0.5 leading-relaxed">
                    Penyelesaian milestone hingga 100% secara otomatis menutup tiket laporan kerusakan terkait menjadi Selesai (Completed).
                </p>
            </div>
        </div>
    </div>

    <x-renovasi.modals.create-project-modal
        :verifiedReports="$verifiedReports"
        :schools="$schools"
        :categories="$categories"
    />

</x-layouts.app>
