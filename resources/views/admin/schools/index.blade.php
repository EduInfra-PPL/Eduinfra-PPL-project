@php
    $levels = ['SD' => 'SD', 'SMP' => 'SMP', 'SMA' => 'SMA'];
    $categories = ['tertinggal' => 'Tertinggal', 'terdepan' => 'Terdepan', 'terluar' => 'Terluar'];
    $sourceLabels = ['dapodik' => 'Dapodik', 'osm' => 'OSM', 'manual' => 'Manual'];
    $statusOptions = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif'];
@endphp

<x-layouts.app active="sekolah" title="Profil Sekolah - EduInfra Tracker">
    <div class="max-w-[1520px] mx-auto px-4 lg:px-6 pb-10">

        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Master Wilayah dan Kelembagaan Pendidikan</p>
                <h1 class="text-xl font-extrabold text-[#14171F]">Profil Sekolah</h1>
            </div>
            <x-ui.button variant="primary" size="md" x-data x-on:click="$dispatch('open-modal', 'create-school')">
                + Tambah Satuan Pendidikan
            </x-ui.button>
        </div>

        {{-- Pesan dari server --}}
        @if (session('success'))
            <div class="mb-4 px-4 py-3 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="mb-4 px-4 py-3 rounded-2xl bg-red-50 border border-red-200 text-xs font-semibold text-red-700">{{ session('error') }}</div>
        @endif

        {{-- Kartu ringkasan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <x-ui.kpi-card title="Sekolah Aktif" :value="number_format($stats['total'])" unit="sekolah" icon="building" subText="Terdaftar di sistem" />
            <x-ui.kpi-card title="Sekolah di Wilayah 3T" :value="number_format($stats['three_t'])" unit="sekolah" variant="urgent" icon="alert" subText="Wilayah 3T" />
            <x-ui.kpi-card title="Kabupaten/Kota" :value="number_format($stats['regencies'])" unit="wilayah" icon="link" subText="Dalam batas wilayah" />
            <x-ui.kpi-card title="Nonaktif" :value="number_format($stats['inactive'])" unit="sekolah" icon="hourglass" subText="Dinonaktifkan" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tabel sekolah --}}
            <div class="lg:col-span-2 bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100">
                <form method="GET" action="{{ route('admin.schools.index') }}" class="flex flex-wrap gap-2">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari NPSN atau nama sekolah"
                           class="flex-1 min-w-[180px] px-4 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70 focus:border-[#FF5C2A] focus:bg-white focus:outline-none">
                    <select name="level" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
                        <option value="">Semua jenjang</option>
                        @foreach ($levels as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['level'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="is_3t" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
                        <option value="">Semua wilayah</option>
                        <option value="1" @selected(($filters['is_3t'] ?? '') === '1')>Wilayah 3T</option>
                        <option value="0" @selected(($filters['is_3t'] ?? '') === '0')>Bukan 3T</option>
                    </select>
                    <select name="category" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
                        <option value="">Semua kategori 3T</option>
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['category'] ?? '') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="status" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
                        @foreach ($statusOptions as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? 'aktif') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-full bg-[#14171F] text-white">Terapkan</button>
                </form>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="text-[10px] font-bold uppercase tracking-wide text-slate-400 border-b border-slate-100">
                                <th class="py-3 pr-4">NPSN</th>
                                <th class="py-3 pr-4">Nama Sekolah</th>
                                <th class="py-3 pr-4">Jenjang</th>
                                <th class="py-3 pr-4">Wilayah &amp; 3T</th>
                                <th class="py-3 pr-4">Sumber</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($schools as $school)
                                <tr class="align-top hover:bg-[#F8FAFC] {{ $selected && $selected->id === $school->id ? 'bg-[#FFF2ED]' : '' }}">
                                    <td class="py-3 pr-4 font-mono text-slate-600">
                                        <a href="{{ route('admin.schools.index', array_merge($filters, ['id' => $school->id])) }}" class="hover:text-[#FF5C2A]">{{ $school->npsn ?? '-' }}</a>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <a href="{{ route('admin.schools.index', array_merge($filters, ['id' => $school->id])) }}" class="font-bold text-[#14171F] hover:text-[#FF5C2A]">{{ $school->name }}</a>
                                    </td>
                                    <td class="py-3 pr-4 font-semibold">{{ $school->educational_level }}</td>
                                    <td class="py-3 pr-4">
                                        <div class="font-semibold text-slate-700">{{ $school->region_name }}</div>
                                        @if ($school->is_3t)
                                            <x-ui.badge variant="orange" size="sm">{{ $school->categories ? implode(' / ', array_map('ucfirst', $school->categories)) : '3T' }}</x-ui.badge>
                                        @else
                                            <span class="text-slate-400">Bukan 3T</span>
                                        @endif
                                    </td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $sourceLabels[$school->source] ?? $school->source }}</td>
                                    <td class="py-3">
                                        @if ($school->is_active)
                                            <x-ui.badge variant="success" size="sm">Aktif</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="neutral" size="sm">Nonaktif</x-ui.badge>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-400">Tidak ada sekolah yang cocok dengan filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $schools->links() }}
                </div>
            </div>

            {{-- Panel detail --}}
            <aside class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100 h-fit">
                @if ($selected)
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Detail Sekolah</p>
                    <h2 class="mt-1 text-lg font-extrabold text-[#14171F]">{{ $selected->name }}</h2>
                    <p class="text-xs text-slate-500 font-mono">NPSN {{ $selected->npsn ?? '-' }}</p>

                    <dl class="mt-5 space-y-3 text-xs">
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Jenjang</dt><dd class="font-semibold">{{ $selected->educational_level }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Wilayah</dt><dd class="font-semibold text-right">{{ $selected->region_name }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Klasifikasi 3T</dt><dd class="font-semibold text-right">{{ $selected->categories ? implode(' / ', array_map('ucfirst', $selected->categories)) : 'Bukan 3T' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Akreditasi</dt><dd class="font-semibold">{{ $selected->accreditation ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Alamat</dt><dd class="text-right">{{ $selected->address ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Telepon</dt><dd>{{ $selected->contact_phone ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Email</dt><dd class="break-all text-right">{{ $selected->contact_email ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Koordinat</dt><dd class="font-mono">{{ number_format($selected->lat, 5) }}, {{ number_format($selected->lng, 5) }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Sumber</dt><dd>{{ $sourceLabels[$selected->source] ?? $selected->source }}</dd></div>
                    </dl>

                    <a href="https://www.openstreetmap.org/?mlat={{ $selected->lat }}&amp;mlon={{ $selected->lng }}#map=17/{{ $selected->lat }}/{{ $selected->lng }}"
                       target="_blank" rel="noopener"
                       class="mt-5 inline-block text-xs font-semibold text-[#FF5C2A] hover:underline">Lihat di peta (OpenStreetMap)</a>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @if ($selected->is_active)
                            <x-ui.button variant="dark" size="sm" x-data x-on:click="$dispatch('open-modal', 'edit-school')">Edit Kontak &amp; Akreditasi</x-ui.button>
                            <form method="POST" action="{{ route('admin.schools.destroy', $selected->id) }}"
                                  onsubmit="return confirm('Nonaktifkan sekolah ini? Data tetap tersimpan dan bisa diaktifkan kembali.')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button variant="danger" size="sm" type="submit">Nonaktifkan</x-ui.button>
                            </form>
                        @else
                            <p class="text-xs text-slate-500">Sekolah ini nonaktif.</p>
                        @endif
                    </div>

                    {{-- Modal edit kontak --}}
                    @if ($selected->is_active)
                        <x-ui.modal name="edit-school" title="Edit Kontak &amp; Akreditasi" maxWidth="lg">
                            <form method="POST" action="{{ route('admin.schools.update', $selected->id) }}" class="space-y-4">
                                @csrf
                                @method('PUT')
                                <div class="flex flex-col gap-1.5">
                                    <label for="edit_accreditation" class="text-xs font-semibold text-slate-700">Akreditasi</label>
                                    <select id="edit_accreditation" name="accreditation" class="px-4 py-2.5 text-xs rounded-xl bg-[#F1F5F9] border border-transparent focus:border-[#FF5C2A] focus:outline-none">
                                        <option value="">Belum ada</option>
                                        @foreach (['A', 'B', 'C'] as $grade)
                                            <option value="{{ $grade }}" @selected($selected->accreditation === $grade)>{{ $grade }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-ui.input name="contact_phone" label="Telepon" :value="$selected->contact_phone" placeholder="Nomor telepon" />
                                <x-ui.input name="contact_email" label="Email" type="email" :value="$selected->contact_email" placeholder="email@sekolah.sch.id" :error="$errors->first('contact_email')" />
                                <div class="flex justify-end gap-2 pt-2">
                                    <x-ui.button variant="outline" size="sm" type="button" x-data x-on:click="$dispatch('close-modal', 'edit-school')">Batal</x-ui.button>
                                    <x-ui.button variant="primary" size="sm" type="submit">Simpan</x-ui.button>
                                </div>
                            </form>
                        </x-ui.modal>
                    @endif
                @else
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Detail Sekolah</p>
                    <p class="mt-3 text-xs text-slate-500">Pilih sekolah di tabel untuk melihat detail, mengubah kontak, atau menonaktifkannya.</p>
                @endif
            </aside>
        </div>
    </div>

    {{-- Modal tambah sekolah --}}
    <x-ui.modal name="create-school" title="Tambah Satuan Pendidikan" maxWidth="2xl">
        <form method="POST" action="{{ route('admin.schools.store') }}" x-data="schoolForm(@js(old()))" @submit="validate($event)" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @csrf
            <x-ui.input name="npsn" label="NPSN" x-model="values.npsn" placeholder="8 digit" :error="$errors->first('npsn')" />
            <x-ui.input name="name" label="Nama sekolah" x-model="values.name" placeholder="Nama resmi sekolah" :error="$errors->first('name')" />

            <div class="flex flex-col gap-1.5">
                <label for="create_level" class="text-xs font-semibold text-slate-700">Jenjang</label>
                <select id="create_level" name="educational_level" x-model="values.educational_level" class="px-4 py-2.5 text-xs rounded-xl bg-[#F1F5F9] border border-transparent focus:border-[#FF5C2A] focus:outline-none">
                    <option value="">Pilih jenjang</option>
                    @foreach ($levels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('educational_level')<span class="text-[11px] text-red-600">{{ $message }}</span>@enderror
            </div>

            <div class="flex flex-col gap-1.5">
                <label for="create_accreditation" class="text-xs font-semibold text-slate-700">Akreditasi</label>
                <select id="create_accreditation" name="accreditation" x-model="values.accreditation" class="px-4 py-2.5 text-xs rounded-xl bg-[#F1F5F9] border border-transparent focus:border-[#FF5C2A] focus:outline-none">
                    <option value="">Belum ada</option>
                    @foreach (['A', 'B', 'C'] as $grade)
                        <option value="{{ $grade }}">{{ $grade }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <x-ui.input name="address" label="Alamat" x-model="values.address" placeholder="Alamat lengkap" :error="$errors->first('address')" />
            </div>
            <x-ui.input name="lat" label="Lintang (lat)" x-model="values.lat" placeholder="-6.91060" :error="$errors->first('lat')" />
            <x-ui.input name="lng" label="Bujur (lng)" x-model="values.lng" placeholder="107.61830" :error="$errors->first('lng')" />
            <x-ui.input name="contact_phone" label="Telepon" x-model="values.contact_phone" placeholder="Nomor telepon" :error="$errors->first('contact_phone')" />
            <x-ui.input name="contact_email" label="Email" type="email" x-model="values.contact_email" placeholder="email@sekolah.sch.id" :error="$errors->first('contact_email')" />

            <p class="sm:col-span-2 text-[11px] text-slate-500">Wilayah sekolah ditentukan otomatis dari koordinat. Pastikan koordinat berada di dalam batas kabupaten/kota.</p>

            <div class="sm:col-span-2 flex justify-end gap-2 pt-2">
                <x-ui.button variant="outline" size="sm" type="button" x-data x-on:click="$dispatch('close-modal', 'create-school')">Batal</x-ui.button>
                <x-ui.button variant="primary" size="sm" type="submit">Simpan Sekolah</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- Buka kembali modal tambah bila validasi server gagal --}}
    @if ($errors->any() && old('npsn') !== null)
        <div x-data x-init="$nextTick(() => $dispatch('open-modal', 'create-school'))" class="hidden"></div>
    @endif
</x-layouts.app>
