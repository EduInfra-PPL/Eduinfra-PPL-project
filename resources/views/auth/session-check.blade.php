<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Memeriksa Sesi — EduInfra Tracker</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F3F5F8] text-[#14171F] font-sans antialiased min-h-screen flex items-center justify-center p-6">

    <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 border border-slate-200/80 shadow-xl text-center space-y-6">
        <!-- Brand Icon & Animated Spinner -->
        <div class="relative mx-auto w-20 h-20 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-4 border-slate-100"></div>
            <div class="absolute inset-0 rounded-full border-4 border-t-[#FF5C2A] border-r-transparent border-b-transparent border-l-transparent animate-spin"></div>
            <div class="w-12 h-12 rounded-2xl bg-[#14171F] text-white flex items-center justify-center shadow-sm">
                <svg class="w-6 h-6 text-[#FF5C2A]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
        </div>

        <!-- Text Status -->
        <div class="space-y-2">
            <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#FFF1EB] text-[#FF5C2A] rounded-full text-[11px] font-bold tracking-wide uppercase">
                <span class="w-1.5 h-1.5 rounded-full bg-[#FF5C2A] animate-pulse"></span>
                <span>Gerbang Keamanan EduInfra</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-[#14171F] tracking-tight">
                Memeriksa Sesi Anda
            </h1>
            <p id="session-status-text" class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Memverifikasi status autentikasi dan keamanan tab browser secara otomatis...
            </p>
        </div>

        <!-- Subtle Progress Bar -->
        <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
            <div id="session-progress" class="h-full bg-[#FF5C2A] rounded-full transition-all duration-500" style="width: 35%;"></div>
        </div>

        <p class="text-[11px] text-slate-400">
            Sistem Audit Fasilitas Sekolah &amp; Matching CSR 3T
        </p>
    </div>

    <script>
        (async function () {
            const statusText = document.getElementById('session-status-text');
            const progressBar = document.getElementById('session-progress');
            const isServerAuthed = @js(auth()->check());
            const isTabAlive = sessionStorage.getItem('eduinfra_tab_active') === '1';

            const loginUrl = @js(route('login'));
            const dashboardUrl = @js(route('dashboard'));
            const expireUrl = @js(route('session.expire-tab'));
            const csrfToken = @js(csrf_token());

            if (isServerAuthed && isTabAlive) {
                progressBar.style.width = '100%';
                statusText.textContent = 'Sesi aktif terverifikasi. Mengarahkan ke Dashboard...';
                setTimeout(() => window.location.replace(dashboardUrl), 450);
                return;
            }

            if (isServerAuthed && !isTabAlive) {
                progressBar.style.width = '75%';
                statusText.textContent = 'Sesi tab sebelumnya telah berakhir. Membersihkan sesi demi keamanan...';
                try {
                    await fetch(expireUrl, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        credentials: 'same-origin',
                    });
                } catch (_) {
                    // Tetap arahkan ke login meskipun request cleanup gagal
                }
            } else {
                progressBar.style.width = '85%';
                statusText.textContent = 'Belum ada sesi aktif. Mengarahkan ke halaman Login...';
                sessionStorage.removeItem('eduinfra_tab_active');
            }

            progressBar.style.width = '100%';
            setTimeout(() => window.location.replace(loginUrl), 500);
        })();
    </script>
</body>
</html>
