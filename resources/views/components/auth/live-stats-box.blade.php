@props([
    'title' => 'LIVE TELEMETRI SATKER',
    'stats' => [], // [['value' => '14.820', 'label' => 'Sekolah 3T Terdata', 'accent' => false], ...]
])

{{-- Kotak ringkasan angka pada panel gelap (mis. "Live Telemetri Satker"). --}}
<div class="rounded-2xl bg-white/10 border border-white/10 p-4">
    <div class="mb-3 text-[10px] font-bold tracking-wide text-slate-300 uppercase">{{ $title }}</div>

    <div class="grid gap-3" style="grid-template-columns: repeat({{ max(count($stats), 1) }}, minmax(0, 1fr));">
        @foreach ($stats as $stat)
            <div>
                <div class="text-2xl font-extrabold leading-none {{ ($stat['accent'] ?? false) ? 'text-[#FF5C2A]' : 'text-white' }}">{{ $stat['value'] }}</div>
                <div class="text-[11px] text-slate-400 mt-1.5">{{ $stat['label'] }}</div>
            </div>
        @endforeach
    </div>
</div>
