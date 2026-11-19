@props([
    'regions' => collect(),
    'projects' => collect(),
])

@php
    $hasVendorCreateErrors = $errors->hasAny([
        'company_name', 'license_num', 'contact_phone', 'service_region_id',
        'grade', 'max_contract_limit', 'rating',
    ]) && old('_form_context') === 'create_vendor';
    $defaultRegionId = old('service_region_id', $regions->first()?->id ?? '');
@endphp

<div x-data="{
        open: {{ $hasVendorCreateErrors ? 'true' : 'false' }},
        submitting: false,
        entityType: '{{ old('entity_type', 'CV') }}',
        grade: '{{ old('grade', 'K3') }}',
        maxLimit: '{{ old('max_contract_limit', '2500000000') }}',
        qualificationLabel: @js(old('qualification_label', 'Kecil - Bangunan Gedung Pendidikan')),
        onGradeChange() {
            const presets = {
                'K1': { limit: '1000000000', label: 'Kecil - Pekerjaan Kayu & Pasangan Bata' },
                'K2': { limit: '1500000000', label: 'Kecil - Renovasi Rangka & Atap Sekolah' },
                'K3': { limit: '2500000000', label: 'Kecil - Bangunan Gedung Pendidikan' },
                'M1': { limit: '10000000000', label: 'Menengah - Konstruksi Fisik & Sanitasi' },
                'M2': { limit: '25000000000', label: 'Menengah - Konstruksi Terpadu Wilayah 3T' },
                'B1': { limit: '50000000000', label: 'Besar & Pengawas Teknis Konstruksi Nasional' }
            };
            if (presets[this.grade]) {
                this.maxLimit = presets[this.grade].limit;
                this.qualificationLabel = presets[this.grade].label;
            }
        }
     }"
     x-show="open"
     @open-modal.window="if ($event.detail === 'modal-tambah-vendor') open = true"
     @keydown.escape.window="open = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-create-vendor-title"
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
                        <h2 id="modal-create-vendor-title" class="text-base font-black text-[#14171F]">Registrasi Kontraktor Rekanan LPJK Baru (PBI-09)</h2>
                        <p class="text-xs text-slate-600 font-medium">Pendaftaran badan usaha konstruksi terverifikasi SBU dan wilayah jangkauan 3T</p>
                    </div>
                </div>
                <button type="button"
                        @click="open = false"
                        aria-label="Tutup dialog"
                        class="cursor-pointer w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-600 hover:text-slate-900 flex items-center justify-center text-base font-bold shadow-2xs">
                    &times;
                </button>
            </div>

            <form action="{{ route('renovasi.vendors.store') }}" method="POST" @submit="submitting = true">
                @csrf
                <input type="hidden" name="_form_context" value="create_vendor">

                <div class="p-6 space-y-4 max-h-[74vh] overflow-y-auto text-xs">

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-3">
                            <label for="vendor-entity-type" class="block font-bold text-slate-800 mb-1.5">Bentuk Usaha</label>
                            <select id="vendor-entity-type"
                                    name="entity_type"
                                    x-model="entityType"
                                    class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option value="CV">CV</option>
                                <option value="PT">PT</option>
                                <option value="KSO">KSO</option>
                                <option value="BUMN / PT">BUMN / PT</option>
                            </select>
                        </div>

                        <div class="sm:col-span-9">
                            <label for="vendor-company-name" class="block font-bold text-slate-800 mb-1.5">
                                Nama Perusahaan Kontraktor <span class="text-rose-600">*</span>
                            </label>
                            <input id="vendor-company-name"
                                   type="text"
                                   name="company_name"
                                   required
                                   value="{{ old('company_name', '') }}"
                                   placeholder="Contoh: CV Mahakam Konstruksi Mandiri"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                            @error('company_name')
                                <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="vendor-license-num" class="block font-bold text-slate-800 mb-1.5">
                                Nomor Sertifikat SBU / Lisensi LPJK <span class="text-rose-600">*</span>
                            </label>
                            <input id="vendor-license-num"
                                   type="text"
                                   name="license_num"
                                   required
                                   value="{{ old('license_num', '') }}"
                                   placeholder="Contoh: LPJK-8821-NTT"
                                   class="w-full font-mono font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                            @error('license_num')
                                <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="vendor-nib" class="block font-bold text-slate-800 mb-1.5">
                                Nomor Induk Berusaha (NIB OSS)
                            </label>
                            <input id="vendor-nib"
                                   type="text"
                                   name="nib"
                                   value="{{ old('nib', '') }}"
                                   placeholder="Contoh: 9120401837492"
                                   class="w-full font-mono font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="vendor-region-id" class="block font-bold text-slate-800 mb-1.5">
                                Wilayah Jangkauan Utama (Master Data) <span class="text-rose-600">*</span>
                            </label>
                            <select id="vendor-region-id"
                                    name="service_region_id"
                                    required
                                    class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                @foreach($regions as $reg)
                                    <option value="{{ $reg->id }}" @selected((string) $defaultRegionId === (string) $reg->id)>
                                        {{ $reg->name }} {{ $reg->is_3t_tier > 0 ? '(3T Tier-' . $reg->is_3t_tier . ')' : '(Reguler)' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('service_region_id')
                                <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="vendor-contact-phone" class="block font-bold text-slate-800 mb-1.5">
                                Nomor Kontak Resmi Rekanan <span class="text-rose-600">*</span>
                            </label>
                            <input id="vendor-contact-phone"
                                   type="text"
                                   name="contact_phone"
                                   required
                                   value="{{ old('contact_phone', '') }}"
                                   placeholder="Contoh: 0812-3940-8812"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                            @error('contact_phone')
                                <p class="text-[11px] text-rose-700 font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label for="vendor-grade" class="block font-bold text-slate-800 mb-1.5">Kualifikasi Grade LPJK</label>
                            <select id="vendor-grade"
                                    name="grade"
                                    x-model="grade"
                                    @change="onGradeChange()"
                                    class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option value="K1">K1 (Kecil - Rp 1 M)</option>
                                <option value="K2">K2 (Kecil - Rp 1,5 M)</option>
                                <option value="K3">K3 (Kecil - Rp 2,5 M)</option>
                                <option value="M1">M1 (Menengah - Rp 10 M)</option>
                                <option value="M2">M2 (Menengah - Rp 25 M)</option>
                                <option value="B1">B1 (Besar - &gt;Rp 50 M)</option>
                            </select>
                        </div>

                        <div>
                            <label for="vendor-max-limit" class="block font-bold text-slate-800 mb-1.5">Batas Pagu Kontrak (Rp)</label>
                            <input id="vendor-max-limit"
                                   type="number"
                                   name="max_contract_limit"
                                   x-model="maxLimit"
                                   min="10000000"
                                   step="50000000"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="vendor-rating" class="block font-bold text-slate-800 mb-1.5">Rating Awal (0.0 - 5.0)</label>
                            <input id="vendor-rating"
                                   type="number"
                                   name="rating"
                                   step="0.05"
                                   min="0"
                                   max="5"
                                   value="{{ old('rating', '4.80') }}"
                                   class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="vendor-specialty" class="block font-bold text-slate-800 mb-1.5">Spesialisasi Teknis Konstruksi</label>
                            <input id="vendor-specialty"
                                   type="text"
                                   name="specialty"
                                   value="{{ old('specialty', '') }}"
                                   placeholder="Contoh: Spesialis Atap Gempa & Sanitasi SNI"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="vendor-lead-engineer" class="block font-bold text-slate-800 mb-1.5">Penanggung Jawab Teknis (PJBU / SKA)</label>
                            <input id="vendor-lead-engineer"
                                   type="text"
                                   name="lead_engineer"
                                   value="{{ old('lead_engineer', '') }}"
                                   placeholder="Contoh: Ir. Ahmad Fauzi, ST (SKA Madya Gedung)"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="vendor-logistics" class="block font-bold text-slate-800 mb-1.5">Armada &amp; Kapasitas Logistik 3T</label>
                            <input id="vendor-logistics"
                                   type="text"
                                   name="logistics_note"
                                   value="{{ old('logistics_note', '') }}"
                                   placeholder="Contoh: Armada Truk 4WD & Kapal Pengangkut Material"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="vendor-sbu-valid" class="block font-bold text-slate-800 mb-1.5">Masa Berlaku SBU LPJK</label>
                            <input id="vendor-sbu-valid"
                                   type="date"
                                   name="sbu_valid_until"
                                   value="{{ old('sbu_valid_until', date('Y-m-d', strtotime('+2 years'))) }}"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    @if($projects->isNotEmpty())
                    <div class="p-3.5 bg-orange-50/70 rounded-2xl border border-orange-200">
                        <label for="vendor-assign-project" class="block font-bold text-orange-950 mb-1">
                            Langsung Tugaskan ke Proyek Renovasi Aktif (Opsional)
                        </label>
                        <select id="vendor-assign-project"
                                name="assign_project_id"
                                class="w-full font-medium rounded-xl border-orange-300 focus:border-[#C83E13] focus:ring-[#C83E13] bg-white py-2 px-3 text-slate-900">
                            <option value="">-- Hanya Simpan ke Direktori Rekanan --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">
                                    [{{ $proj->project_code }}] {{ $proj->school?->name ?? 'Sekolah' }} - {{ \Illuminate\Support\Str::limit($proj->title, 45) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                </div>

                <div class="p-4 px-6 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                    <button type="button"
                            @click="open = false"
                            class="cursor-pointer px-4 py-2 rounded-full text-xs font-bold text-slate-700 hover:bg-slate-200/70 transition-colors">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'"
                            class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#C83E13] hover:bg-[#A9320D] text-white shadow-xs transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="submitting ? 'Menyimpan Data Rekanan...' : 'Registrasikan Rekanan LPJK'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
