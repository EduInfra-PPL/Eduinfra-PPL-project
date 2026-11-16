@props([
    'project'
])

@php
    $milestonesList = $project instanceof \App\Models\RepairProject
        ? $project->milestones->sortBy('milestone_code')->values()
        : collect();

    $costEstimatesList = $project instanceof \App\Models\RepairProject
        ? $project->costEstimates
        : collect();

    $defaultMilestone = $milestonesList->firstWhere('status', 'in_progress')
        ?? $milestonesList->firstWhere('status', 'delayed')
        ?? $milestonesList->firstWhere('status', 'pending')
        ?? $milestonesList->first();

    $milestonesPayload = $milestonesList->mapWithKeys(function ($m) use ($project) {
        return [
            (string) $m->id => [
                'id' => $m->id,
                'code' => $m->milestone_code ?: ('MS-0' . $m->id),
                'title' => $m->title,
                'weight_pct' => (float) $m->weight_pct,
                'progress_pct' => (float) $m->progress_pct,
                'status' => $m->status ?: 'in_progress',
                'surveyor_name' => $m->surveyor_name ?: ($project->surveyor_name ?: 'Tim Teknis Pengawas Dinas'),
                'notes' => $m->notes ?: ($m->description ?: ''),
                'due_date' => $m->due_date ? $m->due_date->format('Y-m-d') : date('Y-m-d'),
                'proof_media_url' => $m->proof_media_url,
            ],
        ];
    });

    $firstId = $defaultMilestone ? (string) $defaultMilestone->id : '1';
    $firstData = $milestonesPayload->get($firstId, [
        'progress_pct' => 0,
        'status' => 'in_progress',
        'surveyor_name' => $project->surveyor_name ?? 'Tim Teknis Pengawas Dinas',
        'notes' => '',
        'due_date' => date('Y-m-d'),
        'proof_media_url' => null,
    ]);
@endphp

<div x-data="{
        open: false,
        submitting: false,
        milestonesMap: @js($milestonesPayload),
        activeMilestoneId: '{{ $firstId }}',
        progressVal: {{ (float) ($firstData['progress_pct'] ?? 0) }},
        statusVal: '{{ $firstData['status'] ?? 'in_progress' }}',
        assessmentDate: '{{ date('Y-m-d') }}',
        surveyor: @js($firstData['surveyor_name'] ?? ''),
        notes: @js($firstData['notes'] ?? ''),
        selectedFileName: '',
        syncMilestoneFields() {
            const current = this.milestonesMap[String(this.activeMilestoneId)];
            if (!current) return;
            this.progressVal = current.progress_pct;
            this.statusVal = current.status;
            this.surveyor = current.surveyor_name;
            this.notes = current.notes;
        },
        onSliderInput() {
            if (Number(this.progressVal) >= 100) {
                this.statusVal = 'completed';
            } else if (Number(this.progressVal) === 0 && this.statusVal === 'completed') {
                this.statusVal = 'pending';
            } else if (this.statusVal === 'completed' || this.statusVal === 'pending') {
                this.statusVal = 'in_progress';
            }
        }
     }"
     x-show="open"
     @open-modal.window="if ($event.detail === 'modal-update-milestone') open = true"
     @keydown.escape.window="open = false"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modal-update-milestone-title"
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

    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-8 flex items-center justify-center">
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-4xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col max-h-[92vh]">

            <form :action="'{{ url('/renovasi/milestone') }}/' + activeMilestoneId"
                  method="POST"
                  enctype="multipart/form-data"
                  @submit="submitting = true"
                  class="flex flex-col flex-1 overflow-hidden">
                @csrf
                <input type="hidden" name="sync_estimates" value="1">

                <div class="p-6 pb-4 border-b border-slate-200 flex items-start justify-between gap-4 bg-slate-50">
                    <div class="flex items-start gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-[#C83E13] text-white flex items-center justify-center shadow-xs shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h2 id="modal-update-milestone-title" class="text-lg font-black text-[#14171F]">Pembaruan Progres Milestone &amp; Checklist RAB</h2>
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200">
                                    NPSN {{ $project->npsn ?? '-' }} &bull; {{ $project->zonasi ?? 'Wilayah Pendidikan' }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-600 font-medium">
                                Proyek: <strong class="text-slate-800">{{ $project->project_code ?? '#PRI' }}</strong> &bull; {{ $project->school_name ?? 'Sekolah' }}
                            </p>
                        </div>
                    </div>

                    <button type="button"
                            @click="open = false"
                            aria-label="Tutup dialog"
                            class="cursor-pointer w-8 h-8 rounded-full bg-white border border-slate-300 hover:bg-slate-100 text-slate-600 flex items-center justify-center transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-slate-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto grid grid-cols-1 md:grid-cols-12 gap-6 text-xs flex-1">

                    <!-- LEFT COLUMN: MILESTONE PROGRESS (7 cols) -->
                    <div class="md:col-span-7 flex flex-col gap-4">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <h3 class="font-extrabold uppercase tracking-wider text-[11px] text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-[#C83E13]"></span>
                                Bagian 1: Pilih Tahapan &amp; Input Realisasi Fisik
                            </h3>
                        </div>

                        <div>
                            <label for="select-milestone-id" class="block text-[11px] font-bold text-slate-800 mb-1.5">Pilih Tahapan / Milestone yang Diperbarui:</label>
                            <select id="select-milestone-id"
                                    x-model="activeMilestoneId"
                                    @change="syncMilestoneFields()"
                                    class="w-full text-xs font-semibold rounded-xl border-slate-300 focus:border-[#C83E13] focus:ring-[#C83E13] py-2.5 px-3 bg-slate-50 text-slate-900">
                                @foreach($milestonesList as $m)
                                    <option value="{{ $m->id }}">
                                        [{{ $m->milestone_code }}] {{ $m->title }} (Bobot: {{ round($m->weight_pct) }}% &bull; Realisasi: {{ round($m->progress_pct) }}%)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            <div class="flex items-center justify-between mb-2">
                                <label for="slider-progress-pct" class="font-bold text-slate-800">Input Kemajuan Fisik Tahapan Ini:</label>
                                <span class="text-sm font-black text-emerald-800 bg-white px-2.5 py-0.5 rounded-full border border-emerald-200 shadow-2xs">
                                    <span x-text="progressVal"></span>%
                                    <span class="text-[10px] text-emerald-800 font-bold ml-1" x-text="Number(progressVal) >= 100 ? 'Tahap Tuntas' : 'Berjalan'"></span>
                                </span>
                            </div>
                            <input id="slider-progress-pct"
                                   type="range"
                                   min="0"
                                   max="100"
                                   step="1"
                                   name="progress_pct"
                                   x-model="progressVal"
                                   @input="onSliderInput()"
                                   class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-[#C83E13]">
                            <div class="flex justify-between text-[10px] text-slate-600 font-semibold mt-1">
                                <span>0% (Belum Mulai)</span>
                                <span>50% (Konstruksi)</span>
                                <span>100% (Tuntas)</span>
                            </div>
                        </div>

                        <fieldset>
                            <legend class="block text-[11px] font-bold text-slate-800 mb-1.5">Status Tahapan:</legend>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-300 bg-white text-[11px] font-bold text-slate-800 cursor-pointer">
                                    <input type="radio" name="status" value="in_progress" x-model="statusVal" class="text-[#C83E13] focus:ring-[#C83E13]">
                                    <span>Berjalan</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-emerald-300 bg-emerald-50/70 text-[11px] font-bold text-emerald-900 cursor-pointer">
                                    <input type="radio" name="status" value="completed" x-model="statusVal" @change="progressVal = 100" class="text-emerald-700 focus:ring-emerald-600">
                                    <span>Selesai (100%)</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-rose-300 bg-rose-50/70 text-[11px] font-bold text-rose-900 cursor-pointer">
                                    <input type="radio" name="status" value="delayed" x-model="statusVal" class="text-rose-700 focus:ring-rose-600">
                                    <span>Tertunda</span>
                                </label>
                            </div>
                        </fieldset>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="update-assessment-date" class="block text-[11px] font-bold text-slate-800 mb-1">Tanggal Asesmen Lapangan:</label>
                                <input id="update-assessment-date"
                                       type="date"
                                       name="assessment_date"
                                       x-model="assessmentDate"
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-hidden focus:border-[#C83E13]">
                            </div>
                            <div>
                                <label for="update-surveyor" class="block text-[11px] font-bold text-slate-800 mb-1">Verifikator / Konsultan Pengawas:</label>
                                <input id="update-surveyor"
                                       type="text"
                                       name="surveyor_name"
                                       x-model="surveyor"
                                       placeholder="Nama verifikator lapangan"
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-semibold text-slate-900 focus:outline-hidden focus:border-[#C83E13]">
                            </div>
                        </div>

                        <div>
                            <label for="update-notes" class="block text-[11px] font-bold text-slate-800 mb-1">Catatan Teknis Progres Pelaksana &amp; Pengawas:</label>
                            <textarea id="update-notes"
                                      rows="2"
                                      name="notes"
                                      x-model="notes"
                                      placeholder="Tuliskan hasil pemeriksaan mutu material dan realisasi fisik lapangan..."
                                      class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium text-slate-900 focus:outline-hidden focus:border-[#C83E13]"></textarea>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: RAB CHECKLIST & PHOTO EVIDENCE (5 cols) -->
                    <div class="md:col-span-5 flex flex-col gap-4 border-t md:border-t-0 md:border-l border-slate-200 md:pl-6 pt-4 md:pt-0">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                            <h3 class="font-extrabold uppercase tracking-wider text-[11px] text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                Bagian 2: Checklist Komponen RAB &amp; Bukti Foto
                            </h3>
                        </div>

                        @if($costEstimatesList->isNotEmpty())
                        <div>
                            <span class="block text-[11px] font-bold text-slate-800 mb-1.5">Komponen Pekerjaan RAB yang Telah Tuntas:</span>
                            <div class="space-y-1.5 max-h-44 overflow-y-auto pr-1">
                                @foreach($costEstimatesList as $estimate)
                                <label class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 border border-slate-200 hover:border-slate-300 cursor-pointer">
                                    <input type="checkbox"
                                           name="completed_estimates[]"
                                           value="{{ $estimate->id }}"
                                           @checked($estimate->is_completed)
                                           class="mt-0.5 rounded text-[#C83E13] focus:ring-[#C83E13]">
                                    <div class="min-w-0 flex-1">
                                        <span class="font-bold text-slate-800 block text-[11px] leading-snug">{{ $estimate->item_name }}</span>
                                        <span class="text-[10px] text-slate-600 font-medium block mt-0.5">
                                            {{ $estimate->formatted_total_price }} &bull; {{ $estimate->facilityCategory?->name ?? 'Sarpras' }}
                                        </span>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <label for="proof-photo-input" class="block text-[11px] font-bold text-slate-800 mb-1.5">Unggah Foto Bukti Fisik Lapangan (Opsional):</label>
                            <label for="proof-photo-input" class="cursor-pointer block border-2 border-dashed border-slate-300 rounded-2xl p-3.5 text-center bg-slate-50 hover:bg-slate-100/70 transition-colors">
                                <input id="proof-photo-input"
                                       type="file"
                                       name="proof_photo"
                                       accept=".jpg,.jpeg,.png,.webp,.pdf"
                                       @change="selectedFileName = $event.target.files[0]?.name || ''"
                                       class="sr-only">
                                <div class="w-8 h-8 rounded-full bg-orange-100 text-[#C83E13] flex items-center justify-center mx-auto mb-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="font-bold text-slate-800 block text-[11px]" x-text="selectedFileName ? ('File dipilih: ' + selectedFileName) : 'Pilih Foto Dokumentasi Lapangan (JPG/PNG/PDF)'"></span>
                                <span class="text-[10px] text-slate-600 block mt-0.5">Maksimal 5 MB &bull; Koordinat Lokasi Sekolah Terdaftar</span>
                            </label>
                        </div>

                        <div class="p-3 bg-emerald-50 rounded-2xl border border-emerald-200 text-[11px] text-emerald-900 mt-auto">
                            <div class="flex items-center gap-1.5 font-bold mb-1">
                                <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                <span>Rekalkulasi Bobot Otomatis:</span>
                            </div>
                            <p class="text-[11px] text-emerald-800 leading-relaxed">
                                Menyimpan perubahan akan menghitung ulang total persentase fisik proyek berdasarkan bobot tahapan dan memperbarui status tiket laporan kerusakan apabila proyek mencapai 100%.
                            </p>
                        </div>
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
                        <span x-text="submitting ? 'Menyimpan Pembaruan...' : 'Simpan Pembaruan Progres &amp; Kalkulasi'"></span>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
