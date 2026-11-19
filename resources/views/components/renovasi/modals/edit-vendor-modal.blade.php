@props([
    'regions' => collect(),
    'projects' => collect(),
])

<div x-data="{
        open: false,
        submitting: false,
        mode: 'edit',
        vendor: {
            id: '',
            company_name: '',
            entity_type: 'CV',
            license_num: '',
            nib: '',
            contact_phone: '',
            service_region_id: '',
            grade: 'K3',
            qualification_label: '',
            max_contract_limit: 2500000000,
            sbu_status: 'verified',
            sbu_valid_until: '',
            specialty: '',
            logistics_note: '',
            lead_engineer: '',
            engineer_role: '',
            rating: 4.8,
            is_active: true,
        },
        assignProjectId: '',
        openWithVendor(detail) {
            this.vendor = Object.assign({}, this.vendor, detail.vendor || {});
            this.mode = detail.mode || 'edit';
            this.assignProjectId = '';
            this.open = true;
        }
     }"
     x-show="open"
     @open-vendor-modal.window="openWithVendor($event.detail)"
     @keydown.escape.window="open = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-edit-vendor-title"
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
                    <div class="w-10 h-10 rounded-2xl bg-[#14171F] flex items-center justify-center text-white font-black text-xs shadow-xs"
                         x-text="vendor.company_name ? vendor.company_name.substring(0, 2).toUpperCase() : 'VK'"></div>
                    <div>
                        <h2 id="modal-edit-vendor-title" class="text-base font-black text-[#14171F]"
                            x-text="mode === 'assign' ? 'Tugaskan Kontraktor ke Proyek Renovasi' : 'Detail, Pembaruan SBU & Kontak Rekanan'"></h2>
                        <p class="text-xs text-slate-600 font-medium">
                            Lisensi: <strong class="font-mono text-slate-800" x-text="vendor.license_num"></strong> &bull;
                            <span x-text="vendor.company_name"></span>
                        </p>
                    </div>
                </div>
                <button type="button"
                        @click="open = false"
                        aria-label="Tutup dialog"
                        class="cursor-pointer w-8 h-8 rounded-full bg-white border border-slate-300 text-slate-600 hover:text-slate-900 flex items-center justify-center text-base font-bold shadow-2xs">
                    &times;
                </button>
            </div>

            <form :action="'{{ url('/renovasi/vendors') }}/' + vendor.id" method="POST" @submit="submitting = true">
                @csrf
                @method('PUT')

                <div class="p-6 space-y-4 max-h-[72vh] overflow-y-auto text-xs">

                    <!-- Penugasan Proyek Renovasi -->
                    <div class="p-4 rounded-2xl border" :class="mode === 'assign' ? 'bg-orange-50 border-orange-300 shadow-2xs' : 'bg-slate-50 border-slate-200'">
                        <label for="edit-vendor-assign-project" class="block font-bold text-slate-900 mb-1 flex items-center justify-between">
                            <span>Tugaskan Rekanan Ini ke Proyek Renovasi (Domain 3)</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-orange-200/80 text-orange-950 uppercase">Penugasan SPK</span>
                        </label>
                        <select id="edit-vendor-assign-project"
                                name="assign_project_id"
                                x-model="assignProjectId"
                                class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] bg-white py-2.5 px-3 text-slate-900">
                            <option value="">-- Tidak Mengubah Penugasan Proyek Saat Ini --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}">
                                    [{{ $proj->project_code }}] {{ $proj->school?->name ?? 'Sekolah' }} - {{ \Illuminate\Support\Str::limit($proj->title, 42) }} (Pelaksana: {{ $proj->vendor_name }})
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-600 mt-1">
                            Memilih proyek akan mengikat <code class="font-mono">vendor_id</code> proyek tersebut ke kontraktor ini.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                        <div class="sm:col-span-3">
                            <label for="edit-vendor-entity" class="block font-bold text-slate-800 mb-1.5">Bentuk Usaha</label>
                            <select id="edit-vendor-entity"
                                    name="entity_type"
                                    x-model="vendor.entity_type"
                                    class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option value="CV">CV</option>
                                <option value="PT">PT</option>
                                <option value="KSO">KSO</option>
                                <option value="BUMN / PT">BUMN / PT</option>
                            </select>
                        </div>

                        <div class="sm:col-span-9">
                            <label for="edit-vendor-name" class="block font-bold text-slate-800 mb-1.5">Nama Perusahaan Kontraktor <span class="text-rose-600">*</span></label>
                            <input id="edit-vendor-name"
                                   type="text"
                                   name="company_name"
                                   required
                                   x-model="vendor.company_name"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit-vendor-license" class="block font-bold text-slate-800 mb-1.5">Nomor SBU / Lisensi LPJK <span class="text-rose-600">*</span></label>
                            <input id="edit-vendor-license"
                                   type="text"
                                   name="license_num"
                                   required
                                   x-model="vendor.license_num"
                                   class="w-full font-mono font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="edit-vendor-nib" class="block font-bold text-slate-800 mb-1.5">Nomor Induk Berusaha (NIB)</label>
                            <input id="edit-vendor-nib"
                                   type="text"
                                   name="nib"
                                   x-model="vendor.nib"
                                   class="w-full font-mono font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit-vendor-region" class="block font-bold text-slate-800 mb-1.5">Wilayah Jangkauan (Master Wilayah) <span class="text-rose-600">*</span></label>
                            <select id="edit-vendor-region"
                                    name="service_region_id"
                                    required
                                    x-model="vendor.service_region_id"
                                    class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                @foreach($regions as $reg)
                                    <option value="{{ $reg->id }}">{{ $reg->name }} {{ $reg->is_3t_tier > 0 ? '(3T Tier-' . $reg->is_3t_tier . ')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="edit-vendor-phone" class="block font-bold text-slate-800 mb-1.5">Nomor Kontak Rekanan <span class="text-rose-600">*</span></label>
                            <input id="edit-vendor-phone"
                                   type="text"
                                   name="contact_phone"
                                   required
                                   x-model="vendor.contact_phone"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div>
                            <label for="edit-vendor-grade" class="block font-bold text-slate-800 mb-1.5">Grade LPJK</label>
                            <select id="edit-vendor-grade"
                                    name="grade"
                                    x-model="vendor.grade"
                                    class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option value="K1">K1</option>
                                <option value="K2">K2</option>
                                <option value="K3">K3</option>
                                <option value="M1">M1</option>
                                <option value="M2">M2</option>
                                <option value="B1">B1</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit-vendor-limit" class="block font-bold text-slate-800 mb-1.5">Pagu Maks (Rp)</label>
                            <input id="edit-vendor-limit"
                                   type="number"
                                   name="max_contract_limit"
                                   x-model="vendor.max_contract_limit"
                                   min="10000000"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="edit-vendor-sbu-status" class="block font-bold text-slate-800 mb-1.5">Status SBU</label>
                            <select id="edit-vendor-sbu-status"
                                    name="sbu_status"
                                    x-model="vendor.sbu_status"
                                    class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option value="verified">SBU Aktif Valid</option>
                                <option value="review">Perlu Perpanjangan</option>
                            </select>
                        </div>

                        <div>
                            <label for="edit-vendor-rating" class="block font-bold text-slate-800 mb-1.5">Rating (0-5)</label>
                            <input id="edit-vendor-rating"
                                   type="number"
                                   name="rating"
                                   step="0.05"
                                   min="0"
                                   max="5"
                                   x-model="vendor.rating"
                                   class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit-vendor-specialty" class="block font-bold text-slate-800 mb-1.5">Spesialisasi Teknis</label>
                            <input id="edit-vendor-specialty"
                                   type="text"
                                   name="specialty"
                                   x-model="vendor.specialty"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="edit-vendor-engineer" class="block font-bold text-slate-800 mb-1.5">Penanggung Jawab Teknis (PJBU)</label>
                            <input id="edit-vendor-engineer"
                                   type="text"
                                   name="lead_engineer"
                                   x-model="vendor.lead_engineer"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="edit-vendor-logistics" class="block font-bold text-slate-800 mb-1.5">Kapasitas Logistik Wilayah 3T</label>
                            <input id="edit-vendor-logistics"
                                   type="text"
                                   name="logistics_note"
                                   x-model="vendor.logistics_note"
                                   class="w-full font-medium rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                        </div>

                        <div>
                            <label for="edit-vendor-active" class="block font-bold text-slate-800 mb-1.5">Status Izin Operasional Rekanan</label>
                            <select id="edit-vendor-active"
                                    name="is_active"
                                    x-model="vendor.is_active"
                                    class="w-full font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 text-slate-900">
                                <option :value="true" value="1">Aktif (Diizinkan Menangani Proyek)</option>
                                <option :value="false" value="0">Blacklist / Tangguhkan Izin Rekanan</option>
                            </select>
                        </div>
                    </div>

                </div>

                <div class="p-4 px-6 border-t border-slate-200 bg-slate-50 flex items-center justify-between gap-3">
                    <button type="button"
                            @click="open = false"
                            class="cursor-pointer px-4 py-2 rounded-full text-xs font-bold text-slate-700 hover:bg-slate-200/70 transition-colors">
                        Tutup
                    </button>

                    <button type="submit"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : 'cursor-pointer'"
                            class="px-5 py-2.5 rounded-full text-xs font-bold bg-[#C83E13] hover:bg-[#A9320D] text-white shadow-xs transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Pembaruan Rekanan'"></span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
