<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ pageLoading: true, sidebarOpen: false, searchQuery: '', selectedCategory: 'All', profileModalOpen: false, notificationsOpen: false }" x-init="setTimeout(() => pageLoading = false, 350)">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Dashboard — {{ config('app.name', 'Skill Marketplace') }}</title>

        <!-- Modern Clean Typography (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
        </style>
    </head>
    <body class="bg-slate-100/70 font-sans antialiased text-slate-900 min-h-full selection:bg-slate-900 selection:text-white">

        <!-- Skeleton Preloader Overlay -->
        <div 
            x-show="pageLoading" 
            x-transition:leave="transition ease-out duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-98 pointer-events-none"
            class="fixed inset-0 z-50 bg-slate-100/90 backdrop-blur-md flex p-4 sm:p-6 gap-6 overflow-hidden"
        >
            <!-- Left Sidebar Skeleton -->
            <div class="hidden lg:flex w-64 xl:w-72 shrink-0 bg-white border border-slate-200/80 rounded-2xl p-5 flex-col justify-between h-[calc(100vh-3rem)] space-y-6">
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-200 animate-pulse"></div>
                        <div class="space-y-1.5 flex-1">
                            <div class="h-4 w-28 bg-slate-200 rounded-md animate-pulse"></div>
                            <div class="h-3 w-20 bg-slate-100 rounded-md animate-pulse"></div>
                        </div>
                    </div>
                    <div class="h-10 w-full bg-slate-200 rounded-xl animate-pulse"></div>
                    <div class="space-y-2 pt-2">
                        <div class="h-3 w-16 bg-slate-100 rounded-md mb-2"></div>
                        <div class="h-9 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
                        <div class="h-9 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
                        <div class="h-9 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
                        <div class="h-9 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
                    </div>
                </div>
                <div class="h-14 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
            </div>

            <!-- Main Content Viewport Skeleton -->
            <div class="flex-1 space-y-6 overflow-hidden">
                <!-- Top Navbar Skeleton -->
                <div class="bg-white border border-slate-200/80 rounded-2xl px-5 py-3.5 flex items-center justify-between">
                    <div class="h-9 w-64 bg-slate-200 rounded-xl animate-pulse"></div>
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 bg-slate-200 rounded-xl animate-pulse"></div>
                        <div class="h-9 w-28 bg-slate-200 rounded-xl animate-pulse"></div>
                    </div>
                </div>

                <!-- Hero / Banner Skeleton -->
                <div class="h-40 w-full bg-slate-200/80 rounded-2xl animate-pulse"></div>

                <!-- Cards Grid Skeleton -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="h-44 bg-white border border-slate-200/80 rounded-2xl p-5 space-y-3">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-200 animate-pulse"></div>
                            <div class="space-y-1.5 flex-1">
                                <div class="h-4 w-3/4 bg-slate-200 rounded-md animate-pulse"></div>
                                <div class="h-3 w-1/2 bg-slate-100 rounded-md animate-pulse"></div>
                            </div>
                        </div>
                        <div class="h-10 w-full bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="flex justify-between items-center pt-2">
                            <div class="h-4 w-24 bg-slate-200 rounded-md"></div>
                            <div class="h-8 w-20 bg-slate-200 rounded-lg"></div>
                        </div>
                    </div>
                    <div class="h-44 bg-white border border-slate-200/80 rounded-2xl p-5 space-y-3">
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-xl bg-slate-200 animate-pulse"></div>
                            <div class="space-y-1.5 flex-1">
                                <div class="h-4 w-3/4 bg-slate-200 rounded-md animate-pulse"></div>
                                <div class="h-3 w-1/2 bg-slate-100 rounded-md animate-pulse"></div>
                            </div>
                        </div>
                        <div class="h-10 w-full bg-slate-100 rounded-xl animate-pulse"></div>
                        <div class="flex justify-between items-center pt-2">
                            <div class="h-4 w-24 bg-slate-200 rounded-md"></div>
                            <div class="h-8 w-20 bg-slate-200 rounded-lg"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Overlay -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Main Dashboard Container -->
        <div class="w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex gap-6 min-h-screen">

            <!-- Sidebar Navigation -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:sticky inset-y-0 lg:top-6 left-0 z-50 lg:z-10 w-72 lg:w-64 xl:w-72 shrink-0 bg-white border-r lg:border border-slate-200/80 lg:rounded-2xl p-5 shadow-xl lg:shadow-xs flex flex-col justify-between h-full lg:h-[calc(100vh-3rem)] transition-transform duration-300 ease-in-out overflow-y-auto no-scrollbar"
            >
                <div class="space-y-6">
                    
                    <!-- Sidebar Header & Logo -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-9 h-9 rounded-xl bg-[#0F172B] flex items-center justify-center text-white shadow-xs group-hover:bg-slate-800 transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                            </div>
                            <div>
                                <span class="text-slate-900 font-bold text-base tracking-tight block">Skill Marketplace</span>
                                <span class="text-[11px] text-slate-500 font-medium tracking-wide">Member Dashboard</span>
                            </div>
                        </a>

                        <!-- Mobile Close Button -->
                        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Close menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Post Task Primary CTA -->
                    <div>
                        <a href="#post-opportunity" @click="sidebarOpen = false" class="w-full flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-2.5 px-4 rounded-xl shadow-xs transition-colors">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            <span>Post an Opportunity</span>
                        </a>
                    </div>

                    <!-- Navigation Links Grouped -->
                    <div class="space-y-4">
                        
                        <!-- Group 1: Navigation -->
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Navigation</span>
                            <nav class="space-y-0.5">
                                <!-- Home Dashboard -->
                                <a href="{{ url('/dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl bg-[#0F172B] text-white text-xs font-normal transition-colors shadow-xs">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                                        <span>Overview</span>
                                    </div>
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                </a>

                                <!-- Browse Directory / Find Talent -->
                                <a href="{{ url('/dashboard/talent') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                        <span>Find Talent</span>
                                    </div>
                                </a>
                            </nav>
                        </div>

                        <!-- Group 2: Work & Requests -->
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Workplace</span>
                            <nav class="space-y-0.5">
                                <!-- Messages & Requests -->
                                <a href="{{ url('/dashboard/messages') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        <span>Messages</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-400 text-slate-900">2</span>
                                </a>
                            </nav>
                        </div>

                        <!-- Group 3: Settings -->
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Account</span>
                            <nav class="space-y-0.5">
                                <!-- Profile & Settings -->
                                <button @click="profileModalOpen = true; sidebarOpen = false" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors text-left cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        <span>View Profile Card</span>
                                    </div>
                                </button>
                                <a href="{{ url('/profile/edit') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        <span>Edit Settings</span>
                                    </div>
                                </a>
                            </nav>
                        </div>

                    </div>
                </div>

                <!-- Sidebar Footer Sign Out -->
                <div class="pt-4 mt-6 border-t border-slate-200/80 shrink-0">
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Sign Out</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Main Dashboard Viewport -->
            <main class="flex-1 min-w-0 space-y-6">
                
                <!-- Top Header Bar -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-3.5 sm:px-6 shadow-xs flex items-center justify-between gap-4">
                    
                    <!-- Mobile Menu Button -->
                    <button 
                        @click="sidebarOpen = true" 
                        class="lg:hidden flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium text-xs hover:bg-slate-100 shrink-0 cursor-pointer transition-colors"
                        aria-label="Open navigation menu"
                    >
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>Menu</span>
                    </button>

                    <!-- Global Search Bar -->
                    <div class="relative flex-1 max-w-lg">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            placeholder="Search opportunities, tutors, or services..." 
                            class="w-full bg-slate-50 border border-slate-200 text-sm text-slate-900 font-normal rounded-xl pl-10 pr-4 py-2 outline-none focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all"
                        />
                    </div>

                    <!-- Right Header Icons: Notifications Dropdown & Profile Avatar Trigger -->
                    <div class="flex items-center gap-3 shrink-0">
                        
                        <!-- Notifications Popup Trigger Button -->
                        <div class="relative">
                            <button 
                                @click="notificationsOpen = !notificationsOpen" 
                                class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 relative transition-colors cursor-pointer" 
                                title="Notifications"
                            >
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span class="w-2 h-2 rounded-full bg-sky-500 absolute top-2 right-2 ring-2 ring-white"></span>
                            </button>

                            <!-- Notifications Popup Dropdown Panel -->
                            <div 
                                x-show="notificationsOpen" 
                                @click.outside="notificationsOpen = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-200/90 rounded-2xl shadow-xl z-50 overflow-hidden space-y-0"
                                style="display: none;"
                            >
                                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-900">Notifications</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172B] text-white">3 New</span>
                                    </div>
                                    <button @click="notificationsOpen = false" class="text-xs text-slate-400 hover:text-slate-600 font-medium cursor-pointer">Close</button>
                                </div>

                                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                                    @if (!$user->hasVerifiedEmail())
                                        <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <div class="flex-1 min-w-0 space-y-0.5">
                                                    <p class="text-xs font-bold text-slate-900">Action Required: Verify Email</p>
                                                    <p class="text-[11px] text-slate-500 font-normal">Please confirm {{ $user->email }} to unlock full access to application requests.</p>
                                                    <span class="text-[10px] text-slate-400 font-medium block">Just now</span>
                                                </div>
                                            </div>
                                            <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors cursor-pointer shrink-0" title="Dismiss notification">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    @endif

                                    <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 mt-0.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0 space-y-0.5">
                                                <p class="text-xs font-bold text-slate-900">Welcome to Skill Marketplace</p>
                                                <p class="text-[11px] text-slate-500 font-normal">Your account is active! Browse opportunities and connect with clients or tutors.</p>
                                                <span class="text-[10px] text-slate-400 font-medium block">10 minutes ago</span>
                                            </div>
                                        </div>
                                        <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors cursor-pointer shrink-0" title="Dismiss notification">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>

                                    <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center shrink-0 border border-sky-200 mt-0.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0 space-y-0.5">
                                                <p class="text-xs font-bold text-slate-900">New Tutoring Opportunity Posted</p>
                                                <p class="text-[11px] text-slate-500 font-normal">SS2 Mathematics & Physics Tutor needed in Ikeja, Lagos.</p>
                                                <span class="text-[10px] text-slate-400 font-medium block">1 hour ago</span>
                                            </div>
                                        </div>
                                        <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors cursor-pointer shrink-0" title="Dismiss notification">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-2.5 bg-slate-50/80 border-t border-slate-200/80 text-center">
                                    <span class="text-[11px] text-slate-500 font-medium">All notifications up to date</span>
                                </div>
                            </div>
                        </div>

                        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- Header Profile Button Trigger (Opens Profile Details Slide-Over Popup) -->
                        <button @click="profileModalOpen = true" class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" />
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-xs font-semibold text-slate-800 hidden sm:inline">{{ $user->name ?? 'User' }}</span>
                        </button>

                    </div>
                </div>

                <!-- Email Verification Required Alert Banner -->
                @if (!$user->hasVerifiedEmail())
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-amber-50 border border-amber-200 text-amber-900 text-sm font-medium rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 border border-amber-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-slate-900 font-semibold block text-xs sm:text-sm">Email verification required</span>
                                <span class="text-xs text-slate-600 font-normal">Please verify <strong>{{ $user->email }}</strong> to unlock applications and connections.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            <a href="{{ route('verification.notice') }}" class="px-3.5 py-1.5 bg-[#0F172B] hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shrink-0 transition-colors">
                                Verify Email →
                            </a>
                            <button @click="show = false" class="text-amber-700 hover:text-amber-950 hover:bg-amber-100 p-1.5 rounded-lg transition-colors cursor-pointer" title="Cancel notification">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Session Alert Status Banner -->
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-medium rounded-2xl p-4 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="truncate sm:whitespace-normal">{{ session('status') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 hover:bg-emerald-100/80 p-1 rounded-lg transition-colors cursor-pointer shrink-0 ml-3" title="Cancel notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <!-- Classic Welcome Hero Section -->
                <div class="bg-[#0F172B] text-white rounded-2xl p-6 sm:p-7 shadow-sm relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="space-y-2 max-w-xl">
                        @if($completionPercentage == 100)
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                <span>Profile Active & Verified</span>
                            </div>

                            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                                Welcome back, {{ $user->name }}
                            </h1>

                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-normal">
                                Explore live opportunities listed below and connect directly with clients or tutors.
                            </p>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-800 border border-slate-700 text-slate-300 text-xs font-medium">
                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                <span>Profile Setup In Progress</span>
                            </div>

                            <h1 class="text-xl sm:text-2xl font-bold text-white tracking-tight">
                                Welcome to Skill Marketplace, {{ $user->name }}
                            </h1>

                            <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-normal">
                                Complete your profile to start receiving client requests and applying for open jobs.
                            </p>
                        @endif

                        <!-- Progress Bar (Only visible when incomplete) -->
                        @if($completionPercentage < 100)
                            <div class="pt-2 max-w-sm">
                                <div class="flex items-center justify-between text-xs font-medium mb-1 text-slate-300">
                                    <span>Setup Progress</span>
                                    <span class="font-semibold text-white">{{ $completionPercentage }}%</span>
                                </div>
                                <div class="w-full bg-slate-800 rounded-full h-2 overflow-hidden border border-slate-700/80">
                                    <div class="bg-white h-full rounded-full transition-all duration-500" style="width: {{ $completionPercentage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Hero Action CTA -->
                    <div class="shrink-0 w-full md:w-auto flex items-center gap-2">
                        <button @click="profileModalOpen = true" class="w-full md:w-auto inline-flex items-center justify-center bg-slate-800 text-white border border-slate-700 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl hover:bg-slate-700 transition-colors cursor-pointer">
                            View Profile Card
                        </button>

                        @if($completionPercentage == 100)
                            <a href="{{ url('/profile/edit') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-white text-slate-900 font-semibold text-xs sm:text-sm px-5 py-2.5 rounded-xl hover:bg-slate-100 transition-colors shadow-xs">
                                Edit Profile →
                            </a>
                        @else
                            <a href="{{ route('onboarding') }}" class="w-full md:w-auto inline-flex items-center justify-center bg-white text-slate-900 font-semibold text-xs sm:text-sm px-5 py-2.5 rounded-xl hover:bg-slate-100 transition-colors shadow-xs">
                                Complete Setup →
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Modern Stats Metrics Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Metric 1 -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                            <span>Active Connections</span>
                            <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900 tracking-tight">0</div>
                        <span class="text-[11px] text-slate-400 font-normal block">0 pending approvals</span>
                    </div>

                    <!-- Metric 2 -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                            <span>Open Opportunities</span>
                            <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900 tracking-tight">{{ count($opportunities) }}</div>
                        <span class="text-[11px] text-slate-400 font-normal block">Available to connect</span>
                    </div>

                    <!-- Metric 3 -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                            <span>Trust Score</span>
                            <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            </span>
                        </div>
                        <div class="text-2xl font-bold text-slate-900 tracking-tight">5.0</div>
                        <span class="text-[11px] text-slate-400 font-normal block">Verified rating</span>
                    </div>

                    <!-- Metric 4 -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                        <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                            <span>Account Category</span>
                            <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                        </div>
                        <div class="text-sm font-bold text-slate-900 truncate tracking-tight">
                            {{ $user->professionalProfile->category->name ?? $user->onboarding_intent ?? 'Client / Talent' }}
                        </div>
                        <span class="text-[11px] text-emerald-600 font-medium block">Verified Status</span>
                    </div>

                </div>

                <!-- Marketplace Opportunities List Section -->
                <section class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-7 shadow-xs space-y-6">
                    
                    <!-- Section Header -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
                        <div>
                            <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                                Live Opportunities & Jobs
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                                Browse requests posted by clients and parents. Filter by category or search by keywords.
                            </p>
                        </div>

                        <!-- Post CTA -->
                        <a href="#post-opportunity" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition-colors shadow-xs shrink-0">
                            + Post New Job
                        </a>
                    </div>

                    <!-- Search Box & Filter Tabs -->
                    <div class="space-y-3.5">
                        
                        <!-- Search Box -->
                        <div class="relative">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <input 
                                type="text" 
                                x-model="searchQuery"
                                placeholder="Search by title, subject, or location (e.g. Lagos, Physics)..." 
                                class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white text-sm text-slate-900 font-normal rounded-xl pl-10 pr-4 py-2.5 outline-none transition-all"
                            />
                        </div>

                        <!-- Category Filter Pills -->
                        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                            <button 
                                @click="selectedCategory = 'All'" 
                                :class="selectedCategory === 'All' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                            >
                                All Categories
                            </button>
                            <button 
                                @click="selectedCategory = 'Academic Tutoring'" 
                                :class="selectedCategory === 'Academic Tutoring' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                            >
                                Academic Tutoring
                            </button>
                            <button 
                                @click="selectedCategory = 'Home & Technical'" 
                                :class="selectedCategory === 'Home & Technical' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                            >
                                Home & Technical
                            </button>
                            <button 
                                @click="selectedCategory = 'Creative & Digital'" 
                                :class="selectedCategory === 'Creative & Digital' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                            >
                                Creative & Digital
                            </button>
                        </div>

                    </div>

                    <!-- Opportunities List Grid -->
                    <div class="space-y-4">
                        @foreach($opportunities as $opp)
                            <div 
                                x-show="(selectedCategory === 'All' || selectedCategory === '{{ $opp['category'] }}') && ('{{ strtolower($opp['title'] . ' ' . $opp['description'] . ' ' . $opp['location']) }}'.includes(searchQuery.toLowerCase()))"
                                class="border border-slate-200/80 hover:border-slate-300 rounded-xl p-5 bg-white hover:shadow-xs transition-all space-y-3 group"
                            >
                                <!-- Top Info Line -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                            {{ $opp['category'] }}
                                        </span>
                                        <span class="text-xs text-slate-400 font-normal">• {{ $opp['time_ago'] }}</span>
                                    </div>
                                    <div class="text-base font-bold text-slate-900">
                                        {{ $opp['budget'] }}
                                    </div>
                                </div>

                                <!-- Title & Description -->
                                <div class="space-y-1">
                                    <h3 class="text-base font-semibold text-slate-900 group-hover:text-sky-700 transition-colors">
                                        {{ $opp['title'] }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-slate-600 font-normal leading-relaxed">
                                        {{ $opp['description'] }}
                                    </p>
                                </div>

                                <!-- Metadata & Action -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-normal">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $opp['location'] }}
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                            {{ $opp['type'] }}
                                        </span>
                                    </div>

                                    <!-- Action Button -->
                                    <a href="#connect" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-4 rounded-xl transition-colors shadow-xs cursor-pointer">
                                        Apply & Connect →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </section>

                <!-- Clean Footer -->
                <footer class="py-6 text-center text-xs text-slate-400 font-normal border-t border-slate-200/80">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Skill Marketplace') }}. All rights reserved.
                </footer>

            </main>
        </div>

        <!-- Profile Details Slide-Over Drawer Popup -->
        <div 
            x-show="profileModalOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="profileModalOpen = false" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex justify-end"
            style="display: none;"
        >
            <!-- Drawer Body -->
            <div 
                @click.stop
                x-show="profileModalOpen"
                x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-full max-w-md bg-white h-full shadow-2xl p-6 flex flex-col justify-between overflow-y-auto no-scrollbar border-l border-slate-200/80"
            >
                <div class="space-y-6">
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <span class="text-sm font-bold text-slate-900">My Profile Card</span>
                        </div>
                        <button @click="profileModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Profile Avatar Card -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center space-y-3">
                        <div class="relative inline-block">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border border-slate-300 shadow-xs mx-auto" />
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-2xl shadow-xs mx-auto">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="w-4 h-4 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0" title="Online & Active"></span>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                        </div>

                        <div class="pt-1 flex justify-center">
                            @if($completionPercentage == 100)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Verified Profile (100%)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-800 border border-slate-300">
                                    Profile {{ $completionPercentage }}% Complete
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Table -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Overview</h4>
                        
                        <div class="bg-white border border-slate-200/80 rounded-xl divide-y divide-slate-100 text-xs">
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Full Name</span>
                                <span class="font-semibold text-slate-900">{{ $user->name }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Email Address</span>
                                <span class="font-semibold text-slate-900 truncate max-w-[200px]">{{ $user->email }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Phone Number</span>
                                <span class="font-semibold text-slate-900">{{ $user->phone ?? 'Not specified' }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Location</span>
                                <span class="font-semibold text-slate-900">{{ $user->location ?? 'Nigeria' }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Primary Category</span>
                                <span class="font-semibold text-slate-900">{{ $user->professionalProfile->category->name ?? $user->onboarding_intent ?? 'Client / Talent' }}</span>
                            </div>
                        </div>

                        @if($user->professionalProfile && $user->professionalProfile->bio)
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Service Biography</h4>
                                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 text-xs text-slate-700 leading-relaxed font-normal">
                                    {{ $user->professionalProfile->bio }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-6 border-t border-slate-200/80 space-y-3">
                    <a href="{{ url('/profile/edit') }}" class="w-full flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs transition-colors">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        <span>Edit Full Profile & Services →</span>
                    </a>

                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 transition-colors cursor-pointer">
                            <span>Sign Out Account</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
