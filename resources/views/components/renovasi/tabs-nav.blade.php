@props([
    'activeTab' => 'proyek',
    'projectCount' => null,
    'milestoneCount' => null,
    'vendorCount' => null,
])

@php
    $resolvedProjectCount = $projectCount ?? \App\Models\RepairProject::count();
    $resolvedMilestoneCount = $milestoneCount ?? \App\Models\ProjectMilestone::count();
    $resolvedVendorCount = $vendorCount ?? (\Illuminate\Support\Facades\Schema::hasTable('vendors') ? \App\Models\Vendor::count() : 0);
@endphp

<div class="flex flex-wrap items-center justify-between gap-3 mb-6 pb-2 border-b border-slate-200">
    <nav aria-label="Navigasi Modul Renovasi" class="flex flex-wrap items-center gap-1.5 p-1 bg-[#E6ECF2] rounded-full">
        <a href="{{ route('renovasi.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13] {{ $activeTab === 'proyek' ? 'bg-[#C83E13] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
            <svg class="w-3.5 h-3.5 {{ $activeTab === 'proyek' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            <span>Proyek Berjalan</span>
            <span class="{{ $activeTab === 'proyek' ? 'bg-black/25 text-white' : 'bg-slate-200 text-slate-800' }} text-[11px] px-2 py-0.5 rounded-full font-bold">{{ $resolvedProjectCount }}</span>
        </a>

        <a href="{{ route('renovasi.index', ['view' => 'table']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13] {{ $activeTab === 'rab' ? 'bg-[#C83E13] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
            <svg class="w-3.5 h-3.5 {{ $activeTab === 'rab' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
            </svg>
            <span>Rincian Anggaran / RAB</span>
        </a>

        <a href="{{ route('renovasi.milestones') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13] {{ $activeTab === 'milestone' ? 'bg-[#C83E13] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
            <svg class="w-3.5 h-3.5 {{ $activeTab === 'milestone' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>Monitoring Tahapan &amp; BAST</span>
            <span class="{{ $activeTab === 'milestone' ? 'bg-black/25 text-white' : 'bg-slate-200 text-slate-800' }} text-[11px] px-2 py-0.5 rounded-full font-bold">{{ $resolvedMilestoneCount }}</span>
        </a>

        <a href="{{ route('renovasi.vendors') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-bold transition-all focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#C83E13] {{ $activeTab === 'vendors' ? 'bg-[#C83E13] text-white shadow-xs' : 'text-slate-700 hover:text-slate-900 hover:bg-white/80' }}">
            <svg class="w-3.5 h-3.5 {{ $activeTab === 'vendors' ? 'text-white' : 'text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <span>Direktori Kontraktor LPJK</span>
            <span class="{{ $activeTab === 'vendors' ? 'bg-black/25 text-white' : 'bg-slate-200 text-slate-800' }} text-[11px] px-2 py-0.5 rounded-full font-bold">{{ $resolvedVendorCount }}</span>
        </a>
    </nav>

    <div class="hidden md:flex items-center gap-2 text-xs text-slate-700 font-semibold bg-white px-3.5 py-1.5 rounded-full border border-slate-200 shadow-2xs">
        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
        <span>Penyelarasan Standar SNI Bangunan 3T</span>
    </div>
</div>
