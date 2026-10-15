@props([
    'variant' => 'default', // 'default' | 'urgent' | 'highlight' | 'muted'
    'padding' => 'md', // 'none' | 'sm' | 'md' | 'lg'
    'rounded' => '3xl', // 'xl' | '2xl' | '3xl'
    'header' => null,
    'footer' => null,
])

@php
    $baseClasses = 'transition-all duration-150 flex flex-col';

    $radii = [
        'xl' => 'rounded-xl',
        '2xl' => 'rounded-2xl',
        '3xl' => 'rounded-2xl lg:rounded-3xl',
    ];

    $paddings = [
        'none' => 'p-0',
        'sm' => 'p-4',
        'md' => 'p-5 lg:p-6',
        'lg' => 'p-6 lg:p-8',
    ];

    $variants = [
        'default' => 'bg-white text-[#14171F] border border-slate-100 shadow-sm',
        'urgent' => 'bg-[#FF5C2A] text-white border-transparent shadow-md shadow-[#FF5C2A]/20',
        'highlight' => 'bg-white text-[#14171F] border-2 border-[#FF5C2A] shadow-sm',
        'muted' => 'bg-slate-50 text-slate-800 border border-slate-200/70',
    ];

    $classes = "{$baseClasses} {$radii[$rounded]} {$variants[$variant]}";
    $contentPadding = $paddings[$padding];
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    @if ($header)
        <div class="px-5 py-4 border-b {{ $variant === 'urgent' ? 'border-white/20' : 'border-slate-100' }}">
            {{ $header }}
        </div>
    @endif

    <div class="{{ $contentPadding }} flex-1">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-5 py-4 border-t {{ $variant === 'urgent' ? 'border-white/20' : 'border-slate-100' }}">
            {{ $footer }}
        </div>
    @endif
</div>

