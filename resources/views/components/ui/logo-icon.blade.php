@props([
    'size' => 'w-11 h-11',
    'rounded' => 'rounded-2xl',
])

<div {{ $attributes->merge(['class' => "{$size} {$rounded} bg-[#14171F] flex items-center justify-center shrink-0 shadow-sm relative overflow-hidden select-none"]) }}>
    <svg viewBox="0 0 56 56" fill="none" class="w-full h-full p-1" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="eduOrangeGrad" x1="6" y1="6" x2="50" y2="50" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FF6B3D" />
                <stop offset="100%" stop-color="#EA4A18" />
            </linearGradient>
            <linearGradient id="pinContrastGrad" x1="16" y1="16" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                <stop offset="0%" stop-color="#FFFFFF" />
                <stop offset="100%" stop-color="#FFE8E0" />
            </linearGradient>
        </defs>

        <!-- Stylized Geotagged School Roof / Compass Arch -->
        <path d="M28 10L12 22.8V44C12 45.1 12.9 46 14 46H42C43.1 46 44 45.1 44 44V22.8L28 10Z" 
              fill="url(#eduOrangeGrad)" />

        <!-- Interior Structural Window & Geo-Target Ring (Negative Space) -->
        <path d="M28 20C23.58 20 20 23.58 20 28C20 33.5 26.5 40.5 28 42C29.5 40.5 36 33.5 36 28C36 23.58 32.42 20 28 20Z" 
              fill="#14171F" />

        <!-- Central Verification Focal Point / Radar Core -->
        <circle cx="28" cy="27.5" r="3.5" fill="url(#pinContrastGrad)" />

        <!-- Dynamic Foundation Line -->
        <path d="M19 41.5H37" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" opacity="0.9" />
    </svg>
</div>

