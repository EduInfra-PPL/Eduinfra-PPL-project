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
        <div class="lg:col-span-8 bg-white rounded-2xl lg:rounded-3xl p-5 shadow-sm border border-slate-100 flex flex-col min-h-[640px]">
            
            <!-- Map Header Controls -->
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-[#FF5C2A]"></span>
                    <h2 class="text-base font-bold text-[#14171F]">Peta Spasial Kluster Kerusakan 3T</h2>
                    <span class="px-2 py-0.5 text-[10px] font-semibold text-slate-500 bg-slate-100 rounded-md">
                        EPSG:4326 WGS84
                    </span>
                </div>

                <!-- Layer Pills Controls -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <button class="flex items-center gap-1.5 px-3 py-1.5 bg-[#14171F] text-white font-medium rounded-full shadow-xs">
                        <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                        OpenStreetMap <span class="text-[10px] text-emerald-400 font-bold">Aktif</span>
                    </button>
                    
                    <button class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium rounded-full border border-slate-200/80 transition-colors">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Layer Batas GeoJSON 3T
                    </button>

                    <button class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium rounded-full border border-slate-200/80 transition-colors">
                        <svg class="w-3.5 h-3.5 text-[#FF5C2A]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.63 1.066-.94 1.717a23.3 23.3 0 00-.943 2.378 19.3 19.3 0 00-.77 2.784c-.05.28-.088.56-.118.84a9.7 9.7 0 00-3.352-1.742 1 1 0 00-1.127 1.488c.45.674.882 1.393 1.258 2.148.647 1.295 1.096 2.658 1.288 3.993a7.48 7.48 0 003.54-1.285 1 1 0 00.32-1.297 8.01 8.01 0 01-.67-2.736c.264-.325.56-.632.883-.912.87-.753 1.956-1.34 3.018-1.777a1 1 0 00.6-1.19z" clip-rule="evenodd"/></svg>
                        Heatmap Kerusakan
                    </button>

                    <button class="flex items-center gap-1.5 px-3 py-1.5 bg-[#FFF2ED] text-[#FF5C2A] font-semibold rounded-full border border-[#FFD9CC]">
                        <svg class="w-3.5 h-3.5 text-[#FF5C2A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg>
                        Radius Geofence 250m Indicator
                    </button>
                </div>
            </div>

            <!-- Visual Map Canvas Area -->
            <div class="relative flex-1 mt-4 rounded-2xl bg-[#EAF0F6] overflow-hidden min-h-[460px] border border-slate-200/60 flex items-center justify-center select-none">
                
                <!-- Map Background Grids & Labels -->
                <div class="absolute inset-0 opacity-40 pointer-events-none" style="background-image: radial-gradient(#94a3b8 1px, transparent 1px); background-size: 32px 32px;"></div>
                
                <!-- Coordinate Markings -->
                <span class="absolute top-4 left-16 text-[10px] font-semibold text-slate-400">98°E</span>
                <span class="absolute top-4 left-1/3 text-[10px] font-semibold text-slate-400">108°E</span>
                <span class="absolute top-4 left-1/2 text-[10px] font-semibold text-slate-400">118°E</span>
                <span class="absolute top-4 right-1/4 text-[10px] font-semibold text-slate-400">128°E</span>
                <span class="absolute top-4 right-12 text-[10px] font-semibold text-slate-400">138°E</span>

                <span class="absolute top-1/4 left-3 text-[10px] font-semibold text-slate-400">0°00' (Khatulistiwa)</span>
                <span class="absolute top-1/2 left-3 text-[10px] font-semibold text-slate-400">5°00'S</span>
                <span class="absolute bottom-1/4 left-3 text-[10px] font-semibold text-slate-400">10°00'S</span>

                <!-- Stylized Archipelago SVG Silhouette -->
                <svg class="w-full h-full max-h-[420px] p-6 text-emerald-200/80 fill-current opacity-70" viewBox="0 0 1000 450">
                    <!-- Sumatera -->
                    <path d="M70,120 L150,220 L190,280 L160,300 L90,190 Z" />
                    <!-- Jawa -->
                    <path d="M220,330 L380,335 L420,340 L410,360 L230,350 Z" />
                    <!-- Kalimantan -->
                    <path d="M250,110 L370,120 L380,240 L280,250 L240,180 Z" />
                    <!-- Sulawesi -->
                    <path d="M440,140 L480,145 L470,230 L520,240 L480,290 L440,240 Z" />
                    <!-- Maluku -->
                    <path d="M570,170 L600,180 L590,250 L560,240 Z" />
                    <!-- Nusa Tenggara -->
                    <path d="M430,345 L540,350 L530,370 L430,365 Z" />
                    <!-- Papua -->
                    <path d="M630,190 L750,200 L840,250 L830,340 L700,340 L640,250 Z" />
                </svg>

                <!-- GEOSPATIAL CLUSTERS & MARKERS -->

                <!-- 1. Kalimantan Barat / Sambas (Active Focus) -->
                <div class="absolute top-[28%] left-[28%] -translate-x-1/2 -translate-y-1/2 flex flex-col items-center">
                    <!-- Tooltip Card -->
                    <div class="bg-white rounded-xl shadow-lg border border-slate-200/80 p-3 mb-2 min-w-[210px] animate-bounce-short">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <span class="px-1.5 py-0.5 text-[9px] font-bold text-[#FF5C2A] bg-[#FFF2ED] rounded">Fokus Terpilih</span>
                            <span class="flex items-center gap-0.5 text-[10px] font-semibold text-emerald-600">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                GPS Match
                            </span>
                        </div>
                        <div class="text-xs font-bold text-slate-900 leading-tight">SDN 02 Sambas Terluar</div>
                        <div class="text-[10px] text-slate-500 font-medium">NPSN: 10802199 &bull; Kalbar</div>
                        <div class="mt-1.5 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] font-semibold text-slate-600">Skor Kerusakan:</span>
                            <span class="text-[10px] font-extrabold text-[#FF5C2A]">4.8 / 5.0 (Rusak Berat)</span>
                        </div>
                    </div>

                    <!-- Marker Pin -->
                    <div class="relative flex items-center justify-center">
                        <span class="absolute w-12 h-12 rounded-full bg-emerald-500/20 animate-ping"></span>
                        <div class="w-9 h-9 rounded-full bg-[#10B981] text-white flex items-center justify-center font-extrabold text-xs shadow-md border-2 border-white">
                            84
                        </div>
                    </div>
                    <span class="mt-1 text-[10px] font-bold text-[#FF5C2A] bg-white/80 px-1.5 py-0.5 rounded shadow-xs">KALBAR / SAMBAS (KRITIS)</span>
                </div>

                <!-- 2. NTT / Alor Cluster -->
                <div class="absolute bottom-[24%] left-[48%] flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center font-extrabold text-xs shadow-md border-2 border-white">
                        68
                    </div>
                    <span class="mt-0.5 text-[9px] font-bold text-[#FF5C2A] bg-white/90 px-1 rounded shadow-xs">NTT / ALOR (KRITIS)</span>
                </div>

                <!-- 3. Papua Kluster Darurat Tier-1 -->
                <div class="absolute bottom-[30%] right-[18%] flex flex-col items-center">
                    <div class="px-3 py-1 bg-red-500/10 border border-red-500/40 rounded-lg p-2 flex flex-col items-center backdrop-blur-xs">
                        <span class="text-[9px] font-extrabold text-red-600 tracking-wider">PAPUA (DARURAT TIER-1)</span>
                        <div class="w-9 h-9 rounded-full bg-[#EA4A18] text-white flex items-center justify-center font-extrabold text-xs shadow-md border-2 border-white mt-1">
                            215
                        </div>
                        <span class="text-[9px] font-bold text-red-600">Darurat</span>
                    </div>
                </div>

                <!-- MAP CONTROLS & BOTTOM ATTRIBUTIONS -->
                <div class="absolute bottom-3 left-3 flex flex-wrap items-center gap-3 bg-white/90 backdrop-blur-md px-3 py-2 rounded-xl border border-slate-200/80 text-xs">
                    <span class="font-bold text-slate-700 text-[11px]">Indeks Kerusakan 3T:</span>
                    <span class="flex items-center gap-1 text-[11px] font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Kritis (&gt;4.0)
                    </span>
                    <span class="flex items-center gap-1 text-[11px] font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Sedang (2.5 &ndash; 4.0)
                    </span>
                    <span class="flex items-center gap-1 text-[11px] font-medium text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Tervalidasi / Prima
                    </span>
                </div>

                <!-- Zoom Controls -->
                <div class="absolute bottom-3 right-3 flex items-center gap-1 bg-white/90 backdrop-blur-md p-1 rounded-xl border border-slate-200/80">
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 font-bold text-slate-700 text-sm">+</button>
                    <button class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 font-bold text-slate-700 text-sm">&minus;</button>
                    <button class="px-2 h-7 flex items-center justify-center rounded-lg hover:bg-slate-100 text-[10px] font-semibold text-slate-600">Reset Peta</button>
                    <span class="text-[9px] text-slate-400 border-l border-slate-200 pl-2 pr-1">Leaflet / OpenStreetMap</span>
                </div>
            </div>

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
