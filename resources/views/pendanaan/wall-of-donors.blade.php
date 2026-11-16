<x-layouts.app active="csr">
    <div 
        x-data="{
            activeTab: 'all',
            selectedDonor: null,
            donorDetailOpen: false,
            bannerDismissed: false,
            openDonorDetail(donor) {
                this.selectedDonor = donor;
                this.donorDetailOpen = true;
            },
            disbursements: [
                {
                    code: 'TRX-CSR-041',
                    date: '12 Okt 2026',
                    school: 'SDN Waisika',
                    location: 'Kab. Alor, NTT',
                    donor: 'PT Telkom Indonesia',
                    unit: 'CSR Divre VII',
                    amount: 150000000,
                    amount_formatted: 'Rp 150.000.000',
                    stage: 'Termin 1 (Pondasi)',
                    term_key: 'termin1',
                    verification_type: 'drone',
                    verification_label: 'Drone Terverifikasi',
                    bap_file: 'BAP_Termin1.pdf'
                },
                {
                    code: 'TRX-CSR-039',
                    date: '09 Okt 2026',
                    school: 'SMP Satap Asmat',
                    location: 'Kab. Asmat, Papua',
                    donor: 'PT Astra International',
                    unit: 'Yayasan Pendidikan Astra',
                    amount: 180000000,
                    amount_formatted: 'Rp 180.000.000',
                    stage: 'Termin 2 (Struktur)',
                    term_key: 'termin2',
                    verification_type: 'tide',
                    verification_label: 'Pasang-Surut Selesai',
                    bap_file: 'BAP_Termin2.pdf'
                },
                {
                    code: 'TRX-CSR-035',
                    date: '03 Okt 2026',
                    school: 'SDN 02 Sambas',
                    location: 'Kab. Sambas, Kalbar',
                    donor: 'PT BRI (Persero) Tbk',
                    unit: 'BRI Peduli 3T',
                    amount: 255000000,
                    amount_formatted: 'Rp 255.000.000',
                    stage: 'Termin 3 Final',
                    term_key: 'termin3',
                    verification_type: 'full',
                    verification_label: '100% Serah Terima',
                    bap_file: 'BAP_Final.pdf'
                }
            ],
            get filteredDisbursements() {
                if (this.activeTab === 'all') return this.disbursements;
                return this.disbursements.filter(d => d.term_key === this.activeTab);
            }
        }"
        class="space-y-8 pb-16 pt-4 max-w-7xl mx-auto"
    >
        <!-- ======================================================== -->
        <!-- 1. TOP NAV PILLS & BREADCRUMB BAR                       -->
        <!-- ======================================================== -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <a 
                    href="{{ route('pendanaan.index') }}" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/90 rounded-2xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-[#FF5C2A] hover:border-slate-300 shadow-xs transition-all group"
                >
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-[#FF5C2A] transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span>Katalog Kampanye Sekolah 3T</span>
                </a>
            </div>

            <div class="flex items-center gap-2.5">
                <a 
                    href="#audit-log" 
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/90 rounded-2xl text-xs sm:text-sm font-semibold text-slate-700 hover:text-slate-900 hover:border-slate-300 shadow-xs transition-all"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Laporan Audit BPKP</span>
                </a>

                @if (Route::has('registration.index'))
                    <a 
                        href="{{ route('registration.index') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white rounded-2xl text-xs sm:text-sm font-bold shadow-xs transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Registrasi Mitra Baru</span>
                    </a>
                @else
                    <a 
                        href="{{ route('pendanaan.index') }}" 
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white rounded-2xl text-xs sm:text-sm font-bold shadow-xs transition-all cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Registrasi Mitra Baru</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 2. TITLE & HEADER TRANSPARANSI                           -->
        <!-- ======================================================== -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pt-2">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 text-xs font-bold tracking-wider text-[#FF5C2A] uppercase">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Transparansi &amp; Akuntabilitas Fiskal 3T</span>
                </div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#14171F] tracking-tight">
                    Wall of Donors &amp; Penyaluran CSR
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-3xl leading-relaxed">
                    Alokasi riil dana CSR mitra korporasi terintegrasi BAP Kemendikbudristek &amp; insentif Super Tax Deduction 200%.
                </p>
            </div>

            <div class="flex items-center gap-2 self-start md:self-auto px-3.5 py-1.5 bg-emerald-50 border border-emerald-200 rounded-full text-xs font-semibold text-emerald-700">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sinkron Dapodik Q4-2026</span>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 3. 4 STAT CARDS (AGREGASI METRIK)                        -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1: Total Dana CSR Terserap -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Total Dana CSR Terserap
                </p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-[#14171F]">
                    Rp 18,42 M
                </h3>
                <div class="flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>100% Diaudit BPKP</span>
                </div>
            </div>

            <!-- Card 2: Sekolah 3T Terehabilitasi -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Sekolah 3T Terehabilitasi
                </p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-[#14171F]">
                    64 Sekolah
                </h3>
                <p class="text-xs font-medium text-slate-500">
                    14.820 Siswa Terfasilitasi
                </p>
            </div>

            <!-- Card 3: Total Dana Escrow Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Total Dana Escrow Aktif
                </p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-[#14171F]">
                    Rp 4,75 M
                </h3>
                <div class="flex items-center gap-1.5 text-xs font-medium text-amber-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span>22 Proyek Konstruksi</span>
                </div>
            </div>

            <!-- Card 4: Mitra Korporasi Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <p class="text-[11px] font-bold tracking-wider text-slate-400 uppercase">
                    Mitra Korporasi Aktif
                </p>
                <h3 class="text-2xl sm:text-3xl font-extrabold text-[#14171F]">
                    42 Mitra
                </h3>
                <div class="flex items-center gap-1.5 text-xs font-medium text-emerald-600">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>Fasilitas Tax 200%</span>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 4. SECTION: TOP CORPORATE CONTRIBUTORS (HONOR ROLL)      -->
        <!-- ======================================================== -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <div class="flex items-center gap-2.5">
                    <h2 class="text-lg sm:text-xl font-extrabold text-[#14171F]">
                        Top Corporate Contributors
                    </h2>
                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-600 rounded-md text-[11px] font-bold tracking-wide">
                        Honor Roll CSR
                    </span>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                    <span>Urutan:</span>
                    <span class="font-bold text-slate-800">Alokasi Terbesar</span>
                </div>
            </div>

            <!-- Grid 6 Contributors Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- 1. PT Telkom Indonesia -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-[#FFF1EB] text-[#FF5C2A] font-extrabold text-sm flex items-center justify-center shrink-0">
                                TL
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-1">
                                    PT Telkom Indonesia Tbk
                                </h4>
                                <p class="text-xs text-slate-500">BUMN Telekomunikasi</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            Platinum
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50/80 rounded-xl">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Alokasi</p>
                            <p class="text-sm font-extrabold text-slate-900">Rp 3,2 Miliar</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Rehabilitasi</p>
                            <p class="text-sm font-extrabold text-[#FF5C2A]">8 Sekolah 3T</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tax Deduction Siap</span>
                        </div>
                        <button 
                            type="button" 
                            @click="openDonorDetail({ name: 'PT Telkom Indonesia Tbk', sector: 'BUMN Telekomunikasi', tier: 'Platinum', allocation: 'Rp 3.200.000.000', schools: 8, pmk_status: 'Terbit BAP Kemendikbud' })" 
                            class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                        >
                            Detail &rsaquo;
                        </button>
                    </div>
                </div>

                <!-- 2. PT Bank Rakyat Indonesia -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 font-extrabold text-sm flex items-center justify-center shrink-0">
                                BR
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-1">
                                    PT Bank Rakyat Indonesia Tbk
                                </h4>
                                <p class="text-xs text-slate-500">BUMN Perbankan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            Platinum
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50/80 rounded-xl">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Alokasi</p>
                            <p class="text-sm font-extrabold text-slate-900">Rp 2,8 Miliar</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Rehabilitasi</p>
                            <p class="text-sm font-extrabold text-[#FF5C2A]">7 Sekolah 3T</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tax Deduction Siap</span>
                        </div>
                        <button 
                            type="button" 
                            @click="openDonorDetail({ name: 'PT Bank Rakyat Indonesia Tbk', sector: 'BUMN Perbankan', tier: 'Platinum', allocation: 'Rp 2.800.000.000', schools: 7, pmk_status: 'Terbit BAP Kemendikbud' })" 
                            class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                        >
                            Detail &rsaquo;
                        </button>
                    </div>
                </div>

                <!-- 3. PT Astra International -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 font-extrabold text-sm flex items-center justify-center shrink-0">
                                AS
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-1">
                                    PT Astra International Tbk
                                </h4>
                                <p class="text-xs text-slate-500">Konglomerasi Industri</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            Gold
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50/80 rounded-xl">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Alokasi</p>
                            <p class="text-sm font-extrabold text-slate-900">Rp 2,1 Miliar</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Rehabilitasi</p>
                            <p class="text-sm font-extrabold text-[#FF5C2A]">5 Sekolah 3T</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tax Deduction Siap</span>
                        </div>
                        <button 
                            type="button" 
                            @click="openDonorDetail({ name: 'PT Astra International Tbk', sector: 'Konglomerasi Industri', tier: 'Gold', allocation: 'Rp 2.100.000.000', schools: 5, pmk_status: 'Terbit BAP Kemendikbud' })" 
                            class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                        >
                            Detail &rsaquo;
                        </button>
                    </div>
                </div>

                <!-- 4. PT Indofood CBP Sukses Makmur -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-orange-50 text-orange-600 font-extrabold text-sm flex items-center justify-center shrink-0">
                                IC
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-1">
                                    PT Indofood CBP Tbk
                                </h4>
                                <p class="text-xs text-slate-500">FMCG &amp; Pangan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            Gold
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50/80 rounded-xl">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Alokasi</p>
                            <p class="text-sm font-extrabold text-slate-900">Rp 1,5 Miliar</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Rehabilitasi</p>
                            <p class="text-sm font-extrabold text-[#FF5C2A]">4 Sekolah 3T</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tax Deduction Siap</span>
                        </div>
                        <button 
                            type="button" 
                            @click="openDonorDetail({ name: 'PT Indofood CBP Tbk', sector: 'FMCG & Pangan', tier: 'Gold', allocation: 'Rp 1.500.000.000', schools: 4, pmk_status: 'Terbit BAP Kemendikbud' })" 
                            class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                        >
                            Detail &rsaquo;
                        </button>
                    </div>
                </div>

                <!-- 5. Yayasan Bakti BCA -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-sm flex items-center justify-center shrink-0">
                                BC
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-1">
                                    Yayasan Bakti BCA
                                </h4>
                                <p class="text-xs text-slate-500">Filantropi Swasta</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-200 text-slate-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            Silver
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50/80 rounded-xl">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Alokasi</p>
                            <p class="text-sm font-extrabold text-slate-900">Rp 1,2 Miliar</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Rehabilitasi</p>
                            <p class="text-sm font-extrabold text-[#FF5C2A]">3 Sekolah 3T</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tax Deduction Siap</span>
                        </div>
                        <button 
                            type="button" 
                            @click="openDonorDetail({ name: 'Yayasan Bakti BCA', sector: 'Filantropi Swasta', tier: 'Silver', allocation: 'Rp 1.200.000.000', schools: 3, pmk_status: 'Terbit BAP Kemendikbud' })" 
                            class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                        >
                            Detail &rsaquo;
                        </button>
                    </div>
                </div>

                <!-- 6. MIND ID -->
                <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all flex flex-col justify-between space-y-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 font-extrabold text-sm flex items-center justify-center shrink-0">
                                MI
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm sm:text-base text-slate-900 line-clamp-1">
                                    MIND ID
                                </h4>
                                <p class="text-xs text-slate-500">Holding BUMN Pertambangan</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 bg-slate-200 text-slate-800 text-[10px] font-bold rounded-lg uppercase tracking-wider">
                            Silver
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3 bg-slate-50/80 rounded-xl">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Alokasi</p>
                            <p class="text-sm font-extrabold text-slate-900">Rp 1,1 Miliar</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400">Rehabilitasi</p>
                            <p class="text-sm font-extrabold text-[#FF5C2A]">3 Sekolah 3T</p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                        <div class="flex items-center gap-1.5 text-emerald-600 font-medium">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Tax Deduction Siap</span>
                        </div>
                        <button 
                            type="button" 
                            @click="openDonorDetail({ name: 'MIND ID', sector: 'Holding BUMN Pertambangan', tier: 'Silver', allocation: 'Rp 1.100.000.000', schools: 3, pmk_status: 'Terbit BAP Kemendikbud' })" 
                            class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                        >
                            Detail &rsaquo;
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 5. SECTION: REAL-TIME ESCROW & DISBURSEMENT AUDIT LOG     -->
        <!-- ======================================================== -->
        <div id="audit-log" class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- Table Header Bar -->
            <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#FFF1EB] text-[#FF5C2A] flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-extrabold text-[#14171F]">
                            Real-Time Escrow &amp; Disbursement Audit Log
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 pl-10.5">
                        Buku besar digital rekonsiliasi termin konstruksi LPJK dan Berita Acara Penerimaan (BAP).
                    </p>
                </div>

                <!-- Tabs Filter (Alpine.js) -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl overflow-x-auto">
                    <button 
                        type="button" 
                        @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-[#14171F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                    >
                        Semua Pencairan
                    </button>
                    <button 
                        type="button" 
                        @click="activeTab = 'termin1'"
                        :class="activeTab === 'termin1' ? 'bg-[#14171F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                    >
                        Termin 1 (30%)
                    </button>
                    <button 
                        type="button" 
                        @click="activeTab = 'termin2'"
                        :class="activeTab === 'termin2' ? 'bg-[#14171F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                    >
                        Termin 2 (60%)
                    </button>
                    <button 
                        type="button" 
                        @click="activeTab = 'termin3'"
                        :class="activeTab === 'termin3' ? 'bg-[#14171F] text-white shadow-xs' : 'text-slate-600 hover:text-slate-900'"
                        class="px-3.5 py-1.5 text-xs font-bold rounded-lg transition-all cursor-pointer whitespace-nowrap"
                    >
                        Termin 3 (100%)
                    </button>
                </div>
            </div>

            <!-- Responsive Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead class="bg-slate-50 text-[11px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">ID &amp; Tanggal</th>
                            <th class="py-3.5 px-6">Sekolah Sasaran &amp; Wilayah</th>
                            <th class="py-3.5 px-6">Mitra Donatur</th>
                            <th class="py-3.5 px-6">Nominal &amp; Termin</th>
                            <th class="py-3.5 px-6">Verifikasi Lapangan</th>
                            <th class="py-3.5 px-6 text-right">Dokumen BAP</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <template x-for="item in filteredDisbursements" :key="item.code">
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <!-- ID & Tanggal -->
                                <td class="py-4 px-6">
                                    <p class="font-extrabold text-slate-900" x-text="item.code"></p>
                                    <p class="text-xs text-slate-400" x-text="item.date"></p>
                                </td>

                                <!-- Sekolah Sasaran -->
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-900" x-text="item.school"></p>
                                    <p class="text-xs text-slate-500" x-text="item.location"></p>
                                </td>

                                <!-- Mitra Donatur -->
                                <td class="py-4 px-6">
                                    <p class="font-bold text-slate-900" x-text="item.donor"></p>
                                    <p class="text-xs text-slate-500" x-text="item.unit"></p>
                                </td>

                                <!-- Nominal & Termin -->
                                <td class="py-4 px-6">
                                    <p class="font-extrabold text-slate-900" x-text="item.amount_formatted"></p>
                                    <p class="text-xs text-slate-500" x-text="item.stage"></p>
                                </td>

                                <!-- Verifikasi Lapangan -->
                                <td class="py-4 px-6">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold"
                                        :class="{
                                            'bg-emerald-50 text-emerald-700 border border-emerald-200': item.verification_type === 'drone' || item.verification_type === 'full',
                                            'bg-amber-50 text-amber-700 border border-amber-200': item.verification_type === 'tide'
                                        }"
                                    >
                                        <template x-if="item.verification_type === 'drone'">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                            </svg>
                                        </template>
                                        <template x-if="item.verification_type === 'tide'">
                                            <svg class="w-3.5 h-3.5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </template>
                                        <template x-if="item.verification_type === 'full'">
                                            <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                            </svg>
                                        </template>
                                        <span x-text="item.verification_label"></span>
                                    </div>
                                </td>

                                <!-- Dokumen BAP -->
                                <td class="py-4 px-6 text-right">
                                    <a 
                                        href="#" 
                                        @click.prevent="alert('Mengunduh ' + item.bap_file + ' dari arsip digital Kemendikbudristek')" 
                                        class="inline-flex items-center gap-1.5 font-bold text-red-500 hover:text-red-700 transition-colors text-xs"
                                    >
                                        <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/>
                                        </svg>
                                        <span x-text="item.bap_file"></span>
                                    </a>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredDisbursements.length === 0">
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                                Tidak ada log transaksi untuk filter termin ini.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Table Footer Pagination bar -->
            <div class="p-4 px-6 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500 font-medium">
                <span x-text="'Menampilkan ' + filteredDisbursements.length + ' transaksi pencairan terbaru'"></span>
                <button 
                    type="button" 
                    @click="alert('Seluruh 24 log arsip telah tersinkronisasi dengan SIMKeu BPKP.')" 
                    class="font-bold text-slate-800 hover:text-[#FF5C2A] transition-colors inline-flex items-center gap-1 cursor-pointer"
                >
                    <span>Lihat Semua 24 Log</span>
                    <span>&rarr;</span>
                </button>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 6. BANNER: SUPER TAX DEDUCTION 200%                      -->
        <!-- ======================================================== -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h4 class="text-sm sm:text-base font-extrabold text-[#14171F]">
                            Super Tax Deduction 200% PMK-128
                        </h4>
                        <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded-md">
                            PP 45/2019
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Fasilitas pengurangan penghasilan bruto maksimal 200% atas kontribusi revitalisasi sekolah 3T.
                    </p>
                </div>
            </div>

            <button 
                type="button" 
                @click="alert('Mengunduh Surat Keterangan Fiskal Form PMK-128 (.PDF)')" 
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-[#14171F] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm rounded-2xl transition-all shadow-xs shrink-0 cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                <span>Form PMK-128 (.PDF)</span>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- 7. FLOATING INSTITUTIONAL NOTIFICATION                   -->
        <!-- ======================================================== -->
        <div 
            x-show="!bannerDismissed" 
            x-transition
            class="p-4 bg-[#14171F] text-white rounded-2xl shadow-lg flex items-center justify-between gap-4"
        >
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                </div>
                <p class="text-xs sm:text-sm font-medium">
                    Data audit penyaluran CSR terintegrasi dengan SIMKeu Kemendikbud &amp; LPJK Nasional.
                </p>
            </div>

            <button 
                type="button" 
                @click="bannerDismissed = true" 
                class="text-slate-400 hover:text-white transition-colors p-1"
                aria-label="Tutup"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- 8. MODAL DETAIL DONATUR                                  -->
        <!-- ======================================================== -->
        <div 
            x-show="donorDetailOpen" 
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs"
            @keydown.escape.window="donorDetailOpen = false"
        >
            <div 
                x-show="donorDetailOpen" 
                x-transition
                @click.away="donorDetailOpen = false" 
                class="bg-white rounded-3xl max-w-lg w-full p-6 space-y-6 shadow-2xl border border-slate-100"
            >
                <div class="flex items-center justify-between">
                    <div class="space-y-1">
                        <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[10px] font-bold rounded uppercase tracking-wider" x-text="selectedDonor?.tier"></span>
                        <h3 class="text-lg font-extrabold text-slate-900" x-text="selectedDonor?.name"></h3>
                        <p class="text-xs text-slate-500" x-text="selectedDonor?.sector"></p>
                    </div>
                    <button 
                        type="button" 
                        @click="donorDetailOpen = false" 
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center"
                    >
                        &times;
                    </button>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="p-4 bg-slate-50 rounded-2xl space-y-2">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Total Akumulasi CSR:</span>
                            <span class="font-extrabold text-slate-900" x-text="selectedDonor?.allocation"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Sekolah Binaan 3T:</span>
                            <span class="font-extrabold text-[#FF5C2A]" x-text="selectedDonor?.schools + ' Sekolah'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Status Pajak PMK-128:</span>
                            <span class="font-bold text-emerald-600" x-text="selectedDonor?.pmk_status"></span>
                        </div>
                    </div>
                    <p class="text-slate-500 leading-relaxed">
                        Seluruh kontribusi mitra ini telah melewati audit berjenjang BPKP dan verifikasi faktual lapangan LPJK Nasional.
                    </p>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button 
                        type="button" 
                        @click="donorDetailOpen = false" 
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl"
                    >
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
