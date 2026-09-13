<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ sidebarOpen: false, searchQuery: '', selectedCategory: 'All' }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Dashboard — {{ config('app.name', 'Skill Marketplace') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        <!-- Alpine.js for interactive mobile menu drawer & live opportunity filters -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50/90 font-sans antialiased text-slate-900 min-h-full selection:bg-sky-500 selection:text-white">

        <!-- Abstract Light Net Background Pattern -->
        <div class="fixed inset-0 pointer-events-none opacity-[0.05] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>
        <div class="fixed inset-0 pointer-events-none opacity-[0.03] [background-image:radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px] z-0"></div>

        <!-- Mobile Drawer Backdrop Overlay -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-slate-950/70 backdrop-blur-md z-40 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Main Wrapper Container (Full screen width) -->
        <div class="relative z-10 w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 flex gap-6 min-h-screen">

            <!-- Clean, Simplified Floating Side Navigation Menu -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:sticky inset-y-0 lg:top-6 left-0 z-50 lg:z-10 w-72 lg:w-64 xl:w-72 shrink-0 bg-white/95 backdrop-blur-xl border-r lg:border border-sky-100 lg:rounded-3xl p-5 sm:p-6 shadow-2xl lg:shadow-[0_20px_50px_rgba(14,165,233,0.12)] flex flex-col justify-between h-full lg:h-[calc(100vh-3rem)] transition-transform duration-300 ease-in-out overflow-y-auto no-scrollbar"
            >
                <div class="space-y-6">
                    
                    <!-- Sidebar Brand Header + Mobile Close Button -->
                    <div class="flex items-center justify-between pb-4 border-b border-sky-100">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                            </div>
                            <div>
                                <span class="text-slate-900 font-extrabold text-base tracking-tight block">Skill Marketplace</span>
                                <span class="text-[11px] text-sky-700 font-bold uppercase tracking-wider flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                    Member Portal
                                </span>
                            </div>
                        </a>

                        <!-- Mobile Close Cross -->
                        <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-slate-900 p-2 rounded-2xl hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Close menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Prominent "+ Post Task" Action Button -->
                    <div>
                        <a href="#post-opportunity" @click="sidebarOpen = false" class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-sky-500 via-sky-600 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-extrabold text-xs sm:text-sm py-3 px-4 rounded-2xl shadow-md shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all group">
                            <svg class="w-4 h-4 text-sky-100 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            <span>Post Task or Opportunity</span>
                        </a>
                    </div>

                    <!-- Clean, Uncluttered Menu Items (4 Clear Primary Links) -->
                    <nav class="space-y-1.5 pt-1">
                        
                        <!-- 1. Home Dashboard -->
                        <a href="{{ url('/dashboard') }}" class="flex items-center justify-between px-3.5 py-3 rounded-2xl bg-gradient-to-r from-sky-500 via-sky-600 to-blue-600 text-white shadow-md shadow-sky-500/20 text-xs sm:text-sm font-extrabold transition-all">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                                <span>Home Dashboard</span>
                            </div>
                            <span class="w-2 h-2 rounded-full bg-white shadow-xs"></span>
                        </a>

                        <!-- 2. Browse Directory -->
                        <a href="{{ url('/#freelancers') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-slate-800 hover:text-sky-800 hover:bg-sky-50 text-xs sm:text-sm font-bold transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-slate-800 group-hover:text-sky-700" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <span>Find Talent</span>
                            </div>
                        </a>

                        <!-- 3. Messages & Requests -->
                        <a href="#messages" @click="sidebarOpen = false" class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-slate-800 hover:text-sky-800 hover:bg-sky-50 text-xs sm:text-sm font-bold transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-slate-800 group-hover:text-sky-700" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>Messages & Requests</span>
                            </div>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-900 border border-slate-300">3</span>
                        </a>

                        <!-- 4. Profile & Settings -->
                        <a href="{{ url('/profile/edit') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-3.5 py-3 rounded-2xl text-slate-800 hover:text-sky-800 hover:bg-sky-50 text-xs sm:text-sm font-bold transition-all group">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-slate-800 group-hover:text-sky-700" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                <span>My Profile & Settings</span>
                            </div>
                        </a>
                    </nav>

                </div>

                <!-- Sidebar Bottom Status & Logout -->
                <div class="pt-4 mt-6 border-t border-sky-100 space-y-3 shrink-0">
                    
                    <!-- Simplified User Badge -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-xl bg-slate-900 text-white font-extrabold flex items-center justify-center text-xs shadow-xs shrink-0 border border-slate-800">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-xs font-extrabold text-slate-900 truncate">{{ $user->name ?? 'User' }}</h5>
                                <span class="text-[11px] text-slate-700 font-bold block truncate flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5 text-slate-900 inline" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                    {{ $completionPercentage }}% Complete
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Sign Out Button -->
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-2xl text-xs font-extrabold text-slate-800 hover:text-rose-700 bg-slate-100 hover:bg-rose-50 border border-slate-300 transition-all cursor-pointer">
                            <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Sign Out</span>
                        </button>
                    </form>

                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 min-w-0 space-y-6">
                
                <!-- Senior-Friendly Top Header Area -->
                <div class="bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-3xl p-4 sm:px-6 shadow-xs flex items-center justify-between gap-3 sm:gap-4">
                    
                    <!-- Left: Mobile Menu Button -->
                    <button 
                        @click="sidebarOpen = true" 
                        class="lg:hidden flex items-center gap-2 px-3.5 py-2.5 rounded-2xl border border-sky-200 bg-sky-50 text-sky-800 font-extrabold text-xs hover:bg-sky-100 shrink-0 cursor-pointer transition-colors"
                        aria-label="Open menu"
                    >
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>MENU</span>
                    </button>

                    <!-- Top Quick Search -->
                    <div class="relative flex-1 max-w-xl">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            placeholder="Type to search tutors, plumbers, jobs..." 
                            class="w-full bg-slate-50 border border-slate-300 text-sm text-slate-900 font-medium rounded-2xl pl-11 pr-4 py-2.5 outline-none focus:border-sky-600 focus:bg-white focus:ring-4 focus:ring-sky-100 transition-all"
                        />
                    </div>

                    <!-- Right Header Icons -->
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="#notifications" class="w-10 h-10 rounded-2xl border border-slate-200 hover:border-sky-300 bg-slate-50 flex items-center justify-center text-slate-700 relative transition-all" title="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500 absolute top-2 right-2 ring-2 ring-white"></span>
                        </a>

                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-sky-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-xs">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                </div>

                <!-- Session Alert Status Banner -->
                @if (session('status'))
                    <div class="bg-sky-50 border-2 border-sky-300 text-sky-950 text-sm font-extrabold rounded-2xl p-4 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Dynamic Welcome Banner Card -->
                <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-sky-950 text-white rounded-3xl p-6 sm:p-8 shadow-xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="space-y-3 max-w-2xl relative z-10">
                        @if($completionPercentage == 100)
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 text-xs font-extrabold">
                                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                                <span>✓ Profile Active & Verified</span>
                            </div>

                            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                Welcome back, {{ $user->name }}!
                            </h2>

                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-medium">
                                Explore available job postings below and apply directly. Connection fee is only charged after your application is accepted.
                            </p>
                        @else
                            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 text-xs font-extrabold">
                                <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
                                <span>Step 1: Finish Profile Setup</span>
                            </div>

                            <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                                Welcome, {{ $user->name }}!
                            </h2>

                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-medium">
                                Complete your profile to list your tutoring or trade skills, or browse available job postings below.
                            </p>
                        @endif

                        <!-- Progress Bar -->
                        <div class="pt-1 max-w-md">
                            <div class="flex items-center justify-between text-xs font-bold mb-1.5 text-sky-200">
                                <span>Profile Completion</span>
                                <span>{{ $completionPercentage }}% Complete</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-2.5 overflow-hidden border border-slate-700">
                                <div class="bg-gradient-to-r from-sky-400 to-blue-500 h-full rounded-full transition-all duration-500" style="width: {{ $completionPercentage }}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="relative z-10 shrink-0 w-full md:w-auto">
                        @if($completionPercentage == 100)
                            <a href="{{ url('/profile/edit') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-white text-slate-950 font-extrabold text-xs sm:text-sm px-6 py-3.5 rounded-2xl hover:bg-sky-50 transition-all shadow-md">
                                Edit Profile & Services →
                            </a>
                        @else
                            <a href="{{ route('onboarding') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-white text-slate-950 font-extrabold text-xs sm:text-sm px-6 py-3.5 rounded-2xl hover:bg-sky-50 transition-all shadow-md">
                                Complete Profile Now →
                            </a>
                        @endif
                    </div>
                </div>

                <!-- High-Legibility Stats Overview Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-5 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between text-slate-600 text-xs font-extrabold">
                            <span>Active Connections</span>
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-900 border border-slate-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900">0</div>
                        <span class="text-[11px] text-slate-500 font-bold block">0 pending approvals</span>
                    </div>

                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-5 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between text-slate-600 text-xs font-extrabold">
                            <span>Available Opportunities</span>
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-900 border border-slate-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ count($opportunities) }}</div>
                        <span class="text-[11px] text-slate-500 font-bold block">Open for applications</span>
                    </div>

                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-5 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between text-slate-600 text-xs font-extrabold">
                            <span>Trust Score</span>
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-900 border border-slate-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900">5.0</div>
                        <span class="text-[11px] text-slate-500 font-bold block">Verified member rating</span>
                    </div>

                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-5 shadow-2xs space-y-2">
                        <div class="flex items-center justify-between text-slate-600 text-xs font-extrabold">
                            <span>Account Role</span>
                            <span class="w-8 h-8 rounded-xl bg-slate-100 text-slate-900 border border-slate-200 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                        </div>
                        <div class="text-sm font-black text-slate-900 uppercase tracking-tight truncate">
                            {{ $user->professionalProfile->category->name ?? $user->onboarding_intent ?? 'Client / Talent' }}
                        </div>
                        <span class="text-[11px] text-sky-700 font-extrabold block">Verified Status</span>
                    </div>

                </div>

                <!-- Replaced Section: LIVE JOBS & OPPORTUNITIES LIST WITH SEARCH ENGINE -->
                <section class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                    
                    <!-- Section Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200 pb-5">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-900 border border-slate-300 text-xs font-extrabold mb-1">
                                <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                                <span>Available Marketplace Opportunities</span>
                            </div>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                                Live Job & Tutoring Opportunities
                            </h3>
                            <p class="text-xs sm:text-sm text-slate-600 font-medium">
                                Browse opportunities posted by clients and parents near you. Connect to start working.
                            </p>
                        </div>

                        <!-- Post New Job CTA Button -->
                        <a href="#post-opportunity" class="inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs sm:text-sm px-5 py-3 rounded-2xl transition-all shadow-xs shrink-0">
                            + Post New Opportunity
                        </a>
                    </div>

                    <!-- Search Mechanism & Category Filter Bar -->
                    <div class="space-y-4">
                        
                        <!-- Search Box -->
                        <div class="relative">
                            <svg class="w-5 h-5 text-slate-900 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                placeholder="Search by job title, subject (e.g. Mathematics), or location (e.g. Lagos)..." 
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-slate-900 focus:bg-white text-sm text-slate-900 font-medium rounded-2xl pl-11 pr-4 py-3 outline-none transition-all"
                            />
                        </div>

                        <!-- Category Filter Pills with Minimal Black Line Icons -->
                        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                            <button 
                                @click="selectedCategory = 'All'" 
                                :class="selectedCategory === 'All' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-300'"
                                class="px-4 py-2 rounded-xl text-xs font-extrabold border transition-all shrink-0 cursor-pointer flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                                All Opportunities
                            </button>
                            <button 
                                @click="selectedCategory = 'Academic Tutoring'" 
                                :class="selectedCategory === 'Academic Tutoring' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-300'"
                                class="px-4 py-2 rounded-xl text-xs font-extrabold border transition-all shrink-0 cursor-pointer flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                Academic Tutoring
                            </button>
                            <button 
                                @click="selectedCategory = 'Home & Technical'" 
                                :class="selectedCategory === 'Home & Technical' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-300'"
                                class="px-4 py-2 rounded-xl text-xs font-extrabold border transition-all shrink-0 cursor-pointer flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                Home & Technical
                            </button>
                            <button 
                                @click="selectedCategory = 'Creative & Digital'" 
                                :class="selectedCategory === 'Creative & Digital' ? 'bg-slate-900 text-white border-slate-900 shadow-xs' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-300'"
                                class="px-4 py-2 rounded-xl text-xs font-extrabold border transition-all shrink-0 cursor-pointer flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="12" x="3" y="4" rx="2"/><path d="M2 20h20"/></svg>
                                Creative & Digital
                            </button>
                        </div>

                    </div>

                    <!-- Live Opportunities List -->
                    <div class="space-y-4">
                        @foreach($opportunities as $opp)
                            <div 
                                x-show="(selectedCategory === 'All' || selectedCategory === '{{ $opp['category'] }}') && ('{{ strtolower($opp['title'] . ' ' . $opp['description'] . ' ' . $opp['location']) }}'.includes(searchQuery.toLowerCase()))"
                                class="border-2 border-slate-200 hover:border-slate-900 rounded-3xl p-5 sm:p-6 bg-slate-50/50 hover:bg-slate-100/50 transition-all space-y-4 group"
                            >
                                <!-- Card Header -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-900 border border-slate-300 flex items-center gap-1.5">
                                            @if($opp['category'] === 'Academic Tutoring')
                                                <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                                            @elseif($opp['category'] === 'Home & Technical')
                                                <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                                            @else
                                                <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="18" height="12" x="3" y="4" rx="2"/><path d="M2 20h20"/></svg>
                                            @endif
                                            <span>{{ $opp['category'] }}</span>
                                        </span>
                                        <span class="text-xs text-slate-500 font-bold">• {{ $opp['time_ago'] }}</span>
                                    </div>
                                    <div class="text-base sm:text-lg font-black text-slate-900">
                                        {{ $opp['budget'] }}
                                    </div>
                                </div>

                                <!-- Title & Description -->
                                <div class="space-y-1">
                                    <h4 class="text-base sm:text-lg font-black text-slate-900 group-hover:text-sky-900 transition-colors">
                                        {{ $opp['title'] }}
                                    </h4>
                                    <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                                        {{ $opp['description'] }}
                                    </p>
                                </div>

                                <!-- Metadata Details & Action Row -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-3 border-t border-slate-200/80">
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-700 font-bold">
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $opp['location'] }}
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-900" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                            {{ $opp['type'] }}
                                        </span>
                                    </div>

                                    <!-- Apply / Connect Button -->
                                    <a href="#connect" class="inline-flex items-center justify-center bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs sm:text-sm py-2.5 px-5 rounded-2xl shadow-xs transition-all cursor-pointer">
                                        Apply & Connect →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </section>

                <!-- Footer -->
                <footer class="py-6 text-center text-xs sm:text-sm text-slate-600 font-medium border-t border-slate-200/80">
                    &copy; {{ date('Y') }} Skill Marketplace® Global LLC. All rights reserved. • High Accessibility Mode Active
                </footer>

            </main>
        </div>

        @livewireScripts
    </body>
</html>
