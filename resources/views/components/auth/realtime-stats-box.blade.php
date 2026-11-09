@props([
    'title' => 'TELEMETRI SATKER 3T AKTIF',
    'stats' => [], // [['value' => '1.248', 'label' => 'Akun Terverifikasi', 'accent' => false], ...]
])

{{-- Kotak statistik berlabel "Real-time" pada panel gelap halaman registrasi. --}}
<div class="rounded-2xl bg-white/10 border border-white/10 p-4">
    <div class="flex items-center justify-between mb-3">
        <span class="text-[10px] font-bold tracking-wide text-slate-300 uppercase">{{ $title }}</span>
        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold text-emerald-400">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Real-time
        </span>
    </div>

    <div class="grid gap-3" style="grid-template-columns: repeat({{ max(count($stats), 1) }}, minmax(0, 1fr));">
        @foreach ($stats as $stat)
            <div class="rounded-xl bg-white/5 p-3">
                <div class="text-xl font-extrabold leading-none {{ ($stat['accent'] ?? false) ? 'text-[#FF5C2A]' : 'text-white' }}">{{ $stat['value'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1.5">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>
</div>