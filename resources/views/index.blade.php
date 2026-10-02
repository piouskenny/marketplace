<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ searchQuery: '{{ $searchQuery ?? '' }}', selectedCategory: '{{ $selectedCategory ?? 'All' }}', mobileMenuOpen: false }" x-on:keydown.escape.window="mobileMenuOpen = false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>{{ config('app.name', 'Skill Link NG') }} — Powered by CSISS | Find Skilled Talent & Tutors Near You</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet" />

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
            [x-cloak] { display: none !important; }

            /* ── Desktop Navbar Wrapper (Floating Pill — visible on md+) ── */
            .desktop-nav-wrapper {
                display: none;
                position: sticky;
                top: 1rem;
                z-index: 50;
                width: 100%;
                max-width: 80rem;
                margin-left: auto;
                margin-right: auto;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            @media (min-width: 768px) {
                .desktop-nav-wrapper { display: block !important; }
                .site-navbar        { display: none !important; }
                .drawer-overlay     { display: none !important; }
                .drawer-panel       { display: none !important; }
            }
            @media (max-width: 767.98px) {
                .desktop-nav-wrapper { display: none !important; }
                .site-navbar        { display: flex !important; }
            }

            /* ── Mobile Navbar ── */
            .site-navbar {
                position: sticky;
                top: 0;
                z-index: 50;
                background: #ffffff;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 1rem;
                height: 64px;
            }

            /* hamburger button */
            .nav-hamburger {
                display: flex;
                flex-direction: column;
                justify-content: center;
                gap: 5px;
                width: 40px;
                height: 40px;
                cursor: pointer;
                border: none;
                background: transparent;
                padding: 6px;
                border-radius: 8px;
                transition: background 0.15s;
            }
            .nav-hamburger:hover { background: #f1f5f9; }
            .nav-hamburger span {
                display: block;
                height: 2.5px;
                width: 24px;
                background: #0f172b;
                border-radius: 2px;
                transition: all 0.25s ease;
                transform-origin: center;
            }
            /* animate to X when open */
            .nav-hamburger.is-open span:nth-child(1) { transform: translateY(7.5px) rotate(45deg); }
            .nav-hamburger.is-open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
            .nav-hamburger.is-open span:nth-child(3) { transform: translateY(-7.5px) rotate(-45deg); }

            /* nav logo alone (mobile) */
            .nav-logo {
                display: flex;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                position: absolute;
                left: 45%;
                transform: translateX(-50%);
            }
            .nav-logo img {
                height: 40px;
                width: auto;
                max-width: 175px;
                object-fit: contain;
                border-radius: 0;
                box-shadow: none;
                transition: transform 0.2s ease;
            }
            .nav-logo img:hover {
                transform: scale(1.04);
            }
            .nav-logo-text { font-size: 1rem; font-weight: 800; color: #0f172b; letter-spacing: -0.01em; }

            /* right CTA button */
            .nav-cta {
                text-decoration: none;
                font-size: 0.75rem;
                font-weight: 700;
                padding: 0.5rem 1.1rem;
                border-radius: 9999px;
                background: #2563eb;
                color: #ffffff;
                transition: background 0.15s, transform 0.15s;
                white-space: nowrap;
            }
            .nav-cta:hover { background: #1d4ed8; transform: scale(1.04); }

            /* ── Drawer Overlay ── */
            .drawer-overlay {
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.35);
                z-index: 49;
                backdrop-filter: blur(2px);
            }

            /* ── Drawer Panel ── */
            .drawer-panel {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                width: min(340px, 90vw);
                background: #ffffff;
                z-index: 60;
                display: flex;
                flex-direction: column;
                box-shadow: 6px 0 30px rgba(0,0,0,0.12);
                transform: translateX(-100%);
                transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
            }
            .drawer-panel.is-open { transform: translateX(0); }

            /* drawer header */
            .drawer-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0 1.25rem;
                height: 64px;
                border-bottom: 1px solid #f1f5f9;
            }
            .drawer-close {
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                border: none;
                background: transparent;
                cursor: pointer;
                border-radius: 8px;
                font-size: 1.25rem;
                color: #64748b;
                transition: background 0.15s;
            }
            .drawer-close:hover { background: #f1f5f9; color: #0f172b; }

            /* nav links inside drawer */
            .drawer-nav {
                flex: 1;
                padding: 1rem 0;
                overflow-y: auto;
            }
            .drawer-nav a {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 0.85rem 1.5rem;
                font-size: 1rem;
                font-weight: 500;
                color: #1e293b;
                text-decoration: none;
                transition: background 0.12s;
            }
            .drawer-nav a:hover { background: #f8fafc; color: #2563eb; }
            .drawer-nav a svg { color: #94a3b8; }

            /* drawer footer */
            .drawer-footer {
                padding: 1rem 1.25rem 1.5rem;
                border-top: 1px solid #f1f5f9;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .drawer-login-btn {
                flex: 0 0 auto;
                font-size: 0.875rem;
                font-weight: 600;
                color: #1e293b;
                text-decoration: none;
                padding: 0.65rem 1.1rem;
                border-radius: 9999px;
                transition: background 0.15s;
            }
            .drawer-login-btn:hover { background: #f1f5f9; }
            .drawer-signup-btn {
                flex: 1;
                text-align: center;
                font-size: 0.875rem;
                font-weight: 700;
                color: #ffffff;
                background: #2563eb;
                text-decoration: none;
                padding: 0.7rem 1rem;
                border-radius: 9999px;
                transition: background 0.15s, transform 0.15s;
            }
            .drawer-signup-btn:hover { background: #1d4ed8; transform: scale(1.02); }
            .drawer-dashboard-btn {
                flex: 1;
                text-align: center;
                font-size: 0.875rem;
                font-weight: 700;
                color: #ffffff;
                background: #0f172b;
                text-decoration: none;
                padding: 0.7rem 1rem;
                border-radius: 9999px;
                transition: background 0.15s;
            }
            .drawer-dashboard-btn:hover { background: #1e293b; }
        </style>
    </head>
    <body class="bg-[#F8FAFC] font-sans antialiased text-slate-900 selection:bg-[#0F172B] selection:text-white">

        <!-- ══════════════════════════════════════════════
             HERO WRAPPER CONTAINER (hosts Navbar + Hero Section + Dark Background Image Overlay)
        ══════════════════════════════════════════════ -->
        <div class="relative w-full overflow-hidden bg-[#F8FAFC]">

            <!-- 100% Width Dark Hero Background Image Backdrop (Spans top of page behind Navbar down to ~40% of dashboard screen container) -->
            <div class="absolute inset-x-0 top-0 w-full h-[85%] overflow-hidden pointer-events-none z-0">
                <img src="{{ asset('images/hero_workers_bg.png') }}" alt="Skill Link NG Professional Workers" class="w-full h-full object-cover object-top opacity-35 mix-blend-luminosity" />
                <!-- Dark Navy Overlay fading into light page background below -->
                <div class="absolute inset-0 bg-gradient-to-b from-[#0F172B]/95 via-[#0F172B]/85 to-[#F8FAFC]"></div>
            </div>
        <header class="site-navbar" role="banner">

            <!-- ① Hamburger icon (left) -->
            <button
                id="nav-hamburger-btn"
                class="nav-hamburger"
                :class="{ 'is-open': mobileMenuOpen }"
                @click="mobileMenuOpen = !mobileMenuOpen"
                aria-label="Toggle navigation menu"
                :aria-expanded="mobileMenuOpen.toString()"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- ② Logo ALONE (centre, absolutely positioned, 44px big & visible) -->
            <a href="/" class="nav-logo" aria-label="{{ config('app.name', 'Skill Link NG') }} home">
                <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }} logo" />
            </a>

            <!-- ③ Right CTA: Dashboard (auth) or Sign up (guest) -->
            @auth
                <a href="{{ url('/dashboard') }}" class="nav-cta" style="background:#0f172b;" id="nav-dashboard-btn-mobile">
                    Dashboard
                </a>
            @else
                <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="nav-cta" id="nav-signup-btn-mobile">
                    Sign up
                </a>
            @endauth

        </header>

        <!-- ══════════════════════════════════════════════
             DRAWER OVERLAY  (mobile only)
        ══════════════════════════════════════════════ -->
        <div
            x-cloak
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="drawer-overlay"
            @click="mobileMenuOpen = false"
            aria-hidden="true"
        ></div>

        <!-- ══════════════════════════════════════════════
             DRAWER PANEL  (mobile only)
        ══════════════════════════════════════════════ -->
        <nav
            id="mobile-drawer"
            class="drawer-panel"
            :class="{ 'is-open': mobileMenuOpen }"
            aria-label="Mobile navigation"
            aria-hidden="!mobileMenuOpen"
            role="navigation"
        >
            <!-- Drawer header -->
            <div class="drawer-header">
                <a href="/" class="nav-logo" style="position:static;transform:none;" @click="mobileMenuOpen = false">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="Skill Link NG" />
                    <span class="nav-logo-text">Skill Link NG</span>
                </a>
                <button class="drawer-close" @click="mobileMenuOpen = false" aria-label="Close menu">✕</button>
            </div>

            <!-- Drawer nav links -->
            <div class="drawer-nav">
                <a href="/" @click="mobileMenuOpen = false">
                    <span>Home</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="#find-talent" @click="mobileMenuOpen = false" class="text-[#2563eb] font-bold">
                    <span>Find Talent Near You</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="#how-it-works" @click="mobileMenuOpen = false">
                    <span>How It Works</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="#why-us" @click="mobileMenuOpen = false">
                    <span>Why Choose Us</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="#categories" @click="mobileMenuOpen = false">
                    <span>Categories</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </a>
                <a href="#featured-jobs" @click="mobileMenuOpen = false">
                    <span>Featured Jobs</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                </a>
            </div>

            <!-- Drawer footer: Log in + Sign up / Dashboard -->
            <div class="drawer-footer">
                @auth
                    <a href="{{ url('/dashboard') }}" class="drawer-dashboard-btn" @click="mobileMenuOpen = false">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="drawer-login-btn" @click="mobileMenuOpen = false">
                        Log in
                    </a>
                    <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="drawer-signup-btn" @click="mobileMenuOpen = false">
                        Sign up
                    </a>
                @endauth
            </div>
        </nav>

        <!-- ══════════════════════════════════════════════
             DESKTOP NAVBAR — Floating Glassmorphism Pill (White Background)
        ══════════════════════════════════════════════ -->
        <div class="desktop-nav-wrapper sticky top-4 z-50 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <header class="bg-white/95 backdrop-blur-2xl border border-slate-200/90 shadow-xl shadow-slate-950/10 rounded-full px-5 sm:px-7 py-3 flex items-center justify-between gap-4 transition-all duration-300">

                <!-- Logo -->
                <a href="/" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-40 h-10 object-contain rounded-full shrink-0" />
                </a>

                <!-- Nav Links -->
                <nav class="flex items-center gap-6" aria-label="Desktop navigation">
                    <a href="#" class="text-xs font-bold text-[#0F172B]">Home</a>
                    <a href="#find-talent" class="text-xs font-bold text-[#2563EB] hover:text-[#1d4ed8] transition-colors flex items-center gap-1">
                        <span>Find Talent Near You</span>
                    </a>
                    <a href="#how-it-works" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">How It Works</a>
                    <a href="#why-us" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Why Choose Us</a>
                    <a href="#categories" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Categories</a>
                    <a href="#featured-jobs" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Featured Jobs</a>
                </nav>

                <!-- Action CTA Buttons -->
                <div class="flex items-center gap-3 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" id="nav-dashboard-btn-desktop" class="px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-lg shadow-slate-900/15 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2">
                            <span>Dashboard</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="text-xs font-semibold text-slate-700 hover:text-[#0F172B] px-4 py-2 rounded-full hover:bg-slate-100 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" id="nav-signup-btn-desktop" class="px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-lg shadow-slate-900/15 hover:scale-105 active:scale-95 transition-all duration-200">
                            Get Started
                        </a>
                    @endauth
                </div>

            </header>
        </div>

        <!-- Main Content Area -->
        <main class="space-y-28 sm:space-y-36 lg:space-y-44 pt-6 pb-32 overflow-hidden">

            <!-- Hero Section Body -->
            <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-14 pb-12 text-center space-y-12 z-10">

                <!-- Ambient Soft Background Glow Orbs -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[48rem] h-[28rem] bg-gradient-to-b from-blue-500/20 via-sky-500/10 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute top-40 right-10 w-80 h-80 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6 max-w-4xl mx-auto">
                    
                    <!-- Pre-Header Text -->
                    <div>
                        <span class="inline-flex items-center gap-2 px-4.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-widest bg-[#2563EB]/20 border border-[#2563EB]/40 text-blue-300 shadow-sm">
                            <span class="w-2 h-2 rounded-full bg-[#E8472A] animate-pulse"></span>
                            Powered by CSISS
                        </span>
                    </div>

                      <!-- Main Hero Title (Crisp Light Text with Tasteful Blue & Coral Accents) -->
                    <h1 class="text-4xl sm:text-6xl xl:text-7xl font-black text-white tracking-tight leading-[1.12]">
                        Find Verified <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-sky-300 to-[#2563EB]">Skilled Workers</span><br />
                        & Proffesionals Near You
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
                        Connecting households, property owners, and businesses with CSISS-vetted skilled labour workers (electricians, plumbers, carpenters, technicians) and qualified academic tutors near you.
                    </p>

                    <!-- Hero Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                        <a href="{{ route('jobs.index') }}" class="px-8 py-3.5 text-sm font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-full shadow-xl shadow-blue-500/25 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2">
                            <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Search for Jobs</span>
                        </a>

                        <a href="{{ route('talent.index') }}" class="px-7 py-3.5 text-sm font-bold text-white bg-white/10 hover:bg-white/20 border border-white/20 backdrop-blur-md rounded-full shadow-md hover:shadow-lg hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2.5">
                            <svg class="w-4.5 h-4.5 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <span>Hire a Professional</span>
                        </a>
                    </div>

                    <!-- Universal Job Search Field (Placed directly under the Hero Buttons) -->
                    <form action="{{ route('jobs.index') }}" method="GET" class="max-w-3xl mx-auto pt-5 relative z-30">
                        <div class="bg-white/95 backdrop-blur-xl p-2 sm:p-2.5 rounded-2xl sm:rounded-full shadow-2xl border border-slate-200/90 flex flex-col sm:flex-row items-center gap-2">
                            
                            <!-- Search Keyword Input -->
                            <div class="relative flex-1 w-full flex items-center pl-3.5 pr-2">
                                <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input 
                                    type="text" 
                                    name="query" 
                                    placeholder="Search jobs, skills, or trades (e.g. Electrician, Maths Tutor)..." 
                                    class="w-full bg-transparent text-slate-900 placeholder-slate-400 text-xs sm:text-sm font-medium px-3 py-2 outline-none border-none focus:ring-0"
                                />
                            </div>

                            <!-- Divider (desktop) -->
                            <div class="hidden sm:block w-px h-7 bg-slate-200"></div>

                            <!-- Location Input -->
                            <div class="relative w-full sm:w-44 flex items-center px-3">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                <input 
                                    type="text" 
                                    name="location" 
                                    placeholder="State / City" 
                                    class="w-full bg-transparent text-slate-900 placeholder-slate-400 text-xs sm:text-sm font-medium px-2 py-2 outline-none border-none focus:ring-0"
                                />
                            </div>

                            <!-- Search Action Button -->
                            <button type="submit" class="w-full sm:w-auto bg-[#2563EB] hover:bg-blue-600 active:scale-95 text-white font-bold text-xs sm:text-sm px-6 py-3 rounded-xl sm:rounded-full shadow-lg shadow-blue-500/25 transition-all duration-200 flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                                <span>Search Jobs</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Central Hero Dashboard Showcase with Floating Side Badges & Fading Bottom Effect -->
                <div class="relative z-10 max-w-5xl mx-auto pt-6">
                    
                    <!-- Floating Top Cards (Above Screenshot) -->
                    <div class="flex justify-between items-center max-w-4xl mx-auto px-4 mb-3 relative z-30">
                        <!-- Top Left Floating Card -->
                        <div class="bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl px-4 py-2.5 shadow-lg flex items-center gap-2.5 transform -rotate-2 hover:rotate-0 transition-transform">
                            <div class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                📊
                            </div>
                            <span class="text-xs font-bold text-slate-900">1,200+ Active Jobs</span>
                        </div>

                        <!-- Top Right Floating Card -->
                        <div class="bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl px-4 py-2.5 shadow-lg flex items-center gap-2.5 transform rotate-2 hover:rotate-0 transition-transform">
                            <div class="w-7 h-7 rounded-lg bg-sky-100 text-sky-800 flex items-center justify-center font-bold text-xs">
                                ✉️
                            </div>
                            <span class="text-xs font-bold text-slate-900">Direct Connect</span>
                        </div>
                    </div>

                    <!-- Main Dashboard Frame Outer Container -->
                    <div class="relative bg-white/90 backdrop-blur-xl border border-slate-200/90 rounded-[2.5rem] p-3 sm:p-5 shadow-[0_30px_80px_-20px_rgba(15,23,42,0.14)] overflow-hidden">
                        
                        <!-- Dashboard Screen Image -->
                        <div class="rounded-2xl overflow-hidden border border-slate-200/80 shadow-xs bg-slate-900 relative">
                            <img src="{{ asset('images/dashboard_screen.png') }}" alt="Skill Link NG Member Dashboard" class="w-full h-auto object-cover object-top max-h-[500px]" />
                        </div>

                        <!-- Smooth Fading Bottom Effect Overlay -->
                        <div class="absolute bottom-0 inset-x-0 h-48 bg-gradient-to-t from-[#F8FAFC] via-[#F8FAFC]/90 to-transparent pointer-events-none z-20"></div>

                        <!-- Bottom Floating Glass Pill Card 1 (Bottom Left) -->
                        <div class="absolute bottom-10 left-6 sm:left-10 bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl p-3 sm:p-3.5 shadow-xl hidden sm:flex items-center gap-3 z-30">
                            <div class="w-10 h-10 rounded-full bg-[#0F172B] text-white font-black text-xs flex items-center justify-center shrink-0">
                                4.9★
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-bold text-slate-900 block leading-tight">5,000+ Completed Tasks</span>
                                <span class="text-[10px] text-slate-500 font-medium block">Verified Community Ratings</span>
                            </div>
                        </div>

                        <!-- Bottom Floating Glass Pill Card 2 (Bottom Right) -->
                        <div class="absolute bottom-10 right-6 sm:right-10 bg-white/95 backdrop-blur-md border border-slate-200/90 rounded-2xl p-3 sm:p-3.5 shadow-xl hidden sm:flex items-center gap-3 z-30">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm shrink-0">
                                ✓
                            </div>
                            <div class="text-left">
                                <span class="text-xs font-bold text-slate-900 block leading-tight">CSISS Certified</span>
                                <span class="text-[10px] text-emerald-700 font-semibold block">100% Identity Verified</span>
                            </div>
                        </div>

                    </div>

                </div>

            </section>
        </div>

        <!-- Quick Actions Grid Section: Search Jobs, Hire Professional, Hire Skilled Worker -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 relative z-20">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                
                <!-- Card 1: Search for Jobs -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group hover:-translate-y-1">
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center text-[#2563EB] group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-[#2563EB] transition-colors">
                            Search for Job
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 font-normal leading-relaxed">
                            Browse active job listings, short-term contracts, and daily tasks posted by verified clients across Nigeria.
                        </p>
                    </div>
                    <a href="{{ route('jobs.index') }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-all">
                        <span>Search Jobs</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Card 2: Hire a Professional -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group hover:-translate-y-1">
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 14l9-5-9-5-9 5 9 5z"/><path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-emerald-600 transition-colors">
                            Hire a Professional
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 font-normal leading-relaxed">
                            Connect with certified academic tutors, consultants, software engineers, accountants, and industry experts.
                        </p>
                    </div>
                    <a href="{{ route('talent.index', ['talent_type' => 'professional']) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs sm:text-sm shadow-md transition-all">
                        <span>Hire a Professional</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Card 3: Hire a Skilled Worker Near You -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-6 group hover:-translate-y-1">
                    <div class="space-y-4">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 border border-orange-100 flex items-center justify-center text-[#E8472A] group-hover:scale-110 transition-transform">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 group-hover:text-[#E8472A] transition-colors">
                            Hire a Skilled Worker Near You
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 font-normal leading-relaxed">
                            Find CSISS-vetted electricians, plumbers, carpenters, mechanics, and local trade technicians near you.
                        </p>
                    </div>
                    <a href="{{ route('talent.index', ['talent_type' => 'skilled_labour']) }}" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-[#E8472A] hover:bg-orange-600 text-white font-bold text-xs sm:text-sm shadow-md transition-all">
                        <span>Find Skilled Worker</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

            </div>
        </section>



            <!-- Section: How It Works (Skill Marketplace 3 Cards Layout with Real Worker Photos) -->
            <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16 relative">
                
                <!-- Section Header with Pre-Header Pill Badge -->
                <div class="space-y-3 max-w-2xl mx-auto">
                    <div>
                        <span class="px-5 py-1.5 rounded-full text-xs font-extrabold bg-[#2563EB]/10 text-[#2563EB] border border-[#2563EB]/20 uppercase tracking-wider inline-block">
                            How it works
                        </span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">How It Works</h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal leading-relaxed max-w-xl mx-auto">
                        Get connected with verified academic tutors and skilled artisans across Nigeria in three simple steps.
                    </p>
                </div>

                <!-- 3 Cards Layout matching reference screenshot -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 text-center">
                    
                    <!-- Card 1: Create Profile -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.04)] hover:shadow-xl hover:border-[#2563EB]/40 transition-all duration-300 flex flex-col justify-between space-y-6 relative overflow-hidden group">
                        
                        <!-- Royal Blue Accent Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-[#2563EB] rounded-t-[2rem]"></div>

                        <!-- Top Visual Graphic Container (Real Tutor Photo) -->
                        <div class="h-60 rounded-2xl border border-slate-100 relative overflow-hidden group-hover:scale-[1.02] transition-transform">
                            <img src="{{ asset('images/tutor_photo.png') }}" alt="Academic Tutor" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172B]/80 via-transparent to-transparent flex items-end p-4">
                                <div class="text-left text-white space-y-0.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#2563EB] text-white inline-block">Academic Tutor</span>
                                    <h4 class="text-sm font-bold text-white">Teachers & Tutors</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Title & Description (Centered) -->
                        <div class="space-y-2 text-center">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Create Your Profile</h3>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed font-normal">
                                Register as a client looking to hire, or list your skills as a tutor or artisan. Set your location, trade category, subjects, and rate expectations.
                            </p>
                        </div>
                    </div>

                    <!-- Card 2: Search & Filter Talent -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.04)] hover:shadow-xl hover:border-[#E8472A]/40 transition-all duration-300 flex flex-col justify-between space-y-6 relative overflow-hidden group">
                        
                        <!-- Coral Red Accent Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-[#E8472A] rounded-t-[2rem]"></div>

                        <!-- Top Visual Graphic Container (Real Artisan Photo) -->
                        <div class="h-60 rounded-2xl border border-slate-100 relative overflow-hidden group-hover:scale-[1.02] transition-transform">
                            <img src="{{ asset('images/artisan_photo.png') }}" alt="Solar Artisan Technician" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172B]/80 via-transparent to-transparent flex items-end p-4">
                                <div class="text-left text-white space-y-0.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#E8472A] text-white inline-block">Certified Artisan</span>
                                    <h4 class="text-sm font-bold text-white">Electrical & Plumbing Pros</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Title & Description (Centered) -->
                        <div class="space-y-2 text-center">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Search & Filter Talent</h3>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed font-normal">
                                Explore verified academic tutors by subject (Math, WAEC, Coding) or skilled trade professionals (Plumbing, Electrical) near your neighborhood.
                            </p>
                        </div>
                    </div>

                    <!-- Card 3: Connect & Hire Safely -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.04)] hover:shadow-xl hover:border-[#0F172B]/40 transition-all duration-300 flex flex-col justify-between space-y-6 relative overflow-hidden group">
                        
                        <!-- Navy Accent Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1.5 bg-[#0F172B] rounded-t-[2rem]"></div>

                        <!-- Top Visual Graphic Container (Real Workers Team Photo) -->
                        <div class="h-60 rounded-2xl border border-slate-100 relative overflow-hidden group-hover:scale-[1.02] transition-transform">
                            <img src="{{ asset('images/workers_team_photo.png') }}" alt="Vetted Nigerian Workers" class="w-full h-full object-cover" />
                            <div class="absolute inset-0 bg-gradient-to-t from-[#0F172B]/85 via-transparent to-transparent flex items-end p-4">
                                <div class="text-left text-white space-y-0.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#0F172B] text-white border border-white/20 inline-block">CSISS Verified</span>
                                    <h4 class="text-sm font-bold text-white">Trusted Guild Professionals</h4>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Title & Description (Centered) -->
                        <div class="space-y-2 text-center">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Connect & Hire Safely</h3>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed font-normal">
                                Message professionals directly, discuss home tutoring schedules or trade project details, and hire with background assurance.
                            </p>
                        </div>
                    </div>

                </div>

            </section>

            <!-- Section: What Makes Us Different (Skill Marketplace Pixel-Perfect Layout) -->
            <section id="why-us" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-14 items-center">
                    
                    <!-- Left Column: Pre-Header Pill, Title, Subtitle, & 3 Stacked Feature Items -->
                    <div class="lg:col-span-6 space-y-6 text-left">
                        
                        <!-- Pre-Header Pill Badge matching reference -->
                        <div>
                            <span class="px-4 py-1.5 rounded-full text-xs font-extrabold bg-[#E8472A]/10 text-[#E8472A] border border-[#E8472A]/20 inline-block uppercase tracking-wider">
                                Why Choose Us
                            </span>
                        </div>

                        <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            What Makes Us Different
                        </h2>
                        
                        <p class="text-slate-500 text-sm sm:text-base leading-relaxed font-normal">
                            Built specifically for Nigerian households, property owners, parents, and businesses seeking vetted skilled labour workers, trade artisans, and academic tutors.
                        </p>

                        <!-- 3 Feature Items Stacked (Bold title + description) -->
                        <div class="space-y-6 pt-2">
                            
                            <!-- Item 1 -->
                            <div class="space-y-1">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#2563EB]"></span>
                                    CSISS Credential & Background Verification
                                </h4>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal pl-4">
                                    Every trade artisan, technician, and academic tutor undergoes identity checking and CSISS qualification verification, giving clients complete peace of mind for property repairs and home tutoring.
                                </p>
                            </div>

                            <!-- Item 2 -->
                            <div class="space-y-1">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#E8472A]"></span>
                                    Dual Skilled Trades & Academic Tutoring Focus
                                </h4>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal pl-4">
                                    Whether you need a solar electrician, plumber, auto mechanic, carpenter, WAEC Physics tutor, or language instructor, our platform specializes in both skilled labour trades and academic excellence.
                                </p>
                            </div>

                            <!-- Item 3 -->
                            <div class="space-y-1">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900 flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-[#0F172B]"></span>
                                    Direct Local Connections & Transparent Rates
                                </h4>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal pl-4">
                                    Find top-rated artisans and tutors in your immediate city (Lagos, Abuja, Port Harcourt, Ibadan, Enugu, & more). Contact workers directly with transparent rate expectations and community reviews.
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: 2x2 Floating Feature Cards Container with Soft Tinted Backdrop -->
                    <div class="lg:col-span-6 bg-gradient-to-tr from-slate-100 via-blue-50/40 to-slate-100 p-6 sm:p-9 rounded-[2.5rem] border border-slate-200 shadow-inner">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            
                            <!-- Card 1 (Smart Matches) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-sm font-extrabold text-slate-900">CSISS Match</h5>
                                    <div class="w-8 h-8 rounded-full bg-[#2563EB] text-white flex items-center justify-center font-bold text-[10px]">
                                        95%
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 pt-1">
                                    <img src="{{ asset('images/avatars/zainab.png') }}" class="w-7 h-7 rounded-full object-cover" />
                                    <div>
                                        <span class="text-[10px] font-bold text-slate-400 block leading-none">Recommended</span>
                                        <span class="text-xs font-bold text-slate-900">For Your Needs</span>
                                    </div>
                                </div>

                                <div class="bg-slate-50 rounded-2xl p-2.5 border border-slate-100 space-y-1.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-extrabold text-slate-900">WAEC Physics Tutor</span>
                                        <span class="w-5 h-5 rounded-full bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center text-[10px] font-bold">✓</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[9px] font-semibold text-slate-500">
                                        <span class="px-1.5 py-0.5 rounded bg-[#2563EB]/10 text-[#2563EB]">In-Person</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100">Online</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100">Lagos</span>
                                    </div>
                                </div>

                                <div class="text-[11px] font-extrabold text-[#2563EB] pt-1">
                                    95% <span class="text-slate-500 font-normal">Match Score</span>
                                </div>
                            </div>

                            <!-- Card 2 (CSISS Verification) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3 sm:translate-y-3">
                                <h5 class="text-sm font-extrabold text-slate-900">CSISS Verification</h5>

                                <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-[#E8472A]/10 text-[#E8472A] flex items-center justify-center text-xs font-bold shrink-0">
                                        ✓
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-slate-900 block truncate">Vetted_Profile.pdf</span>
                                        <span class="text-[9px] text-slate-400 font-medium block">Identity Verified</span>
                                    </div>
                                </div>

                                <div class="pt-1">
                                    <span class="w-full bg-[#E8472A] text-white font-bold text-xs py-2 px-4 rounded-full text-center block shadow-xs">
                                        Verification Approved
                                    </span>
                                </div>

                                <span class="text-[10px] text-[#E8472A] font-bold block text-center">
                                    ✓ 100% CSISS Credential Verified
                                </span>
                            </div>

                            <!-- Card 3 (Hiring Progress) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3">
                                <h5 class="text-sm font-extrabold text-slate-900">Connection Process</h5>

                                <!-- Stepper Bar -->
                                <div class="py-2 space-y-2">
                                    <div class="flex items-center justify-between relative">
                                        <div class="absolute inset-x-2 top-1/2 -translate-y-1/2 h-1 bg-slate-100 z-0"></div>
                                        <div class="absolute left-2 w-2/3 top-1/2 -translate-y-1/2 h-1 bg-[#2563EB] z-0"></div>

                                        <div class="w-4 h-4 rounded-full bg-[#2563EB] ring-2 ring-white z-10"></div>
                                        <div class="w-4 h-4 rounded-full bg-[#2563EB] ring-2 ring-white z-10"></div>
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-[#E8472A] z-10" />
                                        <div class="w-4 h-4 rounded-full bg-slate-200 ring-2 ring-white z-10"></div>
                                    </div>

                                    <div class="flex items-center justify-between text-[9px] font-bold text-slate-500 pt-1">
                                        <span>Search</span>
                                        <span>Inquire</span>
                                        <span class="text-[#2563EB]">Connected</span>
                                        <span>Hired</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4 (Session Scheduled) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3 sm:translate-y-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center text-sm font-bold shrink-0">
                                        📅
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-extrabold text-slate-900 block leading-tight">Session Scheduled</h5>
                                        <span class="text-[10px] text-[#2563EB] font-semibold block">Tomorrow at 10:30 AM</span>
                                    </div>
                                </div>

                                <div class="pt-2 flex items-center justify-center">
                                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#0F172B] text-white text-[11px] font-bold shadow-xs">
                                        <div class="w-4 h-4 rounded-full bg-white text-[#0F172B] flex items-center justify-center text-[9px] font-black">✓</div>
                                        <span>CSISS Approved</span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </section>

            <!-- Section: Location Selector & Public Discovery (Find Talent Near You) -->
            <section id="find-talent" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
                
                <!-- Section Header: Find Talent Near You -->
                <div class="text-center space-y-3 max-w-2xl mx-auto">
                    <div>
                        <span class="px-4 py-1.5 rounded-full text-xs font-extrabold bg-[#2563EB]/10 text-[#2563EB] border border-[#2563EB]/20 uppercase tracking-wider inline-block">
                            Location Search
                        </span>
                    </div>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">
                        Find Talent Near You
                    </h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal leading-relaxed">
                        Search and filter verified skilled labour workers, electricians, plumbers, technicians, artisans, and academic tutors by state, city, or neighborhood across Nigeria.
                    </p>
                </div>

                <!-- Location Selector Bar -->
                <x-location-selector :searchLocation="$searchLocation ?? []" :hasSelectedLocation="$hasSelectedLocation ?? false" />

                <!-- SECTION A: Skilled Labour Workers -->
                <div class="space-y-8">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200/80 pb-5">
                        <div>
                            <span class="text-[#E8472A] text-xs font-extrabold uppercase tracking-widest block">
                                VETTED HANDYMEN & ARTISANS
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-1">
                                @if(!empty($hasSelectedLocation) && (!empty($searchLocation['location_city']) || !empty($searchLocation['location_state'])))
                                    Find Skilled Labour Workers Near {{ $searchLocation['location_city'] ?? $searchLocation['location_state'] }}
                                @else
                                    Skilled Labour Workers
                                @endif
                            </h2>
                            <p class="text-slate-500 text-sm font-normal mt-1">
                                Experienced electricians, plumbers, technicians, and artisans verified by CSISS.
                            </p>
                        </div>
                        <a href="{{ route('talent.index', ['talent_type' => 'skilled_labour']) }}" class="px-5 py-2.5 rounded-full text-xs font-bold text-white bg-[#0F172B] hover:bg-[#2563EB] transition-all flex items-center gap-2 shrink-0 self-start md:self-auto shadow-sm">
                            <span>View all skilled workers</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <!-- Trade Shortcuts -->
                    @if(!empty($tradeCategories) && count($tradeCategories) > 0)
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Popular Trades:</span>
                            @foreach($tradeCategories->take(8) as $trade)
                                <a href="{{ route('talent.index', ['talent_type' => 'skilled_labour', 'trade_category_id' => $trade->id]) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200 hover:border-[#0F172B] text-slate-700 hover:text-[#0F172B] shadow-2xs transition-all shrink-0">
                                    {{ $trade->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Skilled Labour Talent Cards Grid -->
                    @if(!empty($skilledLabourWorkers) && count($skilledLabourWorkers) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($skilledLabourWorkers as $pro)
                                <x-talent-card :pro="$pro" talentType="skilled_labour" />
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white border border-slate-200/80 rounded-3xl p-10 text-center space-y-4 shadow-sm">
                            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl font-bold">🔧</div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">No skilled workers found in this area yet</h3>
                                <p class="text-xs text-slate-500 max-w-md mx-auto">Try expanding your location search or browse all skilled workers across Nigeria.</p>
                            </div>
                            <div>
                                <a href="{{ route('talent.index', ['talent_type' => 'skilled_labour']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-[#0F172B] rounded-full hover:bg-slate-800 transition-colors">
                                    Browse all skilled workers
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- SECTION B: Teachers -->
                <div class="space-y-8 pt-4">
                    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 border-b border-slate-200/80 pb-5">
                        <div>
                            <span class="text-[#2563EB] text-xs font-extrabold uppercase tracking-widest block">
                                ACADEMIC TUTORS & INSTRUCTORS
                            </span>
                            <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mt-1">
                                @if(!empty($hasSelectedLocation) && (!empty($searchLocation['location_city']) || !empty($searchLocation['location_state'])))
                                    Find Teachers Near {{ $searchLocation['location_city'] ?? $searchLocation['location_state'] }}
                                @else
                                    Find Teachers
                                @endif
                            </h2>
                            <p class="text-slate-500 text-sm font-normal mt-1">
                                Qualified home tutors, WAEC/JAMB educators, and private instructors.
                            </p>
                        </div>
                        <a href="{{ route('talent.index', ['talent_type' => 'teacher']) }}" class="px-5 py-2.5 rounded-full text-xs font-bold text-white bg-[#0F172B] hover:bg-[#2563EB] transition-all flex items-center gap-2 shrink-0 self-start md:self-auto shadow-sm">
                            <span>View all teachers</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <!-- Subject Shortcuts -->
                    @if(!empty($subjects) && count($subjects) > 0)
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider shrink-0 mr-1">Popular Subjects:</span>
                            @foreach($subjects->take(8) as $subj)
                                <a href="{{ route('talent.index', ['talent_type' => 'teacher', 'subject_id' => $subj->id]) }}" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200 hover:border-[#0F172B] text-slate-700 hover:text-[#0F172B] shadow-2xs transition-all shrink-0">
                                    {{ $subj->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Teacher Talent Cards Grid -->
                    @if(!empty($teachers) && count($teachers) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($teachers as $pro)
                                <x-talent-card :pro="$pro" talentType="teacher" />
                            @endforeach
                        </div>
                    @else
                        <div class="bg-white border border-slate-200/80 rounded-3xl p-10 text-center space-y-4 shadow-sm">
                            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl font-bold">🎓</div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">No teachers found in this area yet</h3>
                                <p class="text-xs text-slate-500 max-w-md mx-auto">Try expanding your location search or browse all qualified teachers across Nigeria.</p>
                            </div>
                            <div>
                                <a href="{{ route('talent.index', ['talent_type' => 'teacher']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-xs font-bold text-white bg-[#0F172B] rounded-full hover:bg-slate-800 transition-colors">
                                    Browse all teachers
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

            </section>

            <!-- Section: Categories (12 Categories Grid) -->
            <section id="categories" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16 relative">
                
                <div class="space-y-2 max-w-2xl mx-auto">
                    <span class="text-[#E8472A] text-xs font-extrabold uppercase tracking-widest block">
                        EXPLORE CATEGORIES
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">Popular Service Categories</h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal">Whatever the task or project, find experienced local skilled workers, trade artisans, handymen, and academic tutors ready to help in your area.</p>
                </div>

                <!-- 12-Card Category Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">

                    <!-- 1. Academic Tutoring -->
                    <a href="{{ url('/talent?category=Academic+Tutoring') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#2563EB]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#2563EB] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#2563EB] transition-colors">Academic Tutoring</h4>
                                <span class="text-xs text-slate-500 font-medium">874 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#2563EB] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 2. Exam Prep & Languages -->
                    <a href="{{ url('/talent?category=Exam+Prep') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#E8472A]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#E8472A]/10 text-[#E8472A] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#E8472A] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#E8472A] transition-colors">Exam Prep & Languages</h4>
                                <span class="text-xs text-slate-500 font-medium">620 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#E8472A] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 3. STEM & Sciences -->
                    <a href="{{ url('/talent?category=STEM') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#0F172B]/10 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3" stroke-width="2"/><ellipse cx="12" cy="12" rx="7" ry="3" stroke-width="1.8" transform="rotate(30 12 12)"/><ellipse cx="12" cy="12" rx="7" ry="3" stroke-width="1.8" transform="rotate(-30 12 12)"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">STEM & Sciences</h4>
                                <span class="text-xs text-slate-500 font-medium">412 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 4. Plumbing -->
                    <a href="{{ url('/talent?category=Plumbing') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#2563EB]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#2563EB] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 011 1V4z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#2563EB] transition-colors">Plumbing</h4>
                                <span class="text-xs text-slate-500 font-medium">542 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#2563EB] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 5. Electrical -->
                    <a href="{{ url('/talent?category=Electrical') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#E8472A]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#E8472A]/10 text-[#E8472A] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#E8472A] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#E8472A] transition-colors">Electrical</h4>
                                <span class="text-xs text-slate-500 font-medium">389 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#E8472A] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 6. Carpentry -->
                    <a href="{{ url('/talent?category=Carpentry') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#0F172B]/10 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Carpentry</h4>
                                <span class="text-xs text-slate-500 font-medium">210 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 7. Painting -->
                    <a href="{{ url('/talent?category=Painting') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#2563EB]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#2563EB] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#2563EB] transition-colors">Painting</h4>
                                <span class="text-xs text-slate-500 font-medium">401 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#2563EB] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 8. Cleaning -->
                    <a href="{{ url('/talent?category=Cleaning') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#E8472A]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#E8472A]/10 text-[#E8472A] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#E8472A] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#E8472A] transition-colors">Cleaning</h4>
                                <span class="text-xs text-slate-500 font-medium">625 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#E8472A] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 9. Photography -->
                    <a href="{{ url('/talent?category=Photography') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#0F172B]/10 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Photography</h4>
                                <span class="text-xs text-slate-500 font-medium">194 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 10. Web Development -->
                    <a href="{{ url('/talent?category=Web+Development') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#2563EB]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#2563EB]/10 text-[#2563EB] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#2563EB] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#2563EB] transition-colors">Web Development</h4>
                                <span class="text-xs text-slate-500 font-medium">488 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#2563EB] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 11. Graphic Design -->
                    <a href="{{ url('/talent?category=Graphic+Design') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#E8472A]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#E8472A]/10 text-[#E8472A] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#E8472A] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#E8472A] transition-colors">Graphic Design</h4>
                                <span class="text-xs text-slate-500 font-medium">350 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#E8472A] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 12. Music & Instrument Tutors -->
                    <a href="{{ url('/talent?category=Music') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-50 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B]/40 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-[#0F172B]/10 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 .895-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 .895-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Music & Instruments</h4>
                                <span class="text-xs text-slate-500 font-medium">280 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                </div>

            </section>

            <!-- Section: Top Featured Jobs (Live from Database) -->
            <section id="featured-jobs" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16 relative">
                
                <!-- Section Header -->
                <div class="space-y-3 max-w-2xl mx-auto">
                    <div>
                        <span class="px-5 py-1.5 rounded-full text-xs font-extrabold bg-[#E8472A]/10 text-[#E8472A] border border-[#E8472A]/20 inline-block uppercase tracking-wider">
                            Featured Jobs
                        </span>
                    </div>
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">Top Featured Jobs</h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal leading-relaxed max-w-xl mx-auto">
                        Explore the latest project requests and job posts — skilled labour workers (electricians, plumbers, carpenters, mechanics), trade artisans, and tutors welcome.
                    </p>
                </div>

                <!-- 6 Featured Job Cards Grid (Dynamic from DB) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 text-left">

                    @forelse($featuredJobs as $index => $job)

                        @php
                            $isFirst    = $loop->first;
                            $budgetText = null;
                            if ($job->budget_min && $job->budget_max) {
                                $budgetText = '₦' . number_format($job->budget_min) . ' – ₦' . number_format($job->budget_max);
                            } elseif ($job->budget_min) {
                                $budgetText = 'From ₦' . number_format($job->budget_min);
                            } elseif ($job->budget_max) {
                                $budgetText = 'Up to ₦' . number_format($job->budget_max);
                            }

                            // Generate a consistent avatar colour from the poster's name
                            $avatarColors  = ['#0F172B','#0369A1','#15803D','#7C3AED','#B45309','#BE123C','#0891B2'];
                            $colorIndex    = crc32($job->user->name ?? 'U') % count($avatarColors);
                            $avatarBg      = $avatarColors[abs($colorIndex)];
                            $initials      = strtoupper(substr($job->user->name ?? 'U', 0, 2));

                            $isExpired     = $job->application_deadline && $job->application_deadline->isPast();
                            $deadlineSoon  = $job->application_deadline && !$isExpired && $job->application_deadline->diffInDays(now()) <= 3;
                        @endphp

                        {{-- First card: highlighted (gradient + border) --}}
                        @if($isFirst)
                        <div class="bg-gradient-to-br from-sky-50/80 via-white to-slate-100 border-2 border-[#0F172B] rounded-[2rem] p-6 lg:p-7 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        @else
                        <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.03)] hover:shadow-xl hover:border-slate-400 transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        @endif

                            {{-- New badge for very recent posts --}}
                            @if($job->created_at->diffInHours(now()) <= 24)
                                <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wide">New</span>
                            @elseif($isExpired)
                                <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-red-100 text-red-600 text-[10px] font-bold uppercase tracking-wide">Closed</span>
                            @elseif($deadlineSoon)
                                <span class="absolute top-4 right-4 px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-700 text-[10px] font-bold uppercase tracking-wide">Closing Soon</span>
                            @endif

                            <!-- Top Row: Poster avatar + info -->
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-full text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-sm" style="background:{{ $avatarBg }}">
                                    {{ $initials }}
                                </div>
                                <div class="min-w-0">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight truncate">{{ $job->user->name ?? 'Anonymous' }}</h4>
                                    <span class="text-xs text-slate-400 font-medium block truncate">{{ $job->location }}</span>
                                </div>
                            </div>

                            <!-- Job Title -->
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 tracking-tight line-clamp-2">{{ $job->title }}</h3>
                            </div>

                            <!-- Tags Row: Category + Type -->
                            <div class="flex flex-wrap items-center gap-2">
                                @if($job->category)
                                    <span class="px-3.5 py-1 rounded-full bg-white border border-slate-200/70 text-[11px] font-semibold text-[#0F172B] shadow-2xs">{{ $job->category->name }}</span>
                                @endif
                                @if($job->opportunity_type)
                                    <span class="px-3.5 py-1 rounded-full bg-slate-100 text-[11px] font-semibold text-slate-600">{{ ucfirst(str_replace('_', ' ', $job->opportunity_type)) }}</span>
                                @endif
                            </div>

                            <!-- Description snippet -->
                            <p class="text-xs text-slate-500 leading-relaxed line-clamp-2">{{ $job->description }}</p>

                            <!-- Bottom Row: Budget, applicants count & Apply CTA -->
                            <div class="pt-2 flex items-end justify-between border-t {{ $isFirst ? 'border-slate-200' : 'border-slate-100' }}">
                                <div class="space-y-1">
                                    @if($budgetText)
                                        <div class="text-xs font-bold text-slate-900">{{ $budgetText }}</div>
                                    @else
                                        <div class="text-xs font-medium text-slate-400 italic">Budget negotiable</div>
                                    @endif
                                    <div class="flex items-center gap-1.5 text-[10px] text-slate-500 font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $job->connection_requests_count }} applicant{{ $job->connection_requests_count !== 1 ? 's' : '' }}
                                        @if($job->application_deadline && !$isExpired)
                                            &nbsp;·&nbsp; Closes {{ $job->application_deadline->diffForHumans() }}
                                        @endif
                                    </div>
                                </div>

                                @if($isFirst)
                                    <a href="{{ url('/dashboard/my-jobs') }}" class="bg-gradient-to-r from-[#0F172B] to-[#1E293B] hover:from-[#1E293B] hover:to-[#0F172B] text-white rounded-full px-5 py-2 text-xs font-bold shadow-md hover:shadow-lg flex items-center gap-1.5 transition-all shrink-0">
                                        <span>Apply</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                @else
                                    <a href="{{ url('/dashboard/my-jobs') }}" class="text-slate-900 font-bold text-xs hover:text-sky-600 flex items-center gap-1 transition-colors group-hover:translate-x-0.5 shrink-0">
                                        <span>Apply</span>
                                        <span>→</span>
                                    </a>
                                @endif
                            </div>

                        </div>

                    @empty

                        <!-- Empty State -->
                        <div class="col-span-full bg-white border border-slate-200/80 rounded-3xl p-12 text-center space-y-4 shadow-sm">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-3xl">💼</div>
                            <div class="space-y-1">
                                <h3 class="text-lg font-bold text-slate-900">No featured jobs right now</h3>
                                <p class="text-sm text-slate-500 max-w-md mx-auto">Be the first to post an opportunity and connect with skilled talent across Nigeria.</p>
                            </div>
                            @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] rounded-full hover:bg-slate-800 transition-colors">
                                    Post an Opportunity
                                </a>
                            @else
                                <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="inline-flex items-center gap-2 px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] rounded-full hover:bg-slate-800 transition-colors">
                                    Get Started Free
                                </a>
                            @endauth
                        </div>

                    @endforelse

                </div>

                <!-- Bottom View More Button -->
                @if($featuredJobs->count() >= 6)
                <div class="pt-4">
                    <a href="{{ url('/dashboard/my-jobs') }}" class="inline-flex items-center justify-center px-9 py-3.5 text-sm font-bold text-white bg-[#2563EB] hover:bg-[#0F172B] rounded-full shadow-lg shadow-[#2563EB]/25 hover:shadow-slate-900/40 hover:scale-105 active:scale-95 transition-all text-center mx-auto">
                        View All Jobs
                    </a>
                </div>
                @endif

            </section>


            <!-- Section: Built for Job Seekers & Employers (Dual Tinted Cards) -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Card 1: Job Seekers -->
                    <div class="bg-[#EEF4FF] border border-blue-200/80 rounded-[2.5rem] p-8 sm:p-10 shadow-sm space-y-6 flex flex-col justify-between">
                        <div class="space-y-3">
                            <span class="text-[#2563EB] text-xs font-extrabold uppercase tracking-widest block">FOR SKILLED WORKERS & TUTORS</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Built for Skilled Workers, Artisans & Tutors</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Connect directly with clients, property owners, and households looking for skilled handymen, electricians, plumbers, carpenters, mechanics, and private tutors near you.
                            </p>
                        </div>

                        <!-- Card Visual Graphic -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#2563EB] text-white flex items-center justify-center font-bold text-sm">
                                    🔍
                                </div>
                                <div class="text-left">
                                    <span class="text-xs font-bold text-slate-900 block">Search Work Opportunities</span>
                                    <span class="text-[10px] text-slate-500">100+ new jobs today</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#2563EB] bg-[#2563EB]/10 px-3 py-1 rounded-full">Active</span>
                        </div>

                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="inline-flex items-center justify-center px-7 py-3 text-xs font-bold text-white bg-[#2563EB] hover:bg-[#0F172B] rounded-full shadow-md transition-all self-start">
                            Create Worker Profile →
                        </a>
                    </div>

                    <!-- Card 2: Employers -->
                    <div class="bg-amber-50/50 border border-amber-200/80 rounded-[2.5rem] p-8 sm:p-10 shadow-sm space-y-6 flex flex-col justify-between">
                        <div class="space-y-3">
                            <span class="text-[#E8472A] text-xs font-extrabold uppercase tracking-widest block">FOR CLIENTS & PARENTS</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Built for Clients & Homeowners</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Post custom trade repair projects or tutoring tasks to receive bids from verified local artisans and teachers backed by CSISS credential standards.
                            </p>
                        </div>

                        <!-- Card Visual Graphic -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-amber-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#E8472A] text-white flex items-center justify-center font-bold text-sm">
                                    📋
                                </div>
                                <div class="text-left">
                                    <span class="text-xs font-bold text-slate-900 block">Post Custom Task</span>
                                    <span class="text-[10px] text-slate-500">Receive bids in 15 mins</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-[#E8472A] bg-[#E8472A]/10 px-3 py-1 rounded-full">Easy Post</span>
                        </div>

                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="inline-flex items-center justify-center px-7 py-3 text-xs font-bold text-white bg-[#E8472A] hover:bg-[#0F172B] rounded-full shadow-md transition-all self-start">
                            Post An Opportunity →
                        </a>
                    </div>

                </div>
            </section>

            <!-- Section: What Our Users Say (Skill Marketplace 5-Card Testimonial Grid matching Reference Screenshot) -->
            <section id="testimonials" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16">
                
                <div class="space-y-2 max-w-2xl mx-auto">
                    <span class="text-[#2563EB] text-xs font-extrabold uppercase tracking-widest block">
                        OUR REVIEWS
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">What Our Users Say</h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal">Real feedback from clients, parents, and verified professionals across Nigeria.</p>
                </div>

                <!-- 5-Card Layout matching Reference Screenshot -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left items-stretch">
                    
                    <!-- Left Column Stack (Review 1 & Review 2) -->
                    <div class="space-y-6 flex flex-col justify-between">
                        <!-- Review 1 -->
                        <div class="bg-white border border-slate-200/80 rounded-[2rem] p-6 shadow-xs space-y-3">
                            <div class="flex items-center gap-1 text-[#E8472A]">★★★★★</div>
                            <p class="text-slate-600 text-xs leading-relaxed font-normal">
                                "I found a fantastic WAEC Physics tutor in Ikeja within an hour. The reviews felt genuine, communication was smooth, and my daughter's score improved."
                            </p>
                            <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
                                <img src="{{ asset('images/avatars/funmi.png') }}" class="w-8 h-8 rounded-full object-cover" />
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900">Funmi Adewale</h5>
                                    <span class="text-[10px] text-slate-400">Verified Parent • Lagos</span>
                                </div>
                            </div>
                        </div>

                        <!-- Review 2 -->
                        <div class="bg-white border border-slate-200/80 rounded-[2rem] p-6 shadow-xs space-y-3">
                            <div class="flex items-center gap-1 text-[#E8472A]">★★★★★</div>
                            <p class="text-slate-600 text-xs leading-relaxed font-normal">
                                "Hired a certified electrical technician for solar panel installation. Prompt, professional, and excellent quality."
                            </p>
                            <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
                                <img src="{{ asset('images/avatars/emeka.png') }}" class="w-8 h-8 rounded-full object-cover" />
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900">Emeka Nwosu</h5>
                                    <span class="text-[10px] text-slate-400">Verified Client • Abuja</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Center Large Highlight Card (Matching Screenshot Featured Person Image) -->
                    <div class="bg-gradient-to-b from-[#0F172B] via-slate-900 to-slate-950 text-white rounded-[2.5rem] p-8 shadow-xl flex flex-col justify-between relative overflow-hidden border border-slate-800 min-h-[380px]">
                        <div class="space-y-3 relative z-10">
                            <span class="px-3 py-1 rounded-full bg-[#2563EB] text-white text-[10px] font-extrabold uppercase tracking-wider inline-block">FEATURED TUTOR STORY</span>
                            <div class="flex items-center gap-1 text-[#E8472A] pt-2">★★★★★</div>
                            <h4 class="text-xl font-black text-white leading-snug">
                                "Skill Link NG helped me build a full-time tutoring practice safely."
                            </h4>
                            <p class="text-slate-300 text-xs leading-relaxed">
                                "Protected phone numbers until clients accept gave me total safety. I now tutor 5 students weekly across Abuja."
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-white/20 relative z-10">
                            <img src="{{ asset('images/avatars/zainab.png') }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-[#2563EB]" />
                            <div>
                                <h5 class="text-xs font-bold text-white">Zainab Ibrahim</h5>
                                <span class="text-[11px] text-slate-300">Verified Math Tutor • Abuja</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column Stack (Review 4 & Review 5) -->
                    <div class="space-y-6 flex flex-col justify-between">
                        <!-- Review 4 -->
                        <div class="bg-white border border-slate-200/80 rounded-[2rem] p-6 shadow-xs space-y-3">
                            <div class="flex items-center gap-1 text-[#E8472A]">★★★★★</div>
                            <p class="text-slate-600 text-xs leading-relaxed font-normal">
                                "Great experience finding a private French tutor for my kids preparing for entrance exams."
                            </p>
                            <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
                                <img src="{{ asset('images/avatars/tunde.png') }}" class="w-8 h-8 rounded-full object-cover" />
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900">Tunde Bakare</h5>
                                    <span class="text-[10px] text-slate-400">Verified Parent • Port Harcourt</span>
                                </div>
                            </div>
                        </div>

                        <!-- Review 5 -->
                        <div class="bg-white border border-slate-200/80 rounded-[2rem] p-6 shadow-xs space-y-3">
                            <div class="flex items-center gap-1 text-[#E8472A]">★★★★★</div>
                            <p class="text-slate-600 text-xs leading-relaxed font-normal">
                                "Listing my tailoring services brought me verified high-paying clients across Enugu. Highly recommended!"
                            </p>
                            <div class="flex items-center gap-2.5 pt-2 border-t border-slate-100">
                                <img src="{{ asset('images/avatars/nneka.png') }}" class="w-8 h-8 rounded-full object-cover" />
                                <div>
                                    <h5 class="text-xs font-bold text-slate-900">Nneka Eze</h5>
                                    <span class="text-[10px] text-slate-400">Verified Pro • Enugu</span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </section>

            <!-- Section: FAQ Accordion (Skill Marketplace Pixel-Perfect Layout) -->
            <section id="faq" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14" x-data="{ activeFaq: 2 }">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                    
                    <!-- Left Column: iPhone App Screen Mockup -->
                    <div class="lg:col-span-6 relative flex justify-center py-4">
                        <div class="relative w-full max-w-md h-[460px] flex items-center justify-center">
                            
                            <!-- Background Phone (Left Stacked iPhone) -->
                            <div class="absolute left-2 sm:left-4 top-4 w-60 sm:w-64 h-[400px] bg-slate-900 rounded-[2.5rem] p-3 shadow-2xl border-4 border-slate-800 transform -rotate-6 overflow-hidden hidden sm:block opacity-90">
                                <div class="bg-white h-full rounded-[2rem] p-3 space-y-3 relative text-left">
                                    <!-- Dynamic Island -->
                                    <div class="w-16 h-3 bg-slate-900 rounded-full mx-auto mb-1"></div>
                                    <div class="flex items-center justify-between text-[10px] font-bold text-slate-900">
                                        <span>Welcome back!</span>
                                        <span>🔔</span>
                                    </div>
                                    <h5 class="text-xs font-black text-slate-900">Hello! Louis</h5>
                                    
                                    <div class="bg-slate-100 rounded-xl px-3 py-1.5 text-[10px] text-slate-400 flex items-center gap-1.5">
                                        <span>🔍</span> Search
                                    </div>

                                    <div class="bg-gradient-to-r from-[#0F172B] to-[#1E293B] rounded-xl p-3 text-white space-y-1">
                                        <span class="text-xs font-bold block">30% Off</span>
                                        <span class="text-[9px] opacity-80 block">For Your First Premium</span>
                                    </div>

                                    <div class="space-y-1">
                                        <span class="text-[10px] font-bold text-slate-900 block">Recommended</span>
                                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-2 flex items-center gap-2">
                                            <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-900 text-[10px] font-bold flex items-center justify-center"></div>
                                            <div>
                                                <span class="text-[10px] font-bold text-slate-900 block leading-tight">Apple</span>
                                                <span class="text-[8px] text-slate-400 block">Lead UI/UX Designer</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Foreground Phone (Right Prominent iPhone) -->
                            <div class="relative z-10 w-64 sm:w-72 h-[430px] bg-slate-900 rounded-[2.5rem] p-3 shadow-2xl border-4 border-slate-800 transform rotate-2 hover:rotate-0 transition-transform">
                                <div class="bg-white h-full rounded-[2rem] p-4 space-y-3 text-left relative overflow-hidden">
                                    <!-- Dynamic Island -->
                                    <div class="w-20 h-4 bg-slate-900 rounded-full mx-auto mb-2 flex items-center justify-end px-2">
                                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                    </div>

                                    <!-- Top App Navigation -->
                                    <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                                        <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-xs font-bold">←</span>
                                        <span class="text-xs font-bold text-slate-900">Job Details</span>
                                        <span class="text-slate-400 text-xs">🔖</span>
                                    </div>

                                    <!-- Company Logo & Title Card -->
                                    <div class="bg-gradient-to-b from-sky-50 to-white rounded-2xl p-3 text-center space-y-1.5 border border-sky-100 shadow-2xs">
                                        <div class="w-9 h-9 rounded-full bg-white shadow-xs border border-slate-100 mx-auto flex items-center justify-center text-red-500 font-extrabold text-sm">
                                            G
                                        </div>
                                        <h4 class="text-xs font-black text-slate-900 leading-tight">Lead UI/UX Designer</h4>
                                        <span class="text-[9px] text-slate-400 font-medium block">📍 New York, USA</span>
                                        <div class="flex items-center justify-center gap-1.5 text-[8px] text-sky-800 font-semibold pt-0.5">
                                            <span>4 Days ago</span> • <span>400 Applicant</span> • <span class="bg-sky-100 px-1.5 py-0.5 rounded-full">70% Matches You</span>
                                        </div>
                                    </div>

                                    <!-- 2x2 Detail Cards Grid -->
                                    <div class="grid grid-cols-2 gap-2 text-left pt-1">
                                        <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                            <span class="text-[8px] text-slate-400 block font-medium">Salary (Monthly)</span>
                                            <span class="text-[10px] font-extrabold text-slate-900">$50k -70k</span>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                            <span class="text-[8px] text-slate-400 block font-medium">Job Type</span>
                                            <span class="text-[10px] font-extrabold text-slate-900">Full - Time</span>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                            <span class="text-[8px] text-slate-400 block font-medium">Level</span>
                                            <span class="text-[10px] font-extrabold text-slate-900">Internship</span>
                                        </div>
                                        <div class="bg-slate-50 rounded-xl p-2 border border-slate-100">
                                            <span class="text-[8px] text-slate-400 block font-medium">Working Hours</span>
                                            <span class="text-[10px] font-extrabold text-slate-900">10am - 6pm</span>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: Accordion Content -->
                    <div class="lg:col-span-6 space-y-6 text-left">
                        
                        <div class="space-y-3">
                            <div>
                                <span class="px-4 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200/90 text-[#0F172B] shadow-2xs inline-block">
                                    FAQ
                                </span>
                            </div>
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Frequently Asked Questions</h2>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed font-normal">
                                Have questions about hiring verified tutors or listing your trade skills on Skill Link NG — Powered by CSISS? Find clear answers below.
                            </p>
                        </div>

                        <!-- Accordion Rows -->
                        <div class="space-y-3">
                            
                            <!-- FAQ 1 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
                                <button @click="activeFaq = (activeFaq === 1 ? null : 1)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-sky-600 transition-colors">
                                    <span>What is Skill Link NG — Powered by CSISS?</span>
                                    <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-base font-bold shrink-0 ml-2" x-text="activeFaq === 1 ? '−' : '+'"></span>
                                </button>
                                <div x-show="activeFaq === 1" x-collapse class="px-5 pb-4 text-xs text-slate-500 leading-relaxed font-normal pt-1 border-t border-slate-100">
                                    Skill Link NG is Nigeria's premier digital directory connecting parents, households, and businesses with verified academic tutors (for WAEC, JAMB, primary & secondary subjects) and skilled trade artisans (electricians, plumbers, carpenters, technicians) near them.
                                </div>
                            </div>

                            <!-- FAQ 2 (Active/Expanded State Matching Reference Screenshot) -->
                            <div class="rounded-2xl overflow-hidden transition-all duration-200" :class="activeFaq === 2 ? 'bg-sky-50/90 border border-sky-200/80 shadow-xs' : 'bg-white border border-slate-200/80 shadow-2xs'">
                                <button @click="activeFaq = (activeFaq === 2 ? null : 2)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 transition-colors">
                                    <span>How does CSISS verification protect clients and parents?</span>
                                    <span class="w-7 h-7 rounded-full bg-white text-slate-700 shadow-2xs flex items-center justify-center text-base font-bold shrink-0 ml-2" x-text="activeFaq === 2 ? '−' : '+'"></span>
                                </button>
                                <div x-show="activeFaq === 2" x-collapse class="px-5 pb-4 text-xs text-slate-600 leading-relaxed font-normal pt-1">
                                    CSISS conducts background identity checks and qualification reviews for registered professionals. This ensures parents and clients can confidently invite tutors into their homes or hire artisans for property repairs.
                                </div>
                            </div>

                            <!-- FAQ 3 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
                                <button @click="activeFaq = (activeFaq === 3 ? null : 3)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-sky-600 transition-colors">
                                    <span>How do I hire an academic tutor or trade artisan?</span>
                                    <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-base font-bold shrink-0 ml-2" x-text="activeFaq === 3 ? '−' : '+'"></span>
                                </button>
                                <div x-show="activeFaq === 3" x-collapse class="px-5 pb-4 text-xs text-slate-500 leading-relaxed font-normal pt-1 border-t border-slate-100">
                                    You can browse the directory by category, subject, or location, review verified profiles and ratings, and click "Message Professional" or "Get Started" to discuss schedules, rates, and project details directly.
                                </div>
                            </div>

                            <!-- FAQ 4 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
                                <button @click="activeFaq = (activeFaq === 4 ? null : 4)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-sky-600 transition-colors">
                                    <span>Is it free to list my skills as a tutor or artisan?</span>
                                    <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-base font-bold shrink-0 ml-2" x-text="activeFaq === 4 ? '−' : '+'"></span>
                                </button>
                                <div x-show="activeFaq === 4" x-collapse class="px-5 pb-4 text-xs text-slate-500 leading-relaxed font-normal pt-1 border-t border-slate-100">
                                    Yes! Joining Skill Link NG as a talent or tutor is completely free. You can create a detailed profile showcasing your subjects, skills, past work, location, and hourly or monthly rates.
                                </div>
                            </div>

                            <!-- FAQ 5 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
                                <button @click="activeFaq = (activeFaq === 5 ? null : 5)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-sky-600 transition-colors">
                                    <span>What locations in Nigeria are covered by Skill Link NG?</span>
                                    <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-base font-bold shrink-0 ml-2" x-text="activeFaq === 5 ? '−' : '+'"></span>
                                </button>
                                <div x-show="activeFaq === 5" x-collapse class="px-5 pb-4 text-xs text-slate-500 leading-relaxed font-normal pt-1 border-t border-slate-100">
                                    Our platform supports professionals and clients across all major Nigerian states and cities, including Lagos, Abuja, Port Harcourt, Ibadan, Enugu, Kano, Kaduna, and more, offering both in-person and online tutoring options.
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </section>

            <!-- Pre-Footer Banner ("Take the next big step in your career today") -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <div class="bg-gradient-to-r from-slate-100 via-sky-50 to-slate-100 border border-slate-200 rounded-[2.5rem] p-8 sm:p-12 lg:p-16 text-center space-y-6 relative overflow-hidden shadow-sm">
                    
                    <!-- Floating Side Preview Cards (Matching Reference Screenshot) -->
                    <!-- Left Floating Card (Apple) -->
                    <div class="absolute left-6 top-1/2 -translate-y-1/2 w-56 bg-white/90 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-slate-200 hidden xl:block transform -rotate-6 hover:rotate-0 transition-transform text-left space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-red-500 text-white font-bold text-[10px] flex items-center justify-center">G</div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-900 block leading-tight">Apple</span>
                                    <span class="text-[8px] text-slate-400 block">Hannic Office</span>
                                </div>
                            </div>
                            <span class="text-slate-400 text-[10px]">🔖</span>
                        </div>
                        <h6 class="text-xs font-bold text-slate-900">Lead UI/UX Designer</h6>
                        <div class="flex items-center gap-1 text-[8px] font-semibold text-[#0F172B]">
                            <span class="bg-white border border-slate-200/60 px-1.5 py-0.5 rounded-full">Full Time</span>
                            <span class="bg-white border border-slate-200/60 px-1.5 py-0.5 rounded-full">Remote</span>
                            <span class="bg-white border border-slate-200/60 px-1.5 py-0.5 rounded-full">Part Time</span>
                        </div>
                    </div>

                    <!-- Right Floating Card (Dribbble) -->
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 w-56 bg-white/90 backdrop-blur-md rounded-2xl p-4 shadow-xl border border-slate-200 hidden xl:block transform rotate-6 hover:rotate-0 transition-transform text-left space-y-2">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-pink-500 text-white font-bold text-[10px] flex items-center justify-center">🏀</div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-900 block leading-tight">Dribbble</span>
                                    <span class="text-[8px] text-slate-400 block">Hannic Office</span>
                                </div>
                            </div>
                            <span class="text-slate-400 text-[10px]">🔖</span>
                        </div>
                        <h6 class="text-xs font-bold text-slate-900">Product Designer</h6>
                        <div class="flex items-center gap-1 text-[8px] font-semibold text-[#0F172B]">
                            <span class="bg-white border border-slate-200/60 px-1.5 py-0.5 rounded-full">Full Time</span>
                            <span class="bg-white border border-slate-200/60 px-1.5 py-0.5 rounded-full">Remote</span>
                            <span class="bg-white border border-slate-200/60 px-1.5 py-0.5 rounded-full">Part Time</span>
                        </div>
                    </div>

                    <!-- Center Pill Badge -->
                    <div>
                        <span class="px-5 py-1.5 rounded-full text-xs font-extrabold bg-[#E8472A]/10 text-[#E8472A] border border-[#E8472A]/20 uppercase tracking-wider inline-block">
                            Let's Find your Dream Job
                        </span>
                    </div>

                    <!-- Main Heading -->
                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight max-w-xl mx-auto leading-tight">
                        Take the next big step in your career today
                    </h2>

                    <!-- Subtitle -->
                    <p class="text-slate-500 text-xs sm:text-sm font-normal max-w-md mx-auto leading-relaxed">
                        Discover thousands of opportunities from top companies, apply in seconds, and move closer to the career you've always wanted.
                    </p>

                    <!-- Email CTA Subscribe Input Bar -->
                    <div class="pt-2">
                        <div class="bg-white rounded-full p-1.5 pl-6 shadow-md border border-slate-200/80 flex items-center justify-between max-w-md w-full mx-auto">
                            <input type="email" placeholder="Enter Your email address" class="text-slate-500 placeholder-slate-400 text-xs sm:text-sm outline-none bg-transparent w-full pr-2 font-normal" />
                            <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="bg-[#2563EB] hover:bg-[#0F172B] text-white px-6 py-2.5 rounded-full text-xs font-bold shadow-md hover:shadow-lg transition-all shrink-0">
                                Get Started
                            </a>
                        </div>
                    </div>

                </div>
            </section>

        </main>

        <!-- Upgraded Pixel-Perfect Extended Footer (Skill Marketplace Deep Blue Theme) -->
        <footer class="bg-white pt-20 pb-36 sm:pb-44 lg:pb-52 min-h-[580px] lg:min-h-[640px] px-6 sm:px-10 lg:px-16 border-t border-slate-200/80 relative overflow-hidden rounded-b-[2.5rem] flex flex-col justify-between">
            
            <div class="relative z-10 max-w-7xl mx-auto w-full space-y-16">
                
                <!-- 5 Columns Directory Grid (Brand + 4 Link Columns) -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 lg:gap-12 pb-10 border-b border-slate-100">
                    
                    <!-- Brand Column -->
                    <div class="col-span-2 md:col-span-1 space-y-4 text-left">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-8 h-8 object-contain shrink-0" />
                            <span class="text-xl font-bold text-slate-900 tracking-tight">Skill Link NG</span>
                        </div>

                        <p class="text-xs text-slate-500 font-normal leading-relaxed max-w-xs">
                            Your trusted platform connecting clients with verified skilled labour workers, trade artisans, and academic tutors across Nigeria.
                        </p>

                        <!-- Social Media Circle Badges -->
                        <div class="flex items-center gap-2 pt-1">
                            <div class="w-7 h-7 rounded-full bg-[#1769FF] text-white font-bold text-[9px] flex items-center justify-center shadow-2xs">Be</div>
                            <div class="w-7 h-7 rounded-full bg-[#1877F2] text-white font-bold text-[10px] flex items-center justify-center shadow-2xs">f</div>
                            <div class="w-7 h-7 rounded-full bg-[#0A66C2] text-white font-bold text-[9px] flex items-center justify-center shadow-2xs">in</div>
                            <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-500 via-rose-500 to-sky-600 text-white text-[10px] flex items-center justify-center shadow-2xs">📷</div>
                        </div>
                    </div>

                    <!-- Column 2: For Job Seekers -->
                    <div class="space-y-3 text-left">
                        <h4 class="text-sm font-bold text-slate-900">For Job Seekers</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-500">
                            <li><a href="{{ url('/talent') }}" class="hover:text-slate-900 transition-colors">Browse Jobs</a></li>
                            <li><a href="#featured-jobs" class="hover:text-slate-900 transition-colors">Companies</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-900 transition-colors">Career Advice</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-900 transition-colors">Resume Builder</a></li>
                            <li><a href="#featured-jobs" class="hover:text-slate-900 transition-colors">Salary Insights</a></li>
                        </ul>
                    </div>

                    <!-- Column 3: For Employers -->
                    <div class="space-y-3 text-left">
                        <h4 class="text-sm font-bold text-slate-900">For Employers</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-500">
                            <li><a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="hover:text-slate-900 transition-colors">Post a Job</a></li>
                            <li><a href="{{ url('/talent') }}" class="hover:text-slate-900 transition-colors">Browse Candidates</a></li>
                            <li><a href="#why-us" class="hover:text-slate-900 transition-colors">Pricing</a></li>
                            <li><a href="#why-us" class="hover:text-slate-900 transition-colors">Employer Resources</a></li>
                            <li><a href="#testimonials" class="hover:text-slate-900 transition-colors">Success Stories</a></li>
                        </ul>
                    </div>

                    <!-- Column 4: Company -->
                    <div class="space-y-3 text-left">
                        <h4 class="text-sm font-bold text-slate-900">Company</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-500">
                            <li><a href="#why-us" class="hover:text-slate-900 transition-colors">About Us</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-900 transition-colors">Careers</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Press</a></li>
                            <li><a href="#faq" class="hover:text-slate-900 transition-colors">Contact</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Blog</a></li>
                        </ul>
                    </div>

                    <!-- Column 5: Support -->
                    <div class="space-y-3 text-left">
                        <h4 class="text-sm font-bold text-slate-900">Support</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-500">
                            <li><a href="#faq" class="hover:text-slate-900 transition-colors">Help Center</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Terms of Service</a></li>
                            <li><a href="#" class="hover:text-slate-900 transition-colors">Cookie Policy</a></li>
                            <li><a href="#faq" class="hover:text-slate-900 transition-colors">FAQ</a></li>
                        </ul>
                    </div>

                </div>

                <!-- Copyright & Bottom Links -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm font-semibold text-slate-600">
                    <div>
                        © 2026 Skill Link NG — Powered by CSISS. All rights reserved.
                    </div>

                    <div class="flex items-center gap-6 text-sm font-semibold text-slate-600">
                        <a href="#" class="hover:text-slate-900 transition-colors">Privacy</a>
                        <a href="#" class="hover:text-slate-900 transition-colors">Terms</a>
                        <a href="#" class="hover:text-slate-900 transition-colors">Cookies</a>
                    </div>
                </div>

            </div>

            <!-- Massive Background Watermark Text Positioned at Very Bottom -->
            <div class="absolute bottom-2 sm:bottom-4 left-1/2 -translate-x-1/2 text-[5.5rem] sm:text-[9.5rem] md:text-[12.5rem] lg:text-[15.5rem] font-black text-slate-900/[0.04] pointer-events-none select-none tracking-tighter whitespace-nowrap z-0 uppercase leading-none">
                Skill Link NG
            </div>

        </footer>

        @livewireScripts
    </body>
</html>
