<footer class="w-full bg-white border-t border-slate-100 py-10 px-4 lg:px-6 mt-auto antialiased font-sans">
    <div class="max-w-[1520px] mx-auto flex flex-col gap-8">
        
        <!-- Top Section: Brand & Multi-column Links -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Brand & Institutional Identity (col-span-5) -->
            <div class="lg:col-span-5 flex flex-col gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 hover:opacity-95 transition-opacity w-fit">
                    <x-ui.logo-icon size="w-9 h-9" rounded="rounded-xl" />
                    <span class="text-base font-extrabold text-[#14171F] tracking-tight">EduInfra Tracker</span>
                </a>
                <p class="text-xs text-slate-500 leading-relaxed max-w-md">
                    Platform audit dan sinkronisasi bantuan fasilitas pendidikan daerah Tertinggal, Terdepan, dan Terluar (3T) di bawah koordinasi Direktorat Jenderal Pendidikan Anak Usia Dini, Pendidikan Dasar, dan Pendidikan Menengah &mdash; Kemendikbudristek RI.
                </p>
                <div class="text-[11px] text-slate-400 mt-1">
                    Jl. Jenderal Sudirman, Senayan, Jakarta Pusat 10270
                </div>
            </div>

            <!-- Links Column 1: Modul Utama (col-span-2) -->
            <div class="lg:col-span-2 flex flex-col gap-3">
                <h4 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Modul Utama</h4>
                <ul class="flex flex-col gap-2 text-xs text-slate-500">
                    <li><a href="{{ route('dashboard') }}" class="hover:text-[#FF5C2A] transition-colors">Dashboard Spasial</a></li>
                    <li><a href="#audit" class="hover:text-[#FF5C2A] transition-colors">Audit Fasilitas</a></li>
                    <li><a href="#csr" class="hover:text-[#FF5C2A] transition-colors">Matching CSR</a></li>
                    <li><a href="#master-data" class="hover:text-[#FF5C2A] transition-colors">Master Data 3T</a></li>
                    <li><a href="#renovasi" class="hover:text-[#FF5C2A] transition-colors">Estimasi RAB</a></li>
                </ul>
            </div>

            <!-- Links Column 2: Regulasi & Dokumen (col-span-3) -->
            <div class="lg:col-span-3 flex flex-col gap-3">
                <h4 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Regulasi &amp; Acuan</h4>
                <ul class="flex flex-col gap-2 text-xs text-slate-500">
                    <li><a href="#standar-sarpras" class="hover:text-[#FF5C2A] transition-colors">Standar Sarpras Sekolah 3T</a></li>
                    <li><a href="#pedoman-csr" class="hover:text-[#FF5C2A] transition-colors">Pedoman Kemitraan CSR Pendidikan</a></li>
                    <li><a href="#sop-verifikasi" class="hover:text-[#FF5C2A] transition-colors">SOP Validasi Kerusakan Fisik</a></li>
                    <li><a href="#kebijakan-privasi" class="hover:text-[#FF5C2A] transition-colors">Kebijakan Privasi &amp; Data</a></li>
                </ul>
            </div>

            <!-- Links Column 3: Bantuan & Layanan (col-span-2) -->
            <div class="lg:col-span-2 flex flex-col gap-3">
                <h4 class="text-xs font-bold text-slate-900 tracking-wider uppercase">Pusat Bantuan</h4>
                <ul class="flex flex-col gap-2 text-xs text-slate-500">
                    <li><a href="#helpdesk" class="hover:text-[#FF5C2A] transition-colors">Helpdesk Sarpras 3T</a></li>
                    <li><a href="#layanan-auditor" class="hover:text-[#FF5C2A] transition-colors">Layanan Auditor Utama</a></li>
                    <li><a href="#panduan-pengguna" class="hover:text-[#FF5C2A] transition-colors">Buku Panduan Sistem</a></li>
                    <li><a href="mailto:sarpras3t@kemdikbud.go.id" class="text-slate-400 hover:text-[#FF5C2A] transition-colors">sarpras3t@kemdikbud.go.id</a></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Section: Simple Copyright Row -->
        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} Kemendikbudristek RI &bull; Direktorat Jenderal Paudasmen. Hak Cipta Dilindungi.</p>
            <p class="text-[11px] text-slate-400">Pemerataan Akses &amp; Mutu Fasilitas Pendidikan Daerah 3T</p>
        </div>

    </div>
</footer>
