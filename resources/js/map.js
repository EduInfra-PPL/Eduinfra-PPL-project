import * as maptilersdk from '@maptiler/sdk';
import '@maptiler/sdk/dist/maptiler-sdk.css';

export function initIndonesiaMap() {
    const mapContainer = document.getElementById('indonesia-map');
    if (!mapContainer) return;

    // Load API key from Vite environment
    const apiKey = import.meta.env.VITE_MAPTILER_API_KEY;
    if (apiKey) {
        maptilersdk.config.apiKey = apiKey;
    }

    // Coordinates: Center of Indonesian Archipelago
    const indonesiaCenter = [118.0149, -2.5489];
    const initialZoom = 4.6;

    // Initialize MapLibre/MapTiler Map
    const map = new maptilersdk.Map({
        container: mapContainer,
        style: maptilersdk.MapStyle.PASTEL || maptilersdk.MapStyle.STREETS,
        center: indonesiaCenter,
        zoom: initialZoom,
        minZoom: 3.8,
        maxZoom: 15,
        // Boundary constraint for Indonesia region
        maxBounds: [
            [92.0, -13.5], // Southwest [lng, lat]
            [144.0, 9.0]   // Northeast [lng, lat]
        ],
        navigationControl: false,
        geolocateControl: false,
    });

    map.on('load', () => {
        // Render 3 visual static mock pins matching Figma screenshot
        addVisualMockPins(map);
    });

    // Setup custom zoom & reset controls
    setupMapControls(map, indonesiaCenter, initialZoom);
}

function addVisualMockPins(map) {
    // 1. PIN 1: KALBAR / SAMBAS (Cluster 84 with focus tooltip)
    const sambasCoords = [109.30, 1.30];
    
    // Custom HTML Marker Element
    const sambasEl = document.createElement('div');
    sambasEl.className = 'relative flex items-center justify-center cursor-pointer select-none';
    sambasEl.innerHTML = `
        <span class="absolute w-10 h-10 rounded-full bg-emerald-500/25 animate-ping"></span>
        <div class="w-8 h-8 rounded-full bg-[#10B981] text-white flex items-center justify-center font-extrabold text-xs shadow-md border-2 border-white z-10">
            84
        </div>
        <span class="absolute -bottom-5 whitespace-nowrap text-[9px] font-bold text-[#FF5C2A] bg-white/90 px-1.5 py-0.5 rounded shadow-xs border border-slate-200/50">
            KALBAR (KRITIS)
        </span>
    `;

    // Tooltip Popup matching the mockup
    const popupHtml = `
        <div class="p-2 min-w-[190px] font-sans">
            <div class="flex items-center justify-between gap-1 mb-1">
                <span class="px-1.5 py-0.5 text-[9px] font-bold text-[#FF5C2A] bg-[#FFF2ED] rounded">Fokus Terpilih</span>
                <span class="text-[10px] font-semibold text-emerald-600">✓ GPS Match</span>
            </div>
            <div class="text-xs font-bold text-slate-900 leading-tight">SDN 02 Sambas Terluar</div>
            <div class="text-[10px] text-slate-500 font-medium">NPSN: 10802199 &bull; Kalbar</div>
            <div class="mt-1.5 pt-1.5 border-t border-slate-100 flex items-center justify-between">
                <span class="text-[10px] text-slate-500 font-semibold">Skor Kerusakan:</span>
                <span class="text-[10px] font-extrabold text-[#FF5C2A]">4.8 / 5.0</span>
            </div>
        </div>
    `;

    const popup = new maptilersdk.Popup({
        offset: 25,
        closeButton: false,
        closeOnClick: false,
        className: 'eduinfra-popup'
    }).setHTML(popupHtml);

    new maptilersdk.Marker({ element: sambasEl })
        .setLngLat(sambasCoords)
        .setPopup(popup)
        .addTo(map);

    // Open Sambas popup by default as focused school
    popup.addTo(map);

    // 2. PIN 2: NTT / ALOR (Cluster 68)
    const nttCoords = [124.70, -8.30];
    const nttEl = document.createElement('div');
    nttEl.className = 'relative flex flex-col items-center cursor-pointer select-none';
    nttEl.innerHTML = `
        <div class="w-8 h-8 rounded-full bg-[#FF5C2A] text-white flex items-center justify-center font-extrabold text-xs shadow-md border-2 border-white">
            68
        </div>
        <span class="mt-0.5 whitespace-nowrap text-[9px] font-bold text-[#FF5C2A] bg-white/90 px-1.5 py-0.5 rounded shadow-xs border border-slate-200/50">
            NTT / ALOR (KRITIS)
        </span>
    `;

    new maptilersdk.Marker({ element: nttEl })
        .setLngLat(nttCoords)
        .addTo(map);

    // 3. PIN 3: PAPUA (Cluster 215 Darurat Tier-1)
    const papuaCoords = [138.50, -4.80];
    const papuaEl = document.createElement('div');
    papuaEl.className = 'relative flex flex-col items-center cursor-pointer select-none';
    papuaEl.innerHTML = `
        <div class="w-9 h-9 rounded-full bg-[#EA4A18] text-white flex items-center justify-center font-extrabold text-xs shadow-md border-2 border-white">
            215
        </div>
        <span class="mt-0.5 whitespace-nowrap text-[9px] font-extrabold text-red-600 bg-white/90 px-1.5 py-0.5 rounded shadow-xs border border-red-200">
            PAPUA (DARURAT)
        </span>
    `;

    new maptilersdk.Marker({ element: papuaEl })
        .setLngLat(papuaCoords)
        .addTo(map);
}

function setupMapControls(map, center, zoom) {
    const btnZoomIn = document.getElementById('btn-map-zoom-in');
    const btnZoomOut = document.getElementById('btn-map-zoom-out');
    const btnReset = document.getElementById('btn-map-reset');

    if (btnZoomIn) {
        btnZoomIn.addEventListener('click', () => map.zoomIn());
    }

    if (btnZoomOut) {
        btnZoomOut.addEventListener('click', () => map.zoomOut());
    }

    if (btnReset) {
        btnReset.addEventListener('click', () => {
            map.flyTo({
                center: center,
                zoom: zoom,
                essential: true,
                duration: 1200
            });
        });
    }
}

