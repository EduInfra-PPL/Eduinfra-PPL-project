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
                <x-ui.logo-icon size="w-11 h-11" />
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

                <!-- Master Data -->
                <a href="#master-data" 
                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'master-data' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                    <svg class="w-3.5 h-3.5 {{ $active === 'master-data' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                    </svg>
                    Master Data
                </a>

                <!-- Renovasi & RAB -->
                <a href="#renovasi" 
                   class="flex items-center gap-2 px-3.5 py-2 text-xs font-semibold rounded-full transition-all {{ $active === 'renovasi' ? 'bg-[#14171F] text-white shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-white' }}">
                    <svg class="w-3.5 h-3.5 {{ $active === 'renovasi' ? 'text-white' : 'text-slate-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Renovasi &amp; RAB
                </a>
            </nav>
        </div>

        <!-- TIER 2: SEARCH UTILITY + QUICK ACTIONS + USER PROFILE (GROUPED ON THE LEFT) -->
        <div class="flex flex-wrap items-center gap-3 pt-1">
            
            <!-- Omni Search Pill with Keyboard Shortcut -->
            <div class="relative flex items-center w-full sm:w-[260px] lg:w-[300px]">
                <div class="absolute left-3.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35"/>
                    </svg>
                </div>
                <input 
                    type="text" 
                    placeholder="SDN 02 Sambas..." 
                    class="w-full pl-9 pr-12 py-2 text-xs font-medium text-slate-800 bg-[#F8FAFC] border border-slate-200/70 rounded-full focus:bg-white focus:border-[#FF5C2A] focus:outline-none transition-all placeholder-slate-400 shadow-xs"
                />
                <kbd class="absolute right-3 px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded-md">⌘K</kbd>
            </div>

            <!-- Action & Profile Capsule Group -->
            <div class="flex items-center gap-2.5">
                
                <!-- Notification Button -->
                <button type="button" aria-label="Notifikasi" class="relative w-9 h-9 flex items-center justify-center rounded-full bg-[#F8FAFC] border border-slate-200/70 hover:bg-slate-100 text-slate-600 transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-[#FF5C2A] rounded-full border-2 border-white"></span>
                </button>

                <!-- User Identity Capsule -->
                <div class="flex items-center gap-2.5 pl-1.5 pr-3 py-1 bg-[#F8FAFC] border border-slate-200/70 rounded-full cursor-pointer hover:bg-slate-100 transition-colors shadow-xs">
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

