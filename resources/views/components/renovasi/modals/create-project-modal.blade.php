@props([
    'verifiedReports' => collect(),
    'schools' => collect(),
    'categories' => collect(),
])

@php
    $hasValidationErrors = $errors->hasAny([
        'report_id', 'damage_report_id', 'school_id', 'facility_category_id',
        'title', 'priority', 'budget_allocated', 'contractor_name', 'consortium_name',
        'surveyor_name', 'target_start', 'target_end', 'description',
    ]);
    $defaultCategoryId = old('facility_category_id', $categories->first()?->id ?? '');
    $defaultSchoolId = old('school_id', $schools->first()?->id ?? '');
@endphp

<div x-data="{
        open: {{ $hasValidationErrors ? 'true' : 'false' }},
        selectedReportId: '{{ old('report_id', old('damage_report_id', '')) }}',
        projectTitle: @js(old('title', '')),
        selectedSchoolId: '{{ $defaultSchoolId }}',
        selectedCategoryId: '{{ $defaultCategoryId }}',
        selectedPriority: '{{ old('priority', 'high') }}',
        submitting: false,
        onReportChange(e) {
            const opt = e.target.options[e.target.selectedIndex];
            const title = opt.getAttribute('data-title');
            const schoolId = opt.getAttribute('data-school-id');
            const categoryId = opt.getAttribute('data-category-id');
            const severity = parseInt(opt.getAttribute('data-severity') || '3', 10);

            if (title) {
                this.projectTitle = 'Rehabilitasi ' + title;
            }
            if (schoolId) {
                this.selectedSchoolId = schoolId;
            }
            if (categoryId) {
                this.selectedCategoryId = categoryId;
            }
            if (severity >= 5) {
                this.selectedPriority = 'critical';
            } else if (severity === 4) {
                this.selectedPriority = 'high';
            } else if (severity === 3) {
                this.selectedPriority = 'medium';
            } else {
                this.selectedPriority = 'low';
            }
        }
     }"
     x-show="open"
     @open-modal.window="if ($event.detail === 'modal-tambah-proyek') open = true"
     @keydown.escape.window="open = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-create-project-title"
     class="relative z-50"
     style="display: none;">

    <div x-show="open"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/65 backdrop-blur-xs transition-opacity"
         @click="open = false"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto flex items-center justify-center p-4 sm:p-6">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden text-slate-800">

            <div class="p-5 px-6 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#C83E13] flex items-center justify-center text-white shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h2 id="modal-create-project-title" class="text-base font-black text-[#14171F]">Buka Proyek Renovasi Baru</h2>
                        <p class="text-xs text-slate-600 font-medium">Inisiasi proyek fisik dari Tiket Laporan Kerusakan Terverifikasi &amp; Master Sekolah</p>
                    </div>
                </div>
                <button type="button"
                        @click="open = false"
                        aria-label="Tutup dialog"
                        class="cursor-pointer w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-600 hover:text-slate-900 flex items-center justify-center text-base font-bold shadow-2xs focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                    &times;
                </button>
            </div>

            <form action="{{ route('renovasi.store') }}" method="POST" @submit="submitting = true">
                @csrf
                <div class="p-6 space-y-4 max-h-[74vh] overflow-y-auto">

                    <div class="p-4 bg-orange-50/70 rounded-2xl border border-orange-200">
                        <label for="create-report-id" class="text-xs font-bold text-orange-950 mb-1.5 flex items-center justify-between gap-2">
                            <span>Hubungkan ke Tiket Laporan Kerusakan Terverifikasi (PBI-04)</span>
                            <span class="text-[10px] bg-orange-200/80 text-orange-950 px-2 py-0.5 rounded-full font-extrabold uppercase">Master Tiket</span>
                        </label>
                        <select id="create-report-id"
                                name="report_id"
                                x-model="selectedReportId"
                                @change="onReportChange($event)"
                                class="w-full text-xs font-semibold rounded-xl border-orange-300 focus:border-[#C83E13] focus:ring-[#C83E13] bg-white py-2.5 px-3 text-slate-900">
                            <option value="">Buat Proyek Mandiri (Pilih Sekolah Secara Manual)</option>
                            @foreach($verifiedReports as $rep)
                                <option value="{{ $rep->id }}"
                                        data-title="{{ $rep->title }} ({{ $rep->school?->name ?? 'Sekolah' }})"
                                        data-school-id="{{ $rep->school_id }}"
                                        data-category-id="{{ $rep->category_id }}"
                                        data-severity="{{ $rep->severity_level }}">
                                    [{{ $rep->ticket_num }}] {{ $rep->school?->name ?? 'Sekolah' }} - {{ \Illuminate\Support\Str::limit($rep->title, 45) }} (Keparahan {{ $rep->severity_level }}/5)
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-orange-900 mt-1.5 font-medium">
                            Memilih tiket akan otomatis mengisi sekolah sasaran, taksonomi sarpras, dan mengubah status tiket menjadi <strong>Sedang Diperbaiki (In Progress)</strong>.
                        </p>
                    </div>

                    <div>
                        <label for="create-project-title" class="block text-xs font-bold text-slate-800 mb-1.5">
                            Nama / Lingkup Proyek Renovasi <span class="text-rose-600">*</span>
                        </label>
                        <input id="create-project-title"
                               type="text"
                               name="title"
                               x-model="projectTitle"
                               required
                               placeholder="Contoh: Rehabilitasi Rangka Atap & Plafon Ruang Kelas Utama"
                               class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        @error('title')
                            <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create-school-id" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Pilih Sekolah Sasaran (Master Data) <span class="text-rose-600">*</span>
                            </label>
                            <select id="create-school-id"
                                    name="school_id"
                                    x-model="selectedSchoolId"
                                    required
                                    class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                @foreach($schools as $sch)
                                    <option value="{{ $sch->id }}">
                                        {{ $sch->name }} ({{ $sch->educational_level ?? 'Sekolah' }} &bull; {{ $sch->region?->name ?? 'Wilayah' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('school_id')
                                <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="create-category-id" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Taksonomi Sarpras (Master Kategori)
                            </label>
                            <select id="create-category-id"
                                    name="facility_category_id"
                                    x-model="selectedCategoryId"
                                    class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">
                                        {{ $cat->name }}{{ $cat->sub_category ? ' - ' . $cat->sub_category : '' }} (Bobot {{ $cat->urgency_weight }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create-budget" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Pagu Alokasi Anggaran RAB (Rp) <span class="text-rose-600">*</span>
                            </label>
                            <input id="create-budget"
                                   type="number"
                                   name="budget_allocated"
                                   required
                                   min="1000000"
                                   step="500000"
                                   value="{{ old('budget_allocated', '') }}"
                                   placeholder="Contoh: 245000000"
                                   class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                            <span class="text-[11px] text-slate-600 mt-1 block">Otomatis dibagi ke 4 komponen RAB awal &amp; 4 milestone (20%, 35%, 25%, 20%).</span>
                            @error('budget_allocated')
                                <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="create-priority" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Tingkat Prioritas Intervensi
                            </label>
                            <select id="create-priority"
                                    name="priority"
                                    x-model="selectedPriority"
                                    class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option value="critical">Kritis (Darurat Keselamatan Siswa)</option>
                                <option value="high">Tinggi (Kerusakan Berat Struktur/Ruang)</option>
                                <option value="medium">Sedang (Rehabilitasi Fasilitas Penunjang)</option>
                                <option value="low">Rendah (Pemeliharaan Terjadwal)</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create-contractor" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Kontraktor Pelaksana / Rekanan LPJK
                            </label>
                            <input id="create-contractor"
                                   type="text"
                                   name="contractor_name"
                                   value="{{ old('contractor_name', '') }}"
                                   placeholder="Contoh: CV Karya Bangun Nusantara (LPJK)"
                                   class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="create-surveyor" class="block text-xs font-bold text-slate-800 mb-1.5">
                                Pengawas Teknis / Verifikator Lapangan
                            </label>
                            <input id="create-surveyor"
                                   type="text"
                                   name="surveyor_name"
                                   value="{{ old('surveyor_name', '') }}"
                                   placeholder="Contoh: Tim Teknis Sarpras Dinas Pendidikan"
                                   class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="create-start-date" class="block text-xs font-bold text-slate-800 mb-1.5">Tanggal Mulai SPK</label>
                            <input id="create-start-date"
                                   type="date"
                                   name="target_start"
                                   value="{{ old('target_start', date('Y-m-d')) }}"
                                   class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="create-end-date" class="block text-xs font-bold text-slate-800 mb-1.5">Target Selesai Fisik (BAST)</label>
                            <input id="create-end-date"
                                   type="date"
                                   name="target_end"
                                   value="{{ old('target_end', date('Y-m-d', strtotime('+120 days'))) }}"
                                   class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div>
                        <label for="create-description" class="block text-xs font-bold text-slate-800 mb-1.5">Catatan Spesifikasi Teknis / Uraian Pekerjaan</label>
                        <textarea id="create-description"
                                  name="description"
                                  rows="2"
                                  placeholder="Jelaskan lingkup teknis konstruksi, spesifikasi material SNI, dan sasaran perbaikan ruang."
                                  class="w-full text-xs font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2 px-3 text-slate-900">{{ old('description', '') }}</textarea>
                    </div>

                    <div class="p-3.5 bg-blue-50 rounded-2xl border border-blue-200 text-xs text-blue-900 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-blue-700 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="leading-relaxed">
                            Sistem akan otomatis membuat <strong>4 tahapan milestone fisik (MS-01 s/d MS-04)</strong> beserta <strong>rincian komponen RAB awal</strong> yang langsung terhubung dengan layanan pencairan dana.
                        </p>
                    </div>
                </div>

                <div class="p-4 px-6 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                    <button type="button"
                            @click="open = false"
                            class="cursor-pointer px-4 py-2 rounded-full text-xs font-bold text-slate-700 hover:bg-slate-200/70 transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'"
                            class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#C83E13] hover:bg-[#A9320D] text-white shadow-xs transition-all flex items-center gap-1.5 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="submitting ? 'Memproses Pembukaan Proyek...' : 'Buka Proyek &amp; Rencana Milestone'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
