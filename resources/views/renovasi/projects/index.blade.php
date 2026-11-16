<x-layouts.app active="renovasi" title="EduInfra Tracker - Manajemen Renovasi & Milestone">

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
            <div class="font-bold mb-1">Periksa kembali data proyek yang diinputkan:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <x-renovasi.header :subTitle="$viewType === 'table' ? 'Tabel Rekap & Pagu RAB' : 'Proyek Berjalan'" />

    <!-- 4 KPI CARDS (TOP METRICS FROM MASTER DATA) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-ui.kpi-card
            title="Total Proyek Renovasi"
            :value="$kpis['total_projects']['val'] ?? '0'"
            :unit="$kpis['total_projects']['unit'] ?? 'Proyek'"
            icon="building"
            :badgeText="$kpis['total_projects']['badge'] ?? '0 Aktif'"
            badgeVariant="orange-pill"
        />

        <x-ui.kpi-card
            title="Total Pagu Anggaran (RAB)"
            :value="$kpis['total_rab']['val'] ?? 'Rp 0'"
            :unit="$kpis['total_rab']['unit'] ?? ''"
            icon="link"
            :badgeText="$kpis['total_rab']['badge'] ?? '0 Item RAB'"
            badgeVariant="success"
        />

        <x-ui.kpi-card
            title="Rata-rata Progres Fisik"
            :value="$kpis['avg_progress']['val'] ?? '0%'"
            unit=""
            icon="alert"
            :badgeText="$kpis['avg_progress']['badge'] ?? 'Sesuai Kurva S'"
            badgeVariant="success"
        />

        <x-ui.kpi-card
            title="Rekanan Pelaksana Aktif"
            :value="$kpis['total_vendors']['val'] ?? '0'"
            :unit="$kpis['total_vendors']['unit'] ?? 'Kontraktor'"
            icon="building"
            :badgeText="$kpis['total_vendors']['badge'] ?? 'Wilayah Pendidikan'"
            badgeVariant="orange-pill"
        />
    </div>

    <x-renovasi.tabs-nav :activeTab="$viewType === 'table' ? 'rab' : 'proyek'" :projectCount="$totalCount ?? $projects->total()" />

    <!-- FILTER BAR & VIEW TOGGLE -->
    <form method="GET" action="{{ route('renovasi.index') }}" class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs mb-6 flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-3">
        <input type="hidden" name="view" value="{{ $viewType }}">

        <div class="relative flex-1 max-w-lg flex items-center gap-2">
            <div class="relative flex-1">
                <label for="renovasi-search" class="sr-only">Cari proyek renovasi</label>
                <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input id="renovasi-search"
                       type="text"
                       name="search"
                       value="{{ $search }}"
                       placeholder="Cari kode proyek, nama sekolah, NPSN, atau kontraktor..."
                       class="w-full pl-10 pr-4 py-2 text-xs font-medium rounded-full bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-[#C83E13] focus:outline-hidden transition-colors">
            </div>
            <button type="submit" class="cursor-pointer px-4 py-2 rounded-full bg-[#14171F] hover:bg-slate-800 text-white text-xs font-bold transition-colors shrink-0">
                Cari
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <label for="filter-status" class="sr-only">Filter Status Pengerjaan</label>
            <select id="filter-status"
                    name="status"
                    onchange="this.form.submit()"
                    class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800 hover:border-slate-400 focus:outline-hidden focus:border-[#C83E13] transition-colors">
                <option value="">Semua Status Pengerjaan</option>
                <option value="termin1" @selected($status === 'termin1')>Termin 1 (Pondasi &amp; Awal &lt;30%)</option>
                <option value="termin2" @selected($status === 'termin2')>Termin 2 (Struktur &amp; Fisik 30-89%)</option>
                <option value="termin3" @selected($status === 'termin3')>Termin 3 (Finishing &amp; BAST &ge;90%)</option>
                <option value="escrow_ready" @selected($status === 'escrow_ready')>Siap Otorisasi Pencairan</option>
                <option value="delayed" @selected($status === 'delayed')>Perlu Akselerasi / Tertunda</option>
            </select>

            <label for="filter-region" class="sr-only">Filter Wilayah</label>
            <select id="filter-region"
                    name="region"
                    onchange="this.form.submit()"
                    class="px-3.5 py-2 text-xs font-semibold rounded-full bg-slate-50 border border-slate-300 text-slate-800 hover:border-slate-400 focus:outline-hidden focus:border-[#C83E13] transition-colors max-w-[230px]">
                <option value="">Semua Wilayah (Master Data)</option>
                <option value="3t" @selected($region === '3t')>Seluruh Wilayah Prioritas 3T</option>
                <option value="tier1" @selected($region === 'tier1')>3T Tier-1 (Perbatasan/Kepulauan)</option>
                <option value="tier2" @selected($region === 'tier2')>3T Tier-2 (Pedalaman/Terisolir)</option>
                <option value="non3t" @selected($region === 'non3t')>Wilayah Reguler Non-3T</option>
                @foreach($regions as $reg)
                    <option value="{{ $reg->id }}" @selected((string) $region === (string) $reg->id)>
                        {{ $reg->name }} {{ $reg->is_3t_tier > 0 ? '(3T Tier-' . $reg->is_3t_tier . ')' : '' }}
                    </option>
                @endforeach
            </select>

            @if($search || $status || $region)
                <a href="{{ route('renovasi.index', ['view' => $viewType]) }}"
                   class="px-3 py-2 text-xs font-bold text-[#C83E13] hover:underline">
                    Reset Filter
                </a>
            @endif

            <div class="flex items-center p-1 bg-slate-100 rounded-full border border-slate-200">
                <a href="{{ route('renovasi.index', array_merge(request()->query(), ['view' => 'grid'])) }}"
                   class="p-1.5 rounded-full transition-all {{ $viewType === 'grid' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900' }}"
                   aria-label="Tampilan Grid Kartu"
                   title="Tampilan Grid Kartu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </a>
                <a href="{{ route('renovasi.index', array_merge(request()->query(), ['view' => 'table'])) }}"
                   class="p-1.5 rounded-full transition-all {{ $viewType === 'table' ? 'bg-white shadow-xs text-slate-900' : 'text-slate-600 hover:text-slate-900' }}"
                   aria-label="Tampilan Tabel"
                   title="Tampilan Tabel">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </a>
            </div>
        </div>
    </form>

    @if($projects->isEmpty())
        <div class="bg-white rounded-3xl p-10 border border-slate-200 text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-[#C83E13] flex items-center justify-center mx-auto mb-3 border border-orange-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <h2 class="text-base font-extrabold text-slate-900">Tidak Ada Proyek Renovasi yang Sesuai Kriteria</h2>
            <p class="text-xs text-slate-600 mt-1 max-w-md mx-auto">
                Ubah kata kunci pencarian atau filter wilayah untuk melihat daftar proyek renovasi yang sedang berjalan, atau buka proyek baru dari tiket laporan kerusakan.
            </p>
            <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
                @if($search || $status || $region)
                    <a href="{{ route('renovasi.index', ['view' => $viewType]) }}"
                       class="px-4 py-2 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition-colors">
                        Tampilkan Semua Proyek
                    </a>
                @endif
                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-tambah-proyek' }))"
                        class="cursor-pointer px-4 py-2 rounded-full bg-[#C83E13] hover:bg-[#A9320D] text-white text-xs font-bold transition-colors">
                    Buka Proyek Renovasi Baru
                </button>
            </div>
        </div>
    @elseif($viewType === 'table')
        <!-- TABLE VIEW -->
        <div class="bg-white rounded-2xl lg:rounded-3xl border border-slate-200 shadow-xs overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase tracking-wider text-[10px]">
                            <th scope="col" class="py-3.5 px-4">KODE &amp; ZONASI</th>
                            <th scope="col" class="py-3.5 px-4">SEKOLAH &amp; WILAYAH</th>
                            <th scope="col" class="py-3.5 px-4">TAKSONOMI &amp; LINGKUP</th>
                            <th scope="col" class="py-3.5 px-4">PELAKSANA &amp; PENGAWAS</th>
                            <th scope="col" class="py-3.5 px-4">PAGU RAB</th>
                            <th scope="col" class="py-3.5 px-4">PROGRES FISIK</th>
                            <th scope="col" class="py-3.5 px-4">STATUS TERMIN</th>
                            <th scope="col" class="py-3.5 px-4 text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($projects as $project)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-4 font-mono font-bold text-slate-800">
                                {{ $project['code'] }}
                                <span class="block text-[10px] text-slate-600 font-sans font-semibold mt-0.5">{{ $project['zonasi'] }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <a href="{{ route('renovasi.projects.show', $project['project_code'] ?? $project['id']) }}" class="font-extrabold text-[#14171F] hover:text-[#C83E13] transition-colors">
                                    {{ $project['school_name'] }}
                                </a>
                                <span class="block text-[11px] text-slate-600">{{ $project['location'] }}</span>
                                <span class="block text-[10px] font-mono text-slate-600">NPSN: {{ $project['npsn'] }}</span>
                            </td>
                            <td class="py-4 px-4 max-w-[230px]">
                                <span class="inline-block px-2 py-0.5 rounded-md bg-orange-50 text-orange-900 border border-orange-200 text-[10px] font-bold mb-1">
                                    {{ $project['category_label'] }}
                                </span>
                                <span class="block text-slate-700 truncate" title="{{ $project['scope'] }}">{{ $project['scope'] }}</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-800 block">{{ $project['vendor_name'] }}</span>
                                <span class="text-[11px] text-slate-600">{{ $project['lpjk_surveyor'] }}</span>
                            </td>
                            <td class="py-4 px-4 font-bold text-slate-900 whitespace-nowrap">
                                {{ $project['contract_value'] }}
                                <span class="block text-[10px] text-slate-600 font-normal">{{ $project->costEstimates->count() }} Item RAB</span>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="font-black text-emerald-700">{{ round($project['progress_pct'], 1) }}%</span>
                                    <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden border border-slate-200">
                                        <div class="h-full bg-emerald-600 rounded-full" style="width: {{ min(100, $project['progress_pct']) }}%"></div>
                                    </div>
                                </div>
                                <span class="block text-[10px] text-slate-600 mt-0.5">Target {{ round($project['target_pct'], 1) }}%</span>
                            </td>
                            <td class="py-4 px-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $project['escrow_ready'] ? 'bg-amber-50 text-amber-900 border border-amber-200' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                                    {{ $project['escrow_termin'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-center">
                                <a href="{{ route('renovasi.projects.show', $project['project_code'] ?? $project['id']) }}"
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-[#14171F] text-white hover:bg-[#C83E13] text-xs font-bold transition-colors">
                                    <span>Monitoring</span>
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- CARD GRID VIEW -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($projects as $project)
                <x-renovasi.project-card :project="$project" />
            @endforeach
        </div>
    @endif

    <!-- PAGINATION FOOTER -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 py-4 px-2 text-xs text-slate-600">
        <div>
            Menampilkan <strong class="text-slate-900">{{ $projects->firstItem() ?? 0 }}-{{ $projects->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-900">{{ $projects->total() }}</strong> proyek renovasi
        </div>
        @if($projects->hasPages())
            <div class="flex items-center gap-1.5">
                @if($projects->onFirstPage())
                    <span class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center font-bold" aria-disabled="true">&lt;</span>
                @else
                    <a href="{{ $projects->previousPageUrl() }}" class="w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center font-bold" aria-label="Halaman sebelumnya">&lt;</a>
                @endif

                @foreach($projects->getUrlRange(1, $projects->lastPage()) as $page => $url)
                    @if($page == $projects->currentPage())
                        <span aria-current="page" class="w-8 h-8 rounded-full bg-[#C83E13] text-white flex items-center justify-center font-black shadow-2xs">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center font-bold">{{ $page }}</a>
                    @endif
                @endforeach

                @if($projects->hasMorePages())
                    <a href="{{ $projects->nextPageUrl() }}" class="px-3 h-8 rounded-full bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 flex items-center justify-center font-bold">Selanjutnya &gt;</a>
                @else
                    <span class="px-3 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center font-bold" aria-disabled="true">Selanjutnya &gt;</span>
                @endif
            </div>
        @endif
    </div>

    <x-renovasi.modals.create-project-modal
        :verifiedReports="$verifiedReports"
        :schools="$schools"
        :categories="$categories"
    />
</x-layouts.app>
