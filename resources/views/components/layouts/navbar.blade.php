@props([
    'active' => 'dashboard',
    'userName' => 'Ir. Hendra',
    'userRole' => 'Auditor Utama Kemendikbud',
    'userInitials' => 'IH'
])

<header class="w-full pt-4 lg:pt-6 pb-4 px-4 lg:px-6 antialiased font-sans">
    <div class="max-w-[1520px] mx-auto bg-white rounded-2xl lg:rounded-3xl p-4 lg:p-5 shadow-sm border border-slate-100 flex flex-col gap-4">
        
        <!-- TIER 1: BRAND LOGO + CORE NAVIGATION TABS -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            
            <!-- Brand & Project Badge -->
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 hover:opacity-95 transition-opacity">
                <!-- Logo Mark -->
                <div class="w-11 h-11 bg-[#14171F] rounded-2xl flex items-center justify-center shadow-sm shrink-0">
                    <svg class="w-6 h-6 text-[#FF5C2A]" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3L2 12h3v8h6v-6h2v6h6v-8h3L12 3zm0 4.5c1.38 0 2.5 1.12 2.5 2.5 0 1.88-2.5 4.5-2.5 4.5s-2.5-2.62-2.5-4.5c0-1.38 1.12-2.5 2.5-2.5z"/>
                    </svg>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-lg font-extrabold text-[#14171F] tracking-tight">EduInfra Tracker</h1>
                        <span class="px-2.5 py-0.5 text-[11px] font-bold text-[#FF5C2A] bg-[#FFF2ED] rounded-full border border-[#FFD9CC]">
                            GIS 3T v2.4
                        </span>
                    </div>
                    <p class="text-[11px] font-medium text-slate-500">Audit Fasilitas & Matching CSR Wilayah 3T</p>
                </div>
            </a>

            <!-- Navigation Segmented Tabs (Pill Group) -->
            <nav class="flex items-center gap-1.5 p-1 bg-[#F8FAFC] border border-slate-200/60 rounded-full">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center gap-2 px-4 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'dashboard' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                    <svg class="w-3.5 h-3.5 {{ $active === 'dashboard' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                    Dashboard
                </a>
                
                <!-- Audit Fasilitas -->
                <a href="#audit" 
                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'audit' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                    <svg class="w-3.5 h-3.5 {{ $active === 'audit' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Audit Fasilitas
                </a>

                <!-- Matching CSR -->
                <a href="#csr" 
                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'csr' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                    <svg class="w-3.5 h-3.5 {{ $active === 'csr' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Matching CSR
                </a>

                <!-- Peta 3T Live -->
                <a href="#gis" 
                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'gis' ? 'bg-[#FF5C2A] text-white shadow-sm' : 'text-[#FF5C2A] bg-white border border-[#FFD9CC] hover:bg-[#FFF2ED]' }}">
                    <span class="w-2 h-2 rounded-full {{ $active === 'gis' ? 'bg-white' : 'bg-[#FF5C2A]' }} animate-pulse"></span>
                    Peta 3T Live
                </a>

                <!-- Laporan -->
                <a href="#laporan" 
                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'laporan' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                    <svg class="w-3.5 h-3.5 {{ $active === 'laporan' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Laporan
                </a>
            </nav>
        </div>

        <!-- TIER 2: SEARCH UTILITY + QUICK ACTIONS + USER PROFILE -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-slate-100">
            
            <!-- Omni Search Pill with Keyboard Shortcut -->
            <div class="relative flex items-center min-w-[280px] lg:w-[360px]">
                <div class="absolute left-3.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="SDN 02 Sambas..." 
                    class="w-full pl-10 pr-12 py-2 text-xs font-medium text-slate-800 bg-[#F3F5F8] border border-transparent rounded-full focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-all placeholder-slate-400"
                />
                <kbd class="absolute right-3 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded-md">⌘K</kbd>
            </div>

            <!-- Action & Profile Capsule Group -->
            <div class="flex items-center gap-3">
                
                <!-- Notification Button -->
                <button type="button" aria-label="Notifikasi" class="relative w-9 h-9 flex items-center justify-center rounded-full bg-[#F3F5F8] hover:bg-slate-200 text-slate-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#FF5C2A] rounded-full border-2 border-white"></span>
                </button>

                <!-- User Identity Capsule -->
                <div class="flex items-center gap-3 pl-2 pr-3 py-1.5 bg-[#F3F5F8] border border-slate-200/50 rounded-full cursor-pointer hover:bg-slate-200/70 transition-colors">
                    <!-- Avatar with Online Status Indicator -->
                    <div class="relative">
                        <div class="w-7 h-7 rounded-full bg-[#14171F] text-white flex items-center justify-center text-xs font-bold">
                            {{ $userInitials }}
                        </div>
                        <span class="absolute bottom-0 right-0 w-2 h-2 bg-[#10B981] rounded-full border border-white"></span>
                    </div>

                    <!-- Name & Role -->
                    <div class="text-left">
                        <div class="text-xs font-bold text-slate-900 leading-tight">{{ $userName }}</div>
                        <div class="text-[10px] text-slate-500 font-medium leading-none">{{ $userRole }}</div>
                    </div>

                    <!-- Status Badge -->
                    <span class="px-2 py-0.5 text-[10px] font-semibold text-[#059669] bg-[#DCFCE7] rounded-full">
                        Aktif
                    </span>

                    <!-- Dropdown Icon -->
                    <svg class="w-3.5 h-3.5 text-slate-400 ml-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                    </svg>
                </div>

            </div>
        </div>

    </div>
</header>
