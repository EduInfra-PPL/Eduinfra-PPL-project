{{-- Pemilih bobot urgensi 1-5 (radio berbentuk pil). Dipakai formulir tambah dan ubah kategori (PBI-03). --}}
@props([
    'name' => 'urgency_weight',
    'id' => null,
    'label' => 'Bobot urgensi',
    'selected' => null,
    'error' => null,
])

@php
    $id = $id ?? $name;
    $selected = $selected === null || $selected === '' ? null : (int) $selected;
@endphp

<fieldset {{ $attributes->merge(['class' => 'flex flex-col gap-1.5']) }}>
    <legend class="text-xs font-semibold text-slate-700 mb-1.5">{{ $label }}</legend>
    <div class="grid grid-cols-5 gap-1.5">
        @foreach (\App\Models\FacilityCategory::URGENCY_LABELS as $weight => $text)
            <label class="cursor-pointer">
                <input type="radio" name="{{ $name }}" id="{{ $id }}_{{ $weight }}" value="{{ $weight }}" class="peer sr-only" @checked($selected === $weight)>
                <span class="flex flex-col items-center gap-0.5 px-1 py-2 rounded-xl border border-slate-200/80 bg-[#F8FAFC] text-slate-600 transition-all
                             peer-checked:bg-[#14171F] peer-checked:border-[#14171F] peer-checked:text-white
                             peer-focus-visible:ring-2 peer-focus-visible:ring-[#FF5C2A] hover:border-slate-300">
                    <span class="text-sm font-extrabold leading-none">{{ $weight }}</span>
                    <span class="text-[10px] font-semibold">{{ $text }}</span>
                </span>
            </label>
        @endforeach
    </div>
    <p class="text-[11px] text-slate-500">1 = dampak kecil, 5 = membahayakan keselamatan atau menghentikan kegiatan belajar.</p>
    @if ($error)
        <span class="text-[11px] text-red-600">{{ $error }}</span>
    @endif
</fieldset>
