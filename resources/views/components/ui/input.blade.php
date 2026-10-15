@props([
    'name' => '',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'pill' => true,
    'error' => null,
    'shortcut' => null,
])

@php
    $radiusClass = $pill ? 'rounded-full' : 'rounded-xl';
    $hasPrefix = isset($icon) || $type === 'search';
@endphp

<div class="w-full flex flex-col gap-1.5 font-sans">
    @if ($label)
        <label for="{{ $name }}" class="text-xs font-semibold text-slate-700">
            {{ $label }}
        </label>
    @endif

    <div class="relative flex items-center w-full">
        @if (isset($icon))
            <div class="absolute left-3.5 text-slate-400 pointer-events-none flex items-center justify-center">
                {{ $icon }}
            </div>
        @elseif ($type === 'search')
            <div class="absolute left-3.5 text-slate-400 pointer-events-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                </svg>
            </div>
        @endif

        <input 
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => "w-full text-xs font-medium text-slate-800 bg-[#F1F5F9] border " . 
                           ($error ? "border-red-400 focus:border-red-500 " : "border-transparent focus:border-[#FF5C2A] ") .
                           "focus:bg-white focus:outline-none transition-all placeholder-slate-400 " .
                           ($hasPrefix ? "pl-10 " : "pl-4 ") .
                           ($shortcut ? "pr-12 " : "pr-4 ") .
                           "py-2.5 {$radiusClass}"
            ]) }}
        />

        @if ($shortcut)
            <kbd class="absolute right-3 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded-md pointer-events-none shadow-2xs">
                {{ $shortcut }}
            </kbd>
        @endif
    </div>

    @if ($error)
        <p class="text-[11px] text-red-600 font-medium pl-1">
            {{ $error }}
        </p>
    @endif
</div>
