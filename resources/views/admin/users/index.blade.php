@php
    use Illuminate\Support\Carbon;
    use App\Enums\UserRole;

    $tabs = [
        'all' => 'Semua',
        'approved' => 'Disetujui',
        'pending' => 'Menunggu',
        'rejected' => 'Ditolak',
    ];

    // Matriks Hak Akses RBAC Sesuai Figma Tim (PBI-14)
    $rbacCategories = [
        [
            'id' => 'A',
            'domain_key' => 'domain1',
            'name' => 'A. Modul Master Data Infrastruktur 3T',
            'icon' => 'folder',
            'items' => [
                [
                    'name' => 'Master Sekolah 3T & NPSN',
                    'desc' => 'Sinkronisasi Dapodik, akreditasi, & data koordinat statis.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'read', 'csr' => 'read', 'kps' => 'read', 'vnd' => 'none',
                ],
                [
                    'name' => 'Zonasi Wilayah GeoJSON & Aksesibilitas',
                    'desc' => 'Peta batas administratif distrik 3T dan indeks isolasi jalan.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'full', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'none',
                ],
                [
                    'name' => 'Taksonomi Fasilitas & Komponen Rusak',
                    'desc' => 'Katalog elemen bangunan, pondasi, struktur atap, sanitasi.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'read', 'csr' => 'read', 'kps' => 'read', 'vnd' => 'read',
                ],
            ],
        ],
        [
            'id' => 'B',
            'domain_key' => 'domain2',
            'name' => 'B. Modul Pelaporan Kerusakan Fisik',
            'icon' => 'alert',
            'items' => [
                [
                    'name' => 'Buat Tiket Baru & Unggah Bukti Lapangan',
                    'desc' => 'Input manual kondisi ruang kelas rusak oleh perwakilan sekolah.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'read', 'csr' => 'read', 'kps' => 'full', 'vnd' => 'none',
                ],
                [
                    'name' => 'Validasi EXIF Metadata & Geofence GPS',
                    'desc' => 'Pendeteksi anomali titik koordinat foto kamera vs radius 50m sekolah.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'verify', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'none',
                ],
                [
                    'name' => 'Verifikasi Lapangan & Sign-Off BAP Resmi',
                    'desc' => 'Pemberian tanda tangan elektronik BSrE pada Berita Acara Pemeriksaan.',
                    'adm' => 'read', 'aud' => 'read', 'big' => 'verify', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'none',
                ],
            ],
        ],
        [
            'id' => 'C',
            'domain_key' => 'domain3',
            'name' => 'C. Modul Renovasi & RAB Standar SNI PUPR',
            'icon' => 'hammer',
            'items' => [
                [
                    'name' => 'Penyusunan Line Item RAB Renovasi',
                    'desc' => 'Kalkulasi volume bahan, koefisien tenaga kerja daerah terpencil.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'read', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'full',
                ],
                [
                    'name' => 'Otorisasi Harga Standar Satuan PUPR',
                    'desc' => 'Pemberlakuan HPS (Harga Perkiraan Sendiri) wilayah kepulauan.',
                    'adm' => 'full', 'aud' => 'verify', 'big' => 'read', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'none',
                ],
                [
                    'name' => 'Kunci Anggaran RAB Definitif (Lock Budget)',
                    'desc' => 'Pembekuan revisi nilai proyek sebelum matching penawaran CSR.',
                    'adm' => 'full', 'aud' => 'verify', 'big' => 'none', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'none',
                ],
            ],
        ],
        [
            'id' => 'D',
            'domain_key' => 'domain4',
            'name' => 'D. Modul Manajemen Rekanan Vendor Konstruksi',
            'icon' => 'building',
            'items' => [
                [
                    'name' => 'Registrasi Kontraktor & Profil Kualifikasi',
                    'desc' => 'Pendaftaran data NIB, akta pendirian, dan domisili kantor cabang.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'none', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'full',
                ],
                [
                    'name' => 'Verifikasi SBU LPJK & Sertifikat Tenaga Kerja',
                    'desc' => 'Pengecekan integrasi API LPJK PUPR untuk sertifikat keahlian gedung.',
                    'adm' => 'full', 'aud' => 'verify', 'big' => 'none', 'csr' => 'read', 'kps' => 'none', 'vnd' => 'read',
                ],
                [
                    'name' => 'Blacklist Vendor & Penalti Prestasi',
                    'desc' => 'Blokir akun rekanan terindikasi wanprestasi / mangkir pekerjaan.',
                    'adm' => 'full', 'aud' => 'verify', 'big' => 'none', 'csr' => 'none', 'kps' => 'none', 'vnd' => 'none',
                ],
            ],
        ],
        [
            'id' => 'E',
            'domain_key' => 'domain5',
            'name' => 'E. Modul Pencairan Dana CSR & Pajak',
            'icon' => 'credit-card',
            'items' => [
                [
                    'name' => 'Otorisasi Termin Escrow Mandiri/BNI',
                    'desc' => 'Multi-sign pencairan dana escrow (Termin I 30%, Termin II 40%, Akhir 30%).',
                    'adm' => 'full', 'aud' => 'verify', 'big' => 'none', 'csr' => 'full', 'kps' => 'none', 'vnd' => 'read',
                ],
                [
                    'name' => 'Unduh Bukti Potong Pajak PMK-128',
                    'desc' => 'Sertifikasi insentif pengurangan penghasilan bruto s.d 200%.',
                    'adm' => 'full', 'aud' => 'read', 'big' => 'none', 'csr' => 'full', 'kps' => 'none', 'vnd' => 'none',
                ],
            ],
        ],
        [
            'id' => 'F',
            'domain_key' => 'domain6',
            'name' => 'F. Modul Investigasi Keamanan & Dugaan Fraud',
            'icon' => 'shield',
            'items' => [
                [
                    'name' => 'Akses Log Audit Forensik & Kriptografi',
                    'desc' => 'Pemeriksaan jejak digital SHA-256 mutasi status dan IP login.',
                    'adm' => 'full', 'aud' => 'full', 'big' => 'none', 'csr' => 'none', 'kps' => 'none', 'vnd' => 'none',
                ],
                [
                    'name' => 'Pembekuan Rekening Escrow Darurat',
                    'desc' => 'Freeze saldo rekening bersama atas temuan audit penyimpangan.',
                    'adm' => 'full', 'aud' => 'full', 'big' => 'none', 'csr' => 'none', 'kps' => 'none', 'vnd' => 'none',
                ],
                [
                    'name' => 'Eskalasi Tiket Fraud ke Irjen / APH',
                    'desc' => 'Penerusan berkas bukti ke Komisi Pemberantasan Korupsi / Kejaksaan Agung.',
                    'adm' => 'none', 'aud' => 'full', 'big' => 'none', 'csr' => 'none', 'kps' => 'none', 'vnd' => 'none',
                ],
            ],
        ],
    ];

    $totalRolesCount = 6 + ($customRoles ? $customRoles->count() : 0);

    $renderBadge = function ($type) {
        if ($type === 'full') {
            return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-300/80 shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Full</span>';
        } elseif ($type === 'read') {
            return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-sky-50 text-sky-700 border border-sky-300/80 shadow-2xs"><span class="w-1.5 h-1.5 rounded-full bg-sky-500"></span>Read</span>';
        } elseif ($type === 'verify') {
            return '<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-orange-50 text-[#FF5C2A] border border-orange-300/80 shadow-2xs"><svg class="w-3 h-3 text-[#FF5C2A]" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/></svg>Verify</span>';
        } else {
            return '<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-semibold text-slate-400 bg-slate-100/70 border border-slate-200/60">— No</span>';
        }
    };
@endphp

<x-layouts.app title="Tata Kelola Akun, Matriks RBAC & Eskalasi Pengaduan Fraud">
    <div x-data="{ 
            activeMainTab: '{{ $tab ?? 'users' }}',
            editModalOpen: false,
            addRoleModalOpen: false,
            selectedUser: null,
            selectedRole: '',
            showBsreToast: true,
            expandedCategories: ['A', 'B', 'C', 'D', 'E', 'F'],

            // Form data Tambah Peran Khusus (Custom RBAC Role)
            customRole: {
                name: 'Auditor Khusus Satgassus Tipikor Daerah 3T',
                code: 'STP',
                institution: 'KPK / Satgas Investigasi Tipikor Irjen',
                access_level: 'Level 3 - Audit Forensik & Verifikasi Spasial (Ad-Hoc)',
                description: 'Diberikan mandat khusus investigasi penyelewengan dana renovasi sekolah 3T, inspeksi bukti fisik drone/lapangan, pengujian spasial real-time, serta hak instruksi pembekuan sementara akun vendor dan rekening proyek bermasalah berdasarkan SK Mendikbudristek No. 492/P/2024.',
                permissions: {
                    domain1: { read: true, write: false, delete: false, verify: true },
                    domain2: { read: true, write: true, delete: false, verify: true },
                    domain3: { read: true, write: false, delete: false, verify: true },
                    domain4: { read: true, write: false, delete: false, verify: true },
                    domain5: { read: true, write: false, delete: false, verify: true },
                    domain6: { read: true, write: true, delete: true, verify: true }
                }
            },

            activePreset: 'investigatif',

            applyPreset(preset) {
                this.activePreset = preset;
                if (preset === 'investigatif') {
                    this.customRole.name = 'Auditor Khusus Satgassus Tipikor Daerah 3T';
                    this.customRole.code = 'STP';
                    this.customRole.institution = 'KPK / Satgas Investigasi Tipikor Irjen';
                    this.customRole.access_level = 'Level 3 - Audit Forensik & Verifikasi Spasial (Ad-Hoc)';
                    this.customRole.description = 'Diberikan mandat khusus investigasi penyelewengan dana renovasi sekolah 3T, inspeksi bukti fisik drone/lapangan, pengujian spasial real-time, serta hak instruksi pembekuan sementara akun vendor dan rekening proyek bermasalah berdasarkan SK Mendikbudristek No. 492/P/2024.';
                    this.customRole.permissions = {
                        domain1: { read: true, write: false, delete: false, verify: true },
                        domain2: { read: true, write: true, delete: false, verify: true },
                        domain3: { read: true, write: false, delete: false, verify: true },
                        domain4: { read: true, write: false, delete: false, verify: true },
                        domain5: { read: true, write: false, delete: false, verify: true },
                        domain6: { read: true, write: true, delete: true, verify: true }
                    };
                } else if (preset === 'finansial') {
                    this.customRole.name = 'Pengawas Finansial & Akuntabilitas BPKP';
                    this.customRole.code = 'PFN';
                    this.customRole.institution = 'Badan Pengawasan Keuangan dan Pembangunan (BPKP)';
                    this.customRole.access_level = 'Level 4 - Otorisator Anggaran & Administrator Penuh';
                    this.customRole.description = 'Pengawasan berkala arus kas termin CSR, audit faktur pengadaan konstruksi sekolah 3T, dan sinkronisasi rekening bank escrow pemerintah.';
                    this.customRole.permissions = {
                        domain1: { read: true, write: false, delete: false, verify: false },
                        domain2: { read: true, write: false, delete: false, verify: true },
                        domain3: { read: true, write: false, delete: false, verify: true },
                        domain4: { read: true, write: true, delete: false, verify: true },
                        domain5: { read: true, write: false, delete: false, verify: false },
                        domain6: { read: true, write: true, delete: false, verify: true }
                    };
                } else if (preset === 'spasial') {
                    this.customRole.name = 'Verifikator Spasial & Batas Terluar BIG';
                    this.customRole.code = 'VSB';
                    this.customRole.institution = 'Badan Informasi Geospasial (BIG)';
                    this.customRole.access_level = 'Level 2 - Verifikator Lapangan & Inspeksi Fisik';
                    this.customRole.description = 'Validasi batas poligon wilayah 3T batas negara, koreksi radius geofencing 250m titik koordinat sekolah, dan analisis citra satelit resolusi tinggi.';
                    this.customRole.permissions = {
                        domain1: { read: true, write: true, delete: false, verify: true },
                        domain2: { read: true, write: false, delete: false, verify: true },
                        domain3: { read: true, write: false, delete: false, verify: false },
                        domain4: { read: true, write: false, delete: false, verify: false },
                        domain5: { read: true, write: false, delete: false, verify: false },
                        domain6: { read: true, write: false, delete: false, verify: false }
                    };
                } else if (preset === 'kosong') {
                    this.customRole.name = '';
                    this.customRole.code = '';
                    this.customRole.institution = 'Kemendikbudristek RI / Ditjen PAUD Dikdasmen';
                    this.customRole.access_level = 'Level 1 - Akses Baca Spesifik & Pelaporan Lapangan';
                    this.customRole.description = '';
                    this.customRole.permissions = {
                        domain1: { read: false, write: false, delete: false, verify: false },
                        domain2: { read: false, write: false, delete: false, verify: false },
                        domain3: { read: false, write: false, delete: false, verify: false },
                        domain4: { read: false, write: false, delete: false, verify: false },
                        domain5: { read: false, write: false, delete: false, verify: false },
                        domain6: { read: false, write: false, delete: false, verify: false }
                    };
                }
            },

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
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold">✕</button>
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

                <!-- Action Buttons: Unduh CSV, Tambah Peran Kustom, & Registrasi Baru -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.export') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-full border border-slate-200/80 shadow-xs transition-all">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                        </svg>
                        Unduh Audit Log Keamanan (.CSV)
                    </a>

                    <!-- Trigger Tambah Peran Khusus Baru (PBI-14) -->
                    <button type="button" 
                            @click="addRoleModalOpen = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#14171F] hover:bg-slate-800 text-white text-xs font-bold rounded-full shadow-xs transition-all">
                        <svg class="w-4 h-4 text-[#FF5C2A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Tambah Peran Khusus
                    </button>

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
                    {{ $totalRolesCount }} Peran
                </span>
            </button>

            <!-- Tab 3: Investigasi Dugaan Fraud (Dummy Tampilan) -->
            <button type="button" 
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-full transition-all text-slate-600 hover:text-slate-800">
                Investigasi Dugaan Fraud
                <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-[#FF5C2A] text-white">
                    4 Tiket
                </span>
            </button>

            <!-- Tab 4: Sesi & Aktivitas (Dummy Tampilan) -->
            <button type="button" 
                    class="flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-full transition-all text-slate-600 hover:text-slate-800">
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
                        {{ $totalRolesCount }} Peran <span class="text-xs font-semibold text-slate-400">Definisi</span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500 mt-2 truncate max-w-[240px]" title="Admin, Auditor, Verifikator, CSR, Kepsek, Vendor{{ ($customRoles && $customRoles->count() > 0) ? ', ' . $customRoles->pluck('code')->implode(', ') : '' }}">
                        Admin, Auditor, Verifikator, CSR, Kepsek, Vendor{{ ($customRoles && $customRoles->count() > 0) ? ', ' . $customRoles->pluck('code')->implode(', ') : '' }}
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
                                
                                $channels = [
                                    'Kanal Satelit VSAT Prioritas',
                                    'SMS Gateway Cadangan (3T)',
                                    'Jaringan Terestrial 4G',
                                    'Fiber Optic Terestrial',
                                    'Terkoneksi VPN Enkripsi BSrE',
                                    'Citra Satelit Resolusi Tinggi'
                                ];
                                $userChannel = $channels[$u->id % count($channels)];

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
                                                Menunggu Verifikasi (OSS)
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
        <!-- TAB CONTENT 2: MATRIKS RBAC (PBI-14 FIGMA) -->
        <!-- ========================================== -->
        <div x-show="activeMainTab === 'rbac'" x-cloak class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-7 shadow-sm border border-slate-100">
            <!-- Header & ISO Compliance Subtitle + Legend -->
            <div class="flex flex-wrap items-start justify-between gap-4 pb-4 border-b border-slate-100">
                <div>
                    <h2 class="text-xl font-black text-[#14171F] tracking-tight">
                        Matriks Otorisasi Modul Terintegrasi
                    </h2>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Konfigurasi hak akses berbasis klausul ISO/IEC 27001:2022 A.9.2 (Manajemen Hak Istimewa Pengguna).
                    </p>
                </div>

                <!-- Right Legend (Badges preview) -->
                <div class="flex flex-wrap items-center gap-2 p-1.5 bg-[#F8FAFC] border border-slate-200/60 rounded-full text-[11px] font-bold">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Full Akses (W/R/D/A)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200">
                        <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                        Read-Only (R)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-[#FF5C2A] border border-orange-200">
                        <span class="w-2 h-2 rounded-full bg-[#FF5C2A]"></span>
                        Verification / Sign-Off (V)
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                        No Access (X)
                    </span>
                </div>
            </div>

            <!-- Action Sub-Bar: Sub-module summary & View Toggles -->
            <div class="flex flex-wrap items-center justify-between gap-3 py-3.5 border-b border-slate-100 mb-4">
                <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-medium">
                    <span>Menampilkan 6 Kategori Modul Utama (28 Sub-izin Fungsional)</span>
                    <span class="text-slate-300">•</span>
                    <span class="inline-flex items-center gap-1.5 text-emerald-700 font-semibold bg-emerald-50/80 px-2.5 py-1 rounded-full border border-emerald-200/60">
                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        Tervalidasi Kebijakan Privasi Siber
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Toggle Buka Semua / Ringkas -->
                    <div class="inline-flex p-1 bg-[#F8FAFC] border border-slate-200/70 rounded-full text-xs font-bold">
                        <button type="button" 
                                @click="expandedCategories = ['A', 'B', 'C', 'D', 'E', 'F']"
                                :class="expandedCategories.length === 6 ? 'bg-[#14171F] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1 rounded-full transition-all">
                            Buka Semua Sub-modul
                        </button>
                        <button type="button" 
                                @click="expandedCategories = []"
                                :class="expandedCategories.length === 0 ? 'bg-[#14171F] text-white shadow-2xs' : 'text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1 rounded-full transition-all">
                            Ringkas Tampilan
                        </button>
                    </div>

                    <!-- Trigger Tambah Peran Khusus Baru -->
                    <button type="button" 
                            @click="addRoleModalOpen = true"
                            class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white text-xs font-bold rounded-full shadow-2xs transition-all">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        + Tambah Peran Khusus
                    </button>
                </div>
            </div>

            <!-- RBAC Matrix Table (Matching Figma Exact Layout) -->
            <div class="overflow-x-auto border border-slate-200/80 rounded-2xl shadow-2xs">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-white border-b border-slate-200 text-xs">
                        <tr class="text-left font-bold text-slate-700">
                            <th class="py-4 px-4 text-[11px] font-black uppercase tracking-wider text-slate-500 w-[38%]">
                                KATEGORI &amp; MODUL FUNGSIONAL
                            </th>
                            <!-- Role 1: Admin -->
                            <th class="py-3.5 px-3 text-center border-l border-slate-100 w-[9%]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#14171F] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                    ADM
                                </span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1">Admin</div>
                            </th>
                            <!-- Role 2: Auditor -->
                            <th class="py-3.5 px-3 text-center border-l border-slate-100 w-[9%]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#0284C7] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                    AUD
                                </span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1">Auditor</div>
                            </th>
                            <!-- Role 3: Verifikator (BIG) -->
                            <th class="py-3.5 px-3 text-center border-l border-slate-100 w-[9%]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#059669] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                    BIG
                                </span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1">Verifikator</div>
                            </th>
                            <!-- Role 4: Mitra CSR -->
                            <th class="py-3.5 px-3 text-center border-l border-slate-100 w-[9%]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#D97706] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                    CSR
                                </span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1">Mitra CSR</div>
                            </th>
                            <!-- Role 5: Satdik 3T (KPS) -->
                            <th class="py-3.5 px-3 text-center border-l border-slate-100 w-[9%]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#334155] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                    KPS
                                </span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1">Satdik 3T</div>
                            </th>
                            <!-- Role 6: LPK Rekanan (VND) -->
                            <th class="py-3.5 px-3 text-center border-l border-slate-100 w-[9%]">
                                <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#64748B] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                    VND
                                </span>
                                <div class="text-[10px] font-semibold text-slate-500 mt-1">LPK Rekanan</div>
                            </th>

                            <!-- Dynamic Custom Roles Columns (if any) -->
                            @if ($customRoles && $customRoles->count() > 0)
                                @foreach ($customRoles as $cr)
                                    <th class="py-3.5 px-3 text-center border-l border-slate-100 bg-orange-50/30">
                                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#FF5C2A] text-white text-[10px] font-black tracking-wider shadow-2xs">
                                            {{ data_get($cr, 'code') }}
                                        </span>
                                        <div class="text-[10px] font-bold text-slate-700 mt-1 truncate max-w-[110px]" title="{{ data_get($cr, 'name') }}">
                                            {{ data_get($cr, 'name') }}
                                        </div>
                                    </th>
                                @endforeach
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @foreach ($rbacCategories as $cat)
                            <!-- Category Section Header Row -->
                            <tr class="bg-[#F8FAFC] border-y border-slate-200/80 cursor-pointer hover:bg-slate-100/80 transition-colors"
                                @click="expandedCategories.includes('{{ $cat['id'] }}') ? expandedCategories = expandedCategories.filter(c => c !== '{{ $cat['id'] }}') : expandedCategories.push('{{ $cat['id'] }}')">
                                <td colspan="{{ 7 + ($customRoles ? $customRoles->count() : 0) }}" class="py-3 px-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-6 h-6 rounded-lg bg-white border border-slate-200/80 flex items-center justify-center text-xs shadow-2xs">
                                                @if ($cat['icon'] === 'folder')
                                                    📁
                                                @elseif ($cat['icon'] === 'alert')
                                                    ⚠️
                                                @elseif ($cat['icon'] === 'hammer')
                                                    🏗️
                                                @elseif ($cat['icon'] === 'building')
                                                    🏢
                                                @elseif ($cat['icon'] === 'credit-card')
                                                    💳
                                                @else
                                                    ⚖️
                                                @endif
                                            </span>
                                            <span class="font-extrabold text-[#14171F] text-xs tracking-tight">
                                                {{ $cat['name'] }}
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 bg-slate-200/60 px-2 py-0.5 rounded-full">
                                                {{ count($cat['items']) }} Sub-modul
                                            </span>
                                        </div>

                                        <div class="flex items-center gap-2 text-slate-400">
                                            <span class="text-[10px] font-semibold" x-text="expandedCategories.includes('{{ $cat['id'] }}') ? 'Tutup' : 'Buka'"></span>
                                            <svg class="w-4 h-4 transition-transform duration-200 text-slate-400" 
                                                 :class="expandedCategories.includes('{{ $cat['id'] }}') ? 'rotate-180' : ''" 
                                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Sub-module Rows -->
                            @foreach ($cat['items'] as $item)
                                <tr x-show="expandedCategories.includes('{{ $cat['id'] }}')" 
                                    x-transition
                                    class="hover:bg-slate-50/70 transition-colors">
                                    <!-- Name & Description -->
                                    <td class="py-3 px-4 pl-8 align-middle">
                                        <div class="font-extrabold text-slate-900 text-xs">
                                            {{ $item['name'] }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                                            {{ $item['desc'] }}
                                        </div>
                                    </td>

                                    <!-- ADM (Admin) -->
                                    <td class="py-3 px-3 text-center border-l border-slate-100 align-middle">
                                        {!! $renderBadge($item['adm']) !!}
                                    </td>

                                    <!-- AUD (Auditor) -->
                                    <td class="py-3 px-3 text-center border-l border-slate-100 align-middle">
                                        {!! $renderBadge($item['aud']) !!}
                                    </td>

                                    <!-- BIG (Verifikator) -->
                                    <td class="py-3 px-3 text-center border-l border-slate-100 align-middle">
                                        {!! $renderBadge($item['big']) !!}
                                    </td>

                                    <!-- CSR (Mitra CSR) -->
                                    <td class="py-3 px-3 text-center border-l border-slate-100 align-middle">
                                        {!! $renderBadge($item['csr']) !!}
                                    </td>

                                    <!-- KPS (Satdik 3T) -->
                                    <td class="py-3 px-3 text-center border-l border-slate-100 align-middle">
                                        {!! $renderBadge($item['kps']) !!}
                                    </td>

                                    <!-- VND (LPK Rekanan) -->
                                    <td class="py-3 px-3 text-center border-l border-slate-100 align-middle">
                                        {!! $renderBadge($item['vnd']) !!}
                                    </td>

                                    <!-- Custom Roles (Dynamic) -->
                                    @if ($customRoles && $customRoles->count() > 0)
                                        @foreach ($customRoles as $cr)
                                            @php
                                                $catDomainKey = $cat['domain_key'];
                                                $allPerms = data_get($cr, 'permissions', []);
                                                $perms = is_array($allPerms) ? ($allPerms[$catDomainKey] ?? []) : [];
                                                $hasVerify = !empty($perms['verify']);
                                                $hasWrite = !empty($perms['write']);
                                                $hasRead = !empty($perms['read']);
                                                $crType = $hasVerify ? 'verify' : ($hasWrite ? 'full' : ($hasRead ? 'read' : 'none'));
                                            @endphp
                                            <td class="py-3 px-3 text-center border-l border-slate-100 align-middle bg-slate-50/30">
                                                {!! $renderBadge($crType) !!}
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer: Security Compliance Note & Ekspor PDF Regulasi -->
            <div class="flex flex-wrap items-center justify-between gap-4 mt-5 pt-4 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2 text-slate-500 font-medium">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                    <span>Perubahan hak akses membutuhkan otentikasi biometrik / Passkey FIDO2 oleh Auditor Utama Kemendikbud.</span>
                </div>

                <button type="button" 
                        onclick="window.print()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-full border border-slate-200/80 shadow-2xs transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Ekspor PDF Regulasi
                </button>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL: TAMBAH PERAN KHUSUS BARU (CUSTOM RBAC ROLE) (PBI-14) -->
        <!-- ======================================================== -->
        <div x-show="addRoleModalOpen" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex justify-center items-start sm:items-center p-3 sm:p-5">
            <div @click.away="addRoleModalOpen = false" 
                 class="bg-white rounded-3xl max-w-3xl w-full max-h-[92vh] flex flex-col shadow-2xl border border-slate-100 overflow-hidden my-auto">
                
                <!-- Modal Top Header & Badges (Sticky Header) -->
                <div class="p-5 sm:p-6 pb-4 border-b border-slate-100 flex items-start justify-between shrink-0 bg-white">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-orange-50 text-[#FF5C2A] border border-orange-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#FF5C2A]"></span>
                                Definisi Peran Kustom • PBI-14
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                ISO/IEC 27001 Compliant
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                                Prinsip Least-Privilege
                            </span>
                        </div>
                        <h2 class="text-lg sm:text-xl font-black text-[#14171F] tracking-tight">
                            Tambah Peran Khusus Baru (Custom RBAC Role)
                        </h2>
                        <p class="text-[11px] sm:text-xs text-slate-500 mt-1 leading-relaxed">
                            Konfigurasikan kode peran, hierarki instansi penanggung jawab, dan matriks hak akses modular berbasis prinsip <em>Least-Privilege</em> &amp; standar kepatuhan BSrE BSSN.
                        </p>
                    </div>

                    <button type="button" @click="addRoleModalOpen = false" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-slate-100 text-lg transition-colors shrink-0 ml-3">
                        ✕
                    </button>
                </div>

                <!-- Form Section with Scrollable Body & Fixed Footer -->
                <form action="{{ route('admin.users.custom-roles.store') }}" method="POST" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    @csrf

                    <!-- Scrollable Form Body -->
                    <div class="overflow-y-auto p-5 sm:p-6 space-y-5 flex-1">
                        <!-- SECTION 01: IDENTITAS & KLASIFIKASI PERAN -->
                    <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-slate-200/70">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-200/60 mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center text-xs font-black">
                                    01
                                </span>
                                <div>
                                    <h3 class="text-xs font-black text-[#14171F]">Identitas &amp; Klasifikasi Peran</h3>
                                    <p class="text-[10px] text-slate-400">Atribut formal wewenang otoritas, penamaan sistem, dan instansi penanggung jawab.</p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-orange-100 text-[#FF5C2A]">
                                Wajib Diisi
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                            <!-- Nama Peran Resmi -->
                            <div class="md:col-span-2">
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Nama Peran Resmi (Role Name) <span class="text-[#FF5C2A]">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0M12 12.75h.008v.008H12v-.008z" />
                                        </svg>
                                    </span>
                                    <input type="text" 
                                           name="name" 
                                           x-model="customRole.name" 
                                           required
                                           placeholder="Contoh: Auditor Khusus Satgassus Tipikor Daerah 3T"
                                           class="w-full pl-9 pr-3 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 focus:border-[#FF5C2A] focus:outline-none">
                                </div>
                            </div>

                            <!-- Kode Peran -->
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-[11px] font-bold text-slate-700">
                                        Kode Peran <span class="text-[#FF5C2A]">*</span>
                                    </label>
                                    <span class="text-[9px] text-slate-400">3-4 Huruf Kapital</span>
                                </div>
                                <div class="relative flex items-center">
                                    <input type="text" 
                                           name="code" 
                                           x-model="customRole.code" 
                                           maxlength="6"
                                           required
                                           @input="customRole.code = customRole.code.toUpperCase()"
                                           placeholder="STP"
                                           class="w-full px-3 py-2 text-xs font-black tracking-wider text-slate-900 rounded-xl bg-white border border-slate-200 focus:border-[#FF5C2A] focus:outline-none">
                                    <span class="absolute right-2 px-1.5 py-0.5 text-[9px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 rounded">
                                        Tersedia
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Instansi & Level Akses -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Instansi / Entitas Induk Wewenang <span class="text-[#FF5C2A]">*</span>
                                </label>
                                <select name="institution" x-model="customRole.institution" required class="w-full px-3 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 focus:border-[#FF5C2A] focus:outline-none">
                                    <option value="KPK / Satgas Investigasi Tipikor Irjen">KPK / Satgas Investigasi Tipikor Irjen</option>
                                    <option value="Kemendikbudristek RI / Ditjen PAUD Dikdasmen">Kemendikbudristek RI / Ditjen PAUD Dikdasmen</option>
                                    <option value="Inspektorat Jenderal (Itjen Kemendikbud)">Inspektorat Jenderal (Itjen Kemendikbud)</option>
                                    <option value="Kementerian PUPR / Balai Cipta Karya">Kementerian PUPR / Balai Cipta Karya</option>
                                    <option value="Badan Informasi Geospasial (BIG)">Badan Informasi Geospasial (BIG)</option>
                                    <option value="Badan Pengawasan Keuangan dan Pembangunan (BPKP)">Badan Pengawasan Keuangan dan Pembangunan (BPKP)</option>
                                    <option value="Konsorsium BUMN / Mitra CSR Nasional">Konsorsium BUMN / Mitra CSR Nasional</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Tingkat Granularitas / Level Akses <span class="text-[#FF5C2A]">*</span>
                                </label>
                                <select name="access_level" x-model="customRole.access_level" required class="w-full px-3 py-2 text-xs font-semibold rounded-xl bg-white border border-slate-200 focus:border-[#FF5C2A] focus:outline-none">
                                    <option value="Level 3 - Audit Forensik & Verifikasi Spasial (Ad-Hoc)">Level 3 - Audit Forensik &amp; Verifikasi Spasial (Ad-Hoc)</option>
                                    <option value="Level 1 - Pengawas Publik & Monitoring Umum">Level 1 - Pengawas Publik &amp; Monitoring Umum</option>
                                    <option value="Level 2 - Verifikator Lapangan & Inspeksi Fisik">Level 2 - Verifikator Lapangan &amp; Inspeksi Fisik</option>
                                    <option value="Level 4 - Otorisator Anggaran & Administrator Penuh">Level 4 - Otorisator Anggaran &amp; Administrator Penuh</option>
                                </select>
                            </div>
                        </div>

                        <!-- Deskripsi Tugas & SK -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                Deskripsi Tugas, Landasan SK &amp; Wewenang Audit <span class="text-[#FF5C2A]">*</span>
                            </label>
                            <textarea name="description" 
                                      x-model="customRole.description"
                                      rows="3" 
                                      required
                                      class="w-full p-3 text-xs leading-relaxed rounded-xl bg-white border border-slate-200 focus:border-[#FF5C2A] focus:outline-none"></textarea>
                        </div>
                    </div>

                    <!-- SECTION 02: MATRIKS OTORISASI & HAK AKSES MODUL -->
                    <div class="p-5 rounded-2xl bg-[#F8FAFC] border border-slate-200/70">
                        <div class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-slate-200/60 mb-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-6 h-6 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center text-xs font-black">
                                    02
                                </span>
                                <div>
                                    <h3 class="text-xs font-black text-[#14171F]">Matriks Otorisasi &amp; Hak Akses Modul</h3>
                                    <p class="text-[10px] text-slate-400">Pilih konfigurasi izin granular pada 6 domain inti infrastruktur 3T.</p>
                                </div>
                            </div>

                            <!-- Presets Pills -->
                            <div class="flex items-center gap-1.5">
                                <button type="button" 
                                        @click="applyPreset('investigatif')"
                                        :class="activePreset === 'investigatif' ? 'bg-orange-50 border-[#FF5C2A] text-[#FF5C2A] font-extrabold' : 'bg-white text-slate-600 border-slate-200'"
                                        class="px-2.5 py-1 text-[10px] rounded-full border transition-all">
                                    Audit Khusus (Investigatif)
                                </button>
                                <button type="button" 
                                        @click="applyPreset('finansial')"
                                        :class="activePreset === 'finansial' ? 'bg-orange-50 border-[#FF5C2A] text-[#FF5C2A] font-extrabold' : 'bg-white text-slate-600 border-slate-200'"
                                        class="px-2.5 py-1 text-[10px] rounded-full border transition-all">
                                    Pengawas Finansial
                                </button>
                                <button type="button" 
                                        @click="applyPreset('spasial')"
                                        :class="activePreset === 'spasial' ? 'bg-orange-50 border-[#FF5C2A] text-[#FF5C2A] font-extrabold' : 'bg-white text-slate-600 border-slate-200'"
                                        class="px-2.5 py-1 text-[10px] rounded-full border transition-all">
                                    Verifikator Spasial BIG
                                </button>
                                <button type="button" 
                                        @click="applyPreset('kosong')"
                                        :class="activePreset === 'kosong' ? 'bg-orange-50 border-[#FF5C2A] text-[#FF5C2A] font-extrabold' : 'bg-white text-slate-600 border-slate-200'"
                                        class="px-2.5 py-1 text-[10px] rounded-full border transition-all">
                                    + Kosongkan Form (Kustom Bebas)
                                </button>
                            </div>
                        </div>

                        <!-- 6 Modules Permissions Checklist Grid -->
                        <div class="space-y-3">
                            <!-- Module 1: Master Wilayah & Sekolah 3T -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-[#14171F]">1. Master Wilayah &amp; Sekolah 3T</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">Domain 1</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">NPSN, geolokasi batas terluar, validasi kepemilikan lahan satdik 3T.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label :class="customRole.permissions.domain1.read ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain1][read]" x-model="customRole.permissions.domain1.read" class="rounded text-emerald-600 focus:ring-0"> Read
                                    </label>
                                    <label :class="customRole.permissions.domain1.write ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain1][write]" x-model="customRole.permissions.domain1.write" class="rounded text-blue-600 focus:ring-0"> Write / Create
                                    </label>
                                    <label :class="customRole.permissions.domain1.delete ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain1][delete]" x-model="customRole.permissions.domain1.delete" class="rounded text-rose-600 focus:ring-0"> Delete
                                    </label>
                                    <label :class="customRole.permissions.domain1.verify ? 'bg-orange-50 text-[#FF5C2A] border-orange-300 font-extrabold' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain1][verify]" x-model="customRole.permissions.domain1.verify" class="rounded text-[#FF5C2A] focus:ring-0"> Verification
                                    </label>
                                </div>
                            </div>

                            <!-- Module 2: Pelaporan Kerusakan & Media EXIF -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-[#14171F]">2. Pelaporan Kerusakan &amp; Media EXIF</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">Domain 2</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Unggahan foto kerusakan, metadata EXIF GPS (&lt;250m), dan tingkat urgensi fasilitas.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label :class="customRole.permissions.domain2.read ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain2][read]" x-model="customRole.permissions.domain2.read" class="rounded text-emerald-600 focus:ring-0"> Read
                                    </label>
                                    <label :class="customRole.permissions.domain2.write ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain2][write]" x-model="customRole.permissions.domain2.write" class="rounded text-blue-600 focus:ring-0"> Write / Create
                                    </label>
                                    <label :class="customRole.permissions.domain2.delete ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain2][delete]" x-model="customRole.permissions.domain2.delete" class="rounded text-rose-600 focus:ring-0"> Delete
                                    </label>
                                    <label :class="customRole.permissions.domain2.verify ? 'bg-orange-50 text-[#FF5C2A] border-orange-300 font-extrabold' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain2][verify]" x-model="customRole.permissions.domain2.verify" class="rounded text-[#FF5C2A] focus:ring-0"> Verification
                                    </label>
                                </div>
                            </div>

                            <!-- Module 3: Proyek Renovasi & Rencana Anggaran (RAB) -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5v1.409l4.242 4.243" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-[#14171F]">3. Proyek Renovasi &amp; Rencana Anggaran (RAB)</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">Domain 3</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Item pekerjaan fisik, kurva S milestone, dan validasi spesifikasi harga satuan wilayah 3T.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label :class="customRole.permissions.domain3.read ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain3][read]" x-model="customRole.permissions.domain3.read" class="rounded text-emerald-600 focus:ring-0"> Read
                                    </label>
                                    <label :class="customRole.permissions.domain3.write ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain3][write]" x-model="customRole.permissions.domain3.write" class="rounded text-blue-600 focus:ring-0"> Write / Create
                                    </label>
                                    <label :class="customRole.permissions.domain3.delete ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain3][delete]" x-model="customRole.permissions.domain3.delete" class="rounded text-rose-600 focus:ring-0"> Delete
                                    </label>
                                    <label :class="customRole.permissions.domain3.verify ? 'bg-orange-50 text-[#FF5C2A] border-orange-300 font-extrabold' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain3][verify]" x-model="customRole.permissions.domain3.verify" class="rounded text-[#FF5C2A] focus:ring-0"> Verification
                                    </label>
                                </div>
                            </div>

                            <!-- Module 4: Matching CSR & Pencairan Dana Vendor -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-[#14171F]">4. Matching CSR &amp; Pencairan Dana Vendor</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">Domain 4</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Penerimaan donasi mitra CSR, termin pencairan, dan audit rekening escrow.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label :class="customRole.permissions.domain4.read ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain4][read]" x-model="customRole.permissions.domain4.read" class="rounded text-emerald-600 focus:ring-0"> Read
                                    </label>
                                    <label :class="customRole.permissions.domain4.write ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain4][write]" x-model="customRole.permissions.domain4.write" class="rounded text-blue-600 focus:ring-0"> Write / Create
                                    </label>
                                    <label :class="customRole.permissions.domain4.delete ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain4][delete]" x-model="customRole.permissions.domain4.delete" class="rounded text-rose-600 focus:ring-0"> Delete
                                    </label>
                                    <label :class="customRole.permissions.domain4.verify ? 'bg-orange-50 text-[#FF5C2A] border-orange-300 font-extrabold' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain4][verify]" x-model="customRole.permissions.domain4.verify" class="rounded text-[#FF5C2A] focus:ring-0"> Verification
                                    </label>
                                </div>
                            </div>

                            <!-- Module 5: Tata Kelola Akun, Registrasi & RBAC -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.765l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-[#14171F]">5. Tata Kelola Akun, Registrasi &amp; RBAC</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">Domain 5</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Persetujuan akun pendaftar, manajemen matriks hak akses, dan audit log sesi BSrE.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label :class="customRole.permissions.domain5.read ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain5][read]" x-model="customRole.permissions.domain5.read" class="rounded text-emerald-600 focus:ring-0"> Read
                                    </label>
                                    <label :class="customRole.permissions.domain5.write ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain5][write]" x-model="customRole.permissions.domain5.write" class="rounded text-blue-600 focus:ring-0"> Write / Create
                                    </label>
                                    <label :class="customRole.permissions.domain5.delete ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain5][delete]" x-model="customRole.permissions.domain5.delete" class="rounded text-rose-600 focus:ring-0"> Delete
                                    </label>
                                    <label :class="customRole.permissions.domain5.verify ? 'bg-orange-50 text-[#FF5C2A] border-orange-300 font-extrabold' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain5][verify]" x-model="customRole.permissions.domain5.verify" class="rounded text-[#FF5C2A] focus:ring-0"> Verification
                                    </label>
                                </div>
                            </div>

                            <!-- Module 6: Investigasi Dugaan Fraud & Pembekuan -->
                            <div class="p-3.5 bg-white rounded-xl border border-slate-200/80 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 flex items-center justify-center text-[#FF5C2A] shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-[#14171F]">6. Investigasi Dugaan Fraud &amp; Pembekuan</span>
                                            <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-orange-100 text-[#FF5C2A]">Anti-Fraud</span>
                                        </div>
                                        <p class="text-[10px] text-slate-400">Eskalasi tiket temuan kecurangan, rekomendasi blacklist vendor, dan pelaporan langsung ke Irjen.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label :class="customRole.permissions.domain6.read ? 'bg-emerald-50 text-emerald-700 border-emerald-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain6][read]" x-model="customRole.permissions.domain6.read" class="rounded text-emerald-600 focus:ring-0"> Read
                                    </label>
                                    <label :class="customRole.permissions.domain6.write ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain6][write]" x-model="customRole.permissions.domain6.write" class="rounded text-blue-600 focus:ring-0"> Write / Create
                                    </label>
                                    <label :class="customRole.permissions.domain6.delete ? 'bg-rose-50 text-rose-700 border-rose-300' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain6][delete]" x-model="customRole.permissions.domain6.delete" class="rounded text-rose-600 focus:ring-0"> Delete
                                    </label>
                                    <label :class="customRole.permissions.domain6.verify ? 'bg-orange-50 text-[#FF5C2A] border-orange-300 font-extrabold' : 'bg-slate-50 text-slate-600 border-slate-200'" class="px-2 py-1 text-[10px] font-bold rounded border cursor-pointer flex items-center gap-1.5">
                                        <input type="checkbox" name="permissions[domain6][verify]" x-model="customRole.permissions.domain6.verify" class="rounded text-[#FF5C2A] focus:ring-0"> Verification
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>

                    <!-- Fixed Modal Actions Footer -->
                    <div class="p-4 px-6 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 shrink-0">
                        <button type="button" 
                                @click="addRoleModalOpen = false" 
                                class="px-5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-200/60 rounded-full transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-extrabold text-white bg-[#FF5C2A] hover:bg-[#e04f22] rounded-full shadow-md hover:shadow-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan &amp; Terapkan Peran Baru
                        </button>
                    </div>
                </form>

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
                    <button type="button" @click="editModalOpen = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">✕</button>
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
                                <optgroup label="Persona Standar PRD">
                                    @foreach ($roles as $r)
                                        <option value="{{ $r->value }}">{{ $r->label() }} ({{ $r->value }})</option>
                                    @endforeach
                                </optgroup>
                                @if ($customRoles && $customRoles->count() > 0)
                                    <optgroup label="Peran Khusus Kustom">
                                        @foreach ($customRoles as $cr)
                                            <option value="{{ strtolower(data_get($cr, 'code')) }}">{{ data_get($cr, 'name') }} ({{ data_get($cr, 'code') }})</option>
                                        @endforeach
                                    </optgroup>
                                @endif
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
