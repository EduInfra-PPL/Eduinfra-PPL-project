@props([
    'variant' => 'orange', // 'orange' | 'success' | 'warning' | 'danger' | 'dark' | 'neutral' | 'solid-orange'
    'size' => 'sm', // 'sm' | 'md'
    'dot' => false,
    'pulse' => false,
])

@php
    $baseClasses = 'inline-flex items-center font-bold rounded-full select-none gap-1.5 transition-all';

    $sizes = [
        'sm' => 'px-2.5 py-0.5 text-[10px]',
        'md' => 'px-3 py-1 text-xs',
    ];

    $variants = [
        'orange' => 'bg-[#FFF2ED] text-[#FF5C2A] border border-[#FFD9CC]',
        'solid-orange' => 'bg-[#FF5C2A] text-white',
        'success' => 'bg-[#DCFCE7] text-[#059669] border border-[#A7F3D0]/60',
        'warning' => 'bg-[#FEF3C7] text-[#D97706] border border-[#FDE68A]',
        'danger' => 'bg-[#FEE2E2] text-[#DC2626] border border-[#FECACA]',
        'dark' => 'bg-[#14171F] text-white',
        'neutral' => 'bg-[#F1F5F9] text-slate-600 border border-slate-200/80',
    ];

    $dotColors = [
        'orange' => 'bg-[#FF5C2A]',
        'solid-orange' => 'bg-white',
        'success' => 'bg-[#10B981]',
        'warning' => 'bg-[#F59E0B]',
        'danger' => 'bg-[#EF4444]',
        'dark' => 'bg-emerald-400',
        'neutral' => 'bg-slate-400',
    ];

    $classes = "{$baseClasses} {$sizes[$size]} {$variants[$variant]}";
    $dotClass = $dotColors[$variant] ?? 'bg-current';
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if ($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }} {{ $pulse ? 'animate-pulse' : '' }}"></span>
    @endif
    {{ $slot }}
</span>

