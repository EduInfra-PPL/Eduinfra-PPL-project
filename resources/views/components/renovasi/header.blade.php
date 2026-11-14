@props([
    'subTitle' => 'Proyek Berjalan',
    'showActions' => true
])

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
            <span class="text-[#C83E13] font-bold" aria-current="page">{{ $subTitle }}</span>
        </nav>

        <h1 class="text-2xl font-black text-[#14171F] tracking-tight">Manajemen Renovasi &amp; Milestone Fisik</h1>
        <p class="text-xs text-slate-600 mt-0.5 max-w-[65ch]">Pemantauan pelaksanaan konstruksi sekolah dari laporan kerusakan terverifikasi, rincian RAB, dan capaian tahapan fisik.</p>
    </div>

    @if($showActions)
    <div class="flex flex-wrap items-center gap-3">
        <button type="button"
                onclick="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'modal-tambah-proyek' }))"
                class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-[#C83E13] hover:bg-[#A9320D] text-white text-xs font-bold shadow-xs transition-all active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            <span>Buka Proyek Baru</span>
        </button>

        <a href="{{ route('renovasi.index', array_merge(request()->query(), ['view' => request('view') === 'table' ? 'grid' : 'table'])) }}"
           class="cursor-pointer inline-flex items-center gap-2 px-4 py-2.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 text-xs font-semibold shadow-2xs transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
            <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span>{{ request('view') === 'table' ? 'Tampilan Kartu' : 'Rekap Tabel RAB' }}</span>
        </a>
    </div>
    @endif
</div>
