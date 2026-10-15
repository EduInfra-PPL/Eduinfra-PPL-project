@props([
    'variant' => 'info', // 'success' | 'warning' | 'danger' | 'info' | 'orange'
    'title' => null,
    'dismissible' => false,
])

@php
    $variants = [
        'success' => [
            'container' => 'bg-[#ECFDF5] border-[#A7F3D0] text-[#065F46]',
            'iconBg' => 'bg-emerald-100 text-emerald-700',
        ],
        'warning' => [
            'container' => 'bg-[#FFFBEB] border-[#FDE68A] text-[#92400E]',
            'iconBg' => 'bg-amber-100 text-amber-700',
        ],
        'danger' => [
            'container' => 'bg-[#FEF2F2] border-[#FECACA] text-[#991B1B]',
            'iconBg' => 'bg-red-100 text-red-700',
        ],
        'info' => [
            'container' => 'bg-[#EFF6FF] border-[#BFDBFE] text-[#1E40AF]',
            'iconBg' => 'bg-blue-100 text-blue-700',
        ],
        'orange' => [
            'container' => 'bg-[#FFF2ED] border-[#FFD9CC] text-[#C2410C]',
            'iconBg' => 'bg-[#FFE2D6] text-[#FF5C2A]',
        ],
    ];

    $cfg = $variants[$variant] ?? $variants['info'];
@endphp

<div {{ $attributes->merge(['class' => "p-3.5 rounded-2xl border flex items-start gap-3 {$cfg['container']} transition-all"]) }}
     @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif>
    
    <!-- Icon Container -->
    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 {{ $cfg['iconBg'] }}">
        @if ($variant === 'success')
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        @elseif ($variant === 'warning')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        @elseif ($variant === 'danger')
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        @elseif ($variant === 'orange')
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.63 1.066-.94 1.717a23.3 23.3 0 00-.943 2.378 19.3 19.3 0 00-.77 2.784c-.05.28-.088.56-.118.84a9.7 9.7 0 00-3.352-1.742 1 1 0 00-1.127 1.488c.45.674.882 1.393 1.258 2.148.647 1.295 1.096 2.658 1.288 3.993a7.48 7.48 0 003.54-1.285 1 1 0 00.32-1.297 8.01 8.01 0 01-.67-2.736c.264-.325.56-.632.883-.912.87-.753 1.956-1.34 3.018-1.777a1 1 0 00.6-1.19z" clip-rule="evenodd"/></svg>
        @else
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
        @endif
    </div>

    <!-- Content -->
    <div class="flex-1 text-xs leading-relaxed">
        @if ($title)
            <div class="font-bold text-sm mb-0.5 leading-snug">{{ $title }}</div>
        @endif
        {{ $slot }}
    </div>

    <!-- Dismiss button -->
    @if ($dismissible)
        <button type="button" 
                onclick="this.closest('[x-data]').remove()"
                aria-label="Tutup alert"
                class="text-current opacity-60 hover:opacity-100 transition-opacity p-0.5 ml-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    @endif
</div>
