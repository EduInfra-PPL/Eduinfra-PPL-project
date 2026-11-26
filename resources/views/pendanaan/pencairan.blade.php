@php
    $firstProject = $projects[0] ?? null;
    $defaultStage = 2;
    $defaultAmount = $firstProject['stages'][$defaultStage]['amount'] ?? 0;
@endphp

<x-layouts.app active="csr">
    <div
        x-data="{
            selectedProjectId: @js($firstProject['code'] ?? ''),
            selectedStage: {{ $defaultStage }},
            amount: {{ (int) $defaultAmount }},
            receiptFileName: 'Belum ada file dipilih (PDF maks. 10MB)',
            taxInvoiceAdded: false,
            otpDigits: ['', '', '', '', '', ''],
            activeFilterTab: 'all',
            searchQuery: '',
            toastVisible: {{ session('success') ? 'true' : 'false' }},
            toastMessage: @js(session('success', '')),
            projects: @js($projects ?? []),
            mutations: @js($mutations ?? []),
            get activeProject() {
                return this.projects.find(p => p.code === this.selectedProjectId) || this.projects[0] || {
                    code: '-',
                    title: 'Belum ada proyek renovasi',
                    donor: '-',
                    progress: '0%',
                    stages: {
                        1: { title: 'Termin 1 (30%)', note: '-', amount: 0, status: 'locked' },
                        2: { title: 'Termin 2 (40%)', note: '-', amount: 0, status: 'locked' },
                        3: { title: 'Termin 3 (30%)', note: '-', amount: 0, status: 'locked' }
                    }
                };
            },
            selectStage(stage) {
                this.selectedStage = stage;
                this.amount = this.activeProject.stages[stage].amount;
            },
            onProjectChange() {
                this.amount = this.activeProject.stages[this.selectedStage].amount;
            },
            formatRupiah(value) {
                return new Intl.NumberFormat('id-ID').format(Number(value || 0));
            },
            get filteredMutations() {
                return this.mutations.filter(item => {
                    const matchTab = this.activeFilterTab === 'all'
                        || (this.activeFilterTab === 'tax_pending' && item.needs_tax_invoice)
                        || (this.activeFilterTab === 'termin' + item.stage_num);
                    const q = this.searchQuery.trim().toLowerCase();
                    const matchSearch = !q
                        || item.code.toLowerCase().includes(q)
                        || item.school.toLowerCase().includes(q)
                        || item.vendor.toLowerCase().includes(q)
                        || item.project_code.toLowerCase().includes(q);
                    return matchTab && matchSearch;
                });
            },
            submitMockDisbursement() {
                this.toastMessage = 'Pencairan Termin ' + this.selectedStage + ' Rp ' + this.formatRupiah(this.amount) + ' berhasil diotorisasi (Preview FE)';
                this.toastVisible = true;
            }
        }"
        class="space-y-6 pb-16 pt-4 max-w-7xl mx-auto"
    >
        <!-- ======================================================== -->
        <!-- 1. BREADCRUMB                                            -->
        <!-- ======================================================== -->
        <nav class="flex items-center gap-2 text-xs font-medium text-slate-400">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 hover:text-slate-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Beranda</span>
            </a>
            <span>&rsaquo;</span>
            <a href="{{ route('pendanaan.index') }}" class="hover:text-slate-700 transition-colors">Matching CSR</a>
            <span>&rsaquo;</span>
            <span class="font-bold text-[#FF5C2A]">Pencairan Dana Vendor</span>
        </nav>

        <!-- ======================================================== -->
        <!-- 2. HEADER & TOP ACTION BUTTONS                           -->
        <!-- ======================================================== -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="px-2.5 py-1 bg-[#FFF1EB] text-[#FF5C2A] text-[10px] font-extrabold tracking-wider rounded-md uppercase">
                        ENTITAS 13 • CSR LEDGER MODULE
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Koneksi Kliring Bank Terintegrasi</span>
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-[#14171F] tracking-tight">
                    Manajemen Pencairan Anggaran &amp; Log Termin Vendor
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-3xl leading-relaxed">
                    Buku besar digital mutasi pencairan dana escrow CSR kepada kontraktor pelaksana berdasarkan verifikasi termin fisik LPJK &amp; persetujuan PPK.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <button
                    type="button"
                    @click="alert('Menyiapkan ekspor Rekapitulasi Mutasi (.XLSX)')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200/90 hover:border-slate-300 rounded-2xl text-xs font-bold text-slate-700 shadow-xs transition-all cursor-pointer"
                >
                    <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Export Rekapitulasi Mutasi (.XLSX)</span>
                </button>
                <a
                    href="#form-pencairan"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white rounded-2xl text-xs font-bold shadow-xs transition-all"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Catat Pencairan Termin Baru</span>
                </a>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 3. 4 KPI STAT CARDS                                      -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- KPI 1 -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-500">Total Pagu Escrow Terpencairkan</p>
                    <div class="w-8 h-8 rounded-xl bg-[#FFF1EB] text-[#FF5C2A] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-[#14171F]">{{ $stats['total_disbursed_formatted'] ?? 'Rp 0' }}</h3>
                <div class="flex items-center justify-between text-[11px] font-semibold">
                    <span class="text-[#FF5C2A]">{{ $stats['portfolio_pct'] ?? 0 }}% dari Pagu Portofolio</span>
                    <span class="text-slate-400">{{ $stats['total_batches'] ?? 0 }} Batch</span>
                </div>
            </div>

            <!-- KPI 2 -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-500">Pencairan Bulan Ini ({{ now()->translatedFormat('F Y') }})</p>
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-[#14171F]">{{ $stats['monthly_disbursed_formatted'] ?? 'Rp 0' }}</h3>
                <div class="flex items-center justify-between text-[11px] font-semibold">
                    <span class="text-emerald-600">{{ $stats['monthly_count'] ?? 0 }} Transaksi Valid</span>
                    <span class="text-slate-400">SP2D Diterbitkan</span>
                </div>
            </div>

            <!-- KPI 3 -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-500">Menunggu Approval PPK &amp; Kuitansi</p>
                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-amber-600">{{ $stats['pending_count'] ?? 0 }} Pengajuan</h3>
                <div class="flex items-center justify-between text-[11px] font-semibold">
                    <span class="text-amber-600">{{ $stats['pending_amount_formatted'] ?? 'Rp 0' }} Dalam Antrean</span>
                    <span class="text-slate-400">&lt; 48 Jam</span>
                </div>
            </div>

            <!-- KPI 4 -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs space-y-3">
                <div class="flex items-start justify-between gap-2">
                    <p class="text-xs font-semibold text-slate-500">Faktur &amp; BAP Tervalidasi</p>
                    <div class="w-8 h-8 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl sm:text-2xl font-extrabold text-[#14171F]">{{ $stats['tax_verified_pct'] ?? 100 }}%</h3>
                <div class="flex items-center justify-between text-[11px] font-semibold">
                    <span class="text-emerald-600">Tersinkronisasi Pajak &amp; BPKP</span>
                    <span class="text-slate-400">Sinkron</span>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 4. MAIN 2-COLUMN WORKSPACE (CREATE FORM & AUDIT LOG)     -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- KOLOM KIRI: FORM CATAT PENCAIRAN DANA KE VENDOR -->
            <div id="form-pencairan" class="lg:col-span-5 space-y-4">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 space-y-5">
                    <!-- Form Header -->
                    <div class="flex items-start justify-between gap-3 pb-4 border-b border-slate-100">
                        <div class="space-y-1">
                            <span class="inline-flex items-center gap-1.5 text-[10px] font-extrabold tracking-wider text-[#FF5C2A] uppercase">
                                <span>ENTITAS 13 • DISBURSEMENTS</span>
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            </span>
                            <h2 class="text-lg font-extrabold text-[#14171F]">
                                Catat Pencairan Dana ke Vendor
                            </h2>
                            <p class="text-xs text-slate-500">
                                Pembuatan mandat transfer escrow dana CSR ke rekening giro rekanan pelaksana.
                            </p>
                        </div>
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[10px] font-bold shrink-0">
                            Dual-Token Aktif
                        </span>
                    </div>

                    <form @submit.prevent="submitMockDisbursement" class="space-y-4">
                        <!-- 1. Proyek Sasaran -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800">
                                    1. Proyek Sasaran (`project_id`) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[11px] font-bold text-emerald-600">Verifikasi LPJK Valid</span>
                            </div>

                            <select
                                x-model="selectedProjectId"
                                @change="onProjectChange()"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#FF5C2A]/20 focus:border-[#FF5C2A]"
                            >
                                <template x-for="proj in projects" :key="proj.code">
                                    <option :value="proj.code" x-text="proj.label"></option>
                                </template>
                            </select>

                            <!-- Preview Card Proyek -->
                            <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 border border-slate-200/70 rounded-xl">
                                <div class="flex items-center gap-3 min-w-0">
                                    <img
                                        src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=120&q=80"
                                        alt="Preview Sekolah"
                                        class="w-10 h-10 rounded-lg object-cover shrink-0"
                                    >
                                    <div class="min-w-0">
                                        <p class="text-xs font-extrabold text-slate-900 truncate" x-text="activeProject.title"></p>
                                        <p class="text-[11px] text-slate-500 truncate" x-text="'Donor Escrow: ' + activeProject.donor"></p>
                                    </div>
                                </div>
                                <span class="text-[11px] font-extrabold text-emerald-600 shrink-0" x-text="'Fisik: ' + activeProject.progress"></span>
                            </div>
                        </div>

                        <!-- 2. Tahap Termin Pencairan -->
                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-slate-800">
                                2. Tahap Termin Pencairan (`stage_num`) <span class="text-red-500">*</span>
                            </label>

                            <div class="space-y-2">
                                <template x-for="num in [1, 2, 3]" :key="num">
                                    <label
                                        @click="selectStage(num)"
                                        class="flex items-center justify-between gap-3 p-3 rounded-xl border cursor-pointer transition-all"
                                        :class="selectedStage === num
                                            ? 'border-[#FF5C2A] bg-[#FFF8F5] shadow-xs'
                                            : 'border-slate-200 bg-white hover:border-slate-300'"
                                    >
                                        <div class="flex items-start gap-2.5">
                                            <input type="radio" name="stage_num" :value="num" :checked="selectedStage === num" class="mt-0.5 accent-[#FF5C2A]">
                                            <div>
                                                <p class="text-xs font-bold text-slate-900" x-text="activeProject.stages[num].title"></p>
                                                <p
                                                    class="text-[11px] font-medium"
                                                    :class="activeProject.stages[num].status === 'paid'
                                                        ? 'text-emerald-600'
                                                        : (activeProject.stages[num].status === 'ready' ? 'text-[#FF5C2A]' : 'text-slate-400')"
                                                    x-text="activeProject.stages[num].note"
                                                ></p>
                                            </div>
                                        </div>
                                        <span class="text-xs font-extrabold text-slate-900 shrink-0" x-text="'Rp ' + formatRupiah(activeProject.stages[num].amount)"></span>
                                    </label>
                                </template>
                            </div>
                        </div>

                        <!-- 3. Nominal Pencairan Mandat -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800">
                                    3. Nominal Pencairan Mandat (`amount`) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">100% Escrow Clear</span>
                            </div>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-slate-400">Rp</span>
                                <input
                                    type="text"
                                    :value="formatRupiah(amount)"
                                    readonly
                                    class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-900 focus:outline-none"
                                >
                            </div>
                            <div class="flex items-center justify-between text-[11px]">
                                <span class="text-slate-500">Sisa Pagu Termin <span x-text="selectedStage"></span>: <strong class="text-slate-800" x-text="'Rp ' + formatRupiah(amount)"></strong></span>
                                <span class="text-emerald-600 font-bold">Terkunci RAB SNI</span>
                            </div>
                        </div>

                        <!-- 4. Unggah Kuitansi / Bukti Bayar & BAP -->
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800">
                                    4. Unggah Kuitansi / Bukti Bayar &amp; BAP (`receipt_file`) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-semibold text-slate-400">Format: PDF (Maks. 10MB)</span>
                            </div>

                            <div class="p-3.5 border-2 border-dashed border-[#FF5C2A]/30 bg-[#FFF8F5]/60 rounded-2xl space-y-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <div class="w-8 h-8 rounded-lg bg-[#FFF1EB] text-[#FF5C2A] flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-slate-900 truncate" x-text="receiptFileName"></p>
                                            <p class="text-[10px] text-slate-500">2.4 MB • TTE BSrE Terverifikasi</p>
                                        </div>
                                    </div>
                                    <label class="px-2.5 py-1 bg-white border border-slate-200 hover:border-slate-300 rounded-lg text-[11px] font-bold text-slate-700 cursor-pointer shrink-0">
                                        Ganti
                                        <input type="file" accept=".pdf" class="hidden" @change="receiptFileName = $event.target.files[0]?.name || receiptFileName">
                                    </label>
                                </div>

                                <div class="flex items-center justify-between pt-2 border-t border-slate-200/70 text-[11px]">
                                    <span class="text-slate-500">Faktur Pajak e-Faktur:</span>
                                    <button
                                        type="button"
                                        @click="taxInvoiceAdded = !taxInvoiceAdded"
                                        class="font-bold text-[#FF5C2A] hover:underline cursor-pointer"
                                        x-text="taxInvoiceAdded ? '✓ Faktur Pajak Terlampir' : '+ Lampirkan Faktur Pajak Susulan (U)'"
                                    ></button>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Otorisasi PPK & OTP -->
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800">
                                    5. Otorisasi Pejabat Pembuat Komitmen (`authorized_by`) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-bold text-emerald-600">BSrE Sertifikat Digital #PUPR-8812</span>
                            </div>

                            <div class="flex items-center justify-between gap-3 p-3 bg-slate-50 border border-slate-200/80 rounded-xl">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-[#FF5C2A] text-white font-extrabold text-xs flex items-center justify-center shrink-0">
                                        IH
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-900">Ir. Hendrawan Sulistyo, M.Eng</p>
                                        <p class="text-[10px] text-slate-500">NIP: 19840211 200801 1 002 | PPK Balai Prasarana Kalbar</p>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>

                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-medium text-slate-500">Masukkan Kode Dual-Token OTP Escrow (SMS/Authenticator):</span>
                                    <button type="button" @click="alert('Kode OTP baru dikirim ulang.')" class="font-bold text-[#FF5C2A] hover:underline cursor-pointer">
                                        Kirim Ulang (34s)
                                    </button>
                                </div>
                                <div class="grid grid-cols-6 gap-2">
                                    <template x-for="(digit, idx) in otpDigits" :key="idx">
                                        <input
                                            type="text"
                                            maxlength="1"
                                            x-model="otpDigits[idx]"
                                            class="w-full py-2 text-center bg-white border border-slate-200 rounded-xl text-sm font-extrabold text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#FF5C2A]/20 focus:border-[#FF5C2A]"
                                        >
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Form Action Buttons -->
                        <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                            <a href="{{ route('pendanaan.index') }}" class="px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:text-slate-900 rounded-xl">
                                Batal
                            </a>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="alert('Draf pencairan disimpan sementara (Preview FE).')"
                                    class="px-4 py-2.5 bg-white border border-slate-200 hover:border-slate-300 text-slate-700 text-xs font-bold rounded-2xl cursor-pointer"
                                >
                                    Simpan Draf
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white text-xs font-bold rounded-2xl shadow-xs cursor-pointer"
                                >
                                    <span>Otorisasi &amp; Eksekusi Transfer Escrow</span>
                                    <span>&rarr;</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Integritas Audit Footer Card -->
                <div class="bg-[#FFF8F5] border border-[#FF5C2A]/20 rounded-2xl p-4 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#FFF1EB] text-[#FF5C2A] flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                        </svg>
                    </div>
                    <div class="space-y-0.5">
                        <h4 class="text-xs font-extrabold text-[#14171F]">Integritas Audit Kemenkeu &amp; KPK RI</h4>
                        <p class="text-[11px] text-slate-500 leading-relaxed">
                            Setiap pencairan dana escrow secara otomatis dicatat pada ledger immutable dan tembusan elektronik ke bendahara Kemendikbudristek.
                        </p>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: LAPORAN MUTASI & REKENING ESCROW -->
            <div class="lg:col-span-7 space-y-5">
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
                    <!-- Table Header & Search -->
                    <div class="p-6 border-b border-slate-100 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h2 class="text-lg font-extrabold text-[#14171F]">
                                        Laporan Mutasi &amp; Riwayat Pencairan Dana
                                    </h2>
                                    <span class="px-2.5 py-0.5 bg-slate-100 text-slate-700 text-[11px] font-bold rounded-full">
                                        {{ $counts['all'] ?? 0 }} Transaksi
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500">
                                    Buku besar mutasi penyaluran dana termin konstruksi berbasis rekening penampung (Escrow).
                                </p>
                            </div>

                            <div class="relative sm:w-56 shrink-0">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input
                                    type="text"
                                    x-model="searchQuery"
                                    placeholder="Cari ID transaksi, vendor, proy..."
                                    class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-[#FF5C2A]/20 focus:border-[#FF5C2A]"
                                >
                            </div>
                        </div>

                        <!-- Filter Tabs -->
                        <div class="flex flex-wrap items-center gap-2">
                            <button
                                type="button"
                                @click="activeFilterTab = 'all'"
                                :class="activeFilterTab === 'all' ? 'bg-[#14171F] text-white' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1.5 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                            >
                                Semua Termin ({{ $counts['all'] ?? 0 }})
                            </button>
                            <button
                                type="button"
                                @click="activeFilterTab = 'termin1'"
                                :class="activeFilterTab === 'termin1' ? 'bg-[#14171F] text-white' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1.5 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                            >
                                Termin 1 ({{ $counts['termin1'] ?? 0 }})
                            </button>
                            <button
                                type="button"
                                @click="activeFilterTab = 'termin2'"
                                :class="activeFilterTab === 'termin2' ? 'bg-[#14171F] text-white' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1.5 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                            >
                                Termin 2 ({{ $counts['termin2'] ?? 0 }})
                            </button>
                            <button
                                type="button"
                                @click="activeFilterTab = 'termin3'"
                                :class="activeFilterTab === 'termin3' ? 'bg-[#14171F] text-white' : 'bg-slate-100 text-slate-600 hover:text-slate-900'"
                                class="px-3 py-1.5 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                            >
                                Termin 3 ({{ $counts['termin3'] ?? 0 }})
                            </button>
                            <button
                                type="button"
                                @click="activeFilterTab = 'tax_pending'"
                                :class="activeFilterTab === 'tax_pending' ? 'bg-[#FF5C2A] text-white' : 'bg-[#FFF1EB] text-[#FF5C2A] hover:bg-[#FFE5DC]'"
                                class="px-3 py-1.5 rounded-full text-[11px] font-bold transition-all cursor-pointer"
                            >
                                &bull; Butuh Faktur Susulan ({{ $counts['tax_pending'] ?? 0 }})
                            </button>
                        </div>
                    </div>

                    <!-- Table Mutasi -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider border-b border-slate-100">
                                <tr>
                                    <th class="py-3.5 px-5">ID Transaksi &amp; Waktu</th>
                                    <th class="py-3.5 px-5">Proyek &amp; Sekolah</th>
                                    <th class="py-3.5 px-5">Kontraktor &amp; Rekening</th>
                                    <th class="py-3.5 px-5">Termin (Stage)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template x-for="row in filteredMutations" :key="row.code">
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="py-4 px-5 whitespace-nowrap">
                                            <p class="font-extrabold text-slate-900" x-text="row.code"></p>
                                            <p class="text-[11px] text-slate-400" x-text="row.datetime"></p>
                                        </td>
                                        <td class="py-4 px-5">
                                            <p class="font-bold text-slate-900" x-text="row.school"></p>
                                            <p class="text-[11px] text-slate-400" x-text="row.project_code"></p>
                                        </td>
                                        <td class="py-4 px-5">
                                            <p class="font-bold text-slate-900" x-text="row.vendor"></p>
                                            <p class="text-[11px] text-slate-400" x-text="row.bank"></p>
                                        </td>
                                        <td class="py-4 px-5 min-w-[140px]">
                                            <span
                                                class="inline-block px-2 py-0.5 rounded-md text-[10px] font-bold mb-1.5"
                                                :class="{
                                                    'bg-[#FFF1EB] text-[#FF5C2A]': row.stage_num === 2,
                                                    'bg-emerald-50 text-emerald-700': row.stage_num === 3,
                                                    'bg-slate-100 text-slate-700': row.stage_num === 1
                                                }"
                                                x-text="row.stage_label"
                                            ></span>
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                                    <div
                                                        class="h-full rounded-full"
                                                        :class="row.stage_num === 3 ? 'bg-emerald-500' : (row.stage_num === 2 ? 'bg-[#FF5C2A]' : 'bg-emerald-500')"
                                                        :style="'width: ' + row.progress + '%'"
                                                    ></div>
                                                </div>
                                                <span class="text-[10px] font-bold text-slate-500" x-text="row.progress + '%'"></span>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="filteredMutations.length === 0">
                                    <td colspan="4" class="py-10 text-center text-slate-400 text-xs">
                                        Tidak ada mutasi pencairan yang cocok dengan pencarian atau filter.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Bar -->
                    <div class="p-4 px-6 bg-slate-50/70 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500">
                        <span x-text="'Menampilkan ' + filteredMutations.length + ' dari {{ $counts['all'] ?? 0 }} mutasi pencairan dana vendor'"></span>
                        <div class="flex items-center gap-1.5">
                            <button type="button" class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-slate-400 text-[11px] font-bold">Sebelumnya</button>
                            <button type="button" class="w-7 h-7 bg-[#FF5C2A] text-white rounded-lg text-[11px] font-bold">1</button>
                            <button type="button" class="w-7 h-7 bg-white border border-slate-200 text-slate-700 rounded-lg text-[11px] font-bold">2</button>
                            <button type="button" class="w-7 h-7 bg-white border border-slate-200 text-slate-700 rounded-lg text-[11px] font-bold">3</button>
                            <button type="button" class="px-2.5 py-1 bg-white border border-slate-200 text-slate-700 rounded-lg text-[11px] font-bold">Berikutnya</button>
                        </div>
                    </div>
                </div>

                <!-- Card Sinkronisasi Rekening Penampung (Escrow Account) -->
                <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                            </svg>
                        </div>
                        <div class="space-y-0.5">
                            <h4 class="text-sm font-extrabold text-[#14171F]">
                                Sinkronisasi Rekening Penampung (Escrow Account)
                            </h4>
                            <p class="text-xs text-slate-500">
                                Bank Kustodian: {{ $escrowSummary['bank_name'] ?? 'PT Bank Mandiri (Persero) Tbk' }} &bull; Saldo Mengendap: <strong class="text-slate-800">{{ $escrowSummary['balance_formatted'] ?? 'Rp 0' }}</strong>
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[11px] font-bold">
                            &bull; Reconciled Realtime
                        </span>
                        <a href="{{ route('pendanaan.wall-of-donors') }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[11px] font-bold transition-colors">
                            Audit BPKP Report
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 5. FOOTER COMPLIANCE & FLOATING TOAST                    -->
        <!-- ======================================================== -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-2">
            <div class="flex flex-wrap items-center gap-3 text-[11px] font-medium text-slate-500">
                <span class="inline-flex items-center gap-1.5 text-[#FF5C2A] font-bold">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Terakreditasi LPJK &amp; Kemendikbudristek</span>
                </span>
                <span>&bull;</span>
                <span class="inline-flex items-center gap-1.5 text-emerald-600 font-bold">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    <span>ISO/IEC 27001 BSSN Secured Ledger</span>
                </span>
            </div>

            <div
                x-show="toastVisible"
                x-transition
                class="px-4 py-2.5 bg-[#14171F] text-white rounded-2xl shadow-lg flex items-center gap-3 text-xs"
            >
                <span class="w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">✓</span>
                <span x-text="toastMessage"></span>
                <button type="button" @click="toastVisible = false" class="text-slate-400 hover:text-white ml-1 cursor-pointer">&times;</button>
            </div>
        </div>
    </div>
</x-layouts.app>
