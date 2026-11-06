<x-layouts.app active="csr">

    <div class="space-y-8 pb-12">

        <!-- ========================================== -->
        <!-- 1. HERO SECTION: TITLE, SEARCH & QUICK CHIPS -->
        <!-- ========================================== -->
        <div class="max-w-4xl mx-auto text-center space-y-6 pt-4">
            <!-- Header Text -->
            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-[#14171F] tracking-tight">
                    Portal Kemitraan &amp; Matching CSR Sekolah 3T
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 max-w-2xl mx-auto leading-relaxed">
                    Pastikan dana CSR Anda tepat sasaran untuk pendidikan daerah terpencil.
                </p>
            </div>

            <!-- Big Omni Search Box -->
            <div class="relative max-w-3xl mx-auto">
                <div class="flex items-center bg-white rounded-full p-1.5 shadow-md border border-slate-200/80 transition-all focus-within:ring-2 focus-within:ring-[#FF5C2A]/30 focus-within:border-[#FF5C2A]">
                    <!-- Search Icon -->
                    <div class="pl-4 pr-2 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                        </svg>
                    </div>

                    <!-- Input Field -->
                    <input 
                        type="text" 
                        placeholder="Masukkan nama sekolah atau daerah..." 
                        class="w-full text-xs sm:text-sm font-medium text-slate-800 placeholder-slate-400 bg-transparent border-0 focus:outline-none focus:ring-0 py-2.5 px-2"
                    />

                    <!-- Submit Button -->
                    <button type="button" class="flex items-center gap-2 px-6 py-2.5 bg-[#FF5C2A] hover:bg-[#e04f22] text-white text-xs sm:text-sm font-bold rounded-full transition-all shadow-sm shrink-0 cursor-pointer">
                        <span>Cari</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Quick Filter Chips -->
            <div class="flex flex-wrap items-center justify-center gap-2 pt-1 text-xs">
                <span class="text-[11px] font-bold text-slate-400 tracking-wider mr-1">Filter Cepat:</span>

                <!-- Active Filter -->
                <button type="button" class="px-4 py-1.5 rounded-full bg-[#14171F] text-white font-semibold shadow-xs cursor-pointer">
                    Semua (142)
                </button>

                <!-- Inactive Filter: Rusak Berat -->
                <button type="button" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-medium border border-slate-200/80 shadow-2xs transition-colors cursor-pointer">
                    Rusak Berat (38)
                </button>

                <!-- Inactive Filter: Kebutuhan < Rp 50 Jt -->
                <button type="button" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-medium border border-slate-200/80 shadow-2xs transition-colors cursor-pointer">
                    Kebutuhan &lt; Rp 50 Jt
                </button>

                <!-- Inactive Filter: Bantuan Fisik -->
                <button type="button" class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white hover:bg-slate-50 text-slate-700 font-medium border border-slate-200/80 shadow-2xs transition-colors cursor-pointer">
                    Bantuan Fisik
                </button>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- 2. QUICK ACTION TILES (3 HORIZONTAL CARDS) -->
        <!-- ========================================== -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            
            <!-- Tile 1: Buat Pendanaan Bantuan -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all cursor-pointer group">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-700 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                        </svg>
                    </div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#FF5C2A] transition-colors">
                            Buat Pendanaan Bantuan
                        </h3>
                        <!-- <span class="px-1.5 py-0.2 text-[10px] font-bold bg-slate-100 text-slate-600 rounded">28</span> -->
                    </div>
                </div>
                <div class="text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>

            <!-- Tile 2: Pencairan Anggaran -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all cursor-pointer group">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 border border-amber-100/80 flex items-center justify-center text-amber-600 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#FF5C2A] transition-colors">
                            Pencairan Anggaran
                        </h3>
                    </div>
                </div>
                <div class="text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>

            <!-- Tile 3: Wall of Donors & Laporan -->
            <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:border-slate-300 transition-all cursor-pointer group">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-teal-50 border border-teal-100/80 flex items-center justify-center text-teal-600 shrink-0 group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#FF5C2A] transition-colors">
                            Wall of Donors &amp; Laporan
                        </h3>
                    </div>
                </div>
                <div class="text-slate-300 group-hover:text-slate-600 group-hover:translate-x-0.5 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/>
                    </svg>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- 3. SECTION: KAMPANYE PALING MENDESAK PEKAN INI -->
        <!-- ========================================== -->
        <div class="space-y-4 pt-2">
            
            <!-- Section Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#FF5C2A]"></span>
                    <h2 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                        Kampanye Paling Mendesak Pekan Ini
                    </h2>
                </div>

                <a href="#semua-kampanye" class="text-xs sm:text-sm font-bold text-[#FF5C2A] hover:text-[#e04f22] transition-colors">
                    Lihat Semua 18 Kampanye
                </a>
            </div>

            <!-- Campaign Cards Grid (3 Columns) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- -------------------------------------- -->
                <!-- CARD 1: SDN 02 Batom Perbatasan -->
                <!-- -------------------------------------- -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Image Container with Badges -->
                        <div class="relative h-48 w-full bg-slate-200 overflow-hidden">
                            <img 
                                src="https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=800&q=80" 
                                alt="SDN 02 Batom Perbatasan" 
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-900/10 to-transparent"></div>

                            <!-- Floating Damage Badge (Top Left) -->
                            <div class="absolute top-3.5 left-3.5">
                                <span class="px-3 py-1 bg-[#14171F]/95 backdrop-blur-xs text-white text-[11px] font-bold rounded-full shadow-sm">
                                    Rusak Total
                                </span>
                            </div>

                            <!-- Location Pill (Bottom Left) -->
                            <div class="absolute bottom-3 left-3.5 flex items-center gap-1 text-[11px] font-medium text-white/95 drop-shadow-xs">
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                                <span>Pegunungan Bintang, Papua Pegunungan</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4">
                            <!-- School Name & Needs -->
                            <div class="space-y-1.5">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight leading-snug">
                                    SDN 02 Batom Perbatasan
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Rekonstruksi 3 ruang kelas roboh akibat longsor dan penyediaan 1 unit PLTS komunal 3000 Wp.
                                </p>
                            </div>

                            <!-- Target & Progress Box -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500 text-[11px]">Target RAB SNI:</span>
                                    <span class="text-slate-900 font-extrabold">Rp 420.000.000</span>
                                </div>

                                <!-- Progress Bar (25%) -->
                                <div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#FF5C2A] h-2 rounded-full" style="width: 25%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] pt-0.5">
                                    <span class="font-bold text-[#FF5C2A]">Terkumpul 25%</span>
                                    <span class="text-slate-400 font-medium">Sisa Rp 315 Juta</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Footer -->
                    <div class="p-5 pt-0">
                        <button type="button" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-[#FF5C2A] hover:bg-[#e04f22] text-white font-bold text-xs sm:text-sm rounded-2xl transition-all shadow-xs cursor-pointer">
                            <span>Salurkan Komitmen CSR</span>
                        </button>
                    </div>
                </div>

                <!-- -------------------------------------- -->
                <!-- CARD 2: SMP Satap Pureman -->
                <!-- -------------------------------------- -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Image Container with Badges -->
                        <div class="relative h-48 w-full bg-slate-200 overflow-hidden">
                            <img 
                                src="https://images.unsplash.com/photo-1509062522246-3755977927d7?auto=format&fit=crop&w=800&q=80" 
                                alt="SMP Satap Pureman" 
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-900/10 to-transparent"></div>

                            <!-- Floating Damage Badge (Top Left) -->
                            <div class="absolute top-3.5 left-3.5">
                                <span class="px-3 py-1 bg-[#14171F]/95 backdrop-blur-xs text-white text-[11px] font-bold rounded-full shadow-sm">
                                    Atap Bocor &amp; Sanitasi Buruk
                                </span>
                            </div>

                            <!-- Location Pill (Bottom Left) -->
                            <div class="absolute bottom-3 left-3.5 flex items-center gap-1 text-[11px] font-medium text-white/95 drop-shadow-xs">
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                                <span>Kabupaten Alor, Nusa Tenggara Timur</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4">
                            <!-- School Name & Needs -->
                            <div class="space-y-1.5">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight leading-snug">
                                    SMP Satap Pureman
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Renovasi atap baja ringan, perbaikan plafon lapuk, dan instalasi tangki pemanen air hujan bersih.
                                </p>
                            </div>

                            <!-- Target & Progress Box -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500 text-[11px]">Target RAB SNI:</span>
                                    <span class="text-slate-900 font-extrabold">Rp 185.000.000</span>
                                </div>

                                <!-- Progress Bar (70% Emerald) -->
                                <div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden">
                                    <div class="bg-emerald-500 h-2 rounded-full" style="width: 70%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] pt-0.5">
                                    <span class="font-bold text-emerald-600">Terkumpul 70%</span>
                                    <span class="text-slate-400 font-medium">Sisa Rp 55,5 Juta</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Footer (Dark Close Button) -->
                    <div class="p-5 pt-0">
                        <button type="button" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-[#14171F] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm rounded-2xl transition-all shadow-xs cursor-pointer">
                            <span>Tutup Sisa Pendanaan</span>
                        </button>
                    </div>
                </div>

                <!-- -------------------------------------- -->
                <!-- CARD 3: SD Filial Tapal Batas Sebatik -->
                <!-- -------------------------------------- -->
                <div class="bg-white rounded-3xl overflow-hidden border border-slate-200/80 shadow-xs hover:shadow-md transition-all flex flex-col justify-between">
                    <div>
                        <!-- Image Container with Badges -->
                        <div class="relative h-48 w-full bg-slate-200 overflow-hidden">
                            <img 
                                src="https://images.unsplash.com/photo-1577896851231-70ef18881754?auto=format&fit=crop&w=800&q=80" 
                                alt="SD Filial Tapal Batas Sebatik" 
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-slate-900/10 to-transparent"></div>

                            <!-- Floating Damage Badge (Top Left) -->
                            <div class="absolute top-3.5 left-3.5">
                                <span class="px-3 py-1 bg-[#14171F]/95 backdrop-blur-xs text-white text-[11px] font-bold rounded-full shadow-sm">
                                    Lab Vokasi &amp; Internet
                                </span>
                            </div>

                            <!-- Location Pill (Bottom Left) -->
                            <div class="absolute bottom-3 left-3.5 flex items-center gap-1 text-[11px] font-medium text-white/95 drop-shadow-xs">
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                                </svg>
                                <span>Pulau Sebatik, Nunukan, Kaltara</span>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-5 space-y-4">
                            <!-- School Name & Needs -->
                            <div class="space-y-1.5">
                                <h3 class="text-base font-bold text-slate-900 tracking-tight leading-snug">
                                    SD Filial Tapal Batas Sebatik
                                </h3>
                                <p class="text-xs text-slate-500 leading-relaxed">
                                    Pengadaan ruang komputer digital, perangkat satelit Starlink, dan laboratorium sains dasar anak TKI.
                                </p>
                            </div>

                            <!-- Target & Progress Box -->
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 space-y-2">
                                <div class="flex items-center justify-between text-xs font-semibold">
                                    <span class="text-slate-500 text-[11px]">Target RAB SNI:</span>
                                    <span class="text-slate-900 font-extrabold">Rp 95.000.000</span>
                                </div>

                                <!-- Progress Bar (45%) -->
                                <div class="w-full bg-slate-200/80 h-2 rounded-full overflow-hidden">
                                    <div class="bg-[#FF5C2A] h-2 rounded-full" style="width: 45%"></div>
                                </div>

                                <div class="flex items-center justify-between text-[11px] pt-0.5">
                                    <span class="font-bold text-[#FF5C2A]">Terkumpul 45%</span>
                                    <span class="text-slate-400 font-medium">Sisa Rp 52,2 Juta</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Action Footer -->
                    <div class="p-5 pt-0">
                        <button type="button" class="w-full flex items-center justify-center gap-2 py-3 px-4 bg-[#FF5C2A] hover:bg-[#e04f22] text-white font-bold text-xs sm:text-sm rounded-2xl transition-all shadow-xs cursor-pointer">
                            <span>Salurkan Komitmen CSR</span>
                        </button>
                    </div>
                </div>

            </div>

        </div>

    </div>

</x-layouts.app>

