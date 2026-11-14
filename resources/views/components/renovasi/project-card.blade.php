@props([
    'project'
])

@php
    $progressPct = round((float) ($project['progress_pct'] ?? 0), 1);
    $targetPct = round((float) ($project['target_pct'] ?? 0), 1);
    $isAhead = $progressPct >= $targetPct;
    $eduLevel = $project->school?->educational_level ?? 'Sekolah';
    $npsn = $project['npsn'] ?? '-';
    $zonasi = $project['zonasi'] ?? 'Wilayah Pendidikan';
@endphp

<article class="bg-white rounded-2xl lg:rounded-3xl p-5 border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between group">
    <div>
        <div class="flex items-start justify-between gap-2 mb-3">
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-xs font-mono font-bold text-slate-700 tracking-tight">{{ $project['code'] ?? '#PRI' }}</span>
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-slate-100 text-slate-700 border border-slate-200">
                    {{ $eduLevel }} &bull; NPSN {{ $npsn }}
                </span>
            </div>
            <span class="px-2.5 py-1 text-[11px] font-bold rounded-full shrink-0 {{ $isAhead ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-amber-50 text-amber-800 border border-amber-200' }}">
                {{ $project['stage_badge'] ?? 'Termin Berjalan' }}
            </span>
        </div>

        <h2 class="text-base font-extrabold text-[#14171F] group-hover:text-[#C83E13] transition-colors leading-snug">
            <a href="{{ route('renovasi.projects.show', $project['project_code'] ?? $project['id']) }}" class="focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13] rounded-xs">
                {{ $project['school_name'] ?? 'Nama Sekolah' }}
            </a>
        </h2>
        <p class="text-xs text-slate-600 font-medium mt-1 mb-3 flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span class="truncate">{{ $project['location'] ?? 'Wilayah Pendidikan' }} ({{ $zonasi }})</span>
        </p>

        <div class="mb-3.5 bg-slate-50 p-2.5 rounded-xl border border-slate-200/70">
            <div class="flex items-center justify-between gap-2 mb-0.5">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-600">Lingkup Pekerjaan:</span>
                @if($project->damageReport)
                    <span class="text-[10px] font-mono font-bold text-[#C83E13]">Tiket {{ $project->damageReport->ticket_num }}</span>
                @endif
            </div>
            <p class="text-xs font-semibold text-slate-800 leading-relaxed line-clamp-2">
                {{ $project['scope'] ?? 'Rehabilitasi Fasilitas Sekolah' }}
            </p>
        </div>

        <div class="relative w-full h-36 rounded-xl overflow-hidden mb-3.5 bg-slate-100 border border-slate-200">
            <img src="{{ $project['photo_thumb'] ?? '' }}"
                 alt="Dokumentasi fisik {{ $project['school_name'] ?? 'Sekolah' }}"
                 width="600"
                 height="320"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-black/30"></div>

            <div class="absolute top-2.5 left-2.5 flex items-center gap-1.5">
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-md bg-black/75 text-white border border-white/20">
                    {{ $project['badge_photo_1'] ?? 'Progres Lapangan' }}
                </span>
            </div>

            <div class="absolute bottom-2.5 left-2.5 right-2.5 flex items-center justify-between gap-2 text-white text-[11px] font-bold">
                <span class="px-2 py-0.5 text-[10px] rounded-md bg-[#C83E13] text-white truncate max-w-[65%]">
                    {{ $project['badge_photo_2'] ?? 'Sarana Sekolah' }}
                </span>
                <span class="flex items-center gap-1 bg-black/75 px-2 py-0.5 rounded-md text-[10px] shrink-0">
                    <svg class="w-3 h-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>Terverifikasi</span>
                </span>
            </div>
        </div>

        <div class="mb-3.5">
            <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="font-bold text-slate-700">Kemajuan Fisik Nyata:</span>
                <span class="font-black {{ $isAhead ? 'text-emerald-700' : 'text-slate-900' }}">
                    {{ $progressPct }}% <span class="text-slate-600 font-semibold">/ Target {{ $targetPct }}%</span>
                </span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden p-0.5 border border-slate-200" role="progressbar" aria-valuenow="{{ $progressPct }}" aria-valuemin="0" aria-valuemax="100" aria-label="Kemajuan fisik {{ $project['school_name'] }}">
                <div class="h-full rounded-full transition-all duration-500 {{ $isAhead ? 'bg-emerald-600' : 'bg-[#C83E13]' }}"
                     style="width: {{ min(100, $progressPct) }}%"></div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-1.5 mb-3.5 text-[11px] font-medium text-slate-700">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-50 text-blue-800 border border-blue-200 font-semibold text-[10px]">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                {{ $project['exif_status'] ?? 'Geofence Valid' }}
            </span>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-100 text-slate-700 border border-slate-200 font-semibold text-[10px] truncate max-w-[190px]">
                {{ $project['lpjk_surveyor'] ?? 'Pengawas Dinas' }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-2 p-2.5 rounded-xl bg-[#F8FAFC] border border-slate-200 mb-3.5 text-xs">
            <div>
                <span class="text-[10px] text-slate-600 block font-semibold">Pagu RAB Disetujui:</span>
                <span class="font-bold text-[#14171F]">{{ $project['contract_value'] ?? 'Rp 0' }}</span>
            </div>
            <div>
                <span class="text-[10px] text-slate-600 block font-semibold">Escrow Terkunci:</span>
                <span class="font-bold text-amber-800">{{ $project['escrow_locked'] ?? 'Rp 0' }}</span>
            </div>
        </div>

        <div class="text-[11px] text-slate-600 flex items-center gap-1.5 mb-4">
            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            <span class="truncate">Pelaksana: <strong class="text-slate-800">{{ $project['vendor_name'] ?? '-' }}</strong></span>
        </div>
    </div>

    <a href="{{ route('renovasi.projects.show', $project['project_code'] ?? $project['id']) }}"
       class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-[#14171F] text-slate-900 hover:text-white font-bold text-xs transition-all border border-slate-200 hover:border-[#14171F] active:scale-[0.98] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#14171F]">
        <span>Buka Monitoring Progres &amp; Milestone</span>
        <svg class="w-3.5 h-3.5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
    </a>
</article>
