<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ config('app.name', 'Skill Marketplace') }} — Find Skilled Professionals Near You</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-950 font-sans antialiased text-slate-900 selection:bg-sky-500 selection:text-white">

        <!-- Navigation Bar (Floating Capsule Glassmorphism Design - White Theme) -->
        <header class="fixed top-4 left-0 right-0 z-50 w-full px-4 sm:px-6 lg:px-8 pointer-events-none">
            <div class="max-w-6xl mx-auto bg-white/90 backdrop-blur-2xl border border-slate-200/90 rounded-full px-5 sm:px-7 h-16 flex items-center justify-between shadow-[0_10px_30px_rgba(0,0,0,0.08)] pointer-events-auto transition-all duration-300 hover:border-slate-300 hover:shadow-xl">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-full bg-sky-500/10 border border-sky-400/30 flex items-center justify-center backdrop-blur-md group-hover:border-sky-500 transition-colors shadow-sm">
                        <svg class="w-4 h-4 text-sky-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-slate-900 font-extrabold text-base tracking-tight">Skill Marketplace</span>
                </a>

                <!-- Nav Links (Capsule Pills) -->
                <nav class="hidden md:flex items-center gap-1 bg-slate-100/90 border border-slate-200/70 rounded-full p-1 backdrop-blur-md">
                    <a href="#freelancers" class="text-xs font-semibold text-slate-700 hover:text-slate-950 px-4 py-1.5 rounded-full hover:bg-white transition-all shadow-2xs">Find Talent</a>
                    <a href="#how-it-works" class="text-xs font-semibold text-slate-700 hover:text-slate-950 px-4 py-1.5 rounded-full hover:bg-white transition-all shadow-2xs">How It Works</a>
                    <a href="#categories" class="text-xs font-semibold text-slate-700 hover:text-slate-950 px-4 py-1.5 rounded-full hover:bg-white transition-all shadow-2xs">Categories</a>
                    <a href="#testimonials" class="text-xs font-semibold text-slate-700 hover:text-slate-950 px-4 py-1.5 rounded-full hover:bg-white transition-all shadow-2xs">Reviews</a>
                </nav>

                <!-- Auth Buttons (Glass & Pill Buttons) -->
                <div class="flex items-center gap-2.5">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-1.5 text-xs font-semibold text-white bg-sky-600 hover:bg-sky-500 rounded-full shadow-md shadow-sky-600/20 transition-all">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="px-4 py-1.5 text-xs font-semibold text-slate-700 hover:text-slate-950 bg-slate-100 hover:bg-slate-200/80 border border-slate-200 rounded-full transition-all">
                            Log in
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="px-4.5 py-1.5 text-xs font-semibold text-white bg-slate-950 hover:bg-slate-800 rounded-full shadow-md shadow-slate-900/10 transition-all">
                            Sign up
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative min-h-[680px] lg:min-h-[740px] flex flex-col justify-center items-center text-center px-4 pt-28 pb-20 overflow-hidden">
            <!-- Less Busy Minimal Background Image with Soft Light Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/clean_hero_bg.png') }}" alt="Skilled Artisans & Tutors" class="w-full h-full object-cover object-center" />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/70 via-slate-950/65 to-slate-950/90"></div>
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-sky-900/20 via-slate-950/40 to-slate-950/80"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center">
                <!-- Top Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 border border-white/30 text-sky-200 text-xs font-semibold backdrop-blur-md mb-6 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Trusted local talent & tutors, one search away</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15] mb-5">
                    Find Skilled Professionals <br class="hidden sm:inline" />Near You
                </h1>

                <!-- Subtitle -->
                <p class="text-slate-200 text-base sm:text-lg max-w-2xl mb-10 font-normal leading-relaxed">
                    Connect with trusted artisans, freelancers and academic tutors in your area
                </p>

                <!-- Search Container Card (Lighter Ultra Glassmorphism) -->
                <div class="w-full max-w-3xl bg-white/15 backdrop-blur-3xl border border-white/30 rounded-3xl p-6 sm:p-8 shadow-[0_20px_60px_rgba(0,0,0,0.3)] text-left relative overflow-hidden group">
                    <!-- Ambient Glow Accents inside Glass Container -->
                    <div class="absolute -top-20 -left-20 w-48 h-48 bg-sky-400/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-20 -right-20 w-48 h-48 bg-emerald-400/20 rounded-full blur-3xl pointer-events-none"></div>

                    <label class="text-[11px] font-bold tracking-widest text-sky-100 uppercase mb-3.5 flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-sky-300"></span>
                        WHAT DO YOU NEED FROM YOUR LOCAL TEAM?
                    </label>

                    <!-- Glass Search Input Form -->
                    <form action="#" method="GET" class="relative flex items-center">
                        <div class="relative w-full group/input">
                            <svg class="w-5 h-5 text-white/80 absolute left-4 top-1/2 -translate-y-1/2 transition-colors group-focus-within/input:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input 
                                type="text" 
                                name="query" 
                                placeholder="Search for a service, trade or skill (e.g. Electrician, Tutor, Painter...)" 
                                class="w-full bg-white/20 border border-white/30 focus:border-white focus:bg-white/25 focus:ring-4 focus:ring-white/20 text-white placeholder-white/80 text-sm sm:text-base rounded-2xl pl-12 pr-32 py-4 outline-none transition-all shadow-inner backdrop-blur-md"
                            />
                            <!-- White Background Button -->
                            <button 
                                type="submit" 
                                class="absolute right-2 top-1/2 -translate-y-1/2 bg-white text-slate-950 hover:bg-slate-100 text-sm font-extrabold px-6 py-2.5 rounded-xl transition-all shadow-md hover:scale-105 active:scale-95"
                            >
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Glass Filter Tags -->
                    <div class="flex flex-wrap items-center gap-2 mt-4 pt-1">
                        @foreach(['Home repair', 'Academic tutoring', 'Cleaning', 'Photography', 'Furniture assembly', 'Moving help'] as $tag)
                            <a href="#categories" class="px-3.5 py-1.5 rounded-full text-xs font-semibold bg-white/20 hover:bg-white/35 text-white border border-white/30 backdrop-blur-md transition-all shadow-2xs hover:scale-105">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: Find experts for every type of work (With Academic Categories) -->
        <section id="freelancers" class="bg-white py-20 px-4 sm:px-6 lg:px-8 border-b border-slate-100">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 tracking-tight mb-10 text-left">
                    Find experts for every type of work:
                </h2>                <!-- 12 Categories Grid (Academic + Trade + Professional) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-5">
                    
                    <!-- 1. Academic & Tutoring -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Academic & Tutoring</span>
                    </a>

                    <!-- 2. Exam Prep & Languages -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"/>
                                <path d="M6 6h10"/>
                                <path d="M6 10h10"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Exam Prep & Languages</span>
                    </a>

                    <!-- 3. Development & IT -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect width="18" height="12" x="3" y="4" rx="2"/>
                                <path d="M2 20h20"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Development & IT</span>
                    </a>

                    <!-- 4. Design & Creative -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 19l7-7 3 3-7 7-3-3z"/>
                                <path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Design & Creative</span>
                    </a>

                    <!-- 5. Sales & Marketing -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <line x1="12" y1="20" x2="12" y2="10"/>
                                <line x1="18" y1="20" x2="18" y2="4"/>
                                <line x1="6" y1="20" x2="6" y2="16"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Sales & Marketing</span>
                    </a>

                    <!-- 6. Writing & Translation -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Writing & Translation</span>
                    </a>

                    <!-- 7. Admin & Support -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Admin & Support</span>
                    </a>

                    <!-- 8. Finance & Accounting -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <rect width="20" height="14" x="2" y="5" rx="2"/>
                                <line x1="2" y1="10" x2="22" y2="10"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Finance & Accounting</span>
                    </a>

                    <!-- 9. Legal -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="m12 15 8.385-8.415a2.122 2.122 0 0 0-3-3L9 12"/>
                                <path d="M16 5 9 12 5 8"/>
                                <path d="M19 11v9H5V5h9"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Legal</span>
                    </a>

                    <!-- 10. HR & Training -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">HR & Training</span>
                    </a>

                    <!-- 11. Engineering & Architecture -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">Engineering & Architecture</span>
                    </a>

                    <!-- 12. AI Services -->
                    <a href="#categories" class="bg-white border border-slate-900 rounded-2xl p-5 shadow-xs hover:shadow-lg hover:border-slate-900 hover:-translate-y-1 transition-all group flex flex-col justify-between h-36">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-300 flex items-center justify-center text-slate-900 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors leading-tight">AI Services</span>
                    </a>

                </div>
            </div>
        </section>

        <!-- Section 3: How It Works (Clean Light Theme with Abstract Net Pattern Background) -->
        <section id="how-it-works" class="relative bg-white py-24 px-4 sm:px-6 lg:px-8 border-b border-slate-100 overflow-hidden">
            <!-- Abstract White & Light Black Net / Grid Background Pattern -->
            <div class="absolute inset-0 pointer-events-none opacity-[0.06] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            <div class="absolute inset-0 pointer-events-none opacity-[0.03] [background-image:radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px]"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-white via-transparent to-white pointer-events-none"></div>

            <div class="relative z-10 max-w-6xl mx-auto">
                
                <!-- Left-aligned Bold Heading matching reference -->
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight text-left mb-12">
                    How Skill Marketplace works:
                </h2>

                <!-- 3-Column Card Layout -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                    
                    <!-- Card 1 -->
                    <div class="bg-white border border-slate-900 rounded-2xl sm:rounded-3xl p-7 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-md transition-all">
                        <div>
                            <!-- Blue Number Badge -->
                            <div class="w-9 h-9 rounded-md bg-[#0284c7] text-white flex items-center justify-center font-extrabold text-base mb-6 shadow-xs">
                                1
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
                                Find your expert.
                            </h3>

                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8 font-normal">
                                We'll connect you with a verified professional or academic tutor who fits your exact goals — from local artisans to exam prep.
                            </p>
                        </div>

                        <!-- Bottom UI Component Mockup (Stacked Profile Cards, no photo stock images) -->
                        <div class="relative pt-4 overflow-hidden">
                            <div class="space-y-3">
                                <!-- Top Card: Milena -->
                                <div class="bg-white border border-slate-200 shadow-md rounded-2xl p-4 flex items-start gap-3.5 relative z-20 transform -rotate-1 hover:rotate-0 transition-transform">
                                    <div class="w-10 h-10 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-sm shrink-0">
                                        M
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <h5 class="text-sm font-bold text-slate-900 truncate">Milena R.</h5>
                                            <div class="flex items-center gap-1 text-xs font-bold text-slate-900">
                                                <span class="text-amber-400">★</span> 4.9
                                            </div>
                                        </div>
                                        <p class="text-xs text-slate-500 font-medium">⚡ Electrical & Solar Specialist</p>
                                        <div class="flex items-center gap-1.5 mt-1.5 text-[11px] text-slate-600">
                                            <span class="px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 font-semibold border border-sky-200">Verified Pro</span>
                                            <span>Lagos, NG</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Stacked Under Card 2 -->
                                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-3.5 flex items-center justify-between opacity-80 z-10 transform scale-95 -mt-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-sky-600 text-white font-bold flex items-center justify-center text-xs">
                                            D
                                        </div>
                                        <div>
                                            <h6 class="text-xs font-bold text-slate-800">David O.</h6>
                                            <span class="text-[11px] text-slate-500">📚 French & Math Tutor</span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-slate-700">★ 5.0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white border border-slate-900 rounded-2xl sm:rounded-3xl p-7 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-md transition-all">
                        <div>
                            <!-- Royal Blue Number Badge -->
                            <div class="w-9 h-9 rounded-md bg-[#3b82f6] text-white flex items-center justify-center font-extrabold text-base mb-6 shadow-xs">
                                2
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
                                Connect safely.
                            </h3>

                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8 font-normal">
                                Review ratings, agree on service scope, and send a connection request with complete contact privacy and zero hassle.
                            </p>
                        </div>

                        <!-- Bottom UI Component Mockup (Connection Badge & Pay guarantee UI) -->
                        <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4.5 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between text-xs font-medium">
                                <span class="text-slate-500">Request Status:</span>
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-sky-100 text-sky-900 border border-sky-300">
                                    Accepted • Pending Payment
                                </span>
                            </div>

                            <div class="bg-white border border-slate-300 rounded-xl p-3.5 flex items-center justify-between shadow-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-lg bg-sky-600 text-white flex items-center justify-center font-extrabold text-sm shadow-xs">
                                        ₦
                                    </div>
                                    <div>
                                        <span class="text-xs font-bold text-slate-900 block">Connection Fee: ₦1,000</span>
                                        <span class="text-[10px] text-slate-500 font-medium">Paystack Encrypted • Direct Connect</span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div class="flex items-center gap-2 text-[11px] text-slate-500 font-medium pt-1">
                                <svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                <span>Backend protects phone & email until connected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white border border-slate-900 rounded-2xl sm:rounded-3xl p-7 flex flex-col justify-between overflow-hidden shadow-xs hover:shadow-md transition-all">
                        <div>
                            <!-- Blue Number Badge -->
                            <div class="w-9 h-9 rounded-md bg-[#3b82f6] text-white flex items-center justify-center font-extrabold text-base mb-6 shadow-xs">
                                3
                            </div>

                            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mb-3">
                                Collaborate & review.
                            </h3>

                            <p class="text-slate-600 text-sm sm:text-base leading-relaxed mb-8 font-normal">
                                Message directly in real-time, get your project or tutoring completed, and leave a genuine review for your community.
                            </p>
                        </div>

                        <!-- Bottom UI Component Mockup (Chat message thread UI) -->
                        <div class="bg-slate-50 border border-slate-200/90 rounded-2xl p-4 space-y-2.5 shadow-inner">
                            <div class="bg-sky-600 text-white rounded-2xl rounded-tr-none px-3.5 py-2.5 ml-auto max-w-[85%] text-xs font-medium shadow-xs">
                                Hi! I accepted your request. Ready for tomorrow at 10 AM?
                            </div>

                            <div class="bg-white border border-slate-200 text-slate-800 rounded-2xl rounded-tl-none px-3.5 py-2.5 max-w-[85%] text-xs font-medium shadow-xs">
                                Perfect! Looking forward to it. Thanks!
                            </div>

                            <div class="pt-2 flex items-center justify-between border-t border-slate-200/80 text-[11px] text-slate-500 font-semibold">
                                <span>Leave Review:</span>
                                <div class="flex items-center gap-1 text-amber-400">
                                    ★★★★★
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 4: Popular Categories (Pure White Light Theme) -->
        <section id="categories" class="bg-white py-24 px-4 sm:px-6 lg:px-8 border-b border-slate-100">
            <div class="max-w-6xl mx-auto text-left">
                <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight mb-3">
                    Popular service categories:
                </h2>
                <p class="text-slate-600 text-sm sm:text-base max-w-2xl mb-12 leading-relaxed">
                    Whatever the task, find experienced local artisans and academic tutors ready to help in your area.
                </p>

                <!-- Category Cards Grid (12 Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">

                    <!-- 1. Academic & K-12 Tutoring -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Academic Tutoring</h4>
                                <span class="text-xs text-slate-500 font-medium">874 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 2. Exam Prep & Languages -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Exam Prep & Languages</h4>
                                <span class="text-xs text-slate-500 font-medium">620 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 3. STEM & Sciences -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="3" stroke-width="2"/>
                                    <ellipse cx="12" cy="12" rx="7" ry="3" stroke-width="1.8" transform="rotate(30 12 12)"/>
                                    <ellipse cx="12" cy="12" rx="7" ry="3" stroke-width="1.8" transform="rotate(-30 12 12)"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">STEM & Sciences</h4>
                                <span class="text-xs text-slate-500 font-medium">412 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 4. Plumbing -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 011 1V4z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Plumbing</h4>
                                <span class="text-xs text-slate-500 font-medium">542 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 5. Electrical -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Electrical</h4>
                                <span class="text-xs text-slate-500 font-medium">389 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 6. Carpentry -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Carpentry</h4>
                                <span class="text-xs text-slate-500 font-medium">210 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 7. Painting -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Painting</h4>
                                <span class="text-xs text-slate-500 font-medium">401 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 8. Cleaning -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Cleaning</h4>
                                <span class="text-xs text-slate-500 font-medium">625 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 9. Photography -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Photography</h4>
                                <span class="text-xs text-slate-500 font-medium">194 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 10. Web Development -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Web Development</h4>
                                <span class="text-xs text-slate-500 font-medium">488 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 11. Graphic Design -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Graphic Design</h4>
                                <span class="text-xs text-slate-500 font-medium">350 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 12. Music & Instrument Tutors -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 text-emerald-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 .895-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 .895-2 3-2 3 .895 3 2zM9 10l12-3"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Music & Instruments</h4>
                                <span class="text-xs text-slate-500 font-medium">280 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                </div>
            </div>
        </section>

        <!-- Section 5: Testimonials -->
        <section id="testimonials" class="bg-white py-24 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto text-center">
                <span class="text-sky-600 text-xs font-bold uppercase tracking-widest mb-2 block">REAL CONNECTIONS</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Loved by customers and professionals</h2>
                <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto mb-16 leading-relaxed">
                    A trusted place for local talent and the people who need their skills.
                </p>

                <!-- Testimonials Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">

                    <!-- Testimonial 1 -->
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-7 flex flex-col justify-between hover:shadow-md transition-all">
                        <div>
                            <!-- Rating Stars -->
                            <div class="flex items-center gap-1 text-amber-400 mb-4">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-slate-700 text-sm leading-relaxed mb-6 font-normal">
                                "I found a fantastic painter in less than an hour. The reviews felt genuine, communication was easy, and the work was beautiful."
                            </p>
                        </div>
                        <div class="flex items-center pt-2">
                            <img src="{{ asset('images/avatars/olivia.png') }}" alt="Olivia Grant" class="w-10 h-10 rounded-full object-cover mr-3 ring-2 ring-white" />
                            <div>
                                <h5 class="text-xs font-bold text-slate-900">Olivia Grant</h5>
                                <span class="text-[11px] text-slate-500 font-medium">Verified customer</span>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 2 -->
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-7 flex flex-col justify-between hover:shadow-md transition-all">
                        <div>
                            <!-- Rating Stars -->
                            <div class="flex items-center gap-1 text-amber-400 mb-4">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-slate-700 text-sm leading-relaxed mb-6 font-normal">
                                "The connection process gave me confidence before sharing details. Our carpenter was punctual, thoughtful and incredibly skilled."
                            </p>
                        </div>
                        <div class="flex items-center pt-2">
                            <img src="{{ asset('images/avatars/marcus.png') }}" alt="Marcus Lee" class="w-10 h-10 rounded-full object-cover mr-3 ring-2 ring-white" />
                            <div>
                                <h5 class="text-xs font-bold text-slate-900">Marcus Lee</h5>
                                <span class="text-[11px] text-slate-500 font-medium">Verified customer</span>
                            </div>
                        </div>
                    </div>

                    <!-- Testimonial 3 -->
                    <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-7 flex flex-col justify-between hover:shadow-md transition-all">
                        <div>
                            <!-- Rating Stars -->
                            <div class="flex items-center gap-1 text-amber-400 mb-4">
                                @for($i = 0; $i < 5; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                @endfor
                            </div>
                            <p class="text-slate-700 text-sm leading-relaxed mb-6 font-normal">
                                "As a tutor, this platform helps me meet families nearby and build lasting working relationships safely."
                            </p>
                        </div>
                        <div class="flex items-center pt-2">
                            <img src="{{ asset('images/avatars/sophia.png') }}" alt="Sophia Bennett" class="w-10 h-10 rounded-full object-cover mr-3 ring-2 ring-white" />
                            <div>
                                <h5 class="text-xs font-bold text-slate-900">Sophia Bennett</h5>
                                <span class="text-[11px] text-slate-500 font-medium">Verified customer</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Pre-Footer CTA Section (Upwork Style Banner - Blue Gradient) -->
        <section class="bg-white py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-6xl mx-auto">
                <div class="bg-gradient-to-r from-sky-500 via-blue-600 to-indigo-600 rounded-3xl p-8 sm:p-14 text-center shadow-lg relative overflow-hidden flex flex-col items-center justify-center gap-6 group hover:shadow-xl transition-all">
                    <!-- Ambient Glow Effects -->
                    <div class="absolute -top-24 -left-24 w-64 h-64 bg-white/20 rounded-full blur-3xl pointer-events-none"></div>
                    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-indigo-900/20 rounded-full blur-3xl pointer-events-none"></div>

                    <h2 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight max-w-3xl leading-tight relative z-10">
                        Find freelancers who can help you build what's next
                    </h2>

                    <div class="relative z-10 pt-2">
                        <a href="#categories" class="inline-flex items-center justify-center bg-white text-slate-950 font-extrabold text-sm sm:text-base px-8 py-3.5 rounded-full hover:bg-slate-950 hover:text-white transition-all shadow-md hover:scale-105 active:scale-95">
                            Explore freelancers
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Upgraded Footer (Light Theme matching Upwork Structure) -->
        <footer class="bg-slate-50 py-12 px-4 sm:px-6 lg:px-8 border-t border-slate-200/80">
            <div class="max-w-6xl mx-auto bg-white border border-slate-900/10 rounded-3xl p-8 sm:p-12 shadow-xs text-slate-700">
                
                <!-- 4 Columns Link Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 lg:gap-12 pb-10">
                    
                    <!-- Column 1: For Clients -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">For Clients</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-600">
                            <li><a href="#how-it-works" class="hover:text-slate-950 transition-colors">How to hire</a></li>
                            <li><a href="#freelancers" class="hover:text-slate-950 transition-colors">Talent Marketplace</a></li>
                            <li><a href="#categories" class="hover:text-slate-950 transition-colors">Project Catalog</a></li>
                            <li><a href="#categories" class="hover:text-slate-950 transition-colors">Academic Tutoring Hub</a></li>
                            <li><a href="#freelancers" class="hover:text-slate-950 transition-colors">Hire an agency</a></li>
                            <li><a href="#freelancers" class="hover:text-slate-950 transition-colors">Enterprise</a></li>
                            <li><a href="#freelancers" class="hover:text-slate-950 transition-colors">Business Plus</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-950 transition-colors">Any Hire</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-950 transition-colors">Contract to hire</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-950 transition-colors">Direct Contracts</a></li>
                            <li><a href="#freelancers" class="hover:text-slate-950 transition-colors">Hire worldwide</a></li>
                            <li><a href="#freelancers" class="hover:text-slate-950 transition-colors">Hire in Nigeria</a></li>
                        </ul>
                    </div>

                    <!-- Column 2: For Talent -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">For Talent</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-600">
                            <li><a href="#how-it-works" class="hover:text-slate-950 transition-colors">How to find work</a></li>
                            <li><a href="#how-it-works" class="hover:text-slate-950 transition-colors">Direct Contracts</a></li>
                            <li><a href="#categories" class="hover:text-slate-950 transition-colors">Find freelance jobs worldwide</a></li>
                            <li><a href="#categories" class="hover:text-slate-950 transition-colors">Find academic tutoring jobs</a></li>
                            <li><a href="#categories" class="hover:text-slate-950 transition-colors">Find trade jobs near you</a></li>
                            <li><a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="hover:text-slate-950 transition-colors">Win work with proposals</a></li>
                            <li><a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="hover:text-slate-950 transition-colors">Exclusive resources with Freelancer Plus</a></li>
                        </ul>
                    </div>

                    <!-- Column 3: Resources -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Resources</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-600">
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Help & support</a></li>
                            <li><a href="#testimonials" class="hover:text-slate-950 transition-colors">Success stories</a></li>
                            <li><a href="#testimonials" class="hover:text-slate-950 transition-colors">Skill Marketplace reviews</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Resources & Guides</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Blog</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Affiliate program</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Refer a client</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Free Business Tools</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Release notes</a></li>
                        </ul>
                    </div>

                    <!-- Column 4: Company -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-4">Company</h4>
                        <ul class="space-y-2.5 text-xs font-normal text-slate-600">
                            <li><a href="#" class="hover:text-slate-950 transition-colors">About us</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Leadership</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Investor relations</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Careers</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Our impact</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Press</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Contact us</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Trust, safety & security</a></li>
                            <li><a href="#" class="hover:text-slate-950 transition-colors">Academic & Trade Quality</a></li>
                        </ul>
                    </div>

                </div>

                <!-- Social Icons & Mobile App Row -->
                <div class="pt-8 pb-8 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-6">
                    <!-- Left: Social Media -->
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-700">
                        <span>Follow us</span>
                        <div class="flex items-center gap-2">
                            <!-- Facebook -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                            </a>
                            <!-- LinkedIn -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                            </a>
                            <!-- X (Twitter) -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <!-- YouTube -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                            <!-- Instagram -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-3.5 h-3.5 fill-none stroke-currentColor stroke-2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Right: Mobile App Badges -->
                    <div class="flex items-center gap-4 text-xs font-medium text-slate-700">
                        <span>Mobile app</span>
                        <div class="flex items-center gap-2">
                            <!-- Apple Store Icon -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M18.71 19.5c-.83 1.24-1.71 2.45-3.05 2.47-1.34.03-1.77-.79-3.29-.79-1.53 0-2 .77-3.27.82-1.31.05-2.3-1.32-3.14-2.53C4.25 17 2.94 12.45 4.7 9.39c.87-1.52 2.43-2.48 4.12-2.51 1.28-.02 2.5.87 3.29.87.78 0 2.26-1.07 3.81-.91.65.03 2.47.26 3.64 1.98-.09.06-2.17 1.28-2.15 3.81.03 3.02 2.65 4.03 2.68 4.04-.03.07-.42 1.44-1.38 2.83M15.97 6.82c.62-.75 1.04-1.8 0.93-2.85-.9.04-2 .6-2.65 1.36-.58.67-1.09 1.74-.95 2.77 1.01.08 2.05-.53 2.67-1.28z"/></svg>
                            </a>
                            <!-- Android Icon -->
                            <a href="#" class="w-8 h-8 rounded-full border border-slate-300 hover:border-slate-900 text-slate-600 hover:text-slate-950 flex items-center justify-center transition-all bg-slate-50 hover:bg-slate-100">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M6 18c0 .55.45 1 1 1h1v3c0 .55.45 1 1 1s1-.45 1-1v-3h4v3c0 .55.45 1 1 1s1-.45 1-1v-3h1c.55 0 1-.45 1-1V8H6v10zM3.5 8C2.67 8 2 8.67 2 9.5v7c0 .83.67 1.5 1.5 1.5S5 17.33 5 16.5v-7C5 8.67 4.33 8 3.5 8zm17 0c-.83 0-1.5.67-1.5 1.5v7c0 .83.67 1.5 1.5 1.5s1.5-.67 1.5-1.5v-7c0-.83-.67-1.5-1.5-1.5ptM7.34 4.19l-1.22-1.22c-.2-.2-.51-.2-.71 0-.2.2-.2.51 0 .71l1.34 1.34C5.69 5.86 5 7.12 5 8.5h14c0-1.38-.69-2.64-1.75-3.48l1.34-1.34c.2-.2.2-.51 0-.71-.2-.2-.51-.2-.71 0l-1.22 1.22C14.88 3.59 13.5 3 12 3s-2.88.59-4.66 1.19zM9 6.5c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm6 0c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Copyright & Bottom Legal Bar -->
                <div class="pt-8 border-t border-slate-200/80 flex flex-col md:flex-row items-center justify-between gap-4 text-xs font-normal text-slate-500">
                    <div>
                        © 2015 - {{ date('Y') }} Skill Marketplace® Global LLC
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2 text-slate-600 font-medium">
                        <a href="#" class="hover:text-slate-950 transition-colors">Terms of Service</a>
                        <a href="#" class="hover:text-slate-950 transition-colors">Privacy Policy</a>
                        <a href="#" class="hover:text-slate-950 transition-colors">CA Notice at Collection</a>
                        <a href="#" class="hover:text-slate-950 flex items-center gap-1.5 transition-colors">
                            <span>Your Privacy Choices</span>
                            <span class="w-4 h-2 bg-sky-500 rounded-full inline-block"></span>
                        </a>
                        <a href="#" class="hover:text-slate-950 transition-colors">Accessibility</a>
                        <a href="#" class="hover:text-slate-950 transition-colors">Sitemap</a>
                    </div>
                </div>

            </div>
        </footer>

        @livewireScripts
    </body>
</html>
