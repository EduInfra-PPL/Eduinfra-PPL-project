<x-layouts.app active="renovasi" title="EduInfra Tracker - Detail Monitoring Proyek & Milestone">

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
            <div class="font-bold mb-1">Gagal memperbarui milestone:</div>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- TOP BREADCRUMB & CONTEXT -->
    <div class="mb-5">
        <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-600 mb-2">
            <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition-colors">Beranda</a>
            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('renovasi.index') }}" class="hover:text-slate-900 transition-colors">Renovasi &amp; RAB</a>
            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <a href="{{ route('renovasi.index') }}" class="hover:text-slate-900 transition-colors">Proyek Berjalan</a>
            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-[#C83E13] font-bold" aria-current="page">{{ $project['code'] }} Monitoring Progres &amp; Milestone</span>
        </nav>

        <div class="flex flex-wrap items-center gap-2 mb-2">
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-orange-100 text-orange-950 border border-orange-200">
                {{ $project['zonasi'] }}
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                {{ $project->school?->educational_level ?? 'Sekolah' }} &bull; NPSN {{ $project['npsn'] }}
            </span>
            <span class="px-3 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                <span>{{ $project['sponsorship_status'] }}</span>
            </span>
        </div>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-[#14171F] tracking-tight">
                    {{ $project['school_name'] }} - {{ $project['title'] }}
                </h1>
                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-600 mt-1">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Lokasi: <strong class="text-slate-800">{{ $project['location'] }}</strong>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Pendanaan: <strong class="text-slate-800">{{ $project['consortium'] }}</strong>
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        Kontraktor: <strong class="text-slate-800">{{ $project['vendor_name'] }}</strong>
                    </span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a href="#rincian-rab-card"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-xs font-bold transition-colors">
                    <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Lihat RAB Proyek ({{ $project->costEstimates->count() }} Item)</span>
                </a>

                <form action="{{ route('renovasi.projects.destroy', $project->project_code ?? $project->id) }}" method="POST"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus proyek ' + {{ json_encode($project->project_code) }} + '? Status laporan kerusakan terkait akan dikembalikan ke Terverifikasi.')"
                      class="inline m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            title="Hapus / Batalkan Proyek Ini"
                            class="cursor-pointer inline-flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-rose-50 hover:bg-rose-100 text-rose-800 border border-rose-200 text-xs font-bold transition-colors">
                        <svg class="w-3.5 h-3.5 text-rose-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Hapus Proyek</span>
                    </button>
                </form>

                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-update-milestone' }))"
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#C83E13] hover:bg-[#A9320D] text-white text-xs font-black shadow-xs transition-all active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span>Pembaruan Milestone</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 4 MINI KPI METRICS -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">PAGU ANGGARAN DISETUJUI</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-xl font-black text-[#14171F]">{{ $project['contract_amount'] }}</span>
            </div>
            <div class="mt-2 flex items-center gap-1.5 text-[11px] text-emerald-800 font-semibold bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full w-fit">
                <svg class="w-3 h-3 text-emerald-700" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>{{ $project['funded_pct'] }}</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">PROGRES FISIK RIIL</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-xl font-black text-emerald-700">{{ round($project['progress_pct'], 1) }}%</span>
                <span class="text-xs text-slate-600 font-semibold">/ Target {{ round($project['target_pct'], 1) }}%</span>
            </div>
            <div class="mt-2 text-[11px] text-emerald-800 font-semibold bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full w-fit">
                Deviasi {{ $project['ahead_pct'] }} terhadap Kurva S
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">DANA ESCROW TERKUNCI</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-xl font-black text-amber-800">{{ $project['escrow_locked'] }}</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-600 font-medium">
                {{ $project['escrow_termin'] }}
            </div>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs">
            <span class="text-[10px] font-bold text-slate-600 uppercase tracking-wider block">TAKSONOMI SARPRAS &amp; SPK</span>
            <div class="flex items-baseline gap-1 mt-1">
                <span class="text-base font-black text-[#C83E13] truncate" title="{{ $project['category_label'] }}">{{ $project['category_label'] }}</span>
            </div>
            <div class="mt-2 text-[11px] text-slate-600 font-medium truncate">
                Target Selesai: {{ $project->target_end ? $project->target_end->translatedFormat('d M Y') : '-' }}
            </div>
        </div>
    </div>

    <!-- MAIN 2-COLUMN CONTENT SECTION -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN (8 cols): MILESTONE TRACKER & GALLERY -->
        <div class="lg:col-span-8 space-y-6">

            <!-- CARD 1: MILESTONE & TERMIN ESCROW TRACKER -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-200">
                    <div>
                        <h2 class="text-base font-extrabold text-[#14171F]">Milestone &amp; Termin Konstruksi</h2>
                        <p class="text-xs text-slate-600">Siklus audit pencairan dana berdasarkan bobot dan validasi progres fisik lapangan.</p>
                    </div>
                    <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-800 border border-slate-200 text-xs font-semibold">
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        <span>Total {{ count($project['stages']) }} Tahapan (Bobot 100%)</span>
                    </span>
                </div>

                <div class="space-y-4">
                    @foreach($project['stages'] as $stage)
                    <div class="p-4 rounded-2xl border transition-all {{ $stage['is_active'] ? 'bg-orange-50/40 border-orange-200 shadow-2xs' : ($stage['is_completed'] ? 'bg-slate-50 border-slate-200' : 'bg-white border-slate-200') }}">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                            <div class="flex items-start gap-3.5">
                                @if($stage['is_completed'])
                                <div class="w-9 h-9 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-2xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                @elseif($stage['is_active'])
                                <div class="w-9 h-9 rounded-full bg-[#C83E13] text-white flex items-center justify-center font-black text-sm shrink-0 shadow-xs">
                                    {{ $stage['num'] }}
                                </div>
                                @else
                                <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ $stage['num'] }}
                                </div>
                                @endif

                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="text-xs font-mono font-bold text-slate-600">[{{ $stage['milestone_code'] }}]</span>
                                        <h3 class="text-sm font-extrabold text-[#14171F]">{{ $stage['name'] }}</h3>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase {{ $stage['is_completed'] ? 'bg-emerald-100 text-emerald-900' : ($stage['is_active'] ? 'bg-orange-100 text-orange-950' : 'bg-slate-200 text-slate-800') }}">
                                            {{ $stage['status'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-700 mt-1 leading-relaxed">{{ $stage['scope'] }}</p>

                                    @if(!empty($stage['notes']))
                                        <p class="text-[11px] text-slate-600 mt-1 bg-white/80 p-2 rounded-lg border border-slate-200/80">
                                            <strong class="text-slate-800">Catatan Teknis:</strong> {{ $stage['notes'] }}
                                        </p>
                                    @endif

                                    <div class="mt-2.5 flex items-center gap-2 text-[11px] font-semibold text-slate-600">
                                        <svg class="w-3.5 h-3.5 {{ $stage['is_completed'] ? 'text-emerald-600' : ($stage['is_active'] ? 'text-[#C83E13]' : 'text-slate-500') }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        <span>{{ $stage['verification'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="sm:text-right shrink-0 pl-12 sm:pl-0">
                                <span class="text-sm font-black text-[#14171F]">{{ $stage['amount'] }}</span>
                                <span class="block text-[11px] font-semibold text-slate-600">Bobot {{ round($stage['weight_pct']) }}%</span>
                                @if($stage['is_active'])
                                <span class="block text-[10px] font-bold text-[#C83E13] mt-0.5">Tahap Aktif ({{ round($stage['progress_pct']) }}%)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- CARD 2: GALERI KOMPARASI VISUAL (BEFORE VS DURING) -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 mb-4 border-b border-slate-200">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-[#14171F]">Komparasi Visual: Kondisi Awal vs Pelaksanaan</h2>
                            <span class="px-2 py-0.5 text-[10px] font-extrabold bg-blue-50 text-blue-900 rounded-md border border-blue-200">
                                Bukti Lapangan
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">Perbandingan kondisi kerusakan awal dan dokumentasi pelaksanaan fisik terbaru.</p>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-600">
                        Diperbarui: {{ $project->updated_at ? $project->updated_at->translatedFormat('d M Y, H:i') : '-' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50">
                        <div class="relative h-44 bg-slate-200">
                            <img src="{{ $project['photos_comparison']['before']['url'] }}"
                                 width="600"
                                 height="360"
                                 loading="lazy"
                                 class="w-full h-full object-cover"
                                 alt="Kondisi Awal {{ $project['school_name'] }}">
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-rose-700 text-white">
                                {{ $project['photos_comparison']['before']['label'] }}
                            </span>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 px-2.5 py-1 bg-black/75 rounded-md text-[10px] font-mono font-semibold text-white truncate">
                                {{ $project['photos_comparison']['before']['geo'] }}
                            </div>
                        </div>
                        <div class="p-3.5 text-xs text-slate-700 font-medium leading-relaxed">
                            {{ $project['photos_comparison']['before']['note'] }}
                        </div>
                    </div>

                    <div class="rounded-2xl border border-orange-200 overflow-hidden bg-orange-50/20">
                        <div class="relative h-44 bg-slate-200">
                            <img src="{{ $project['photos_comparison']['during']['url'] }}"
                                 width="600"
                                 height="360"
                                 loading="lazy"
                                 class="w-full h-full object-cover"
                                 alt="Kondisi Berjalan {{ $project['school_name'] }}">
                            <span class="absolute top-3 left-3 px-2.5 py-1 rounded-md text-[10px] font-extrabold bg-emerald-700 text-white">
                                {{ $project['photos_comparison']['during']['label'] }}
                            </span>
                            <div class="absolute bottom-2.5 left-2.5 right-2.5 px-2.5 py-1 bg-black/75 rounded-md text-[10px] font-mono font-semibold text-emerald-300 truncate">
                                {{ $project['photos_comparison']['during']['geo'] }}
                            </div>
                        </div>
                        <div class="p-3.5 text-xs text-slate-700 font-medium leading-relaxed">
                            {{ $project['photos_comparison']['during']['note'] }}
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 text-xs">
                    <div class="flex items-start gap-2.5">
                        <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            <strong class="text-slate-900 font-bold block mb-0.5">Catatan Verifikator Lapangan ({{ $project['lpjk_surveyor'] }}):</strong>
                            <p class="text-slate-700 font-medium">"{{ $project['inspector_notes'] }}"</p>
                        </div>
                    </div>
                    <button type="button"
                            onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-update-milestone' }))"
                            class="cursor-pointer text-xs font-bold text-[#C83E13] hover:underline whitespace-nowrap">
                        Perbarui Bukti &amp; Catatan &rarr;
                    </button>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN (4 cols): ESCROW CHECKLIST & REAL RAB ITEMS -->
        <div class="lg:col-span-4 space-y-6">

            <!-- CARD 1: OTORISASI ESCROW -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <h2 class="text-base font-extrabold text-[#14171F]">Verifikasi &amp; Syarat Termin</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        Audit Fisik
                    </span>
                </div>

                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 mb-4 text-xs">
                    <span class="text-[10px] font-bold text-slate-600 block uppercase">REKENING ESCROW PROYEK:</span>
                    <span class="font-mono font-bold text-slate-900 block mt-0.5">{{ $project['escrow_account'] }}</span>
                </div>

                <div class="mb-5">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-700 block mb-2.5">
                        CHECKLIST KELENGKAPAN PROYEK:
                    </span>
                    <div class="space-y-2">
                        @foreach($project['checklist_escrow'] as $req)
                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <div class="w-4 h-4 rounded-full {{ ($req['passed'] ?? true) ? 'bg-emerald-600' : 'bg-amber-600' }} text-white flex items-center justify-center shrink-0 mt-0.5">
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="text-xs">
                                <span class="font-bold text-slate-900 block">{{ $req['title'] }}</span>
                                <span class="text-[11px] text-slate-600 font-medium">{{ $req['desc'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-3.5 bg-orange-50/70 rounded-2xl border border-orange-200 mb-4 text-xs">
                    <div class="flex justify-between items-center text-slate-800 font-semibold mb-1">
                        <span>Nilai Termin Aktif:</span>
                        <span class="font-black text-[#C83E13] text-sm">{{ $project['termin2_amount'] }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[11px] text-slate-600">
                        <span>Dana Escrow Belum Cair:</span>
                        <span class="font-bold text-slate-800">{{ $project['escrow_locked'] }}</span>
                    </div>
                </div>

                <button type="button"
                        onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-update-milestone' }))"
                        class="cursor-pointer w-full py-3 px-4 rounded-2xl bg-[#C83E13] hover:bg-[#A9320D] text-white font-extrabold text-xs shadow-xs transition-all active:scale-[0.98] flex items-center justify-center gap-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13]">
                    <span>Update Capaian Termin &amp; Checklist RAB</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </button>
            </div>

            <!-- CARD 2: RINCIAN KOMPONEN RAB & TAKSONOMI SARPRAS (REAL DB DATA) -->
            <div id="rincian-rab-card" class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs">
                <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200">
                    <div>
                        <h2 class="text-base font-extrabold text-[#14171F]">Rincian Komponen RAB</h2>
                        <p class="text-[11px] text-slate-600">Data tabel <code class="font-mono">cost_estimates</code> terhubung Master Kategori</p>
                    </div>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-200">
                        {{ $project->costEstimates->count() }} Item
                    </span>
                </div>

                <div class="space-y-2.5 mb-4 text-xs">
                    @forelse($project->costEstimates as $item)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-start justify-between gap-2 mb-1">
                            <span class="font-bold text-slate-900 text-xs leading-snug">
                                @if($item->item_code)
                                    <span class="font-mono text-[10px] text-slate-600">[{{ $item->item_code }}]</span>
                                @endif
                                {{ $item->item_name }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold shrink-0 {{ $item->is_completed ? 'bg-emerald-100 text-emerald-900' : 'bg-amber-100 text-amber-900' }}">
                                {{ $item->is_completed ? 'Tuntas' : 'Berjalan' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-600 mt-1.5">
                            <span>{{ number_format((float) $item->quantity, 0, ',', '.') }} {{ $item->unit ?: 'Paket' }} &bull; {{ $item->facilityCategory?->name ?? 'Sarpras' }}</span>
                            <strong class="text-slate-900 font-bold">{{ $item->formatted_total_price }}</strong>
                        </div>
                    </div>
                    @empty
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-600">
                        Belum ada rincian item RAB pada proyek ini.
                    </div>
                    @endforelse
                </div>

                <div class="p-3 rounded-2xl bg-slate-900 text-white flex items-center justify-between text-xs">
                    <span class="font-semibold text-slate-200">Total RAB Terverifikasi:</span>
                    <strong class="font-black text-sm">{{ $project['contract_amount'] }}</strong>
                </div>
            </div>

        </div>

    </div>

    <!-- MODAL PEMBARUAN PROGRES MILESTONE & UNGGAH BUKTI -->
    <x-renovasi.modals.update-milestone-modal :project="$project" />

</x-layouts.app>
