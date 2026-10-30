@php
    $basemaps = [
        ['id' => 'satellite',    'name' => 'Satelit',                   'img' => 'satelit.jpg'],
        ['id' => 'hybrid',       'name' => 'Hybrid',                    'img' => 'hybrid.jpg'],
        ['id' => 'big-rupabumi', 'name' => 'Rupabumi Indonesia (BIG)',  'img' => 'big-rupabumi.jpg'],
        ['id' => 'street',       'name' => 'Street',                    'img' => 'street.png'],
        ['id' => 'osm',          'name' => 'Open Street Map',           'img' => 'osm.png'],
        ['id' => 'topo',         'name' => 'Topografi',                 'img' => 'topo.jpg'],
        ['id' => 'outdoor',      'name' => 'Outdoor',                   'img' => 'outdoor.jpg'],
        ['id' => 'terang',       'name' => 'Terang',                    'img' => 'terang.jpg'],
        ['id' => 'dataviz',      'name' => 'Dataviz',                   'img' => 'dataviz.jpg'],
        ['id' => 'esri-imagery', 'name' => 'Citra Esri',                'img' => 'esri-imagery.jpg'],
        ['id' => 'esri-topo',    'name' => 'Topografi Esri',            'img' => 'esri-topo.jpg'],
        ['id' => 'esri-gray',    'name' => 'Abu-abu Terang Esri',       'img' => 'esri-gray.jpg'],
    ];
@endphp

<div x-data="{
    basemapModalOpen: false,
    isExpanded: false,
    activeBasemap: 'osm',
    activeBasemapName: 'Open Street Map',
    activeBasemapShort: 'Open St...',
    activeBasemapImg: 'osm.png',

    basemapDict: {
        'satellite':    { name: 'Satelit',                   short: 'Satelit',     img: 'satelit.jpg' },
        'hybrid':       { name: 'Hybrid',                    short: 'Hybrid',      img: 'hybrid.jpg' },
        'big-rupabumi': { name: 'Rupabumi Indonesia (BIG)',  short: 'Rupabumi',    img: 'big-rupabumi.jpg' },
        'street':       { name: 'Street',                    short: 'Street',      img: 'street.png' },
        'osm':          { name: 'Open Street Map',           short: 'Open St...',  img: 'osm.png' },
        'topo':         { name: 'Topografi',                 short: 'Topografi',   img: 'topo.jpg' },
        'outdoor':      { name: 'Outdoor',                   short: 'Outdoor',     img: 'outdoor.jpg' },
        'terang':       { name: 'Terang',                    short: 'Terang',      img: 'terang.jpg' },
        'dataviz':      { name: 'Dataviz',                   short: 'Dataviz',     img: 'dataviz.jpg' },
        'esri-imagery': { name: 'Citra Esri',                short: 'Citra Esri',  img: 'esri-imagery.jpg' },
        'esri-topo':    { name: 'Topografi Esri',            short: 'Topo Esri',   img: 'esri-topo.jpg' },
        'esri-gray':    { name: 'Abu-abu Terang Esri',       short: 'Gray Esri',   img: 'esri-gray.jpg' },
    },

    getSiblings() {
        const quick = [
            { id: 'osm',          name: 'Open St...', img: 'osm.png' },
            { id: 'esri-imagery', name: 'Citra Esri', img: 'esri-imagery.jpg' },
            { id: 'hybrid',       name: 'Hybrid',     img: 'hybrid.jpg' }
        ];
        return quick.filter(item => item.id !== this.activeBasemap);
    },

    selectBasemap(id) {
        const item = this.basemapDict[id] || this.basemapDict['osm'];
        this.activeBasemap = id;
        this.activeBasemapName = item.name;
        this.activeBasemapShort = item.short;
        this.activeBasemapImg = item.img;
        this.isExpanded = false;
        this.basemapModalOpen = false;
        window.dispatchEvent(new CustomEvent('change-basemap', { detail: { id, name: item.name } }));
    }
}" class="bg-white rounded-3xl p-6 shadow-xs border border-slate-200/80 flex flex-col relative">

    <!-- Header Section (Peta Spasial Kluster Kerusakan 3T) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
        <div class="flex items-center gap-2.5 shrink-0">
            <span class="w-2.5 h-2.5 rounded-full bg-[#FF5C2A] animate-pulse"></span>
            <h3 class="text-sm lg:text-base font-bold text-slate-900 tracking-tight whitespace-nowrap">Peta Spasial Kluster Kerusakan 3T</h3>
        </div>

        <!-- Quick Filter / Layer Badges -->
        <div class="flex items-center gap-1.5 flex-wrap justify-end text-xs">
            <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium rounded-full border border-slate-200/80 transition-colors shrink-0 cursor-pointer">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                Batas Wilayah 3T
            </button>

            <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-medium rounded-full border border-slate-200/80 transition-colors shrink-0 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-[#FF5C2A]" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.316.492-.63 1.066-.94 1.717a23.3 23.3 0 00-.943 2.378 19.3 19.3 0 00-.77 2.784c-.05.28-.088.56-.118.84a9.7 9.7 0 00-3.352-1.742 1 1 0 00-1.127 1.488c.45.674.882 1.393 1.258 2.148.647 1.295 1.096 2.658 1.288 3.993a7.48 7.48 0 003.54-1.285 1 1 0 00.32-1.297 8.01 8.01 0 01-.67-2.736c.264-.325.56-.632.883-.912.87-.753 1.956-1.34 3.018-1.777a1 1 0 00.6-1.19z" clip-rule="evenodd"/></svg>
                Heatmap Kerusakan
            </button>

            <button type="button" class="flex items-center gap-1.5 px-3 py-1.5 bg-[#FFF2ED] hover:bg-[#FFE5DC] text-[#FF5C2A] font-semibold rounded-full border border-[#FFD9CC] transition-colors shrink-0 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-[#FF5C2A]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/></svg>
                Geofence 250m
            </button>
        </div>
    </div>

    <!-- Map Canvas Area (Full Edge-to-Edge with Overlapping Controls) -->
    <div class="relative flex-1 mt-4 rounded-2xl bg-[#DCE7F3] overflow-hidden min-h-[520px] lg:min-h-[560px] border border-slate-200/60 shadow-inner">
        
        <!-- MapLibre/MapTiler Canvas -->
        <div id="indonesia-map" class="w-full h-full min-h-[520px] lg:min-h-[560px] absolute inset-0 z-0"></div>

        <!-- Scoped Watermark & Logo Elimination -->
        <style>
            #indonesia-map .maplibregl-ctrl-bottom-left,
            #indonesia-map .maplibregl-ctrl-bottom-right,
            #indonesia-map .maplibregl-ctrl,
            #indonesia-map a[href*="maptiler"],
            #indonesia-map a[title*="MapTiler"] {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                pointer-events: none !important;
            }
        </style>

        <!-- Coordinate Grid Decorative Hint -->
        <div class="absolute inset-0 pointer-events-none z-[1] opacity-20" style="background-image: radial-gradient(#64748b 1px, transparent 1px); background-size: 36px 36px;"></div>

        <!-- 1. FLOATING TOP-LEFT: STAT INDEKS KERUSAKAN 3T -->
        <div class="absolute top-4 left-4 z-20 pointer-events-auto">
            <div class="flex flex-wrap items-center gap-3 bg-white/95 backdrop-blur-md px-3.5 py-2 rounded-xl border border-slate-200/80 text-xs shadow-md">
                <span class="font-bold text-slate-800 text-[11px]">Indeks Kerusakan 3T:</span>
                <span class="flex items-center gap-1.5 text-[11px] font-medium text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Kritis (&gt;4.0)
                </span>
                <span class="flex items-center gap-1.5 text-[11px] font-medium text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Sedang (2.5 &ndash; 4.0)
                </span>
                <span class="flex items-center gap-1.5 text-[11px] font-medium text-slate-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Tervalidasi / Prima
                </span>
            </div>
        </div>

        <!-- 2. FLOATING BOTTOM-LEFT: QUICK BASEMAP SELECTOR (SINGLE UNIT, ZERO GLITCH) -->
        <div class="absolute bottom-4 left-4 z-20 pointer-events-auto"
             @mouseenter="isExpanded = true"
             @mouseleave="isExpanded = false">

            <div class="flex items-center bg-white/95 backdrop-blur-md p-1 rounded-2xl border border-slate-200/90 shadow-md transition-all duration-300">
                
                <!-- PRIMARY CARD: ACTIVE BASEMAP (ALWAYS VISIBLE, 1 SINGLE BOX) -->
                <button type="button"
                        @click="isExpanded = !isExpanded"
                        class="relative w-14 h-14 rounded-xl overflow-hidden border-2 border-white ring-2 ring-blue-600 shadow-sm flex flex-col justify-end p-1 shrink-0 group cursor-pointer focus:outline-none">
                    <img :src="'/images/basemaps/' + activeBasemapImg" :alt="activeBasemapName" class="absolute inset-0 w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/25 to-transparent"></div>
                    <span class="relative z-10 text-[9px] font-bold text-white tracking-tight leading-none truncate text-center w-full drop-shadow-xs"
                          x-text="activeBasemapShort">
                    </span>
                </button>

                <!-- SLIDING SIBLINGS EXPANDER (EXPANDS HORIZONTALLY, ZERO VERTICAL STACKING) -->
                <div class="flex items-center overflow-hidden transition-all duration-300 ease-in-out"
                     :class="isExpanded ? 'max-w-[280px] opacity-100 ml-2' : 'max-w-0 opacity-0 ml-0 pointer-events-none'">
                    
                    <div class="flex items-center gap-2 pr-0.5 shrink-0">
                        <!-- Sibling Cards -->
                        <template x-for="sbm in getSiblings()" :key="sbm.id">
                            <button type="button"
                                    @click="selectBasemap(sbm.id)"
                                    class="relative w-14 h-14 rounded-xl overflow-hidden border border-slate-200/80 hover:border-slate-300 opacity-90 hover:opacity-100 transition-all flex flex-col justify-end p-1 group cursor-pointer focus:outline-none shrink-0">
                                <img :src="'/images/basemaps/' + sbm.img" :alt="sbm.name" class="absolute inset-0 w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/25 to-transparent"></div>
                                <span class="relative z-10 text-[9px] font-bold text-white tracking-tight leading-none truncate text-center w-full drop-shadow-xs"
                                      x-text="sbm.name">
                                </span>
                            </button>
                        </template>

                        <!-- Subtle Vertical Divider -->
                        <div class="w-px h-10 bg-slate-200/80 mx-0.5 shrink-0"></div>

                        <!-- Tombol Lainnya -->
                        <button type="button"
                                @click="basemapModalOpen = true; isExpanded = false"
                                class="flex flex-col items-center justify-center w-14 h-14 rounded-xl border border-slate-200/80 bg-white hover:bg-slate-50 text-teal-700 transition-all shadow-2xs group cursor-pointer focus:outline-none shrink-0">
                            <svg class="w-5 h-5 text-teal-600 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l10.125 5.25L22.5 12l-4.179-2.25m-11.892 4.5l-4.179 2.25 10.125 5.25 10.125-5.25-4.179-2.25m-11.892-9L12 3.75l9.75 4.5-9.75 4.5-9.75-4.5z" />
                            </svg>
                            <span class="text-[10px] font-bold text-teal-700 mt-1">Lainnya</span>
                        </button>
                    </div>

                </div>

            </div>
        </div>

        <!-- 3. FLOATING BOTTOM-RIGHT: VERTICAL ZOOM & RESET CONTROLS TOOLBAR -->
        <div class="absolute bottom-4 right-4 z-20 pointer-events-auto">
            <div class="flex flex-col items-center bg-white/95 backdrop-blur-md p-1 rounded-2xl border border-slate-200/80 shadow-md gap-0.5">
                
                <!-- Zoom In Button -->
                <div class="relative group">
                    <button type="button" id="btn-map-zoom-in" aria-label="Zoom In" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 active:scale-95 text-slate-700 hover:text-slate-900 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                    <!-- Tooltip -->
                    <div class="absolute right-full mr-2.5 top-1/2 -translate-y-1/2 hidden group-hover:flex items-center pointer-events-none z-30">
                        <div class="bg-slate-900 text-white text-[11px] font-semibold px-2 py-1 rounded-md shadow-lg whitespace-nowrap">
                            Zoom In
                        </div>
                        <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -ml-1"></div>
                    </div>
                </div>

                <!-- Zoom Out Button -->
                <div class="relative group">
                    <button type="button" id="btn-map-zoom-out" aria-label="Zoom Out" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 active:scale-95 text-slate-700 hover:text-slate-900 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                        </svg>
                    </button>
                    <!-- Tooltip -->
                    <div class="absolute right-full mr-2.5 top-1/2 -translate-y-1/2 hidden group-hover:flex items-center pointer-events-none z-30">
                        <div class="bg-slate-900 text-white text-[11px] font-semibold px-2 py-1 rounded-md shadow-lg whitespace-nowrap">
                            Zoom Out
                        </div>
                        <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -ml-1"></div>
                    </div>
                </div>

                <!-- Subtle Divider -->
                <div class="w-5 h-px bg-slate-200/80 my-0.5"></div>

                <!-- Reset Peta Icon Button -->
                <div class="relative group">
                    <button type="button" id="btn-map-reset" aria-label="Reset Peta" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 active:scale-95 text-slate-600 hover:text-slate-900 transition-all cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                    </button>
                    <!-- Tooltip -->
                    <div class="absolute right-full mr-2.5 top-1/2 -translate-y-1/2 hidden group-hover:flex items-center pointer-events-none z-30">
                        <div class="bg-slate-900 text-white text-[11px] font-semibold px-2 py-1 rounded-md shadow-lg whitespace-nowrap">
                            Reset Peta
                        </div>
                        <div class="w-1.5 h-1.5 bg-slate-900 rotate-45 -ml-1"></div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- 4. MODAL POPUP: PILIHAN PETA DASAR (BASEMAP) LENGKAP -->
    <div x-show="basemapModalOpen"
         x-on:keydown.escape.window="basemapModalOpen = false"
         class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0 flex items-center justify-center"
         style="display: none;">
        
        <!-- Backdrop Blur -->
        <div x-show="basemapModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/40 backdrop-blur-xs transition-opacity"
             @click="basemapModalOpen = false">
        </div>

        <!-- Dialog Card -->
        <div x-show="basemapModalOpen"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-slate-100 z-10 overflow-hidden transform transition-all my-8">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-teal-50 border border-teal-100 text-teal-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.429 9.75L2.25 12l10.125 5.25L22.5 12l-4.179-2.25m-11.892 4.5l-4.179 2.25 10.125 5.25 10.125-5.25-4.179-2.25m-11.892-9L12 3.75l9.75 4.5-9.75 4.5-9.75-4.5z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 leading-tight">Pilihan Peta Dasar (Basemap)</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Pilih jenis peta dasar yang sesuai dengan kebutuhan visualisasi Anda.</p>
                    </div>
                </div>

                <!-- Close Button (X) -->
                <button type="button"
                        @click="basemapModalOpen = false"
                        aria-label="Tutup modal"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body (12 Basemaps Grid with Real Raster Previews) -->
            <div class="p-6 max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3.5">
                    @foreach ($basemaps as $bm)
                        <button type="button"
                                @click="selectBasemap('{{ $bm['id'] }}')"
                                :class="activeBasemap === '{{ $bm['id'] }}' ? 'ring-2 ring-blue-600 border-blue-600 shadow-md' : 'border-slate-200/80 hover:border-slate-300 hover:shadow-xs'"
                                class="relative h-24 rounded-2xl overflow-hidden border transition-all flex flex-col justify-end p-2.5 group cursor-pointer focus:outline-none text-left">
                            
                            <!-- Thumbnail Background (Real Map Raster) -->
                            <img src="{{ asset('images/basemaps/' . $bm['img']) }}" alt="{{ $bm['name'] }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            
                            <!-- Gradient Shade -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/85 via-slate-900/30 to-transparent"></div>
                            
                            <!-- Selected Active Checkmark Badge (Top Right) -->
                            <div x-show="activeBasemap === '{{ $bm['id'] }}'"
                                 class="absolute top-2 right-2 w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-sm z-10">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>

                            <!-- Label -->
                            <span class="relative z-10 text-xs font-bold text-white tracking-tight leading-tight line-clamp-2 drop-shadow-xs">
                                {{ $bm['name'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-4 bg-slate-50/60 border-t border-slate-100">
                <div class="text-xs text-slate-600">
                    Basemap aktif: <strong class="text-slate-900 font-bold" x-text="activeBasemapName">Open Street Map</strong>
                </div>
                <button type="button"
                        @click="basemapModalOpen = false"
                        class="px-5 py-2 text-xs font-bold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl transition-colors cursor-pointer shadow-2xs">
                    Tutup
                </button>
            </div>

        </div>
    </div>

</div>
