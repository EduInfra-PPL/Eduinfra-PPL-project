{{-- Bobot urgensi kategori fasilitas 1-5 (PBI-03): lima titik + label. --}}
@props([
    'weight' => 1,
    'showLabel' => true,
])

@php
    $weight = max(1, min(5, (int) $weight));
    $label = \App\Models\FacilityCategory::urgencyLabel($weight);
    $tones = [
        1 => ['dot' => 'bg-slate-400', 'text' => 'text-slate-600', 'bg' => 'bg-[#F1F5F9] border-slate-200/80'],
        2 => ['dot' => 'bg-emerald-500', 'text' => 'text-[#059669]', 'bg' => 'bg-[#DCFCE7] border-[#A7F3D0]/60'],
        3 => ['dot' => 'bg-amber-500', 'text' => 'text-[#D97706]', 'bg' => 'bg-[#FEF3C7] border-[#FDE68A]'],
        4 => ['dot' => 'bg-[#FF5C2A]', 'text' => 'text-[#EA4A18]', 'bg' => 'bg-[#FFF2ED] border-[#FFD9CC]'],
        5 => ['dot' => 'bg-[#DC2626]', 'text' => 'text-[#DC2626]', 'bg' => 'bg-[#FEE2E2] border-[#FECACA]'],
    ];
    $tone = $tones[$weight];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full border text-[10px] font-bold whitespace-nowrap {$tone['bg']} {$tone['text']}"]) }}
      title="Bobot urgensi {{ $weight }} dari 5 ({{ $label }})">
    <span class="flex items-center gap-0.5" aria-hidden="true">
        @for ($i = 1; $i <= 5; $i++)
            <span class="w-1.5 h-1.5 rounded-full {{ $i <= $weight ? $tone['dot'] : 'bg-white/80 ring-1 ring-inset ring-slate-300/70' }}"></span>
        @endfor
    </span>
    <span>{{ $weight }}@if ($showLabel) · {{ $label }}@endif</span>
</span>
