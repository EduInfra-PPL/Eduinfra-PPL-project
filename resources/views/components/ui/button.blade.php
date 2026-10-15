@props([
    'variant' => 'primary', // 'primary' | 'dark' | 'outline' | 'ghost' | 'danger'
    'size' => 'md', // 'sm' | 'md' | 'lg'
    'pill' => true,
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none gap-2';
    
    $radiusClass = $pill ? 'rounded-full' : 'rounded-xl';

    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs',
        'md' => 'px-4 py-2 text-xs',
        'lg' => 'px-5 py-2.5 text-sm',
    ];

    $variants = [
        // Primary Action: Vibrant Infrastructure Orange
        'primary' => 'bg-[#FF5C2A] hover:bg-[#EA4A18] text-white shadow-xs shadow-[#FF5C2A]/20 focus:ring-[#FF5C2A]',
        
        // Dark Control: Sleek Charcoal Slate
        'dark' => 'bg-[#14171F] hover:bg-slate-800 text-white shadow-xs focus:ring-slate-900',
        
        // Neutral Outline: Clean white with slate border
        'outline' => 'bg-white hover:bg-slate-50 text-slate-700 border border-slate-200/80 shadow-xs focus:ring-slate-300',
        
        // Ghost / Sub-action: Transparent hover
        'ghost' => 'bg-transparent hover:bg-slate-100 text-slate-600 hover:text-slate-900 focus:ring-slate-200',
        
        // Danger / Escalation: Critical red
        'danger' => 'bg-red-600 hover:bg-red-700 text-white shadow-xs focus:ring-red-500',
    ];

    $classes = "{$baseClasses} {$radiusClass} {$sizes[$size]} {$variants[$variant]}";
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif

