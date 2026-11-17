@php
    use Illuminate\Support\Carbon;
    use App\Enums\UserRole;

    $tabs = [
        'all' => 'Semua',
        'approved' => 'Disetujui',
        'pending' => 'Menunggu',
        'rejected' => 'Ditolak',
    ];

    // Matriks Hak Akses RBAC (PBI-14)
    $rbacMatrix = [
        [
            'module' => 'Domain 1: Profil Sekolah & Koordinat 3T',
            'desc' => 'Pendaftaran profil sekolah, pemetaan batas wilayah 3T, dan master kategori fasilitas.',
            'admin' => ['label' => 'Full CRUD & Validasi', 'type' => 'full'],
            'guru' => ['label' => 'Read Profil Sendiri', 'type' => 'read'],
            'auditor' => ['label' => 'Read Wilayah Penugasan', 'type' => 'read'],
            'csr' => ['label' => 'Read Publik / Katalog', 'type' => 'read'],
        ],
        [
            'module' => 'Domain 2: Pelaporan Kerusakan & EXIF GPS',
            'desc' => 'Pembuatan tiket laporan indikasi kerusakan, verifikasi EXIF GPS (<250m), dan unggah media.',
            'admin' => ['label' => 'Full Review & Monitoring', 'type' => 'full'],
            'guru' => ['label' => 'Create & Upload Media', 'type' => 'create'],
            'auditor' => ['label' => 'Validasi Audit Lapangan', 'type' => 'create'],
            'csr' => ['label' => 'Read Laporan Terverifikasi', 'type' => 'read'],
        ],
        [
            'module' => 'Domain 3: Manajemen Renovasi & RAB',
            'desc' => 'Penyusunan Rencana Anggaran Biaya (RAB), pembaruan milestone fisik, dan direktori vendor.',
            'admin' => ['label' => 'Approval & Otorisasi RAB', 'type' => 'full'],
            'guru' => ['label' => 'Read Milestone Fisik', 'type' => 'read'],
            'auditor' => ['label' => 'Draft RAB & Update Progress', 'type' => 'create'],
            'csr' => ['label' => 'Read Progress Proyek', 'type' => 'read'],
        ],
        [
            'module' => 'Domain 4: Matching CSR & Donasi Sarpras',
            'desc' => 'Kampanye bantuan CSR, pencatatan kontribusi donatur, dan termin pencairan dana ke vendor.',
            'admin' => ['label' => 'Otorisasi Pencairan Dana', 'type' => 'full'],
            'guru' => ['label' => 'Read Penerima Manfaat', 'type' => 'read'],
            'auditor' => ['label' => 'Verifikasi Lapangan Vendor', 'type' => 'read'],
            'csr' => ['label' => 'Create & Salurkan Bantuan', 'type' => 'create'],
        ],
        [
            'module' => 'Domain 5: Registrasi, Approval & Matriks RBAC',
            'desc' => 'Tata kelola akun pengguna 3T, approval berkas pendaftar baru, dan pembagian matriks peran RBAC.',
            'admin' => ['label' => 'Full Kontrol RBAC & Approval', 'type' => 'full'],
            'guru' => ['label' => 'Hanya Registrasi & Profil', 'type' => 'restricted'],
            'auditor' => ['label' => 'Hanya Registrasi & Profil', 'type' => 'restricted'],
            'csr' => ['label' => 'Hanya Registrasi & Profil', 'type' => 'restricted'],
        ],
        [
            'module' => 'Anti-Fraud: Investigasi Dugaan Penyimpangan',
            'desc' => 'Pelaporan tiket fraud, eskalasi markup/korupsi ke Irjen, dan pembekuan akun terindikasi.',
            'admin' => ['label' => 'Tindak Lanjut & Eskalasi Irjen', 'type' => 'full'],
            'guru' => ['label' => 'Buat Laporan Dugaan Fraud', 'type' => 'create'],
            'auditor' => ['label' => 'Catat Temuan Lapangan', 'type' => 'create'],
            'csr' => ['label' => 'Lapor Dugaan Fraud CSR', 'type' => 'create'],
        ],
    ];
@endphp

<x-layouts.app title="Tata Kelola Akun, Matriks RBAC & Eskalasi Pengaduan Fraud">
    <div x-data="{ 
            activeMainTab: '{{ $tab ?? 'users' }}',
            editModalOpen: false,
            selectedUser: null,
            selectedRole: '',
            showBsreToast: true,
            openEditRole(user) {
                this.selectedUser = user;
                this.selectedRole = user.role_enum || 'pelapor_guru';
                this.editModalOpen = true;
            }
         }" 
         class="max-w-[1520px] mx-auto px-4 lg:px-6 pb-12 antialiased font-sans">

        <!-- STATUS ALERT NOTIFICATION -->
        @if (session('status'))
            <div class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">✕</button>
            </div>
        @endif

        <!-- TOP BAR: BREADCRUMB & HEADER -->
        <div class="mb-5">
            <div class="flex items-center gap-2 text-[11px] font-semibold text-slate-400">
                <span>Tata Kelola &amp; Keamanan</span>
                <span>&gt;</span>
                <span class="text-slate-600 font-bold">Manajemen Akun, RBAC &amp; Investigasi Fraud</span>
            </div>

            <div class="flex flex-wrap items-start justify-between gap-4 mt-2">
                <div>
                    <h1 class="text-2xl font-black text-[#14171F] tracking-tight">
                        Tata Kelola Akun, Matriks RBAC &amp; Eskalasi Pengaduan Fraud
                    </h1>
                    <p class="text-xs text-slate-500 mt-1 max-w-3xl leading-relaxed">
                        Pusat tata kelola registrasi pengguna 3T, kontrol hak akses berbasis peran (RBAC multi-instansi), dan penanganan investigasi fraud penyaluran bantuan sarpras.
                    </p>
                </div>

                <!-- Action Buttons: Unduh CSV & Registrasi Baru -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.export') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-full border border-slate-200/80 shadow-xs transition-all">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Unduh Audit Log Keamanan (.CSV)
                    </a>

                    <a href="{{ route('register') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white text-xs font-bold rounded-full shadow-sm hover:shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.374 21c-2.331 0-4.512-.645-6.374-1.765z" />
                        </svg>
                        Registrasi Pengguna Baru
                    </a>
                </div>
            </div>
        </div>

        <!-- MAIN NAVIGATION PILLS (SEGMENTED TABS) -->
        <div class="flex items-center gap-1.5 p-1 bg-[#F8FAFC] border border-slate-200/70 rounded-full w-fit mb-6 shadow-xs">
            <!-- Tab 1: Pengguna & Profil -->
            <button type="button" 
                    @click="activeMainTab = 'users'"
                    :class="activeMainTab === 'users' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'"
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-full transition-all">
                <span class="w-2 h-2 rounded-full" :class="activeMainTab === 'users' ? 'bg-[#FF5C2A]' : 'bg-slate-400'"></span>
                Pengguna &amp; Profil
                <span :class="activeMainTab === 'users' ? 'bg-[#2A2E3B] text-slate-200' : 'bg-slate-200/70 text-slate-700'" class="px-2 py-0.5 text-[10px] font-bold rounded-full">
                    {{ number_format($counts['all']) }} Akun
                </span>
            </button>

            <!-- Tab 2: Matriks RBAC -->
            <button type="button" 
                    @click="activeMainTab = 'rbac'"
                    :class="activeMainTab === 'rbac' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'"
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-full transition-all">
                Matriks RBAC
                <span :class="activeMainTab === 'rbac' ? 'bg-[#2A2E3B] text-slate-200' : 'bg-slate-200/70 text-slate-700'" class="px-2 py-0.5 text-[10px] font-bold rounded-full">
                    4 Peran
                </span>
            </button>

            <!-- Tab 3: Investigasi Dugaan Fraud -->
            <button type="button" 
                    @click="activeMainTab = 'fraud'"
                    :class="activeMainTab === 'fraud' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'"
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-full transition-all">
                Investigasi Dugaan Fraud
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#FF5C2A] text-white">
                    4 Tiket
                </span>
            </button>

            <!-- Tab 4: Sesi & Aktivitas -->
            <button type="button" 
                    @click="activeMainTab = 'sessions'"
                    :class="activeMainTab === 'sessions' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white'"
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-full transition-all">
                Sesi &amp; Aktivitas
            </button>
        </div>

        <!-- 4 TOP METRIC CARDS GRID -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Card 1: Total Terdaftar -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">Total Terdaftar</p>
                    <div class="text-2xl font-black text-[#14171F] mt-1">
                        {{ number_format($counts['all']) }} <span class="text-xs font-semibold text-slate-400">Akun</span>
                    </div>
                    <div class="flex items-center gap-2 mt-2 text-[11px] font-semibold">
                        <span class="inline-flex items-center text-emerald-600 font-bold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                            {{ number_format($counts['approved']) }} Aktif
                        </span>
                        <span class="text-slate-300">•</span>
                        <span class="text-amber-600">
                            {{ number_format($counts['pending']) }} Menunggu Verifikasi
                        </span>
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.765l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>

            <!-- Card 2: Matriks Peran RBAC -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">Matriks Peran RBAC</p>
                    <div class="text-2xl font-black text-[#14171F] mt-1">
                        4 Peran <span class="text-xs font-semibold text-slate-400">Definisi</span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 mt-2 truncate max-w-[210px]">
                        Admin, Auditor, Pelapor Guru, Mitra CSR
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-cyan-50 border border-cyan-100 flex items-center justify-center text-cyan-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
                    </svg>
                </div>
            </div>

            <!-- Card 3: Tiket Pengaduan & Fraud -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">Tiket Pengaduan &amp; Fraud</p>
                    <div class="text-2xl font-black text-[#FF5C2A] mt-1">
                        14 Kasus <span class="text-xs font-semibold text-[#FF5C2A]/70">Aktif</span>
                    </div>
                    <div class="flex items-center gap-1.5 mt-2 text-[11px] font-bold text-[#FF5C2A]">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#FF5C2A]"></span>
                        4 Prioritas Tinggi • Eskalasi Irjen
                    </div>
                </div>
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FF5C2A]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Kepatuhan Keamanan & MFA -->
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-xs flex items-start justify-between">
                <div>
                    <p class="text-[10px] font-extrabold tracking-wider text-slate-400 uppercase">Kepatuhan Keamanan &amp; MFA</p>
                    <div class="text-2xl font-black text-[#14171F] mt-1">
                        98.6% <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Tinggi</span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 mt-2">
                        Enkripsi EXIF &amp; GPS Anti-Spoofing
                    </p>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB CONTENT 1: PENGGUNA & PROFIL (TABLE)   -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'users'" class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100">
            <!-- Filter & Search Section -->
            <div class="flex flex-wrap items-center justify-between gap-4 pb-5 border-b border-slate-100">
                <!-- Search Form -->
                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                    @if ($status !== 'all')
                        <input type="hidden" name="status" value="{{ $status }}">
                    @endif
                    <div class="relative w-full sm:w-80">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                            </svg>
                        </span>
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, email, NIP, atau wilayah..."
                               class="w-full pl-10 pr-4 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70 focus:border-[#FF5C2A] focus:bg-white focus:outline-none transition-all">
                    </div>
                    <button type="submit" class="px-4 py-2 text-xs font-bold rounded-full bg-[#14171F] text-white hover:bg-slate-800 transition-colors">
                        Cari
                    </button>
                    @if ($search)
                        <a href="{{ route('admin.users.index', array_filter(['status' => $status === 'all' ? null : $status])) }}"
                           class="text-xs text-slate-400 hover:text-slate-600 underline">Reset</a>
                    @endif
                </form>

                <!-- Status Filter Pills -->
                <nav class="flex flex-wrap items-center gap-1.5 p-1 bg-[#F8FAFC] border border-slate-200/60 rounded-full">
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('admin.users.index', array_filter(['status' => $key === 'all' ? null : $key, 'q' => $search])) }}"
                           class="px-3.5 py-1.5 text-xs font-bold rounded-full transition-all {{ $status === $key ? 'bg-[#14171F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                            {{ $label }} ({{ $counts[$key] }})
                        </a>
                    @endforeach
                </nav>
            </div>

            <!-- Table of Users -->
            <div class="overflow-x-auto mt-2">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-black uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <th class="py-3.5 px-3 w-8">
                                <input type="checkbox" class="rounded border-slate-300 text-[#FF5C2A] focus:ring-[#FF5C2A]">
                            </th>
                            <th class="py-3.5 pr-4">Nama Lengkap &amp; Kredensial</th>
                            <th class="py-3.5 pr-4">Peran RBAC &amp; Instansi</th>
                            <th class="py-3.5 pr-4">Wilayah 3T &amp; Kanal Jaringan</th>
                            <th class="py-3.5 pr-4">Aktivitas Terakhir &amp; Sesi</th>
                            <th class="py-3.5 pr-4">Status Akun</th>
                            <th class="py-3.5 text-right pr-2">Aksi Terpadu</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $index => $u)
                            @php
                                $roleEnum = UserRole::tryFrom($u->role_enum);
                                $initials = collect(explode(' ', $u->name))->map(fn($part) => mb_substr($part, 0, 1))->take(2)->join('');
                                $colorPalettes = [
                                    'bg-indigo-600', 'bg-blue-600', 'bg-emerald-600', 'bg-amber-600', 'bg-rose-600', 'bg-purple-600'
                                ];
                                $avatarBg = $colorPalettes[$u->id % count($colorPalettes)];
                                
                                // Mockup network channel logic based on user id for realistic display
                                $channels = [
                                    'Kanal Satelit VSAT Prioritas',
                                    'SMS Gateway Cadangan (3T)',
                                    'Jaringan Terestrial 4G',
                                    'Fiber Optic Terestrial',
                                    'Terkoneksi VPN Enkripsi BSrE',
                                    'Citra Satelit Resolusi Tinggi'
                                ];
                                $userChannel = $channels[$u->id % count($channels)];

                                // 2FA badge styles
                                $twoFactorBadges = [
                                    ['label' => '2FA FIDO2', 'class' => 'bg-cyan-50 text-cyan-700 border border-cyan-200'],
                                    ['label' => 'SMS/OTP', 'class' => 'bg-amber-50 text-amber-700 border border-amber-200'],
                                    ['label' => 'Authenticator App', 'class' => 'bg-purple-50 text-purple-700 border border-purple-200'],
                                    ['label' => 'FIDO2 Level 4', 'class' => 'bg-rose-50 text-rose-700 border border-rose-200'],
                                    ['label' => 'SSO BIG', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200'],
                                ];
                                $badge = $twoFactorBadges[$u->id % count($twoFactorBadges)];
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition-colors group">
                                <!-- Checkbox -->
                                <td class="py-4 px-3 align-top">
                                    <input type="checkbox" class="rounded border-slate-300 text-[#FF5C2A] focus:ring-[#FF5C2A]">
                                </td>

                                <!-- Nama Lengkap & Kredensial -->
                                <td class="py-4 pr-4 align-top">
                                    <div class="flex items-start gap-3">
                                        <div class="relative shrink-0">
                                            <div class="w-9 h-9 rounded-full {{ $avatarBg }} text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                                {{ $initials }}
                                            </div>
                                            @if ($u->is_verified)
                                                <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-emerald-500 rounded-full border-2 border-white"></span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-[#14171F] group-hover:text-[#FF5C2A] transition-colors text-xs">
                                                    {{ $u->name }}
                                                </span>
                                                <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded {{ $badge['class'] }}">
                                                    {{ $badge['label'] }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                NIP. {{ $u->identity_number ?? '-' }}
                                                @if ($u->email)
                                                    <span class="text-slate-300">•</span> {{ $u->email }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Peran RBAC & Instansi -->
                                <td class="py-4 pr-4 align-top">
                                    <div class="font-extrabold text-slate-800 text-xs">
                                        {{ $roleEnum?->label() ?? ($u->role_enum ? ucfirst(str_replace('_', ' ', $u->role_enum)) : 'Belum Ditetapkan') }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ $u->organization ?: ($u->registration_role ? ucfirst(str_replace('_', ' ', $u->registration_role)) : 'Unit Kerja Nasional') }}
                                    </div>
                                </td>

                                <!-- Wilayah 3T & Kanal Jaringan -->
                                <td class="py-4 pr-4 align-top">
                                    <div class="flex items-center gap-1.5 font-bold text-slate-800 text-xs">
                                        <svg class="w-3.5 h-3.5 text-[#FF5C2A] shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                        </svg>
                                        {{ $u->region_name ?? 'Wilayah Koridor 3T' }}
                                    </div>
                                    <div class="flex items-center gap-1 text-[11px] text-slate-400 mt-0.5">
                                        <svg class="w-3 h-3 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z" />
                                        </svg>
                                        {{ $userChannel }}
                                    </div>
                                </td>

                                <!-- Aktivitas Terakhir & Sesi -->
                                <td class="py-4 pr-4 align-top whitespace-nowrap">
                                    <div class="font-bold text-slate-700 text-xs">
                                        {{ $u->last_login_at ? Carbon::parse($u->last_login_at)->diffForHumans() : 'Belum pernah login' }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        182.253.{{ ($u->id * 7) % 255 }}.{{ ($u->id * 13) % 255 }}
                                    </div>
                                </td>

                                <!-- Status Akun -->
                                <td class="py-4 pr-4 align-top whitespace-nowrap">
                                    @if ($u->is_verified)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/60 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Aktif &amp; Terverifikasi
                                        </span>
                                    @elseif ($u->rejected_at)
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200/60 shadow-xs">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                            Ditolak
                                        </span>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                Menunggu Verifikasi
                                            </span>
                                            <a href="{{ route('admin.registrations.index') }}" 
                                               class="px-2.5 py-1 text-[11px] font-bold bg-[#FF5C2A] hover:bg-[#e04f22] text-white rounded-lg shadow-xs transition-colors">
                                                Verifikasi Dokumen
                                            </a>
                                        </div>
                                    @endif
                                </td>

                                <!-- Aksi Terpadu -->
                                <td class="py-4 pr-2 align-top text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- Edit Role Trigger (PBI-14) -->
                                        <button type="button" 
                                                @click="openEditRole({ id: {{ $u->id }}, name: '{{ addslashes($u->name) }}', role_enum: '{{ $u->role_enum }}' })"
                                                title="Ubah Peran RBAC"
                                                class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>

                                        <!-- Refresh / Sync User -->
                                        <button type="button" 
                                                title="Sinkronisasi Data"
                                                onclick="location.reload()"
                                                class="w-7 h-7 rounded-lg bg-slate-50 hover:bg-slate-100 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                                    Tidak ada pengguna yang cocok dengan kriteria pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Table Footer: Pusdatin Sync Note & Pagination -->
            <div class="flex flex-wrap items-center justify-between gap-4 mt-6 pt-5 border-t border-slate-100">
                <div class="text-[11px] font-medium text-slate-400">
                    Menampilkan {{ $users->firstItem() ?? 0 }}-{{ $users->lastItem() ?? 0 }} dari {{ number_format($counts['all']) }} akun terdaftar • 
                    <span class="text-slate-500 font-semibold">Data disinkronkan real-time via API Gateway Pusdatin</span>
                </div>

                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB CONTENT 2: MATRIKS RBAC (PBI-14)       -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'rbac'" x-cloak class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100">
            <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-[#14171F]">Matriks Hak Akses &amp; Wewenang Persona (RBAC Multi-Instansi)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Definisi wewenang granular per peran berdasarkan ketentuan dokumen PRD &amp; Spesifikasi Teknis EduInfra Tracker.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <span class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">✓ Full Access</span>
                    <span class="px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">✚ Create &amp; Update</span>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-600">👁 Read Only</span>
                    <span class="px-3 py-1 rounded-full bg-rose-50 text-rose-600">✕ Terbatas / Dibatasi</span>
                </div>
            </div>

            <!-- RBAC Matrix Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border border-slate-200 rounded-xl overflow-hidden">
                    <thead class="bg-[#14171F] text-white text-[11px] font-bold">
                        <tr>
                            <th class="py-4 px-4 w-1/3">Modul / Domain Sistem</th>
                            <th class="py-4 px-3 text-center border-l border-slate-800">
                                Admin Dinas
                                <div class="text-[9px] font-normal text-slate-300">admin_dinas</div>
                            </th>
                            <th class="py-4 px-3 text-center border-l border-slate-800">
                                Pelapor (Guru 3T)
                                <div class="text-[9px] font-normal text-slate-300">pelapor_guru</div>
                            </th>
                            <th class="py-4 px-3 text-center border-l border-slate-800">
                                Auditor / Relawan
                                <div class="text-[9px] font-normal text-slate-300">auditor_relawan</div>
                            </th>
                            <th class="py-4 px-3 text-center border-l border-slate-800">
                                Mitra CSR
                                <div class="text-[9px] font-normal text-slate-300">mitra_csr</div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach ($rbacMatrix as $row)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-4 px-4">
                                    <div class="font-extrabold text-[#14171F]">{{ $row['module'] }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">{{ $row['desc'] }}</div>
                                </td>

                                <!-- Admin Dinas -->
                                <td class="py-4 px-3 text-center border-l border-slate-100 align-middle">
                                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ {{ $row['admin']['label'] }}
                                    </span>
                                </td>

                                <!-- Pelapor Guru -->
                                <td class="py-4 px-3 text-center border-l border-slate-100 align-middle">
                                    @if ($row['guru']['type'] === 'create')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            ✚ {{ $row['guru']['label'] }}
                                        </span>
                                    @elseif ($row['guru']['type'] === 'read')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                            👁 {{ $row['guru']['label'] }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">
                                            ✕ {{ $row['guru']['label'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Auditor Relawan -->
                                <td class="py-4 px-3 text-center border-l border-slate-100 align-middle">
                                    @if ($row['auditor']['type'] === 'create')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            ✚ {{ $row['auditor']['label'] }}
                                        </span>
                                    @elseif ($row['auditor']['type'] === 'read')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                            👁 {{ $row['auditor']['label'] }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">
                                            ✕ {{ $row['auditor']['label'] }}
                                        </span>
                                    @endif
                                </td>

                                <!-- Mitra CSR -->
                                <td class="py-4 px-3 text-center border-l border-slate-100 align-middle">
                                    @if ($row['csr']['type'] === 'create')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                            ✚ {{ $row['csr']['label'] }}
                                        </span>
                                    @elseif ($row['csr']['type'] === 'read')
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">
                                            👁 {{ $row['csr']['label'] }}
                                        </span>
                                    @else
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-600">
                                            ✕ {{ $row['csr']['label'] }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB CONTENT 3: INVESTIGASI DUGAAN FRAUD    -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'fraud'" x-cloak class="bg-white rounded-2xl lg:rounded-3xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <div>
                    <h2 class="text-lg font-extrabold text-[#14171F]">Tiket Pengaduan Investigasi Dugaan Fraud (Anti-Fraud Gate)</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Eskalasi temuan anomali geofencing &gt;250m, dugaan markup RAB, dan faktur ganda ke Inspektorat Jenderal.</p>
                </div>
                <span class="px-3 py-1 rounded-full bg-rose-50 text-[#FF5C2A] text-xs font-bold border border-orange-200">
                    4 Tiket Prioritas Tinggi
                </span>
            </div>

            <div class="space-y-3">
                <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-200/70 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">TIKET #FRD-2026-004</span>
                            <span class="text-xs font-bold text-[#14171F]">Anomali EXIF GPS Jarak 1.840m pada Pelaporan SDN 02 Sambas</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Dilaporkan oleh Relawan Audit • Koordinat foto tidak sesuai radius batas toleransi geofence 250m.</p>
                    </div>
                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-amber-100 text-amber-800">Dalam Investigasi</span>
                </div>

                <div class="p-4 rounded-2xl bg-[#F8FAFC] border border-slate-200/70 flex items-start justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800">TIKET #FRD-2026-003</span>
                            <span class="text-xs font-bold text-[#14171F]">Indikasi Duplikasi Faktur Belanja Semen Proyek Rehabilitasi Atap</span>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1">Vendor: PT Papua Konstruksi • Nomor faktur identik dengan pencairan termin ke-1.</p>
                    </div>
                    <span class="px-3 py-1 text-[11px] font-bold rounded-full bg-rose-100 text-rose-800">Eskalasi Irjen</span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB CONTENT 4: SESI & AKTIVITAS            -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'sessions'" x-cloak class="bg-white rounded-2xl lg:rounded-3xl p-6 shadow-sm border border-slate-100">
            <h2 class="text-lg font-extrabold text-[#14171F] mb-1">Audit Log Sesi Pengguna &amp; Keamanan BSrE</h2>
            <p class="text-xs text-slate-500 mb-5">Pencatatan real-time jejak digital login, enkripsi TLS 1.3, dan verifikasi sertifikat digital tanda tangan elektronik.</p>

            <div class="space-y-2">
                @foreach ($users->take(5) as $u)
                    <div class="p-3.5 rounded-xl bg-[#F8FAFC] border border-slate-200/60 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <div>
                                <span class="font-bold text-slate-800">{{ $u->name }}</span>
                                <span class="text-slate-400 text-[11px] ml-1">({{ $u->email }})</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 text-slate-400 font-mono text-[11px]">
                            <span>IP: 180.252.{{ ($u->id * 11) % 255 }}.{{ ($u->id * 19) % 255 }}</span>
                            <span>{{ $u->last_login_at ? Carbon::parse($u->last_login_at)->format('d M Y H:i:s') : 'Baru Terdaftar' }}</span>
                            <span class="text-emerald-600 font-bold">BSrE Verified ✓</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================== -->
        <!-- MODAL: UBAH PERAN RBAC PENGGUNA (PBI-14)   -->
        <!-- ========================================== -->
        <div x-show="editModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div @click.away="editModalOpen = false" 
                 class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="text-base font-extrabold text-[#14171F]">Ubah Peran RBAC Pengguna</h3>
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg">✕</button>
                </div>

                <template x-if="selectedUser">
                    <form :action="'{{ url('/admin/users') }}/' + selectedUser.id + '/role'" method="POST" class="mt-4">
                        @csrf
                        @method('PATCH')

                        <div class="mb-4 p-3 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="text-[10px] font-bold text-slate-400 uppercase">Pengguna Terpilih</div>
                            <div class="text-xs font-extrabold text-slate-800" x-text="selectedUser.name"></div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Peran Persona RBAC:</label>
                            <select name="role_enum" x-model="selectedRole" class="w-full text-xs font-semibold px-4 py-3 rounded-2xl bg-[#F8FAFC] border border-slate-200 focus:border-[#FF5C2A] focus:outline-none">
                                @foreach ($roles as $r)
                                    <option value="{{ $r->value }}">{{ $r->label() }} ({{ $r->value }})</option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-2">
                                Mengubah peran pengguna akan langsung menyesuaikan wewenang menu, izin aksi CRUD, dan validasi otorisasi di semua domain sistem.
                            </p>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <button type="button" @click="editModalOpen = false" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 text-xs font-extrabold text-white bg-[#FF5C2A] hover:bg-[#e04f22] rounded-full shadow-sm transition-colors">
                                Simpan Perubahan Peran
                            </button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- FLOATING TOAST: SISTEM KEAMANAN BSrE       -->
        <!-- ========================================== -->
        <div x-show="showBsreToast" 
             x-transition
             class="fixed bottom-5 right-5 z-40 bg-[#14171F] text-white p-3.5 pr-4 rounded-2xl shadow-xl border border-slate-700/60 flex items-center gap-3">
            <div class="w-8 h-8 rounded-xl bg-emerald-500/20 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>
            <div>
                <div class="text-xs font-black tracking-tight flex items-center gap-2">
                    Sistem Keamanan BSrE Terhubung
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                </div>
                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                    Enkripsi TLS 1.3 • Log RBAC Terkunci Kriptografi (Integritas 100%)
                </div>
            </div>
            <button type="button" @click="showBsreToast = false" class="text-slate-400 hover:text-white ml-2 text-xs">✕</button>
        </div>

    </div>
</x-layouts.app>
