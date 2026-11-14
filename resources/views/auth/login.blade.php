<x-layouts.guest title="Login Sistem">
    <x-slot:actions>
        <x-ui.button :href="url('/')" variant="outline" size="md">
            <x-ui.icon name="arrow-left" class="w-3.5 h-3.5" /> Portal Publik
        </x-ui.button>
        <span class="w-9 h-9 rounded-full bg-[#FFE2D6] text-[#C2410C] flex items-center justify-center">
            <x-ui.icon name="user" class="w-4 h-4" />
        </span>
    </x-slot:actions>

    <div class="relative">
        <!-- Chip keamanan melayang -->
        <div class="hidden lg:flex absolute -top-3 right-4 z-10 items-center gap-2 px-3.5 py-2 bg-white rounded-full shadow-md border border-slate-100 text-[11px] font-semibold text-slate-700">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            <x-ui.icon name="shield-check" class="w-3.5 h-3.5 text-emerald-500" />
            Enkripsi TLS 1.3 &amp; Geofencing IP ASN Aktif
        </div>

        <div class="grid lg:grid-cols-2 bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-100">

            <!-- KIRI: PANEL BRAND -->
            <x-auth.brand-panel chip="Portal Sistem Tertutup (Restricted)">
                <div class="flex items-center gap-3">
                    <x-ui.logo-icon size="w-11 h-11" rounded="rounded-xl" />
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-extrabold">EduInfra Tracker</span>
                            <x-ui.badge variant="orange">GIS 3T v2.4</x-ui.badge>
                        </div>
                        <p class="text-[11px] text-slate-400">Audit Fasilitas &amp; Matching CSR Wilayah 3T</p>
                    </div>
                </div>

                <div>
                    <h2 class="text-3xl font-extrabold leading-tight">Portal Pengawasan &amp; Audit Infrastruktur 3T</h2>
                    <p class="text-sm text-slate-300 leading-relaxed mt-3">
                        Konsol terpusat verifikasi teknis, rekonsiliasi data sarpras rawan bencana, dan integrasi kemitraan CSR nasional.
                    </p>
                </div>

                <ul class="space-y-2.5 text-xs text-slate-200 font-medium">
                    <li class="flex items-center gap-2.5"><x-ui.icon name="shield-check" class="w-4 h-4 text-[#FF5C2A]" /> BSrE &amp; BSSN Certified Digital Signature</li>
                    <li class="flex items-center gap-2.5"><x-ui.icon name="switch" class="w-4 h-4 text-sky-400" /> Sinkronisasi Dapodik Pusat v2.8 &amp; BIG Geospasial</li>
                    <li class="flex items-center gap-2.5"><x-ui.icon name="clock" class="w-4 h-4 text-emerald-400" /> Audit Trail Enkripsi SHA-256 Terdesentralisasi</li>
                </ul>

                <x-auth.live-stats-box :stats="[
                    ['value' => '14.820', 'label' => 'Sekolah 3T Terdata'],
                    ['value' => '34', 'label' => 'Audit Aktif Lapangan', 'accent' => true],
                ]" />

                <x-slot:footer>
                    <div class="flex items-start gap-3 text-[11px] text-slate-400 leading-relaxed">
                        <span class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center shrink-0 text-slate-200">
                            <x-ui.icon name="lock" class="w-3.5 h-3.5" />
                        </span>
                        Akses diawasi oleh Ditjen PAUD Dikdasmen &amp; Badan Siber dan Sandi Negara. Pelanggaran tunduk UU ITE No. 1/2024.
                    </div>
                </x-slot:footer>
            </x-auth.brand-panel>

            <!-- KANAN: FORM LOGIN -->
            <section class="p-7 lg:p-12 flex flex-col justify-center">
                <h1 class="text-2xl font-extrabold text-[#14171F]">Login Sistem EduInfra</h1>
                <p class="text-sm text-slate-500 mt-1.5">Autentikasi akun resmi kedinasan atau verifikator wilayah 3T berlisensi.</p>

                @if (session('status'))
                    <x-ui.alert variant="success" class="mt-5">{{ session('status') }}</x-ui.alert>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5"
                      x-data="{ login: @js(old('email', '')), submitting: false }" @submit="submitting = true">
                    @csrf

                    <x-auth.input-field name="email" label="NIP / Email Kedinasan (18 Digit)"
                                        placeholder="198402112008011002 atau nama@kemdikbud.go.id"
                                        :error="$errors->first('email')"
                                        x-model="login" autocomplete="username" required autofocus>
                        <x-slot:icon><x-ui.icon name="user" class="w-4 h-4" /></x-slot:icon>
                        <x-slot:trailing>
                            <span x-show="/^\d{18}$/.test(login) || /^\S+@\S+\.\S+$/.test(login)" x-cloak class="text-emerald-500">
                                <x-ui.icon name="check-circle" class="w-4 h-4" />
                            </span>
                        </x-slot:trailing>
                    </x-auth.input-field>

                    <x-auth.input-field name="password" type="password" label="Kata Sandi Kedinasan"
                                        placeholder="Masukkan kata sandi" :toggle="true"
                                        :error="$errors->first('password')"
                                        autocomplete="current-password" required>
                        <x-slot:icon><x-ui.icon name="lock" class="w-4 h-4" /></x-slot:icon>
                        @if (Route::has('password.request'))
                            <x-slot:aside>
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-[#FF5C2A] hover:text-[#EA4A18]">Lupa Kata Sandi?</a>
                            </x-slot:aside>
                        @endif
                    </x-auth.input-field>

                    <label class="flex items-center gap-2.5 text-xs font-medium text-slate-600 cursor-pointer">
                        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded accent-[#FF5C2A]">
                        Ingat sesi terpercaya ini (Maks. 8 jam sesuai protokol BSSN)
                    </label>

                    <x-ui.button type="submit" variant="primary" size="lg" class="w-full" ::disabled="submitting">
                        Masuk ke Dashboard EduInfra <x-ui.icon name="arrow-right" class="w-4 h-4" />
                    </x-ui.button>
                </form>

                <p class="mt-8 text-center text-[11px] text-slate-400 leading-relaxed">
                    Kendala sinkronisasi token 2FA atau akses satker darurat? Hubungi Pusdatin: 1500-087 atau email
                    <span class="font-semibold text-[#FF5C2A]">helpdesk.sarpras@kemdikbud.go.id</span>
                </p>

                @if (Route::has('register'))
                    <p class="mt-4 text-center text-xs text-slate-500">
                        Belum memiliki akun resmi?
                        <a href="{{ route('register') }}" class="font-bold text-[#FF5C2A] hover:text-[#EA4A18]">Ajukan registrasi</a>
                    </p>
                @endif
            </section>
        </div>

        <x-auth.server-status-bar />
    </div>
</x-layouts.guest>
