import './bootstrap';
import Alpine from 'alpinejs';
import { registrationForm } from './components/registrationForm';
import { schoolForm } from './components/schoolForm';
import { initIndonesiaMap } from './map';

// Komponen Alpine didaftarkan sebelum Alpine.start() agar tersedia saat DOM diinisialisasi.
Alpine.data('registrationForm', registrationForm);
Alpine.data('schoolForm', schoolForm);

window.Alpine = Alpine;
Alpine.start();

// Initialize Indonesia GIS Map on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initIndonesiaMap);
} else {
    initIndonesiaMap();
}
