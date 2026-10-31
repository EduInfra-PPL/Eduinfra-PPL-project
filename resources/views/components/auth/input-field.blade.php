@props([
    'name',
    'label' => null,
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'error' => null,
    'toggle' => false, // true = tombol mata untuk menampilkan/menyembunyikan isi (kolom kata sandi)
])

{{--
    Kolom isian halaman autentikasi: ikon kiri (slot "icon"), ikon kanan (slot "trailing"),
    tautan di sisi label (slot "aside"), dan tombol tampilkan/sembunyikan kata sandi (toggle).
--}}
@php($hasRightSide = $toggle || isset($trailing))

<div class="w-full flex flex-col gap-1.5" @if ($toggle) x-data="{ show: false }" @endif>
    @if ($label || isset($aside))
        <div class="flex items-center justify-between">
            @if ($label)
                <label for="{{ $name }}" class="text-xs font-semibold text-slate-700">{{ $label }}</label>
            @endif
            @isset($aside)
                {{ $aside }}
            @endisset
        </div>
    @endif

    <div class="relative flex items-center w-full">
        @isset($icon)
            <div class="absolute left-3.5 text-slate-400 pointer-events-none flex items-center">
                {{ $icon }}
            </div>
        @endisset

        <input
            type="{{ $type }}"
            @if ($toggle) :type="show ? 'text' : 'password'" @endif
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ $type === 'password' ? '' : old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' => 'w-full text-sm font-medium text-slate-800 bg-[#F1F5F9] border '
                    .($error ? 'border-red-400 focus:border-red-500 ' : 'border-transparent focus:border-[#FF5C2A] ')
                    .'focus:bg-white focus:outline-none transition-all placeholder-slate-400 py-3 rounded-xl '
                    .(isset($icon) ? 'pl-11 ' : 'pl-4 ')
                    .($hasRightSide ? 'pr-11' : 'pr-4'),
            ]) }}
        />

        @if ($hasRightSide)
            <div class="absolute right-3.5 flex items-center gap-2 text-slate-400">
                @isset($trailing)
                    {{ $trailing }}
                @endisset

                @if ($toggle)
                    <button type="button" @click="show = !show" class="hover:text-slate-600" aria-label="Tampilkan atau sembunyikan kata sandi">
                        <span x-show="!show"><x-ui.icon name="eye" class="w-4 h-4" /></span>
                        <span x-show="show" x-cloak><x-ui.icon name="eye-off" class="w-4 h-4" /></span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    @if ($error)
        <p class="text-[11px] text-red-600 font-medium pl-1">{{ $error }}</p>
    @endif
</div>
