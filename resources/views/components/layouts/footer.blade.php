<footer class="w-full bg-white border-t border-slate-100 py-8 px-4 lg:px-6 mt-auto">
    <div class="max-w-[1520px] mx-auto flex flex-col gap-6">
        
        <!-- Top Row: Brand & Policy Links -->
        <div class="flex flex-wrap items-start justify-between gap-6">
            <!-- Brand & Mission Info -->
            <div class="max-w-xl">
                <div class="flex items-center gap-2.5 mb-2">
                    <x-ui.logo-icon size="w-8 h-8" rounded="rounded-xl" />
                    <span class="text-base font-extrabold text-[#14171F]">EduInfra Tracker</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Platform Audit &amp; Sinkronisasi Bantuan Fasilitas Pendidikan Daerah Tertinggal, Terdepan, dan Terluar (3T) di bawah koordinasi Ditjen PAUD, Dikdas, dan Dikmen Kemendikbudristek RI.
                </p>
            </div>

            <!-- Links Row -->
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs font-semibold text-slate-600">
                <a href="#pedoman" class="hover:text-[#FF5C2A] transition-colors">Pedoman Kemitraan CSR</a>
                <a href="#sarpras" class="hover:text-[#FF5C2A] transition-colors">Standar Sarpras 3T</a>
                <a href="#regulasi" class="hover:text-[#FF5C2A] transition-colors">Regulasi Kemendikbud</a>
                <a href="#privasi" class="hover:text-[#FF5C2A] transition-colors">Kebijakan Privasi</a>
            </div>
        </div>

        <!-- Bottom Row: Copyright & Sync Indicator -->
        <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4 text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} Kemendikbudristek &bull; Direktorat Jenderal Paudasmen. Hak Cipta Dilindungi.</p>

            <div class="flex items-center gap-2 text-slate-600 font-medium">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Sistem Sinkronisasi Dapodik Aktif</span>
            </div>
        </div>

    </div>
</footer>
