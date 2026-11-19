@php
    use App\Models\FacilityCategory;

    $statusOptions = ['aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'semua' => 'Semua status'];
    $maxByWeight = max(1, max($stats['by_weight']));
    $formName = old('_form');
@endphp

<x-layouts.app active="master-data" title="Kategori Fasilitas - EduInfra Tracker">
    <div class="max-w-[1520px] mx-auto px-4 lg:px-6 pb-10">

        {{-- Header --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Master Wilayah dan Kelembagaan Pendidikan</p>
                <h1 class="text-xl font-extrabold text-[#14171F]">Kategori Fasilitas &amp; Bobot Urgensi</h1>
                <p class="mt-1 text-xs text-slate-500">Taksonomi fasilitas yang dipilih pelapor. Bobot urgensi 1-5 dipakai untuk menghitung skor prioritas sekolah.</p>
            </div>
            <x-ui.button variant="primary" size="md" x-data x-on:click="$dispatch('open-modal', 'create-category')">
                + Tambah Kategori
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
            <x-ui.kpi-card title="Kategori Aktif" :value="number_format($stats['enabled'])" unit="sub-kategori" icon="building" subText="Dapat dipilih pelapor" />
            <x-ui.kpi-card title="Urgensi Tinggi" :value="number_format($stats['high'])" unit="kategori" variant="urgent" icon="alert" subText="Bobot 4-5 (Tinggi/Kritis)" />
            <x-ui.kpi-card title="Kategori Utama" :value="number_format($stats['groups'])" unit="kelompok" icon="link" subText="Ruang kelas, sanitasi, dll." />
            <x-ui.kpi-card title="Nonaktif" :value="number_format($stats['disabled'])" unit="kategori" icon="hourglass" subText="Disembunyikan dari formulir" />
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Tabel kategori --}}
            <div class="lg:col-span-2 bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap gap-2">
                    <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kategori atau sub-kategori" aria-label="Cari kategori"
                           class="flex-1 min-w-[180px] px-4 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70 focus:border-[#FF5C2A] focus:bg-white focus:outline-none">
                    <select name="name" aria-label="Kategori utama" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
                        <option value="">Semua kategori utama</option>
                        @foreach ($groupNames as $group)
                            <option value="{{ $group }}" @selected(($filters['name'] ?? '') === $group)>{{ $group }}</option>
                        @endforeach
                    </select>
                    <select name="weight" aria-label="Bobot urgensi" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
                        <option value="">Semua bobot</option>
                        @foreach (FacilityCategory::URGENCY_LABELS as $weight => $label)
                            <option value="{{ $weight }}" @selected((string) ($filters['weight'] ?? '') === (string) $weight)>{{ $weight }} - {{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="status" aria-label="Status" class="px-3 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70">
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
                                <th class="py-3 pr-4">Kategori</th>
                                <th class="py-3 pr-4">Sub-kategori</th>
                                <th class="py-3 pr-4">Bobot Urgensi</th>
                                <th class="py-3 pr-4">Satuan</th>
                                <th class="py-3 pr-4 text-right">Laporan</th>
                                <th class="py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($categories as $category)
                                @php $link = route('admin.categories.index', array_merge($filters, ['id' => $category->id, 'page' => $categories->currentPage()])); @endphp
                                <tr class="align-middle hover:bg-[#F8FAFC] {{ $selected && $selected->id === $category->id ? 'bg-[#FFF2ED]' : '' }}">
                                    <td class="py-3 pr-4">
                                        <a href="{{ $link }}" class="font-bold text-[#14171F] hover:text-[#FF5C2A]">{{ $category->name }}</a>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <a href="{{ $link }}" class="text-slate-700 hover:text-[#FF5C2A]">{{ $category->sub_category !== '' ? $category->sub_category : '-' }}</a>
                                    </td>
                                    <td class="py-3 pr-4"><x-ui.urgency-badge :weight="$category->urgency_weight" /></td>
                                    <td class="py-3 pr-4 text-slate-500">{{ $category->default_unit ?? '-' }}</td>
                                    <td class="py-3 pr-4 text-right font-semibold text-slate-700">{{ number_format($category->damage_reports_count) }}</td>
                                    <td class="py-3">
                                        @if ($category->is_enabled)
                                            <x-ui.badge variant="success" size="sm">Aktif</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="neutral" size="sm">Nonaktif</x-ui.badge>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-400">
                                        Tidak ada kategori yang cocok dengan filter.
                                        @if ($stats['enabled'] + $stats['disabled'] === 0)
                                            <span class="block mt-1">Belum ada kategori. Tambahkan kategori baru atau jalankan <code class="font-mono">php artisan db:seed --class=FacilityCategorySeeder</code>.</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $categories->links() }}
                </div>
            </div>

            {{-- Panel kanan: detail kategori terpilih atau sebaran bobot --}}
            <aside class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100 h-fit">
                @if ($selected)
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Detail Kategori</p>
                    <h2 class="mt-1 text-lg font-extrabold text-[#14171F]">{{ $selected->name }}</h2>
                    <p class="text-xs text-slate-500">{{ $selected->sub_category !== '' ? $selected->sub_category : 'Tanpa sub-kategori' }}</p>

                    <dl class="mt-5 space-y-3 text-xs">
                        <div class="flex justify-between items-center gap-3"><dt class="text-slate-500">Bobot urgensi</dt><dd><x-ui.urgency-badge :weight="$selected->urgency_weight" /></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Satuan bawaan</dt><dd class="font-semibold">{{ $selected->default_unit ?? '-' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Laporan kerusakan</dt><dd class="font-semibold">{{ number_format($selected->damage_reports_count) }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Status</dt><dd class="font-semibold">{{ $selected->is_enabled ? 'Aktif' : 'Nonaktif' }}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-500">Terakhir diubah</dt><dd>{{ $selected->updated_at?->timezone(config('app.timezone'))->format('d M Y H:i') ?? '-' }}</dd></div>
                    </dl>

                    <div class="mt-6 flex flex-wrap gap-2">
                        @if ($selected->is_enabled)
                            <x-ui.button variant="dark" size="sm" x-data x-on:click="$dispatch('open-modal', 'edit-category')">Ubah Bobot &amp; Satuan</x-ui.button>
                            <form method="POST" action="{{ route('admin.categories.destroy', $selected->id) }}"
                                  onsubmit="return confirm('Nonaktifkan kategori ini? Kategori tidak lagi muncul di formulir laporan, tetapi laporan lama tetap tersimpan.')">
                                @csrf
                                @method('DELETE')
                                <x-ui.button variant="danger" size="sm" type="submit">Nonaktifkan</x-ui.button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.categories.activate', $selected->id) }}">
                                @csrf
                                @method('PATCH')
                                <x-ui.button variant="primary" size="sm" type="submit">Aktifkan Kembali</x-ui.button>
                            </form>
                        @endif
                    </div>
                    <p class="mt-3 text-[11px] text-slate-500">Nama dan sub-kategori tidak dapat diubah karena sudah dirujuk laporan. Buat kategori baru bila perlu nama lain.</p>

                    {{-- Modal ubah bobot urgensi --}}
                    @if ($selected->is_enabled)
                        <x-ui.modal name="edit-category" title="Ubah Bobot Urgensi" maxWidth="lg">
                            <form method="POST" action="{{ route('admin.categories.update', $selected->id) }}" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="_form" value="edit">
                                <p class="text-xs text-slate-600"><span class="font-bold text-[#14171F]">{{ $selected->full_name }}</span></p>

                                <x-ui.urgency-picker id="edit_urgency_weight"
                                    :selected="$formName === 'edit' ? old('urgency_weight') : $selected->urgency_weight"
                                    :error="$formName === 'edit' ? $errors->first('urgency_weight') : null" />

                                <div class="flex flex-col gap-1.5">
                                    <label for="edit_default_unit" class="text-xs font-semibold text-slate-700">Satuan bawaan</label>
                                    <input type="text" id="edit_default_unit" name="default_unit" maxlength="30"
                                           value="{{ $formName === 'edit' ? old('default_unit') : $selected->default_unit }}" placeholder="ruang, unit, titik, paket"
                                           class="w-full px-4 py-2.5 text-xs font-medium rounded-full bg-[#F1F5F9] border border-transparent focus:border-[#FF5C2A] focus:bg-white focus:outline-none">
                                    @if ($formName === 'edit' && $errors->has('default_unit'))
                                        <span class="text-[11px] text-red-600">{{ $errors->first('default_unit') }}</span>
                                    @endif
                                </div>

                                <div class="flex justify-end gap-2 pt-2">
                                    <x-ui.button variant="outline" size="sm" type="button" x-data x-on:click="$dispatch('close-modal', 'edit-category')">Batal</x-ui.button>
                                    <x-ui.button variant="primary" size="sm" type="submit">Simpan</x-ui.button>
                                </div>
                            </form>
                        </x-ui.modal>
                    @endif
                @else
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Sebaran Bobot Urgensi</p>
                    <p class="mt-1 text-xs text-slate-500">Jumlah kategori aktif per bobot.</p>

                    <ul class="mt-5 grid grid-cols-[auto_1fr_auto] items-center gap-x-3 gap-y-3 text-xs">
                        @foreach (array_reverse($stats['by_weight'], true) as $weight => $total)
                            <li class="contents">
                                <span><x-ui.urgency-badge :weight="$weight" /></span>
                                <span class="h-2 min-w-[40px] rounded-full bg-[#F1F5F9] overflow-hidden">
                                    <span class="block h-full rounded-full {{ $weight >= FacilityCategory::HIGH_WEIGHT ? 'bg-[#FF5C2A]' : 'bg-[#14171F]' }}" style="width: {{ round($total / $maxByWeight * 100) }}%"></span>
                                </span>
                                <span class="w-6 text-right font-bold text-slate-700">{{ $total }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <p class="mt-6 text-xs text-slate-500">Pilih kategori di tabel untuk mengubah bobot urgensi, satuan, atau menonaktifkannya.</p>
                @endif
            </aside>
        </div>
    </div>

    {{-- Modal tambah kategori --}}
    <x-ui.modal name="create-category" title="Tambah Kategori Fasilitas" maxWidth="lg">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="_form" value="create">

            <x-ui.input name="name" label="Kategori utama" list="category-group-names" maxlength="100"
                        :value="$formName === 'create' ? old('name') : ''" placeholder="Contoh: Ruang Kelas, Sanitasi"
                        :error="$formName === 'create' ? $errors->first('name') : null" />
            <datalist id="category-group-names">
                @foreach ($groupNames as $group)
                    <option value="{{ $group }}"></option>
                @endforeach
            </datalist>

            <x-ui.input name="sub_category" label="Sub-kategori (opsional)" maxlength="100"
                        :value="$formName === 'create' ? old('sub_category') : ''" placeholder="Contoh: Struktur dan Atap"
                        :error="$formName === 'create' ? $errors->first('sub_category') : null" />

            <x-ui.urgency-picker id="create_urgency_weight"
                :selected="$formName === 'create' ? old('urgency_weight') : null"
                :error="$formName === 'create' ? $errors->first('urgency_weight') : null" />

            <x-ui.input name="default_unit" label="Satuan bawaan (opsional)" maxlength="30"
                        :value="$formName === 'create' ? old('default_unit') : ''" placeholder="ruang, unit, titik, paket"
                        :error="$formName === 'create' ? $errors->first('default_unit') : null" />

            <div class="flex justify-end gap-2 pt-2">
                <x-ui.button variant="outline" size="sm" type="button" x-data x-on:click="$dispatch('close-modal', 'create-category')">Batal</x-ui.button>
                <x-ui.button variant="primary" size="sm" type="submit">Simpan Kategori</x-ui.button>
            </div>
        </form>
    </x-ui.modal>

    {{-- Buka kembali modal yang gagal divalidasi server --}}
    @if ($errors->any() && in_array($formName, ['create', 'edit'], true))
        <div x-data x-init="$nextTick(() => $dispatch('open-modal', '{{ $formName }}-category'))" class="hidden"></div>
    @endif
</x-layouts.app>
