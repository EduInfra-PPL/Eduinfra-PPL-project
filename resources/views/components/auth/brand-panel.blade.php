@props([
    'chip' => '',
    'chipDot' => 'bg-emerald-400',
])

{{-- Panel gelap di sisi kiri halaman autentikasi: label status, konten (brand + info), dan catatan kaki. --}}
<aside class="relative flex flex-col gap-6 p-7 lg:p-9 text-white bg-[#14171F] bg-[radial-gradient(circle_at_85%_10%,rgba(255,92,42,0.28),transparent_50%)]">
    <span class="self-start inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/10 text-[10px] font-bold tracking-wide uppercase text-slate-200">
        <span class="w-1.5 h-1.5 rounded-full {{ $chipDot }}"></span>
        {{ $chip }}
    </span>

    <div class="flex flex-col gap-6 flex-1">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="mt-auto pt-6">
            {{ $footer }}
        </div>
    @endisset
</aside>
