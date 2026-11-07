<x-layouts.guest title="Registrasi Akun"
                 subtitle="Kementerian Pendidikan, Kebudayaan, Riset, dan Teknologi RI"
                 :chips="['BSSN BSrE 256-Bit', 'ISO/IEC 27001:2022']">
    <x-slot:actions>
        <span class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-[11px] font-semibold text-slate-500">
            <x-ui.icon name="globe" class="w-3.5 h-3.5" /> Portal Publik Sarpras
        </span>
        <x-ui.button :href="route('login')" variant="dark" size="md">
            <x-ui.icon name="arrow-left" class="w-3.5 h-3.5" /> Kembali ke Login Portal
        </x-ui.button>
    </x-slot:actions>

    <div class="flex justify-center mb-5">
        <span class="inline-flex items-center gap-2 px-3.5 py-1 bg-white rounded-full border border-slate-100 shadow-sm text-[10px] font-bold tracking-wide text-slate-600 uppercase">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            Portal Otentikasi Terpadu 3T &bull; Kementerian &amp; Verifikator
        </span>
    </div>

    <div class="grid lg:grid-cols-[0.85fr_1.15fr] bg-white rounded-3xl overflow-hidden shadow-xl border border-slate-100">

        <!-- KIRI: PANEL INFORMASI -->
        <x-auth.brand-panel chip="Portal Sistem Tertutup (Restricted Access)" chip-dot="bg-[#FF5C2A]">
            <div class="flex items-center gap-2 text-[11px] font-bold tracking-wide text-slate-300 uppercase">
                <x-ui.icon name="building" class="w-4 h-4 text-[#FF5C2A]" /> Sistem Audit Sarpras Wilayah 3T
            </div>

            <div>
                <h2 class="text-3xl font-extrabold leading-tight">Registrasi Akun Aparatur &amp; Verifikator Sarpras 3T</h2>
                <p class="text-sm text-slate-300 leading-relaxed mt-3">
                    Pendaftaran akses resmi untuk verifikator lapangan, kepala sekolah 3T, inspektorat audit, dan mitra pelaksana renovasi sarpras sekolah terluar/terpencil.
                </p>
            </div>

            <div class="space-y-2.5">
                @foreach ([
                    ['shield-check', 'Terintegrasi SSO Kedinasan', 'Pusdatin Kemendikbudristek & BKN Terbuka'],
                    ['location', 'Validasi Otomatis NIP / NIK & Geospasial', 'Pemetaan satker presisi berbasis koordinat 3T'],
                    ['lock', 'Otentikasi Berlapis MFA / FIDO2', 'Kepatuhan enkripsi standar BSrE BSSN & TLS 1.3'],
                ] as [$icon, $title, $text])
                    <div class="flex items-center gap-3 rounded-xl bg-white/5 border border-white/10 p-3">
                        <span class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-[#FF8A63] shrink-0">
                            <x-ui.icon :name="$icon" class="w-4 h-4" />
                        </span>
                        <div>
                            <div class="text-xs font-bold">{{ $title }}</div>
                            <div class="text-[11px] text-slate-400">{{ $text }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <x-auth.realtime-stats-box :stats="[
                ['value' => '1.248', 'label' => 'Akun Terverifikasi'],
                ['value' => '6', 'label' => 'Lembaga Terpadu'],
                ['value' => '<24 Jam', 'label' => 'SLA Audit Akses', 'accent' => true],
            ]" />

            <x-slot:footer>
                <div class="text-[11px] leading-relaxed text-slate-400">
                    <div class="font-bold text-slate-200 mb-1">Pemberitahuan Regulasi Kepegawaian</div>
                    Setiap data pendaftaran diverifikasi melalui basis data kepegawaian nasional. Pemalsuan identitas dikenakan sanksi pidana sesuai UU ITE No. 1/2024 &amp; UU Aparatur Sipil Negara.
                </div>
            </x-slot:footer>
        </x-auth.brand-panel>

        <!-- KANAN: FORMULIR REGISTRASI -->
        <section class="p-6 lg:p-9"
                 x-data="registrationForm(@js(['role' => old('registration_role', 'verifikator_3t'), 'identity' => old('identity_number', '')]))">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Registrasi Jalur Kedinasan</p>
                    <h1 class="text-2xl font-extrabold text-[#14171F]">Formulir Registrasi Pengguna Baru</h1>
                </div>
                <p class="text-[11px] text-slate-500 text-right">
                    Sudah memiliki akun resmi?<br>
                    <a href="{{ route('login') }}" class="font-bold text-[#FF5C2A] hover:text-[#EA4A18]">Masuk di sini &rsaquo;</a>
                </p>
            </div>

            <p class="text-xs text-slate-500 leading-relaxed mt-4">
                Lengkapi data identitas kedinasan dan pilih peran penugasan audit/sarpras Anda untuk penerbitan token akses digital.
            </p>

            @if ($errors->any())
                <x-ui.alert variant="danger" title="Formulir belum dapat dikirim" class="mt-4">
                    Periksa kembali isian yang ditandai merah di bawah ini.
                </x-ui.alert>
            @endif

            <!-- SSO (belum tersedia) -->
            <div class="grid sm:grid-cols-2 gap-3 mt-5">
                <button type="button" disabled title="Integrasi SSO belum tersedia"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-400 cursor-not-allowed">
                    <x-ui.icon name="document" class="w-4 h-4" /> Daftar dengan Akun Belajar.id
                </button>
                <button type="button" disabled title="Integrasi SSO belum tersedia"
                        class="flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-xs font-semibold text-slate-400 cursor-not-allowed">
                    <x-ui.icon name="identification" class="w-4 h-4" /> Daftar dengan SSO ASN BKN
                </button>
            </div>

            <div class="flex items-center gap-3 my-5 text-[10px] font-bold tracking-wide text-slate-400 uppercase">
                <span class="h-px flex-1 bg-slate-200"></span>
                Atau daftar manual dengan kredensial kedinasan
                <span class="h-px flex-1 bg-slate-200"></span>
            </div>

            <form method="POST" action="{{ route('register.store') }}" class="space-y-6">
                @csrf

                <!-- LANGKAH 1: PERAN & INSTANSI -->
                <fieldset>
                    <legend class="text-[11px] font-extrabold tracking-wide text-[#14171F] uppercase mb-2.5">Langkah 1: Peran &amp; Instansi Penugasan</legend>
                    <div class="grid sm:grid-cols-2 gap-2.5">
                        @foreach ($roles as $role)
                            <label class="flex items-start gap-2.5 p-3 rounded-xl border cursor-pointer transition-colors"
                                   :class="role === '{{ $role->value }}' ? 'border-[#FF5C2A] bg-[#FFF2ED]' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                <input type="radio" name="registration_role" value="{{ $role->value }}" x-model="role"
                                       @checked(old('registration_role', 'verifikator_3t') === $role->value)
                                       class="mt-0.5 accent-[#FF5C2A]" required>
                                <span>
                                    <span class="block text-xs font-bold text-[#14171F]">{{ $role->title() }}</span>
                                    <span class="block text-[11px] text-slate-500">{{ $role->hint() }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('registration_role')
                        <p class="text-[11px] text-red-600 font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </fieldset>

                <!-- LANGKAH 2: IDENTITAS KEDINASAN -->
                <fieldset>
                    <legend class="text-[11px] font-extrabold tracking-wide text-[#14171F] uppercase mb-2.5">Langkah 2: Identitas Kedinasan</legend>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <x-auth.input-field name="name" label="Nama Lengkap & Gelar Resmi" placeholder="Dr. Budi Santoso, M.Eng"
                                            :error="$errors->first('name')" autocomplete="name" required>
                            <x-slot:icon><x-ui.icon name="user" class="w-4 h-4" /></x-slot:icon>
                        </x-auth.input-field>

                        <x-auth.input-field name="identity_number" label="NIP (18-Digit) / NIK (16-Digit)" placeholder="198403152008121002"
                                            :error="$errors->first('identity_number')"
                                            x-model="identity" inputmode="numeric" maxlength="24" required>
                            <x-slot:icon><x-ui.icon name="identification" class="w-4 h-4" /></x-slot:icon>
                            <x-slot:aside>
                                <span x-show="identityState === 'nip' || identityState === 'nik'" x-cloak class="text-[10px] font-bold text-emerald-600">
                                    &#10003; <span x-text="identityState === 'nip' ? 'Format NIP valid' : 'Format NIK valid'"></span>
                                </span>
                            </x-slot:aside>
                        </x-auth.input-field>

                        <x-auth.input-field name="email" type="email" label="Alamat Email Kedinasan Aktif" placeholder="nama@kemdikbud.go.id"
                                            :error="$errors->first('email')" autocomplete="email" required>
                            <x-slot:icon><x-ui.icon name="mail" class="w-4 h-4" /></x-slot:icon>
                        </x-auth.input-field>

                        <x-auth.input-field name="phone" type="tel" label="Nomor Seluler (Token OTP 2FA SMS/WA)" placeholder="+62 812-4498-2100"
                                            :error="$errors->first('phone')" autocomplete="tel" required>
                            <x-slot:icon><x-ui.icon name="phone" class="w-4 h-4" /></x-slot:icon>
                        </x-auth.input-field>
                    </div>
                </fieldset>

                <!-- LANGKAH 3: WILAYAH TUGAS & SATUAN KERJA -->
                <fieldset>
                    <legend class="text-[11px] font-extrabold tracking-wide text-[#14171F] uppercase mb-2.5">Langkah 3: Penetapan Wilayah Tugas &amp; Satuan Kerja 3T</legend>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="w-full flex flex-col gap-1.5">
                            <label for="region_id" class="text-xs font-semibold text-slate-700">Provinsi &amp; Kabupaten Koridor 3T</label>
                            <div class="relative flex items-center w-full">
                                <div class="absolute left-3.5 text-slate-400 pointer-events-none flex items-center">
                                    <x-ui.icon name="location" class="w-4 h-4" />
                                </div>
                                <select id="region_id" name="region_id" required
                                        class="w-full text-sm font-medium text-slate-800 bg-[#F1F5F9] border {{ $errors->has('region_id') ? 'border-red-400' : 'border-transparent' }} focus:border-[#FF5C2A] focus:bg-white focus:outline-none transition-all py-3 rounded-xl pl-11 pr-4">
                                    <option value="">Pilih kabupaten koridor 3T</option>
                                    @foreach ($regions as $region)
                                        <option value="{{ $region['id'] }}" @selected((string) old('region_id') === (string) $region['id'])>{{ $region['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if ($errors->has('region_id'))
                                <p class="text-[11px] text-red-600 font-medium pl-1">{{ $errors->first('region_id') }}</p>
                            @elseif ($regions->isEmpty())
                                <p class="text-[11px] text-amber-600 font-medium pl-1">Data wilayah belum tersedia. Hubungi administrator.</p>
                            @endif
                        </div>

                        <x-auth.input-field name="organization" label="Nama Satdik / Satuan Kerja Unit Asal" placeholder="Balai Prasarana Permukiman Wilayah"
                                            :error="$errors->first('organization')" required>
                            <x-slot:icon><x-ui.icon name="building" class="w-4 h-4" /></x-slot:icon>
                        </x-auth.input-field>
                    </div>
                </fieldset>

                <!-- LANGKAH 4: KREDENSIAL -->
                <fieldset>
                    <legend class="text-[11px] font-extrabold tracking-wide text-[#14171F] uppercase mb-2.5">Langkah 4: Pengamanan Akun Kredensial</legend>
                    <div class="grid sm:grid-cols-2 gap-4">
                        <x-auth.input-field name="password" type="password" label="Kata Sandi Baru" :toggle="true"
                                            :error="$errors->first('password')" x-model="password"
                                            autocomplete="new-password" required>
                            <x-slot:icon><x-ui.icon name="lock" class="w-4 h-4" /></x-slot:icon>
                        </x-auth.input-field>

                        <x-auth.input-field name="password_confirmation" type="password" label="Konfirmasi Kata Sandi" :toggle="true"
                                            x-model="confirmation" autocomplete="new-password" required>
                            <x-slot:icon><x-ui.icon name="lock" class="w-4 h-4" /></x-slot:icon>
                        </x-auth.input-field>
                    </div>

                    <div class="flex items-center gap-3 mt-3" x-show="password.length > 0" x-cloak>
                        <div class="flex gap-1.5 flex-1">
                            <template x-for="i in 4" :key="i">
                                <span class="h-1.5 flex-1 rounded-full transition-colors" :class="i <= score ? strengthColor : 'bg-slate-200'"></span>
                            </template>
                        </div>
                        <span class="text-[11px] font-bold text-slate-600 whitespace-nowrap" x-text="strengthLabel + ' (' + strengthPercent + '%)'"></span>
                        <span x-show="confirmationState === 'match'" x-cloak class="text-[11px] font-bold text-emerald-600 whitespace-nowrap">&#10003; Kata sandi cocok</span>
                        <span x-show="confirmationState === 'mismatch'" x-cloak class="text-[11px] font-bold text-red-600 whitespace-nowrap">Kata sandi belum cocok</span>
                    </div>
                </fieldset>

                <!-- PAKTA INTEGRITAS -->
                <div>
                    <label class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-200/70 cursor-pointer">
                        <input type="checkbox" name="terms" value="1" @checked(old('terms')) class="mt-0.5 w-4 h-4 rounded accent-[#FF5C2A]">
                        <span class="text-[11px] text-slate-600 leading-relaxed">
                            Saya menyatakan bahwa data yang diisi adalah benar, sah, dan dapat dipertanggungjawabkan secara hukum. Saya bersedia mematuhi
                            <strong class="text-[#14171F]">Pakta Integritas Kerahasiaan Data Sarpras 3T</strong> serta standar audit sistem keamanan informasi
                            <strong class="text-[#14171F]">ISO/IEC 27001</strong>.
                        </span>
                    </label>
                    @error('terms')
                        <p class="text-[11px] text-red-600 font-medium mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                    Kirim Permohonan Registrasi Akun <x-ui.icon name="arrow-right" class="w-4 h-4" />
                </x-ui.button>

                <p class="text-center text-[11px] text-slate-400">
                    Butuh bantuan verifikasi berkas darurat satker? Hubungi Helpdesk Pusdatin:
                    <strong class="text-slate-600">1500-087</strong> atau surel
                    <span class="font-semibold text-[#FF5C2A]">registrasi@kemdikbud.go.id</span>
                </p>
            </form>
        </section>
    </div>

    <!-- BARIS STATUS (jam server WIB berjalan) -->
    <div class="mt-4 px-2 flex flex-wrap items-center justify-between gap-2 text-[11px] font-medium text-slate-500"
         x-data="{ time: '' }"
         x-init="const tick = () => time = new Date().toLocaleTimeString('id-ID', { timeZone: 'Asia/Jakarta', hour12: false }); tick(); setInterval(tick, 1000)">
        <span class="inline-flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            Cluster Node: ID-JKT-PUSD-04 (Operasional Normal)
        </span>
        <span class="inline-flex flex-wrap items-center gap-x-3">
            <span>Standar Keamanan: ISO/IEC 27001:2022</span>
            <span>&bull; Protokol Enkripsi: TLS 1.3 / AES-256-GCM</span>
            <span>&bull; Waktu Server: <span x-text="time"></span> WIB</span>
        </span>
    </div>

    {{-- Logika tampilan form (state Alpine). Didaftarkan lewat event alpine:init karena Alpine dijalankan dari resources/js/app.js. --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registrationForm', (initial = {}) => ({
                role: initial.role || 'verifikator_3t',
                identity: initial.identity || '',
                password: '',
                confirmation: '',

                // 'empty' | 'nip' (18 digit) | 'nik' (16 digit) | 'invalid'
                get identityState() {
                    const digits = this.identity.replace(/\D/g, '');

                    if (digits.length === 0) return 'empty';
                    if (digits.length === 18) return 'nip';
                    if (digits.length === 16) return 'nik';

                    return 'invalid';
                },

                // Skor 0-4: panjang >= 12, huruf besar+kecil, angka, simbol.
                get score() {
                    const value = this.password;
                    if (!value) return 0;

                    return [
                        value.length >= 12,
                        /[a-z]/.test(value) && /[A-Z]/.test(value),
                        /\d/.test(value),
                        /[^A-Za-z0-9]/.test(value),
                    ].filter(Boolean).length;
                },

                get strengthLabel() {
                    return ['', 'Lemah', 'Cukup', 'Kuat', 'Sangat Kuat'][this.score];
                },

                get strengthPercent() {
                    return this.score * 25;
                },

                get strengthColor() {
                    return ['bg-slate-200', 'bg-red-500', 'bg-amber-500', 'bg-emerald-400', 'bg-emerald-500'][this.score];
                },

                get confirmationState() {
                    if (!this.confirmation) return 'empty';

                    return this.confirmation === this.password ? 'match' : 'mismatch';
                },
            }));
        });
    </script>
</x-layouts.guest>