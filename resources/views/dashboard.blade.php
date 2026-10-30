<x-layouts.app active="dashboard" title="EduInfra Tracker - Dashboard Prioritas 3T">

    <!-- KPI STATS CARDS (4 COLUMNS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Card 1: Total Sekolah -->
        <x-ui.kpi-card 
            title="Total Sekolah Terdata 3T"
            value="14,820"
            unit="Unit"
            icon="building"
            badgeText="+12% terverifikasi"
            badgeVariant="success"
            subText="Target Q2: 95%"
        />

        <!-- Card 2: Kondisi Kritis (Vibrant Orange Urgent) -->
        <x-ui.kpi-card 
            title="Kondisi Kritis (Rusak Berat)"
            value="3,412"
            unit="Titik"
            variant="urgent"
            icon="alert"
            badgeText="Prioritas Darurat 3T"
            badgeVariant="urgent-pill"
            subText="Butuh Intervensi Segera"
        />

        <!-- Card 3: CSR Terealisasi -->
        <x-ui.kpi-card 
            title="Alokasi CSR Terealisasi"
            value="Rp 48.6"
            unit="Miliar"
            icon="link"
            badgeText="84 Proyek Aktif"
            badgeVariant="orange-pill"
            subText="32 Mitra BUMN/Swasta"
        />

        <!-- Card 4: Tiket Visitasi -->
        <x-ui.kpi-card 
            title="Tiket Menunggu Visitasi"
            value="128"
            unit="Laporan"
            icon="hourglass"
            badgeText="Pending Review Tim GIS"
            badgeVariant="warning-pill"
            subText="Rata-rata 2.4 Hari"
        />
    </div>

    <!-- MAIN INTERACTIVE SECTION (MAP & PRIORITY LIST) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- LEFT PANEL: SPATIAL GIS CLUSTER MAP (col-span-8) -->
        <!-- SPATIAL GIS MAP (MAPTILER & MULTI-BASEMAP VIEWER) -->
        <div class="lg:col-span-8 flex flex-col">
            <x-map.spatial-card />
        </div>

        <!-- RIGHT PANEL: PRIORITY SCHOOLS LIST (col-span-4) -->
        <div class="lg:col-span-4 bg-white rounded-2xl lg:rounded-3xl p-5 shadow-sm border border-slate-100 flex flex-col">
            
            <!-- Header -->
            <div class="flex items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-full bg-[#FFF2ED] text-[#FF5C2A] flex items-center justify-center">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.63 1.066-.94 1.717a23.3 23.3 0 00-.943 2.378 19.3 19.3 0 00-.77 2.784c-.05.28-.088.56-.118.84a9.7 9.7 0 00-3.352-1.742 1 1 0 00-1.127 1.488c.45.674.882 1.393 1.258 2.148.647 1.295 1.096 2.658 1.288 3.993a7.48 7.48 0 003.54-1.285 1 1 0 00.32-1.297 8.01 8.01 0 01-.67-2.736c.264-.325.56-.632.883-.912.87-.753 1.956-1.34 3.018-1.777a1 1 0 00.6-1.19z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-[#14171F]">Daftar Sekolah Prioritas 3T</h2>
                        <p class="text-[10px] text-slate-500 font-medium">Hasil Audit Spasial &amp; Geotagging Valid</p>
                    </div>
                </div>

                <span class="px-2 py-0.5 text-[10px] font-bold text-white bg-[#14171F] rounded-full">
                    Top 10 Mendesak
                </span>
            </div>

            <!-- Segmented Filter Tabs -->
            <div class="flex items-center gap-1.5 my-3.5 p-1 bg-slate-50 border border-slate-200/60 rounded-full text-xs">
                <button class="flex-1 py-1 px-3 bg-[#14171F] text-white font-semibold rounded-full shadow-xs text-center text-xs">Semua</button>
                <button class="flex-1 py-1 px-3 text-slate-600 hover:text-slate-900 font-medium rounded-full text-center text-xs">Jenjang SD</button>
                <button class="flex-1 py-1 px-3 text-slate-600 hover:text-slate-900 font-medium rounded-full text-center text-xs">Jenjang SMP</button>
                <button class="flex-1 py-1 px-3 text-slate-600 hover:text-slate-900 font-medium rounded-full text-center text-xs">Jenjang SMA</button>
            </div>

            <!-- SCHOOL LIST ITEMS -->
            <div class="space-y-3.5">
                
                <!-- ITEM 1: SDN 02 Sambas Terluar (Selected & Expanded Card) -->
                <div class="p-4 rounded-2xl border-2 border-[#FF5C2A] bg-white shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 text-[10px] font-bold text-white bg-[#FF5C2A] rounded">WILAYAH 3T TIER-1</span>
                        <span class="text-[10px] font-mono text-slate-400">NPSN: 10802199</span>
                    </div>

                    <div>
                        <h3 class="text-sm font-extrabold text-[#14171F]">SDN 02 Sambas Terluar</h3>
                        <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                            <svg class="w-3.5 h-3.5 text-[#FF5C2A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="2"/></svg>
                            Desa Temajuk, Kec. Paloh, Kab. Sambas, Kalbar
                        </p>
                    </div>

                    <!-- Score Progress Bar -->
                    <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-semibold text-slate-600">Damage Urgency Score</span>
                            <span class="font-extrabold text-[#FF5C2A]">4.8 <span class="text-slate-400 font-normal">/ 5.0</span></span>
                        </div>
                        <div class="w-full h-2 bg-slate-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-amber-500 to-[#FF5C2A] rounded-full" style="width: 96%;"></div>
                        </div>
                        <div class="flex items-center justify-between text-[10px] font-medium mt-1 text-slate-500">
                            <span>Atap 3 RKB Runtuh Total</span>
                            <span class="text-red-600 font-bold">Kategori: Rusak Berat</span>
                        </div>
                    </div>

                    <!-- Verified Geofencing Chip -->
                    <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50 border border-emerald-200/60 text-xs">
                        <span class="flex items-center gap-1 font-bold text-emerald-800 text-[11px]">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            GPS Match &lt; 250m Verified
                        </span>
                        <span class="text-[10px] text-emerald-700 font-medium">Akurasi &plusmn;4.2m</span>
                    </div>

                    <!-- Stats Row -->
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] text-slate-500 block">Kebutuhan Biaya</span>
                            <span class="font-bold text-slate-900">Rp 145.000.000</span>
                        </div>
                        <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-[10px] text-slate-500 block">Siswa Terdampak</span>
                            <span class="font-bold text-slate-900">118 Murid</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-2 mt-1">
                        <button class="flex items-center justify-center gap-1.5 py-2 px-3 bg-[#14171F] hover:bg-slate-800 text-white text-xs font-semibold rounded-full shadow-xs transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Buka Lembar Audit
                        </button>
                        <button class="flex items-center justify-center gap-1.5 py-2 px-3 bg-[#FF5C2A] hover:bg-[#EA4A18] text-white text-xs font-semibold rounded-full shadow-xs shadow-[#FF5C2A]/20 transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            Matching Mitra CSR
                        </button>
                    </div>
                </div>

                <!-- ITEM 2: SMP Satap 01 Agats Asmat -->
                <div class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-slate-300 bg-white transition-all">
                    <div class="flex items-center justify-between text-[10px] mb-1">
                        <span class="font-bold text-[#FF5C2A]">Wilayah 3T Tier-1 (Papua)</span>
                        <span class="font-mono text-slate-400">NPSN: 60301412</span>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900">SMP Satap 01 Agats Asmat</h4>
                    <p class="text-[11px] text-slate-500">Pondasi Panggung Pasang Surut Lapuk</p>
                    <div class="flex items-center justify-between text-xs mt-2 pt-2 border-t border-slate-100">
                        <span class="font-semibold text-slate-600">Skor: <strong class="text-[#FF5C2A]">4.6 / 5.0</strong></span>
                        <span class="font-medium text-slate-700">Estimasi: <strong>Rp 210 Jt</strong></span>
                    </div>
                </div>

                <!-- ITEM 3: MIS Al-Falah Pantar Barat -->
                <div class="p-3.5 rounded-2xl border border-slate-200/80 hover:border-slate-300 bg-white transition-all">
                    <div class="flex items-center justify-between text-[10px] mb-1">
                        <span class="font-bold text-amber-600">Wilayah 3T Tier-2 (NTT)</span>
                        <span class="font-mono text-slate-400">NPSN: 50308821</span>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900">MIS Al-Falah Pantar Barat</h4>
                    <p class="text-[11px] text-slate-500">Sanitasi Air Bersih &amp; Dinding Retak Pasca Gempa</p>
                    <div class="flex items-center justify-between text-xs mt-2 pt-2 border-t border-slate-100">
                        <span class="font-semibold text-slate-600">Skor: <strong class="text-[#FF5C2A]">4.1 / 5.0</strong></span>
                        <span class="font-semibold text-emerald-600">CSR Matched 70%</span>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- TOAST NOTIFICATION FLOATING (BOTTOM RIGHT) -->
    <aside aria-label="Notifikasi sistem" class="fixed bottom-6 right-6 z-50 bg-white rounded-2xl p-3.5 shadow-xl border border-slate-200/80 flex items-center gap-3 max-w-md animate-fade-in">
        <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <p class="text-xs text-slate-700 leading-snug">
            Layer data spasial wilayah <strong>Papua &amp; Maluku</strong> berhasil dimuat (<span class="text-[#FF5C2A] font-semibold">142 titik sekolah</span>).
        </p>
        <button type="button" aria-label="Tutup notifikasi" onclick="this.closest('aside').remove()" class="text-slate-400 hover:text-slate-600 ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </aside>

</x-layouts.app>

