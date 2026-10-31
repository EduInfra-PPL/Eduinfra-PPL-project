@props([
    'title' => 'EduInfra Tracker',
    'subtitle' => 'Audit Fasilitas & Matching CSR Wilayah 3T',
    'chips' => ['BSSN 256-Bit', 'ISO/IEC 27001'],
])

{{--
    Layout halaman publik (sebelum login), mis. halaman Login.
    Berbeda dengan <x-layouts.app> yang memuat navbar aplikasi untuk pengguna yang sudah login.
    Slot "actions" = tombol di pojok kanan atas bar brand.
--}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - EduInfra Tracker</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>[x-cloak] { display: none !important; }</style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F5F8] text-[#14171F] antialiased min-h-screen flex flex-col"
      style="font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif;">

    <!-- BRAND BAR -->
    <header class="w-full px-4 lg:px-6 pt-4 lg:pt-5">
        <div class="max-w-[1180px] mx-auto bg-white rounded-2xl lg:rounded-3xl px-4 py-3 shadow-sm border border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ route('login') }}" class="flex items-center gap-3">
                    <x-ui.logo-icon size="w-10 h-10" rounded="rounded-xl" />
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base font-extrabold tracking-tight">EduInfra Tracker</span>
                            <x-ui.badge variant="orange">GIS 3T v2.4</x-ui.badge>
                        </div>
                        <p class="text-[11px] font-medium text-slate-500">{{ $subtitle }}</p>
                    </div>
                </a>

                <span class="hidden md:block h-8 w-px bg-slate-200"></span>

                <div class="hidden md:flex items-center gap-2">
                    @foreach ($chips as $index => $chip)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-semibold text-slate-600 bg-slate-100 rounded-full">
                            <x-ui.icon name="shield-check" class="w-3.5 h-3.5 {{ $index === 0 ? 'text-emerald-500' : 'text-sky-600' }}" />
                            {{ $chip }}
                        </span>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{ $actions ?? '' }}
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main class="flex-1 w-full max-w-[1180px] mx-auto px-4 lg:px-6 py-6">
        {{ $slot }}
    </main>

    <!-- FOOTER -->
    <footer class="w-full bg-white border-t border-slate-100 py-5 px-4 lg:px-6">
        <div class="max-w-[1180px] mx-auto flex flex-wrap items-center justify-between gap-2 text-[11px] text-slate-500 font-medium">
            <p>Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi &bull; Ditjen PAUD, Dikdas, dan Dikmen &bull; Terakreditasi LPJK</p>
            <p class="text-slate-400">&copy; {{ date('Y') }} EduInfra Tracker. Hak Cipta Dilindungi.</p>
        </div>
    </footer>
</body>
</html>
