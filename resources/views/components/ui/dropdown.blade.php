@props([
    'align' => 'right', // 'left' | 'right'
    'width' => 'w-56',
])

@php
    $alignmentClasses = $align === 'left' ? 'left-0 origin-top-left' : 'right-0 origin-top-right';
@endphp

<div class="relative inline-block text-left" 
     x-data="{ open: false }" 
     @click.outside="open = false" 
     @close.stop="open = false">
    
    <!-- Trigger Element -->
    <div @click="open = !open">
        {{ $trigger }}
    </div>

    <!-- Dropdown Menu Content Panel -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} rounded-2xl bg-white shadow-xl border border-slate-100 p-1.5 focus:outline-none {{ $alignmentClasses }}"
         style="display: none;"
         @click="open = false">
        
        <div class="space-y-0.5 text-xs text-slate-700">
            {{ $content ?? $slot }}
        </div>
    </div>
</div>
