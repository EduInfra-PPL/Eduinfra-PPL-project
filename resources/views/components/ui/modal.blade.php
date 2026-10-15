@props([
    'name' => '',
    'title' => '',
    'maxWidth' => 'lg', // 'sm' | 'md' | 'lg' | 'xl' | '2xl'
])

@php
    $widths = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $widthClass = $widths[$maxWidth] ?? 'max-w-lg';
@endphp

<div x-data="{ show: false }"
     x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
     x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
     x-on:keydown.escape.window="show = false"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
     style="display: none;">
    
    <!-- Backdrop Blur Overlay -->
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
         @click="show = false">
    </div>

    <!-- Modal Dialog Surface -->
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 sm:scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="w-full {{ $widthClass }} bg-white rounded-2xl lg:rounded-3xl shadow-2xl border border-slate-100 z-10 overflow-hidden transform transition-all">
        
        <!-- Header -->
        @if ($title)
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <h3 class="text-sm font-bold text-[#14171F]">{{ $title }}</h3>
                <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 rounded-full p-1 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Body -->
        <div class="p-6 text-xs text-slate-700">
            {{ $slot }}
        </div>

        <!-- Footer -->
        @if (isset($footer))
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>

