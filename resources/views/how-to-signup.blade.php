<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ mobileMenuOpen: false }" x-on:keydown.escape.window="mobileMenuOpen = false">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <title>How to Sign Up — Step-by-Step Guide | {{ config('app.name', 'Skill Link NG') }}</title>

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
            .nav-hamburger.is-open span:nth-child(1) { transform: translateY(7.5px) rotate(45deg); }
            .nav-hamburger.is-open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
            .nav-hamburger.is-open span:nth-child(3) { transform: translateY(-7.5px) rotate(-45deg); }

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
            }

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
            }

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
            }
            .drawer-nav a:hover { background: #f8fafc; color: #2563eb; }

            .drawer-footer {
                padding: 1rem 1.25rem 1.5rem;
                border-top: 1px solid #f1f5f9;
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }
            .drawer-login-btn {
                font-size: 0.875rem;
                font-weight: 600;
                color: #1e293b;
                padding: 0.65rem 1.1rem;
                border-radius: 9999px;
            }
            .drawer-signup-btn {
                flex: 1;
                text-align: center;
                font-size: 0.875rem;
                font-weight: 700;
                color: #ffffff;
                background: #2563eb;
                padding: 0.7rem 1rem;
                border-radius: 9999px;
            }
        </style>
    </head>
    <body class="bg-[#F8FAFC] font-sans antialiased text-slate-900 selection:bg-[#0F172B] selection:text-white">

        <!-- ══════════════════════════════════════════════
             HERO HEADER WRAPPER
        ══════════════════════════════════════════════ -->
        <div class="relative w-full overflow-hidden bg-[#F8FAFC]">

            <!-- Dark Navy Hero Background Backdrop -->
            <div class="absolute inset-x-0 top-0 w-full h-[460px] overflow-hidden pointer-events-none z-0">
                <img src="{{ asset('images/hero_workers_bg.png') }}" alt="Skill Link NG Background" class="w-full h-full object-cover object-top opacity-30 mix-blend-luminosity" />
                <div class="absolute inset-0 bg-gradient-to-b from-[#0F172B]/95 via-[#0F172B]/90 to-[#F8FAFC]"></div>
            </div>

            <!-- Mobile Navbar -->
            <header class="site-navbar" role="banner">
                <button
                    id="nav-hamburger-btn"
                    class="nav-hamburger"
                    :class="{ 'is-open': mobileMenuOpen }"
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    aria-label="Toggle navigation menu"
                >
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <a href="/" class="nav-logo" aria-label="Skill Link NG home">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="Skill Link NG logo" />
                </a>

                @auth
                    <a href="{{ url('/dashboard') }}" class="nav-cta" style="background:#0f172b;">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="nav-cta">
                        Sign up
                    </a>
                @endauth
            </header>

            <!-- Mobile Drawer Overlay -->
            <div
                x-cloak
                x-show="mobileMenuOpen"
                class="drawer-overlay"
                @click="mobileMenuOpen = false"
            ></div>

            <!-- Mobile Drawer Panel -->
            <nav
                class="drawer-panel"
                :class="{ 'is-open': mobileMenuOpen }"
                role="navigation"
            >
                <div class="drawer-header">
                    <a href="/" class="nav-logo" style="position:static;transform:none;" @click="mobileMenuOpen = false">
                        <img src="{{ asset('images/skilllingng_logo.png') }}" alt="Skill Link NG" />
                    </a>
                    <button class="drawer-close" @click="mobileMenuOpen = false">✕</button>
                </div>

                <div class="drawer-nav">
                    <a href="{{ route('home') }}" @click="mobileMenuOpen = false">
                        <span>Home</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                    </a>
                    <a href="{{ route('how-to-signup') }}" @click="mobileMenuOpen = false" class="text-[#2563eb] font-bold">
                        <span>How to Sign Up</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                    </a>
                    <a href="{{ route('home') }}#how-it-works" @click="mobileMenuOpen = false">
                        <span>How It Works</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                    </a>
                    <a href="{{ route('talent.index') }}" @click="mobileMenuOpen = false">
                        <span>Find Talent</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                    </a>
                    <a href="{{ route('jobs.index') }}" @click="mobileMenuOpen = false">
                        <span>Explore Jobs</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 18l6-6-6-6"/></svg>
                    </a>
                </div>

                <div class="drawer-footer">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="drawer-signup-btn" style="background:#0f172b;">
                            Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="drawer-login-btn">Log in</a>
                        <a href="{{ route('register') }}" class="drawer-signup-btn">Sign up</a>
                    @endauth
                </div>
            </nav>

            <!-- Desktop Floating Glass Navbar -->
            <div class="desktop-nav-wrapper sticky top-4 z-50 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                <header class="bg-white/95 backdrop-blur-2xl border border-slate-200/90 shadow-xl shadow-slate-950/10 rounded-full px-5 sm:px-7 py-3 flex items-center justify-between gap-4 transition-all duration-300">
                    <a href="/" class="flex items-center gap-3 group shrink-0">
                        <img src="{{ asset('images/skilllingng_logo.png') }}" alt="Skill Link NG" class="w-40 h-10 object-contain shrink-0" />
                    </a>

                    <nav class="flex items-center gap-6" aria-label="Desktop navigation">
                        <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Home</a>
                        <a href="{{ route('how-to-signup') }}" class="text-xs font-bold text-[#2563EB] flex items-center gap-1">
                            <span>How to Sign Up</span>
                        </a>
                        <a href="{{ route('home') }}#how-it-works" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">How It Works</a>
                        <a href="{{ route('talent.index') }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Find Talent</a>
                        <a href="{{ route('jobs.index') }}" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Featured Jobs</a>
                    </nav>

                    <div class="flex items-center gap-3 shrink-0">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-md">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-700 hover:text-[#0F172B] px-4 py-2 rounded-full hover:bg-slate-100">
                                Sign In
                            </a>
                            <a href="{{ route('register') }}" class="px-6 py-2.5 text-xs font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-full shadow-lg shadow-blue-500/20 hover:scale-105 transition-all">
                                Get Started
                            </a>
                        @endauth
                    </div>
                </header>
            </div>

            <!-- Hero Title & Subtitle -->
            <section class="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-14 pb-12 text-center space-y-6 z-10">
                <div>
                    <span class="inline-flex items-center gap-2 px-4.5 py-1.5 rounded-full text-xs font-extrabold uppercase tracking-widest bg-[#2563EB]/20 border border-[#2563EB]/40 text-blue-300 shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-[#2563EB] animate-pulse"></span>
                        Visual Step-by-Step Guide
                    </span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-[1.15]">
                    How to Sign Up & Get Started
                </h1>

                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
                    Follow this simple step-by-step visual walkthrough to create your account, verify your email, and list your services or hire local talent on Skill Link NG.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                    <a href="{{ route('register') }}" class="px-8 py-3.5 text-sm font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-full shadow-xl shadow-blue-500/25 hover:scale-105 transition-all flex items-center gap-2">
                        <span>Create Your Account Now</span>
                        <svg class="w-4.5 h-4.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>
            </section>
        </div>

        <!-- ══════════════════════════════════════════════
             MAIN STEP-BY-STEP VISUAL GUIDE CONTENT
        ══════════════════════════════════════════════ -->
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16 space-y-16">

            <!-- Progress Tracker Bar (Desktop) -->
            <div class="hidden lg:grid grid-cols-6 gap-3 bg-white p-4 rounded-3xl border border-slate-200/90 shadow-sm text-center">
                <div class="space-y-1 p-2 rounded-2xl bg-blue-50/70 border border-blue-100">
                    <span class="text-[11px] font-extrabold text-[#2563EB] block">STEP 1</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">Click Sign Up</span>
                </div>
                <div class="space-y-1 p-2 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[11px] font-extrabold text-slate-500 block">STEP 2</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">Enter Details</span>
                </div>
                <div class="space-y-1 p-2 rounded-2xl bg-amber-50/70 border border-amber-100">
                    <span class="text-[11px] font-extrabold text-amber-700 block">STEP 3</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">Verify Email</span>
                </div>
                <div class="space-y-1 p-2 rounded-2xl bg-emerald-50/70 border border-emerald-100">
                    <span class="text-[11px] font-extrabold text-emerald-700 block">STEP 4</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">Complete Profile</span>
                </div>
                <div class="space-y-1 p-2 rounded-2xl bg-orange-50/70 border border-orange-100">
                    <span class="text-[11px] font-extrabold text-[#E8472A] block">STEP 5</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">Classification</span>
                </div>
                <div class="space-y-1 p-2 rounded-2xl bg-slate-50 border border-slate-100">
                    <span class="text-[11px] font-extrabold text-[#0F172B] block">STEP 6</span>
                    <span class="text-xs font-bold text-slate-800 block truncate">Service & Address</span>
                </div>
            </div>

            <!-- STEP 1: Click on Sign Up -->
            <section class="bg-white border border-slate-200/90 rounded-[2.5rem] p-6 sm:p-10 shadow-xl space-y-8 relative overflow-hidden group hover:border-[#2563EB]/40 transition-all">
                <div class="absolute top-0 inset-x-0 h-2 bg-[#2563EB]"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Text content -->
                    <div class="lg:col-span-6 space-y-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-[#2563EB] text-white font-black text-base flex items-center justify-center shadow-md">1</span>
                            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-50 text-[#2563EB] border border-blue-100">
                                Initial Step
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Click on Sign Up
                        </h2>

                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                            Visit the Skill Link NG homepage. On the top right of the navigation bar (or inside the mobile menu drawer), click the blue <strong class="text-[#2563EB]">"Sign Up"</strong> or <strong class="text-[#0F172B]">"Get Started"</strong> button to open the account registration page.
                        </p>

                        <div class="bg-blue-50/80 border border-blue-100 rounded-2xl p-4 flex items-start gap-3 text-xs sm:text-sm text-blue-950 font-medium">
                            <svg class="w-5 h-5 text-[#2563EB] shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                            <span>Quick Tip: Anyone can create a free account whether you are looking to hire tutors/artisans or offering professional services.</span>
                        </div>
                    </div>

                    <!-- STEP 1 Screenshot -->
                    <div class="lg:col-span-6">
                        <div class="rounded-3xl border border-slate-200/90 overflow-hidden shadow-lg bg-slate-900 group-hover:scale-[1.01] transition-transform">
                            <img src="{{ asset('images/how-to-signup/step1_click_signup.png') }}" alt="Step 1: Click on Sign Up" class="w-full h-auto object-cover object-top max-h-[480px]" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- STEP 2: Enter Your Details -->
            <section class="bg-white border border-slate-200/90 rounded-[2.5rem] p-6 sm:p-10 shadow-xl space-y-8 relative overflow-hidden group hover:border-[#2563EB]/40 transition-all">
                <div class="absolute top-0 inset-x-0 h-2 bg-[#2563EB]"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Screenshot Preview -->
                    <div class="lg:col-span-6 order-2 lg:order-1">
                        <div class="rounded-3xl border border-slate-200/90 overflow-hidden shadow-lg bg-slate-900 group-hover:scale-[1.01] transition-transform">
                            <img src="{{ asset('images/how-to-signup/step2_enter_details.png') }}" alt="Step 2: Enter Your Details" class="w-full h-auto object-cover object-top max-h-[520px] mx-auto" />
                        </div>
                    </div>

                    <!-- Right: Text content -->
                    <div class="lg:col-span-6 space-y-5 text-left order-1 lg:order-2">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-[#2563EB] text-white font-black text-base flex items-center justify-center shadow-md">2</span>
                            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-50 text-[#2563EB] border border-blue-100">
                                Registration Form
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Enter Your Details
                        </h2>

                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                            Fill in your basic information on the sign up form:
                        </p>

                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700 font-medium">
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-[#2563EB] font-bold text-xs flex items-center justify-center">✓</span>
                                <span><strong>First & Last Name</strong> (e.g., John Doe)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-[#2563EB] font-bold text-xs flex items-center justify-center">✓</span>
                                <span><strong>Email Address</strong> (e.g., name@example.com)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-[#2563EB] font-bold text-xs flex items-center justify-center">✓</span>
                                <span><strong>Password</strong> (8 or more characters)</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-blue-100 text-[#2563EB] font-bold text-xs flex items-center justify-center">✓</span>
                                <span>Select your <strong>Country</strong> (e.g., Nigeria) and accept the Terms of Service.</span>
                            </li>
                        </ul>

                        <p class="text-xs sm:text-sm text-slate-600 pt-2">
                            Then click the blue <strong class="text-[#2563EB]">"Create my account"</strong> button to proceed.
                        </p>
                    </div>
                </div>
            </section>

            <!-- STEP 3: Verify Your Email Address -->
            <section class="bg-white border border-slate-200/90 rounded-[2.5rem] p-6 sm:p-10 shadow-xl space-y-8 relative overflow-hidden group hover:border-amber-400/60 transition-all">
                <div class="absolute top-0 inset-x-0 h-2 bg-amber-500"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Text content -->
                    <div class="lg:col-span-6 space-y-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-amber-500 text-white font-black text-base flex items-center justify-center shadow-md">3</span>
                            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-amber-50 text-amber-800 border border-amber-200">
                                Security Check
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Verify Your Email Address
                        </h2>

                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                            Once logged in, an amber warning banner will alert you: <em class="text-slate-800 font-semibold">"Email verification required — Please verify your email to unlock applications and connections."</em>
                        </p>

                        <div class="bg-amber-50 border border-amber-200/90 rounded-2xl p-4 space-y-2 text-xs sm:text-sm text-amber-950">
                            <div class="flex items-center gap-2 font-bold text-amber-900 text-sm">
                                <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                <span>Action Required</span>
                            </div>
                            <p class="leading-relaxed">
                                Check your email inbox for a verification link sent from Skill Link NG. Click <strong>"Verify Email"</strong> in the email message or banner to activate full account features.
                            </p>
                        </div>
                    </div>

                    <!-- Right: Screenshot Preview -->
                    <div class="lg:col-span-6">
                        <div class="rounded-3xl border border-slate-200/90 overflow-hidden shadow-lg bg-slate-900 group-hover:scale-[1.01] transition-transform">
                            <img src="{{ asset('images/how-to-signup/step3_verify_email.png') }}" alt="Step 3: Verify Your Email Address" class="w-full h-auto object-cover object-top max-h-[460px]" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- STEP 4: Click on Complete Profile -->
            <section class="bg-white border border-slate-200/90 rounded-[2.5rem] p-6 sm:p-10 shadow-xl space-y-8 relative overflow-hidden group hover:border-emerald-500/60 transition-all">
                <div class="absolute top-0 inset-x-0 h-2 bg-emerald-500"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Screenshot Preview -->
                    <div class="lg:col-span-6 order-2 lg:order-1">
                        <div class="rounded-3xl border border-slate-200/90 overflow-hidden shadow-lg bg-slate-900 group-hover:scale-[1.01] transition-transform">
                            <img src="{{ asset('images/how-to-signup/step4_complete_profile.png') }}" alt="Step 4: Click on Complete Profile" class="w-full h-auto object-cover object-top max-h-[500px]" />
                        </div>
                    </div>

                    <!-- Right: Text content -->
                    <div class="lg:col-span-6 space-y-5 text-left order-1 lg:order-2">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-emerald-600 text-white font-black text-base flex items-center justify-center shadow-md">4</span>
                            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Onboarding Setup
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Click on Complete Profile
                        </h2>

                        <div class="bg-rose-50 border border-rose-200/90 rounded-2xl p-3.5 text-xs sm:text-sm text-rose-900 font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Important: You must verify your email address before you can complete your profile!</span>
                        </div>

                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                            Once your email is verified, return to your dashboard. Look for the green onboarding banner asking <em class="text-slate-800 font-semibold">"Do you offer a service on Skill Link NG?"</em>
                        </p>

                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Click the green <strong class="text-emerald-700">"Complete Profile →"</strong> or <strong class="text-sky-600">"Setup Service Profile →"</strong> button to launch the classification setup wizard.
                        </p>
                    </div>
                </div>
            </section>

            <!-- STEP 5: Select How You Would Like to Be Listed -->
            <section class="bg-white border border-slate-200/90 rounded-[2.5rem] p-6 sm:p-10 shadow-xl space-y-8 relative overflow-hidden group hover:border-[#E8472A]/50 transition-all">
                <div class="absolute top-0 inset-x-0 h-2 bg-[#E8472A]"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Text content -->
                    <div class="lg:col-span-6 space-y-5 text-left">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-[#E8472A] text-white font-black text-base flex items-center justify-center shadow-md">5</span>
                            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-orange-50 text-[#E8472A] border border-orange-200">
                                Skill Classification
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Select How You Would Like to Be Listed
                        </h2>

                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                            Select all categories that apply to your expertise (you can choose more than one):
                        </p>

                        <!-- 3 Classification Types Breakdown -->
                        <div class="space-y-3 pt-1">
                            <div class="p-3.5 rounded-2xl bg-blue-50/60 border border-blue-100 flex items-start gap-3">
                                <span class="w-7 h-7 rounded-xl bg-blue-100 text-[#2563EB] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">💼</span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Professional</h4>
                                    <p class="text-[11px] sm:text-xs text-slate-500">Graphic designers, developers, accountants, photographers, consultants, event planners, etc.</p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-start gap-3">
                                <span class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">🎓</span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Teacher</h4>
                                    <p class="text-[11px] sm:text-xs text-slate-500">Mathematics tutors, English teachers, WAEC/JAMB/IELTS exam prep specialists, academic educators.</p>
                                </div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-orange-50/60 border border-orange-100 flex items-start gap-3">
                                <span class="w-7 h-7 rounded-xl bg-orange-100 text-[#E8472A] font-bold text-xs flex items-center justify-center shrink-0 mt-0.5">⚙️</span>
                                <div>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900">Skilled Labour Worker</h4>
                                    <p class="text-[11px] sm:text-xs text-slate-500">Carpenters, plumbers, electricians, painters, mechanics, tilers, AC technicians, trade workers.</p>
                                </div>
                            </div>
                        </div>

                        <p class="text-xs sm:text-sm text-slate-600 pt-1">
                            Click <strong class="text-[#2563EB]">"Continue with Selected Classifications →"</strong> to save your selection.
                        </p>
                    </div>

                    <!-- Right: Screenshot Preview -->
                    <div class="lg:col-span-6">
                        <div class="rounded-3xl border border-slate-200/90 overflow-hidden shadow-lg bg-slate-900 group-hover:scale-[1.01] transition-transform">
                            <img src="{{ asset('images/how-to-signup/step5_select_classification.png') }}" alt="Step 5: Select How You Would Like to Be Listed" class="w-full h-auto object-cover object-top max-h-[520px] mx-auto" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- STEP 6: Enter Your Details & Address -->
            <section class="bg-white border border-slate-200/90 rounded-[2.5rem] p-6 sm:p-10 shadow-xl space-y-8 relative overflow-hidden group hover:border-[#0F172B]/50 transition-all">
                <div class="absolute top-0 inset-x-0 h-2 bg-[#0F172B]"></div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                    <!-- Left: Screenshot Preview -->
                    <div class="lg:col-span-6 order-2 lg:order-1">
                        <div class="rounded-3xl border border-slate-200/90 overflow-hidden shadow-lg bg-slate-900 group-hover:scale-[1.01] transition-transform">
                            <img src="{{ asset('images/how-to-signup/step6_service_details.png') }}" alt="Step 6: Enter Your Details & Address" class="w-full h-auto object-cover object-top max-h-[520px] mx-auto" />
                        </div>
                    </div>

                    <!-- Right: Text content -->
                    <div class="lg:col-span-6 space-y-5 text-left order-1 lg:order-2">
                        <div class="flex items-center gap-3">
                            <span class="w-10 h-10 rounded-2xl bg-[#0F172B] text-white font-black text-base flex items-center justify-center shadow-md">6</span>
                            <span class="px-3.5 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-slate-100 text-[#0F172B] border border-slate-200">
                                Final Profile Setup
                            </span>
                        </div>

                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Enter Your Service Details & Address
                        </h2>

                        <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-normal">
                            Complete your service listing fields:
                        </p>

                        <ul class="space-y-2.5 text-xs sm:text-sm text-slate-700 font-medium">
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-900 font-bold text-xs flex items-center justify-center">✓</span>
                                <span>Select your <strong>Primary Category</strong> or trade area</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-900 font-bold text-xs flex items-center justify-center">✓</span>
                                <span>Set your <strong>Public Display Name / Business Name</strong></span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-900 font-bold text-xs flex items-center justify-center">✓</span>
                                <span>Enter your <strong>Years of Experience</strong> and hourly/project rates</span>
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-900 font-bold text-xs flex items-center justify-center">✓</span>
                                <span>Fill in your <strong>General Location & Address</strong> so clients near you can discover you on the local map</span>
                            </li>
                        </ul>

                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-950 font-bold text-xs sm:text-sm flex items-center gap-3">
                            <span class="w-8 h-8 rounded-full bg-emerald-600 text-white font-black text-sm flex items-center justify-center shrink-0">🎉</span>
                            <span>That's it! Click submit and your profile is live and set to receive job offers!</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Bottom Action CTA Banner -->
            <section class="bg-gradient-to-r from-[#0F172B] via-[#1e293b] to-[#0F172B] rounded-[2.5rem] p-8 sm:p-14 text-center space-y-6 text-white shadow-2xl relative overflow-hidden">
                <div class="max-w-3xl mx-auto space-y-4 relative z-10">
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Ready to Get Started?</h2>
                    <p class="text-slate-300 text-sm sm:text-base font-normal max-w-xl mx-auto">
                        Create your free account today and experience Nigeria's premier marketplace for verified trade workers, tutors, and professionals.
                    </p>
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-4">
                        <a href="{{ route('register') }}" class="px-8 py-3.5 text-sm font-bold text-white bg-[#2563EB] hover:bg-blue-600 rounded-full shadow-lg hover:scale-105 transition-all">
                            Sign Up Now
                        </a>
                        <a href="{{ route('home') }}" class="px-7 py-3.5 text-sm font-bold text-slate-200 bg-white/10 hover:bg-white/20 border border-white/20 rounded-full transition-all">
                            Return to Home
                        </a>
                    </div>
                </div>
            </section>

        </main>

        <!-- Footer -->
        <footer class="w-full py-8 text-center text-xs text-slate-500 border-t border-slate-200/80 bg-white">
            <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="Skill Link NG" class="h-6 w-auto" />
                    <span class="font-bold text-slate-700">Skill Link NG</span>
                </div>
                <div>
                    &copy; {{ date('Y') }} {{ config('app.name', 'Skill Link NG') }}. All rights reserved.
                </div>
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
