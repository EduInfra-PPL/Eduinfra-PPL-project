<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'EduInfra Tracker - Audit Fasilitas & Matching CSR 3T' }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">

    <!-- Google Font: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts via Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F5F8] text-[#14171F] font-sans antialiased min-h-screen flex flex-col">
    @auth
        <div id="tab-session-overlay" class="hidden fixed inset-0 z-[9999] bg-[#F3F5F8] items-center justify-center p-6">
            <div class="max-w-sm w-full bg-white rounded-3xl p-8 border border-slate-200/80 shadow-xl text-center space-y-4">
                <div class="mx-auto w-12 h-12 rounded-full border-4 border-slate-100 border-t-[#FF5C2A] animate-spin"></div>
                <div class="space-y-1">
                    <h2 class="text-base font-extrabold text-[#14171F]">Memeriksa Sesi Tab</h2>
                    <p class="text-xs text-slate-500">Sesi tab sebelumnya telah berakhir. Mengarahkan ke halaman Login...</p>
                </div>
            </div>
        </div>
        <script>
            (function () {
                if (sessionStorage.getItem('eduinfra_tab_active') !== '1') {
                    const overlay = document.getElementById('tab-session-overlay');
                    if (overlay) {
                        overlay.classList.remove('hidden');
                        overlay.classList.add('flex');
                    }
                    fetch(@js(route('session.expire-tab')), {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': @js(csrf_token()),
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    }).finally(() => {
                        window.location.replace(@js(route('login')));
                    });
                }
            })();
        </script>
    @endauth

    <!-- REUSABLE TOP NAVBAR -->
    <x-layouts.navbar :active="$active ?? 'dashboard'" />

    <!-- MAIN CONTENT CONTAINER -->
    <main class="flex-1 max-w-[1520px] w-full mx-auto px-4 lg:px-6 pb-12">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- INSTITUTIONAL FOOTER -->
    <x-layouts.footer />
</body>
</html>
