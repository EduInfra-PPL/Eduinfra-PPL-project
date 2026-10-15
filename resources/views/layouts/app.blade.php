<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EduInfra Tracker - Audit Fasilitas & Matching CSR 3T' }}</title>

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F5F8] text-[#14171F] font-sans antialiased min-h-screen flex flex-col">
    <!-- REUSABLE TOP NAVBAR -->
    <x-layouts.navbar :active="$active ?? 'dashboard'" />

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 max-w-[1520px] w-full mx-auto px-4 lg:px-6 pb-12">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- FOOTER / AUDIT COMPLIANCE BANNER -->
    <footer class="border-t border-slate-200/60 py-4 text-center text-xs text-slate-500">
        <p>EduInfra Tracker &copy; {{ date('Y') }} Kemendikbudristek &bull; Sistem Audit Spasial Wilayah 3T Terintegrasi (SDG 4 Non-E-Learning)</p>
    </footer>
</body>
</html>
