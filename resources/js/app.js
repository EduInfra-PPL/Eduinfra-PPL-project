import './bootstrap';
import Alpine from 'alpinejs';
import { initIndonesiaMap } from './map';

window.Alpine = Alpine;
Alpine.start();

// Initialize Indonesia GIS Map on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initIndonesiaMap);
} else {
    initIndonesiaMap();
}
