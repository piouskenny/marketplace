<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ sidebarOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Dashboard — {{ config('app.name', 'Skill Marketplace') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        <!-- Alpine.js for interactive mobile menu drawer -->
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

        <!-- Main Wrapper Container (Full screen width, comfortable spacing for easy viewing) -->
        <div class="relative z-10 w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 flex gap-6 min-h-screen">

            <!-- Senior-Friendly Floating Side Navigation Menu -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:sticky inset-y-0 lg:top-6 left-0 z-50 lg:z-10 w-80 lg:w-72 xl:w-80 shrink-0 bg-white/95 backdrop-blur-xl border-r lg:border border-sky-100 lg:rounded-3xl p-5 sm:p-6 shadow-2xl lg:shadow-[0_20px_50px_rgba(14,165,233,0.12)] flex flex-col justify-between h-full lg:h-[calc(100vh-3rem)] transition-transform duration-300 ease-in-out overflow-y-auto no-scrollbar"
            >
                <div class="space-y-6">
                    
                    <!-- Sidebar Brand Header + Mobile Close Button -->
                    <div class="flex items-center justify-between pb-4 border-b border-sky-100">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center shadow-md shadow-sky-500/20 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" fill="currentColor" fill-opacity="0.25" stroke="currentColor" stroke-width="2"/>
                                    <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <span class="text-slate-900 font-extrabold text-base tracking-tight block">Skill Marketplace</span>
                                <span class="text-xs text-sky-700 font-bold uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-sky-500 animate-pulse"></span>
                                    Verified Member Portal
                                </span>
                            </div>
                        </a>

                        <!-- Mobile Close Cross -->
                        <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-slate-900 p-2 rounded-2xl hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Close menu">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Large Senior-Friendly "+ Post Task / Hire" Action Button -->
                    <div>
                        <a href="#post-opportunity" @click="sidebarOpen = false" class="w-full flex items-center justify-center gap-2.5 bg-gradient-to-r from-sky-500 via-sky-600 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-extrabold text-sm py-3.5 px-4 rounded-2xl shadow-lg shadow-sky-500/25 hover:shadow-sky-500/40 hover:scale-[1.01] active:scale-[0.99] transition-all group">
                            <svg class="w-5 h-5 text-sky-100 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                            <span>Post Task or Find Tutor</span>
                        </a>
                    </div>

                    <!-- Senior User Welcome Pill -->
                    <div class="bg-sky-50/80 border border-sky-200/80 rounded-2xl p-4 flex items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="relative">
                                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-sky-600 to-blue-600 text-white font-black flex items-center justify-center text-base shadow-md shadow-sky-600/20 shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                                <span class="w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-0.5 -right-0.5 shadow-xs"></span>
                            </div>
                            <div class="min-w-0">
                                <h5 class="text-sm font-extrabold text-slate-900 truncate">{{ $user->name ?? 'User' }}</h5>
                                <span class="text-xs text-sky-800 font-bold block truncate">{{ $user->location ?? 'Nigeria' }} • Online</span>
                            </div>
                        </div>
                    </div>

                    <!-- Explicit Senior Navigation Sections -->
                    <nav class="space-y-6">
                        
                        <!-- Module 1: MAIN NAVIGATION -->
                        <div class="space-y-2">
                            <div class="px-3 flex items-center justify-between text-xs font-black tracking-wider text-slate-500 uppercase">
                                <span>Main Menu</span>
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                            </div>
                            
                            <!-- Dashboard (Active) -->
                            <a href="{{ url('/dashboard') }}" class="flex items-center justify-between px-4 py-3 rounded-2xl bg-gradient-to-r from-sky-500 via-sky-600 to-blue-600 text-white shadow-md shadow-sky-500/20 text-sm font-extrabold transition-all">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-white/20 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">Home Dashboard</span>
                                        <span class="text-[11px] text-sky-100 font-medium block">Overview & account status</span>
                                    </div>
                                </div>
                            </a>

                            <!-- Messages Link -->
                            <a href="#messages" @click="sidebarOpen = false" class="flex items-center justify-between px-4 py-3 rounded-2xl text-slate-700 hover:text-sky-800 hover:bg-sky-50 text-sm font-bold transition-all group">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-sky-100 text-slate-500 group-hover:text-sky-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">My Messages</span>
                                        <span class="text-[11px] text-slate-500 font-medium block">Chat with tutors & pros</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-sky-100 text-sky-800 border border-sky-200">3 New</span>
                            </a>

                            <!-- Connection Requests Link -->
                            <a href="#requests" @click="sidebarOpen = false" class="flex items-center justify-between px-4 py-3 rounded-2xl text-slate-700 hover:text-sky-800 hover:bg-sky-50 text-sm font-bold transition-all group">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-amber-100 text-slate-500 group-hover:text-amber-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">Connection Requests</span>
                                        <span class="text-[11px] text-slate-500 font-medium block">Approvals & hiring</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-amber-100 text-amber-900 border border-amber-200">1 New</span>
                            </a>
                        </div>

                        <!-- Module 2: FIND SERVICES & TUTORS -->
                        <div class="space-y-2">
                            <div class="px-3 flex items-center justify-between text-xs font-black tracking-wider text-slate-500 uppercase">
                                <span>Find Help & Tutors</span>
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                            </div>

                            <!-- Browse Directory -->
                            <a href="{{ url('/#freelancers') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-4 py-3 rounded-2xl text-slate-700 hover:text-sky-800 hover:bg-sky-50 text-sm font-bold transition-all group">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-sky-100 text-slate-500 group-hover:text-sky-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">Local Pro Directory</span>
                                        <span class="text-[11px] text-slate-500 font-medium block">Plumbers, electricians & pros</span>
                                    </div>
                                </div>
                            </a>

                            <!-- Academic Tutoring Hub -->
                            <a href="{{ url('/#categories') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-4 py-3 rounded-2xl text-slate-700 hover:text-sky-800 hover:bg-sky-50 text-sm font-bold transition-all group">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-sky-100 text-slate-500 group-hover:text-sky-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">Academic Tutoring</span>
                                        <span class="text-[11px] text-slate-500 font-medium block">Math, English & exam tutors</span>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Module 3: ACCOUNT & SETTINGS -->
                        <div class="space-y-2">
                            <div class="px-3 flex items-center justify-between text-xs font-black tracking-wider text-slate-500 uppercase">
                                <span>My Account</span>
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                            </div>

                            <!-- Opportunities -->
                            <a href="#opportunities" @click="sidebarOpen = false" class="flex items-center justify-between px-4 py-3 rounded-2xl text-slate-700 hover:text-sky-800 hover:bg-sky-50 text-sm font-bold transition-all group">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-sky-100 text-slate-500 group-hover:text-sky-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">My Postings</span>
                                        <span class="text-[11px] text-slate-500 font-medium block">Your active work orders</span>
                                    </div>
                                </div>
                            </a>

                            <!-- Profile Settings -->
                            <a href="#complete-profile" @click="sidebarOpen = false" class="flex items-center justify-between px-4 py-3 rounded-2xl text-slate-700 hover:text-sky-800 hover:bg-sky-50 text-sm font-bold transition-all group">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-7 h-7 rounded-xl bg-slate-100 group-hover:bg-sky-100 text-slate-500 group-hover:text-sky-600 flex items-center justify-center transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    </div>
                                    <div>
                                        <span class="block">Profile & Settings</span>
                                        <span class="text-[11px] text-slate-500 font-medium block">Update phone & location</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </nav>

                </div>

                <!-- Senior Assistance Callout & Logout Button -->
                <div class="pt-4 mt-6 border-t border-sky-100 space-y-3 shrink-0">
                    
                    <!-- Senior Customer Support Pill -->
                    <div class="bg-gradient-to-r from-sky-500/10 to-blue-600/10 border border-sky-200 rounded-2xl p-4 text-left space-y-2">
                        <div class="flex items-center gap-2 text-sky-950 font-extrabold text-xs">
                            <span class="text-base">📞</span>
                            <span>Need Help or Phone Support?</span>
                        </div>
                        <p class="text-[11px] text-slate-600 leading-relaxed font-medium">
                            Our team is here to assist you step-by-step with setting up your profile or finding trusted pros.
                        </p>
                        <a href="tel:+2348000000000" class="inline-block text-xs font-black text-sky-700 hover:text-sky-900 underline">
                            Call Support Line →
                        </a>
                    </div>

                    <!-- Sign Out Button Form -->
                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-4 py-3 rounded-2xl text-xs font-extrabold text-slate-700 hover:text-rose-700 bg-slate-100 hover:bg-rose-50 border border-slate-200 hover:border-rose-200 transition-all cursor-pointer group">
                            <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Sign Out Account</span>
                        </button>
                    </form>

                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 min-w-0 space-y-6">
                
                <!-- Senior-Friendly Top Header Area -->
                <div class="bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-3xl p-4 sm:px-6 shadow-xs flex items-center justify-between gap-3 sm:gap-4">
                    
                    <!-- Left: Large Mobile Menu Button with Text for Seniors -->
                    <button 
                        @click="sidebarOpen = true" 
                        class="lg:hidden flex items-center gap-2 px-3.5 py-2.5 rounded-2xl border border-sky-200 bg-sky-50 text-sky-800 font-extrabold text-xs hover:bg-sky-100 shrink-0 cursor-pointer transition-colors"
                        aria-label="Open menu"
                    >
                        <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>MENU</span>
                    </button>

                    <!-- Large Clear Search Bar -->
                    <div class="relative flex-1 max-w-xl">
                        <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Type to search tutors, plumbers, electricians..." 
                            class="w-full bg-slate-50 border border-slate-300 text-sm sm:text-base text-slate-900 font-medium rounded-2xl pl-11 pr-4 py-3 outline-none focus:border-sky-600 focus:bg-white focus:ring-4 focus:ring-sky-100 transition-all"
                        />
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center gap-3 shrink-0">
                        <a href="#notifications" class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl border border-slate-200 hover:border-sky-300 bg-slate-50 hover:bg-sky-50 flex items-center justify-center text-slate-700 hover:text-sky-700 relative transition-all" title="Notifications">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span class="w-3 h-3 rounded-full bg-sky-500 absolute top-2 right-2 ring-2 ring-white"></span>
                        </a>

                        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-gradient-to-tr from-sky-600 to-blue-600 text-white font-black flex items-center justify-center text-sm shadow-xs">
                            {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                </div>

                <!-- Session Alert Banner -->
                @if (session('status'))
                    <div class="bg-sky-50 border-2 border-sky-300 text-sky-950 text-sm font-extrabold rounded-2xl p-4 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-sky-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Clear Welcome Banner for Senior Adults -->
                <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-sky-950 text-white rounded-3xl p-6 sm:p-8 lg:p-10 shadow-xl relative overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                    <div class="space-y-4 max-w-2xl relative z-10">
                        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-sky-500/20 border border-sky-400/30 text-sky-200 text-xs sm:text-sm font-extrabold">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-400 animate-ping"></span>
                            <span>Step 1: Finish Account Setup</span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white tracking-tight leading-tight">
                            Welcome, {{ $user->name }}!
                        </h2>

                        <p class="text-slate-200 text-sm sm:text-base leading-relaxed">
                            We are happy to have you! Click the button below to complete your profile, list your academic tutoring or trade skills, or hire verified local help near you.
                        </p>

                        <!-- Clear Senior Progress Bar -->
                        <div class="pt-2 max-w-md">
                            <div class="flex items-center justify-between text-sm font-extrabold mb-2 text-sky-200">
                                <span>Profile Completion</span>
                                <span>45% Complete</span>
                            </div>
                            <div class="w-full bg-slate-800 rounded-full h-3 overflow-hidden border border-slate-700">
                                <div class="bg-gradient-to-r from-sky-400 to-blue-500 h-full rounded-full w-[45%]"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Prominent Senior Action Button -->
                    <div class="relative z-10 shrink-0 w-full md:w-auto">
                        <a href="#complete-profile" class="w-full md:w-auto inline-flex items-center justify-center bg-white text-slate-950 font-black text-sm sm:text-base px-7 py-4 rounded-2xl hover:bg-sky-50 hover:text-sky-950 transition-all shadow-lg hover:scale-105 active:scale-95 border-2 border-white">
                            Complete Profile Now →
                        </a>
                    </div>
                </div>

                <!-- High-Legibility Stats Overview Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Stat 1: Connections -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between text-slate-600 text-xs sm:text-sm font-extrabold">
                            <span>Active Connections</span>
                            <div class="w-9 h-9 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-slate-900">0</div>
                        <span class="text-xs text-slate-500 font-bold block">0 pending requests</span>
                    </div>

                    <!-- Stat 2: Postings -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between text-slate-600 text-xs sm:text-sm font-extrabold">
                            <span>Posted Tasks</span>
                            <div class="w-9 h-9 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-slate-900">0</div>
                        <span class="text-xs text-slate-500 font-bold block">Ready for replies</span>
                    </div>

                    <!-- Stat 3: Ratings -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between text-slate-600 text-xs sm:text-sm font-extrabold">
                            <span>Trust Score</span>
                            <div class="w-9 h-9 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <span class="text-amber-600 text-base font-black">★</span>
                            </div>
                        </div>
                        <div class="text-3xl sm:text-4xl font-black text-slate-900">5.0</div>
                        <span class="text-xs text-slate-500 font-bold block">Verified member rating</span>
                    </div>

                    <!-- Stat 4: Role -->
                    <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 shadow-2xs space-y-3">
                        <div class="flex items-center justify-between text-slate-600 text-xs sm:text-sm font-extrabold">
                            <span>Account Type</span>
                            <div class="w-9 h-9 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                        <div class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight truncate">
                            {{ $user->onboarding_intent ?? 'Client / Talent' }}
                        </div>
                        <span class="text-xs text-sky-700 font-extrabold block">Verified Status</span>
                    </div>

                </div>

                <!-- Senior-Friendly Setup & Action Cards -->
                <section id="complete-profile" class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-8 shadow-xs space-y-6">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mb-2">
                            Choose Your Setup Option
                        </h3>
                        <p class="text-sm sm:text-base text-slate-700 max-w-2xl leading-relaxed">
                            Click on one of the two clear options below to set up your profile or start looking for verified services.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Professional / Academic Tutor Setup Card -->
                        <div class="border-2 border-slate-200 hover:border-sky-600 rounded-3xl p-6 sm:p-7 transition-all bg-sky-50/30 flex flex-col justify-between space-y-6">
                            <div class="space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-sky-600 text-white flex items-center justify-center text-xl font-bold shadow-md">
                                    🎓
                                </div>
                                <h4 class="text-lg font-extrabold text-slate-900">Option 1: I offer services or academic tutoring</h4>
                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                                    Set your hourly rate, select subject areas or trade skills (like carpentry or tutoring), and connect with local clients.
                                </p>
                            </div>
                            <a href="{{ url('/onboarding/professional') }}" class="w-full text-center bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm py-4 px-5 rounded-2xl shadow-md transition-all border-2 border-sky-600">
                                Set Up Service Profile →
                            </a>
                        </div>

                        <!-- Client / Hirer Setup Card -->
                        <div class="border-2 border-slate-200 hover:border-sky-600 rounded-3xl p-6 sm:p-7 transition-all bg-slate-50 flex flex-col justify-between space-y-6">
                            <div class="space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-slate-900 text-white flex items-center justify-center text-xl font-bold shadow-md">
                                    🤝
                                </div>
                                <h4 class="text-lg font-extrabold text-slate-900">Option 2: I want to hire tutors or trade pros</h4>
                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed font-medium">
                                    Browse verified plumbers, electricians, or academic tutors for your grandchildren, and request assistance directly.
                                </p>
                            </div>
                            <a href="{{ url('/#freelancers') }}" class="w-full text-center bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-sm py-4 px-5 rounded-2xl shadow-md transition-all border-2 border-slate-900">
                                Browse Pro Directory →
                            </a>
                        </div>

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
