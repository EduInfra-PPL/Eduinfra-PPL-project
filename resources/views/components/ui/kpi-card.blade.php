@props([
    'title' => '',
    'value' => '',
    'unit' => '',
    'variant' => 'default', // 'default' | 'urgent'
    'badgeText' => '',
    'badgeVariant' => 'success', // 'success' | 'urgent-pill' | 'orange-pill' | 'warning-pill'
    'subText' => '',
    'icon' => 'building' // 'building' | 'alert' | 'link' | 'hourglass'
])

@php
    $isUrgent = $variant === 'urgent';
    $cardClasses = $isUrgent 
        ? 'bg-[#FF5C2A] text-white shadow-md shadow-[#FF5C2A]/20 border-transparent' 
        : 'bg-white text-[#14171F] shadow-sm border-slate-100';
@endphp

<div class="rounded-2xl lg:rounded-3xl p-5 border flex flex-col justify-between transition-all hover:shadow-md {{ $cardClasses }}">
    <!-- Top Row: Title + Icon -->
    <div class="flex items-start justify-between gap-2">
        <div class="flex items-center gap-2">
            @if($isUrgent)
                <span class="w-2.5 h-2.5 rounded-full bg-white shrink-0"></span>
            @endif
            <h3 class="text-xs font-semibold {{ $isUrgent ? 'text-white/90' : 'text-slate-500' }}">
                {{ $title }}
            </h3>
        </div>

        <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 {{ $isUrgent ? 'bg-white/20 text-white' : 'bg-slate-50 text-slate-700' }}">
            @if($icon === 'building')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            @elseif($icon === 'alert')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            @elseif($icon === 'link')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                </svg>
            @elseif($icon === 'hourglass')
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @endif
        </div>
    </div>

    <!-- Value Row -->
    <div class="my-4 flex items-baseline gap-1.5">
        <span class="text-3xl font-extrabold tracking-tight">{{ $value }}</span>
        @if($unit)
            <span class="text-sm font-semibold {{ $isUrgent ? 'text-white/80' : 'text-slate-500' }}">{{ $unit }}</span>
        @endif
    </div>

    <!-- Bottom Metadata Row -->
    <div class="flex flex-wrap items-center justify-between gap-2 pt-2 border-t {{ $isUrgent ? 'border-white/20' : 'border-slate-100' }}">
        @if($badgeText)
            @if($badgeVariant === 'success')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 bg-emerald-50 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    {{ $badgeText }}
                </span>
            @elseif($badgeVariant === 'urgent-pill')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-bold text-[#FF5C2A] bg-white rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $badgeText }}
                </span>
            @elseif($badgeVariant === 'orange-pill')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-[#FF5C2A] bg-[#FFF2ED] border border-[#FFD9CC] rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $badgeText }}
                </span>
            @elseif($badgeVariant === 'warning-pill')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 text-[11px] font-semibold text-amber-700 bg-amber-50 border border-amber-200/60 rounded-full">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ $badgeText }}
                </span>
            @endif
        @endif

        @if($subText)
            <span class="text-[11px] font-medium {{ $isUrgent ? 'text-white/80' : 'text-slate-400' }}">
                {{ $subText }}
            </span>
        @endif
    </div>
</div>

