<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ searchQuery: '{{ $searchQuery ?? '' }}', selectedCategory: '{{ $selectedCategory ?? 'All' }}', mobileMenuOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ config('app.name', 'Skill Marketplace') }} — Powered by CSISS | Find Skilled Talent & Tutors Near You</title>

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
        </style>
    </head>
    <body class="bg-[#F8FAFC] font-sans antialiased text-slate-900 selection:bg-[#0F172B] selection:text-white">

        <!-- Floating Glassmorphism Navbar (Skill Marketplace Style) -->
        <div class="sticky top-4 z-50 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <header class="bg-white/85 backdrop-blur-2xl border border-slate-200/80 shadow-[0_10px_30px_-10px_rgba(15,23,42,0.08)] rounded-full px-5 sm:px-7 py-3 flex items-center justify-between gap-4 transition-all duration-300">
                
                <!-- Logo & CSISS Badge -->
                <a href="/" class="flex items-center gap-3 group shrink-0">
                    <div class="w-10 h-10 rounded-full bg-[#0F172B] flex items-center justify-center text-white shadow-md group-hover:bg-slate-800 transition-all duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-slate-900 font-extrabold text-base sm:text-lg tracking-tight leading-none block">Skill Marketplace</span>
                            <span class="hidden sm:inline-block px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-600 tracking-wide uppercase">CSISS</span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-medium tracking-wide block sm:inline-block">Powered by CSISS</span>
                    </div>
                </a>

                <!-- Nav Links -->
                <nav class="hidden lg:flex items-center gap-7">
                    <a href="#" class="text-xs font-bold text-[#0F172B]">Home</a>
                    <a href="#how-it-works" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">How It Works</a>
                    <a href="#why-us" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Why Choose Us</a>
                    <a href="#categories" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Categories</a>
                    <a href="#featured-jobs" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Featured Jobs</a>
                    <a href="#testimonials" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">Reviews</a>
                    <a href="#faq" class="text-xs font-semibold text-slate-600 hover:text-[#0F172B] transition-colors">FAQ</a>
                </nav>

                <!-- Action CTA Buttons -->
                <div class="flex items-center gap-3 shrink-0">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-lg shadow-slate-900/15 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2">
                            <span>Dashboard</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="hidden sm:inline-block text-xs font-semibold text-slate-700 hover:text-[#0F172B] px-4 py-2 rounded-full hover:bg-slate-100 transition-colors">
                            Sign In
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="px-6 py-2.5 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-lg shadow-slate-900/15 hover:scale-105 active:scale-95 transition-all duration-200">
                            Get Started
                        </a>
                    @endauth
                </div>

            </header>
        </div>

        <!-- Main Content Area -->
        <main class="space-y-28 sm:space-y-36 lg:space-y-44 pt-10 pb-32 overflow-hidden">

            <!-- Hero Section (Skill Marketplace Reference Structure with Soft Glow & Dashboard Screen Showcase) -->
            <section class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8 sm:pt-14 pb-12 text-center space-y-12">
                
                <!-- Ambient Soft Background Glow Orbs -->
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[48rem] h-[28rem] bg-gradient-to-b from-sky-200/40 via-indigo-100/30 to-transparent rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute top-40 right-10 w-80 h-80 bg-sky-200/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute top-60 left-10 w-80 h-80 bg-slate-300/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 space-y-6 max-w-4xl mx-auto">
                    
                    <!-- Pre-Header Text -->
                    <span class="text-sky-600 text-xs font-extrabold uppercase tracking-widest block">
                        Powered by CSISS — No. 1 Skill Marketplace
                    </span>

                    <!-- Main Hero Title (Solid Crisp Text without Gradient) -->
                    <h1 class="text-4xl sm:text-6xl xl:text-7xl font-black text-slate-900 tracking-tight leading-[1.12]">
                        Find Your Dream Jobs<br />
                        And plan your next future with us
                    </h1>

                    <!-- Subtitle -->
                    <p class="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto font-normal leading-relaxed">
                        We help connecting parents, households, and businesses with certified academic tutors, technicians, and artisans near you.
                    </p>

                    <!-- Hero Action Buttons -->
                    <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                        <a href="#featured-jobs" class="px-8 py-3.5 text-sm font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-xl shadow-slate-900/20 hover:shadow-slate-900/35 hover:scale-105 active:scale-95 transition-all duration-200 flex items-center gap-2">
                            <span>Explore All Jobs</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>

                        <a href="#how-it-works" class="px-7 py-3.5 text-sm font-bold text-slate-800 hover:text-[#0F172B] bg-white/90 hover:bg-white border border-slate-200/90 rounded-full shadow-md hover:shadow-lg transition-all duration-200 flex items-center gap-2.5">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-[#0F172B] flex items-center justify-center text-xs">▶</span>
                            <span>How It Works</span>
                        </a>
                    </div>

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
                            <img src="{{ asset('images/dashboard_screen.png') }}" alt="Skill Marketplace Member Dashboard" class="w-full h-auto object-cover object-top max-h-[500px]" />
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

            <!-- Trusted By Companies & Guilds Logo Bar -->
            <section class="py-14 sm:py-16 bg-white/80 border-y border-slate-200/80 backdrop-blur-md">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Trusted by 2,000+ companies, guilds & households across Nigeria</p>
                    
                    <!-- Demo Black Logos Bar -->
                    <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 md:gap-16 opacity-85 hover:opacity-100 transition-opacity duration-300">
                        
                        <!-- Logo 1: Slack -->
                        <div class="flex items-center gap-2 text-[#0F172B] font-extrabold text-lg sm:text-xl tracking-tight hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-[#0F172B]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M6 15a3 3 0 0 1-3-3 3 3 0 0 1 3-3h3v3a3 3 0 0 1-3 3zm0-6a3 3 0 0 1 3-3 3 3 0 0 1 3 3v3H9a3 3 0 0 1-3-3zm6-3a3 3 0 0 1 3-3 3 3 0 0 1 3 3v3h-3a3 3 0 0 1-3-3zm6 6a3 3 0 0 1 3 3 3 3 0 0 1-3 3h-3v-3a3 3 0 0 1 3-3zm-6 6a3 3 0 0 1-3 3 3 3 0 0 1-3-3v-3h3a3 3 0 0 1 3 3z"/>
                            </svg>
                            <span>Slack</span>
                        </div>

                        <!-- Logo 2: Spotify -->
                        <div class="flex items-center gap-2 text-[#0F172B] font-extrabold text-lg sm:text-xl tracking-tight hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-[#0F172B]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2zm4.586 14.424a.623.623 0 0 1-.857.207c-2.348-1.435-5.304-1.76-8.785-.964a.625.625 0 0 1-.277-1.218c3.811-.872 7.076-.495 9.712 1.118a.624.624 0 0 1 .207.857zm1.222-2.722a.78.78 0 0 1-1.072.257c-2.687-1.652-6.785-2.131-9.965-1.166a.78.78 0 1 1-.453-1.492c3.633-1.103 8.147-.568 11.234 1.328a.78.78 0 0 1 .256 1.073zm.105-2.835c-3.224-1.914-8.54-2.091-11.611-1.159a.936.936 0 1 1-.546-1.79c3.565-1.082 9.431-.87 13.141 1.332a.936.936 0 0 1-.984 1.617z"/>
                            </svg>
                            <span>Spotify</span>
                        </div>

                        <!-- Logo 3: Google -->
                        <div class="flex items-center gap-2 text-[#0F172B] font-extrabold text-lg sm:text-xl tracking-tight hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-[#0F172B]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12.48 10.92v3.28h7.84c-.24 1.84-.853 3.187-1.787 4.133-1.147 1.147-2.933 2.4-6.053 2.4-4.827 0-8.6-3.893-8.6-8.72s3.773-8.72 8.6-8.72c2.6 0 4.507 1.027 5.907 2.347l2.307-2.307C18.747 1.44 15.987 0 12.48 0 5.867 0 .307 5.387.307 12s5.56 12 12.173 12c3.573 0 6.267-1.173 8.373-3.36 2.16-2.16 2.84-5.213 2.84-7.667 0-.76-.053-1.467-.173-2.053H12.48z"/>
                            </svg>
                            <span>Google</span>
                        </div>

                        <!-- Logo 4: Stripe -->
                        <div class="flex items-center gap-1.5 text-[#0F172B] font-extrabold text-xl sm:text-2xl tracking-tighter hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-[#0F172B]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C17.708.747 15.015 0 12.247 0 6.848 0 3.013 2.92 3.013 7.37c0 5.485 7.423 5.765 7.423 8.74 0 1.024-.916 1.488-2.227 1.488-2.61 0-5.464-1.189-7.391-2.24l-.946 5.619C1.862 21.996 4.792 23 8.36 23c5.688 0 9.873-2.76 9.873-7.518 0-5.753-7.382-6.07-7.382-8.74 0-.858.683-1.282 1.834-1.282 2.227 0 4.515.858 6.09 1.631z"/>
                            </svg>
                            <span>stripe</span>
                        </div>

                        <!-- Logo 5: Amazon -->
                        <div class="flex items-center gap-2 text-[#0F172B] font-extrabold text-lg sm:text-xl tracking-tight hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-[#0F172B]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15.93 17.09c-2.84 2.1-7.14 3.23-10.74 1.25-.49-.27-1.05.15-.65.65 2.87 3.55 8.76 4.3 12.78 1.49.52-.37.95-.91.56-1.57-.33-.56-1.42-2.48-1.95-1.82zM18.8 15.65c-.32.42-1.07.45-1.55.2-1.63-.86-3.76-1.24-5.69-1.24-2.82 0-5.48.9-7.44 2.6-.32.28-.79.23-1.04-.12l-.47-.64c-.26-.35-.2-.84.14-1.14 2.37-2.07 5.6-3.15 9.03-3.15 2.32 0 4.88.46 6.84 1.5.47.25.64.84.37 1.31l-.19.68zM12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                            </svg>
                            <span>amazon</span>
                        </div>

                        <!-- Logo 6: CSISS Guild -->
                        <div class="flex items-center gap-2 text-[#0F172B] font-black text-lg sm:text-xl tracking-tight hover:scale-105 transition-transform">
                            <svg class="w-6 h-6 text-[#0F172B]" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-2 16l-4-4 1.41-1.41L10 14.17l6.59-6.59L18 9l-8 8z"/>
                            </svg>
                            <span>CSISS GUILD</span>
                        </div>

                    </div>
                </div>
            </section>

            <!-- Section: How It Works (Skill Marketplace 3 Cards Layout) -->
            <section id="how-it-works" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16 relative">
                
                <!-- Section Header with Pre-Header Pill Badge -->
                <div class="space-y-3 max-w-2xl mx-auto">
                    <div>
                        <span class="px-5 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200/90 text-[#0F172B] shadow-2xs inline-block">
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
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.04)] hover:shadow-xl hover:border-[#0F172B]/30 transition-all duration-300 flex flex-col justify-between space-y-6 relative overflow-hidden group">
                        
                        <!-- Subtle Deep Blue Accent Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-[#0F172B] via-slate-800 to-sky-600 opacity-80 rounded-t-[2rem]"></div>

                        <!-- Top Visual Graphic Container -->
                        <div class="h-60 bg-[#F8FAFC] rounded-2xl p-4 border border-slate-100 flex flex-col justify-between relative overflow-hidden">
                            
                            <!-- Floating User Avatars Cluster -->
                            <div class="relative w-full h-full">
                                <!-- Subtle Background Aura -->
                                <div class="w-28 h-28 bg-sky-200/40 rounded-full blur-xl absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none"></div>

                                <!-- Center Avatar (Prominent) -->
                                <div class="w-14 h-14 rounded-full border-4 border-white shadow-md absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 z-10 overflow-hidden">
                                    <img src="{{ asset('images/avatars/zainab.png') }}" class="w-full h-full object-cover" />
                                </div>

                                <!-- Surrounding Avatars -->
                                <div class="w-9 h-9 rounded-full border-2 border-white shadow-xs absolute top-2 left-1/2 -translate-x-1/2 overflow-hidden z-10">
                                    <img src="{{ asset('images/avatars/babajide.png') }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="w-8 h-8 rounded-full border-2 border-white shadow-xs absolute top-1/2 -translate-y-1/2 left-5 overflow-hidden z-10">
                                    <img src="{{ asset('images/avatars/emeka.png') }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="w-8 h-8 rounded-full border-2 border-white shadow-xs absolute top-1/2 -translate-y-1/2 right-5 overflow-hidden z-10">
                                    <img src="{{ asset('images/avatars/nneka.png') }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="w-7 h-7 rounded-full border-2 border-white opacity-40 absolute top-4 right-8 overflow-hidden">
                                    <img src="{{ asset('images/avatars/funmi.png') }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="w-7 h-7 rounded-full border-2 border-white opacity-40 absolute bottom-12 left-8 overflow-hidden">
                                    <img src="{{ asset('images/avatars/babajide.png') }}" class="w-full h-full object-cover" />
                                </div>
                                <div class="w-7 h-7 rounded-full border-2 border-white opacity-40 absolute bottom-12 right-8 overflow-hidden">
                                    <img src="{{ asset('images/avatars/zainab.png') }}" class="w-full h-full object-cover" />
                                </div>
                            </div>

                            <!-- Bottom Floating Action Bar -->
                            <div class="absolute bottom-3 inset-x-3 bg-white/95 backdrop-blur-md rounded-full px-3 py-1.5 shadow-sm border border-slate-200/80 flex items-center justify-between z-20">
                                <div class="flex items-center gap-1.5">
                                    <div class="flex -space-x-1.5">
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-5 h-5 rounded-full object-cover border border-white" />
                                        <img src="{{ asset('images/avatars/emeka.png') }}" class="w-5 h-5 rounded-full object-cover border border-white" />
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded-full">+20</span>
                                </div>

                                <div class="bg-white hover:bg-slate-50 px-3.5 py-1 rounded-full text-xs font-semibold text-slate-800 border border-slate-300 shadow-2xs flex items-center gap-1 cursor-pointer">
                                    <span class="text-[#0F172B] font-bold">+</span> Register Profile
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
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.04)] hover:shadow-xl hover:border-[#0F172B]/30 transition-all duration-300 flex flex-col justify-between space-y-6 relative overflow-hidden group">
                        
                        <!-- Subtle Deep Blue Accent Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-sky-600 via-[#0F172B] to-slate-800 opacity-80 rounded-t-[2rem]"></div>

                        <!-- Top Visual Graphic Container (Card Layering Effect) -->
                        <div class="h-60 bg-[#F8FAFC] rounded-2xl p-4 border border-slate-100 flex items-center justify-center relative overflow-hidden">
                            
                            <!-- Layered Background Cards Sticking Out -->
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 w-32 h-44 bg-white/70 border border-slate-200/60 rounded-2xl shadow-2xs transform -rotate-6"></div>
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 w-32 h-44 bg-white/70 border border-slate-200/60 rounded-2xl shadow-2xs transform rotate-6"></div>

                            <!-- Center Floating Modal Card -->
                            <div class="relative z-10 bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xl w-52 text-left space-y-3 transform hover:scale-[1.02] transition-transform">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-800">CSISS Verification</span>
                                    <span class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center text-[10px] text-slate-400 font-bold">•••</span>
                                </div>
                                <div class="space-y-2 pt-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#0F172B] font-bold text-xs">✓</span>
                                        <div class="h-1.5 bg-slate-200/90 rounded-full w-28"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#0F172B] font-bold text-xs">✓</span>
                                        <div class="h-1.5 bg-slate-200/90 rounded-full w-20"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#0F172B] font-bold text-xs">✓</span>
                                        <div class="h-1.5 bg-slate-200/90 rounded-full w-24"></div>
                                    </div>
                                </div>

                                <div class="pt-1">
                                    <span class="w-full bg-[#0F172B] text-white hover:bg-slate-800 font-semibold text-xs py-2 rounded-full block text-center transition-colors cursor-pointer shadow-xs">
                                        Browse Talent
                                    </span>
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
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.04)] hover:shadow-xl hover:border-[#0F172B]/30 transition-all duration-300 flex flex-col justify-between space-y-6 relative overflow-hidden group">
                        
                        <!-- Subtle Deep Blue Accent Top Border -->
                        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-slate-800 via-[#0F172B] to-sky-600 opacity-80 rounded-t-[2rem]"></div>

                        <!-- Top Visual Graphic Container (Apply Card & Social Media Icons) -->
                        <div class="h-60 bg-[#F8FAFC] rounded-2xl p-4 border border-slate-100 flex flex-col items-center justify-center relative overflow-hidden">
                            
                            <!-- Top Floating Brand Tag -->
                            <div class="bg-white/95 backdrop-blur-sm border border-slate-200/80 rounded-full px-3.5 py-1 shadow-2xs flex items-center gap-2 mb-2 z-10">
                                <div class="w-5 h-5 rounded-full bg-[#0F172B] text-white font-bold text-[9px] flex items-center justify-center">CS</div>
                                <div class="text-left leading-tight">
                                    <span class="text-xs font-bold text-slate-800 block">CSISS Guild</span>
                                    <span class="text-[9px] text-slate-400 block font-normal">Verified Professional</span>
                                </div>
                            </div>

                            <!-- Floating Social Brand Badges -->
                            <div class="w-6 h-6 rounded-full bg-[#0F172B] text-white flex items-center justify-center font-bold text-[10px] shadow-xs absolute top-12 left-6">✓</div>
                            <div class="w-6 h-6 rounded-full bg-sky-600 text-white flex items-center justify-center text-[10px] shadow-xs absolute top-28 left-4">📚</div>
                            <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-[9px] absolute bottom-6 left-3">⚡</div>
                            <div class="w-6 h-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow-xs absolute top-14 right-6">🔧</div>
                            <div class="w-6 h-6 rounded-full bg-[#0F172B] text-white flex items-center justify-center font-bold text-[9px] shadow-xs absolute top-28 right-4">★</div>
                            <div class="w-5 h-5 rounded-full bg-sky-500 text-white flex items-center justify-center font-bold text-[9px] absolute bottom-8 right-3">🎓</div>

                            <!-- Center Floating Modal Card with Deep Blue Top Border -->
                            <div class="relative z-10 bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xl w-52 text-left space-y-3 overflow-hidden relative before:absolute before:inset-x-0 before:top-0 before:h-1 before:bg-gradient-to-r before:from-[#0F172B] via-sky-600 before:to-slate-800">
                                <div class="space-y-2 pt-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#0F172B] font-bold text-xs">✓</span>
                                        <div class="h-1.5 bg-slate-200/90 rounded-full w-28"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#0F172B] font-bold text-xs">✓</span>
                                        <div class="h-1.5 bg-slate-200/90 rounded-full w-20"></div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[#0F172B] font-bold text-xs">✓</span>
                                        <div class="h-1.5 bg-slate-200/90 rounded-full w-24"></div>
                                    </div>
                                </div>

                                <div class="pt-1">
                                    <span class="w-full bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 rounded-full block text-center transition-colors cursor-pointer shadow-xs">
                                        Message & Hire
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Title & Description (Centered) -->
                        <div class="space-y-2 text-center">
                            <h3 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Connect & Hire Safely</h3>
                            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed font-normal">
                                Message professionals directly, discuss home tutoring schedules or trade project details, and hire with CSISS background assurance.
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
                            <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-sky-50 text-[#0F172B] border border-sky-200/80 inline-block">
                                Why Choose us
                            </span>
                        </div>

                        <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                            What Makes Us Different
                        </h2>
                        
                        <p class="text-slate-500 text-sm sm:text-base leading-relaxed font-normal">
                            Built specifically for Nigerian households, parents, and businesses seeking vetted academic tutors and trusted trade artisans.
                        </p>

                        <!-- 3 Feature Items Stacked (Bold title + description) -->
                        <div class="space-y-6 pt-2">
                            
                            <!-- Item 1 -->
                            <div class="space-y-1">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900">CSISS Credential & Background Verification</h4>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal">
                                    Every academic tutor and artisan undergoes identity checking and CSISS qualification verification, giving parents and clients complete peace of mind for home tutoring and property repairs.
                                </p>
                            </div>

                            <!-- Item 2 -->
                            <div class="space-y-1">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900">Dual Academic Tutoring & Skilled Trades Focus</h4>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal">
                                    Whether you need a WAEC Physics tutor, an IGCSE Math teacher, a solar electrician, or a plumber, our platform specializes in both academic excellence and trade expertise on one single hub.
                                </p>
                            </div>

                            <!-- Item 3 -->
                            <div class="space-y-1">
                                <h4 class="text-base sm:text-lg font-bold text-slate-900">Direct Local Connections & Transparent Rates</h4>
                                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-normal">
                                    Find top-rated talent in your immediate city (Lagos, Abuja, Port Harcourt, Ibadan, & more). Contact professionals directly with upfront rate expectations and verified community reviews.
                                </p>
                            </div>

                        </div>
                    </div>

                    <!-- Right Column: 2x2 Floating Feature Cards Container with Soft Deep Blue Tinted Backdrop -->
                    <div class="lg:col-span-6 bg-gradient-to-tr from-slate-100 via-sky-50 to-slate-100 p-6 sm:p-9 rounded-[2.5rem] border border-slate-200 shadow-inner">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            
                            <!-- Card 1 (Smart Matches) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-sm font-extrabold text-slate-900">CSISS Match</h5>
                                    <div class="w-8 h-8 rounded-full bg-[#0F172B] text-white flex items-center justify-center font-bold text-[10px]">
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
                                        <span class="w-5 h-5 rounded-full bg-sky-100 text-sky-800 flex items-center justify-center text-[10px] font-bold">✓</span>
                                    </div>
                                    <div class="flex items-center gap-1 text-[9px] font-semibold text-slate-500">
                                        <span class="px-1.5 py-0.5 rounded bg-sky-50 text-sky-800">In-Person</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100">Online</span>
                                        <span class="px-1.5 py-0.5 rounded bg-slate-100">Lagos</span>
                                    </div>
                                </div>

                                <div class="text-[11px] font-extrabold text-slate-900 pt-1">
                                    95% <span class="text-slate-500 font-normal">Match Score</span>
                                </div>
                            </div>

                            <!-- Card 2 (CSISS Verification) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3 sm:translate-y-3">
                                <h5 class="text-sm font-extrabold text-slate-900">CSISS Verification</h5>

                                <div class="bg-slate-50 rounded-2xl p-3 border border-slate-100 flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold shrink-0">
                                        ✓
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-slate-900 block truncate">Vetted_Profile.pdf</span>
                                        <span class="text-[9px] text-slate-400 font-medium block">Identity Verified</span>
                                    </div>
                                </div>

                                <div class="pt-1">
                                    <span class="w-full bg-[#0F172B] text-white font-bold text-xs py-2 px-4 rounded-full text-center block shadow-xs">
                                        Verification Approved
                                    </span>
                                </div>

                                <span class="text-[10px] text-emerald-600 font-bold block text-center">
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
                                        <div class="absolute left-2 w-2/3 top-1/2 -translate-y-1/2 h-1 bg-[#0F172B] z-0"></div>

                                        <div class="w-4 h-4 rounded-full bg-[#0F172B] ring-2 ring-white z-10"></div>
                                        <div class="w-4 h-4 rounded-full bg-[#0F172B] ring-2 ring-white z-10"></div>
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-[#0F172B] z-10" />
                                        <div class="w-4 h-4 rounded-full bg-slate-200 ring-2 ring-white z-10"></div>
                                    </div>

                                    <div class="flex items-center justify-between text-[9px] font-bold text-slate-500 pt-1">
                                        <span>Search</span>
                                        <span>Inquire</span>
                                        <span class="text-[#0F172B]">Connected</span>
                                        <span>Hired</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card 4 (Session Scheduled) -->
                            <div class="bg-white rounded-3xl p-5 shadow-md shadow-slate-950/5 border border-slate-200/80 space-y-3 sm:translate-y-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-sm font-bold shrink-0">
                                        📅
                                    </div>
                                    <div>
                                        <h5 class="text-xs font-extrabold text-slate-900 block leading-tight">Session Scheduled</h5>
                                        <span class="text-[10px] text-sky-700 font-semibold block">Tomorrow at 10:30 AM</span>
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

            <!-- Section: Categories (12 Categories Grid) -->
            <section id="categories" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16 relative">
                
                <div class="space-y-2 max-w-2xl mx-auto">
                    <span class="text-sky-600 text-xs font-extrabold uppercase tracking-widest block">
                        EXPLORE CATEGORIES
                    </span>
                    <h2 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight">Popular Service Categories</h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal">Whatever the task, find experienced local artisans and academic tutors ready to help in your area.</p>
                </div>

                <!-- 12-Card Category Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">

                    <!-- 1. Academic Tutoring -->
                    <a href="{{ url('/talent?category=Academic+Tutoring') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Academic Tutoring</h4>
                                <span class="text-xs text-slate-500 font-medium">874 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 2. Exam Prep & Languages -->
                    <a href="{{ url('/talent?category=Exam+Prep') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Exam Prep & Languages</h4>
                                <span class="text-xs text-slate-500 font-medium">620 tutors</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 3. STEM & Sciences -->
                    <a href="{{ url('/talent?category=STEM') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
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
                    <a href="{{ url('/talent?category=Plumbing') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 011 1V4z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Plumbing</h4>
                                <span class="text-xs text-slate-500 font-medium">542 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 5. Electrical -->
                    <a href="{{ url('/talent?category=Electrical') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Electrical</h4>
                                <span class="text-xs text-slate-500 font-medium">389 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 6. Carpentry -->
                    <a href="{{ url('/talent?category=Carpentry') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
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
                    <a href="{{ url('/talent?category=Painting') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Painting</h4>
                                <span class="text-xs text-slate-500 font-medium">401 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 8. Cleaning -->
                    <a href="{{ url('/talent?category=Cleaning') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Cleaning</h4>
                                <span class="text-xs text-slate-500 font-medium">625 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 9. Photography -->
                    <a href="{{ url('/talent?category=Photography') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
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
                    <a href="{{ url('/talent?category=Web+Development') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Web Development</h4>
                                <span class="text-xs text-slate-500 font-medium">488 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 11. Graphic Design -->
                    <a href="{{ url('/talent?category=Graphic+Design') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-[#0F172B] transition-colors">Graphic Design</h4>
                                <span class="text-xs text-slate-500 font-medium">350 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-[#0F172B] group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>

                    <!-- 12. Music & Instrument Tutors -->
                    <a href="{{ url('/talent?category=Music') }}" class="group flex items-center justify-between p-5 bg-white hover:bg-slate-100/70 border border-slate-200/80 rounded-2xl transition-all duration-300 shadow-2xs hover:shadow-md hover:border-[#0F172B] cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#0F172B] flex items-center justify-center group-hover:scale-110 group-hover:bg-[#0F172B] group-hover:text-white transition-all mr-3.5 shrink-0">
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

            <!-- Section: Top Featured Jobs (Skill Marketplace Pixel-Perfect 6 Card Layout) -->
            <section id="featured-jobs" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16 relative">
                
                <!-- Section Header with Pre-Header Pill Badge -->
                <div class="space-y-3 max-w-2xl mx-auto">
                    <div>
                        <span class="px-5 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200/90 text-[#0F172B] shadow-2xs inline-block">
                            Featured Jobs
                        </span>
                    </div>

                    <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-900 tracking-tight">Top Featured Jobs</h2>
                    <p class="text-slate-500 text-sm sm:text-base font-normal leading-relaxed max-w-xl mx-auto">
                        Explore the best opportunities available today means discovering the most promising paths, industries, and careers that are growing right now.
                    </p>
                </div>

                <!-- 6 Featured Job Cards Grid (Matching Reference Screenshot Layout) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 text-left">
                    
                    <!-- Job Card 1 (Highlighted Active Card with Gradient Fill & Deep Blue CTA) -->
                    <div class="bg-gradient-to-br from-sky-50/80 via-white to-slate-100 border-2 border-[#0F172B] rounded-[2rem] p-6 lg:p-7 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        <!-- Top Row: Company Info & Bookmark -->
                        <div class="flex items-start justify-between">
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-bold text-slate-900 leading-tight">Dribbble</h4>
                                <span class="text-xs text-slate-400 font-medium block">Northam Office</span>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-700 shadow-2xs hover:border-[#0F172B] transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>

                        <!-- Job Title -->
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Product Designer</h3>
                        </div>

                        <!-- Tags Row (White Pill Badges) -->
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3.5 py-1 rounded-full bg-white border border-slate-200/70 text-[11px] font-semibold text-[#0F172B] shadow-2xs">Full Time</span>
                            <span class="px-3.5 py-1 rounded-full bg-white border border-slate-200/70 text-[11px] font-semibold text-[#0F172B] shadow-2xs">Remote</span>
                            <span class="px-3.5 py-1 rounded-full bg-white border border-slate-200/70 text-[11px] font-semibold text-[#0F172B] shadow-2xs">Part Time</span>
                        </div>

                        <!-- Bottom Row: Salary, Applicant Avatars, & Apply Button -->
                        <div class="pt-2 flex items-end justify-between border-t border-slate-200">
                            <div class="space-y-1.5">
                                <div class="text-xs font-bold text-slate-900">$90K-$110K <span class="text-[10px] text-slate-500 font-normal">/ Year</span></div>
                                <div class="flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/emeka.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/zainab.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                    </div>
                                    <span class="w-5 h-5 rounded-full bg-sky-100 text-sky-800 flex items-center justify-center text-[10px] font-bold">+</span>
                                </div>
                            </div>

                            <a href="{{ url('/dashboard/messages') }}" class="bg-gradient-to-r from-[#0F172B] to-[#1E293B] hover:from-[#1E293B] hover:to-[#0F172B] text-white rounded-full px-5 py-2 text-xs font-bold shadow-md hover:shadow-lg flex items-center gap-1.5 transition-all">
                                <span>Apply</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        </div>

                    </div>

                    <!-- Job Card 2 (Behance) -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.03)] hover:shadow-xl hover:border-slate-400 transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        <!-- Top Row: Company Logo, Info & Bookmark -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#1769FF] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    Be
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight">Behance</h4>
                                    <span class="text-xs text-slate-400 font-medium block">Ronikal Office</span>
                                </div>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 shadow-2xs transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>

                        <!-- Job Title -->
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Web Developer</h3>
                        </div>

                        <!-- Tags Row -->
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0F172B]">
                            <span>Full Time</span>
                            <span>Remote</span>
                            <span>Part Time</span>
                        </div>

                        <!-- Bottom Row -->
                        <div class="pt-2 flex items-end justify-between border-t border-slate-100">
                            <div class="space-y-1.5">
                                <div class="text-xs font-bold text-slate-900">$80K-$90K <span class="text-[10px] text-slate-500 font-normal">/ Year</span></div>
                                <div class="flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        <img src="{{ asset('images/avatars/babajide.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/nneka.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                    </div>
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">+</span>
                                </div>
                            </div>

                            <a href="{{ url('/dashboard/messages') }}" class="text-slate-900 font-bold text-xs hover:text-sky-600 flex items-center gap-1 transition-colors group-hover:translate-x-0.5">
                                <span>Apply</span>
                                <span>→</span>
                            </a>
                        </div>

                    </div>

                    <!-- Job Card 3 (Upwork) -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.03)] hover:shadow-xl hover:border-slate-400 transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        <!-- Top Row: Company Logo, Info & Bookmark -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#14A800] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    up
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight">Upwork</h4>
                                    <span class="text-xs text-slate-400 font-medium block">Southam Office</span>
                                </div>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 shadow-2xs transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>

                        <!-- Job Title -->
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">WordPress Developer</h3>
                        </div>

                        <!-- Tags Row -->
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0F172B]">
                            <span>Full Time</span>
                            <span>Remote</span>
                            <span>Part Time</span>
                        </div>

                        <!-- Bottom Row -->
                        <div class="pt-2 flex items-end justify-between border-t border-slate-100">
                            <div class="space-y-1.5">
                                <div class="text-xs font-bold text-slate-900">$75K-$105K <span class="text-[10px] text-slate-500 font-normal">/ Year</span></div>
                                <div class="flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        <img src="{{ asset('images/avatars/emeka.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/zainab.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/babajide.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                    </div>
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">+</span>
                                </div>
                            </div>

                            <a href="{{ url('/dashboard/messages') }}" class="text-slate-900 font-bold text-xs hover:text-sky-600 flex items-center gap-1 transition-colors group-hover:translate-x-0.5">
                                <span>Apply</span>
                                <span>→</span>
                            </a>
                        </div>

                    </div>

                    <!-- Job Card 4 (Dribbble - Sr. UI Designer) -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.03)] hover:shadow-xl hover:border-slate-400 transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        <!-- Top Row: Company Logo, Info & Bookmark -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-[#0A66C2] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    in
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight">Dribbble</h4>
                                    <span class="text-xs text-slate-400 font-medium block">Northam Office</span>
                                </div>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 shadow-2xs transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>

                        <!-- Job Title -->
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Sr. UI Designer</h3>
                        </div>

                        <!-- Tags Row -->
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0F172B]">
                            <span>Full Time</span>
                            <span>Remote</span>
                            <span>Part Time</span>
                        </div>

                        <!-- Bottom Row -->
                        <div class="pt-2 flex items-end justify-between border-t border-slate-100">
                            <div class="space-y-1.5">
                                <div class="text-xs font-bold text-slate-900">$70K-$100K <span class="text-[10px] text-slate-500 font-normal">/ Year</span></div>
                                <div class="flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/babajide.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                    </div>
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">+</span>
                                </div>
                            </div>

                            <a href="{{ url('/dashboard/messages') }}" class="text-slate-900 font-bold text-xs hover:text-sky-600 flex items-center gap-1 transition-colors group-hover:translate-x-0.5">
                                <span>Apply</span>
                                <span>→</span>
                            </a>
                        </div>

                    </div>

                    <!-- Job Card 5 (Instagram) -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.03)] hover:shadow-xl hover:border-slate-400 transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        <!-- Top Row: Company Logo, Info & Bookmark -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 via-rose-500 to-sky-600 text-white font-bold text-xs flex items-center justify-center shrink-0">
                                    📷
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight">Instagram</h4>
                                    <span class="text-xs text-slate-400 font-medium block">Southam Office</span>
                                </div>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 shadow-2xs transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>

                        <!-- Job Title -->
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Marketing Manager</h3>
                        </div>

                        <!-- Tags Row -->
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0F172B]">
                            <span>Full Time</span>
                            <span>Remote</span>
                            <span>Part Time</span>
                        </div>

                        <!-- Bottom Row -->
                        <div class="pt-2 flex items-end justify-between border-t border-slate-100">
                            <div class="space-y-1.5">
                                <div class="text-xs font-bold text-slate-900">$60K-$80K <span class="text-[10px] text-slate-500 font-normal">/ Year</span></div>
                                <div class="flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        <img src="{{ asset('images/avatars/zainab.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/nneka.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/emeka.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                    </div>
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">+</span>
                                </div>
                            </div>

                            <a href="{{ url('/dashboard/messages') }}" class="text-slate-900 font-bold text-xs hover:text-sky-600 flex items-center gap-1 transition-colors group-hover:translate-x-0.5">
                                <span>Apply</span>
                                <span>→</span>
                            </a>
                        </div>

                    </div>

                    <!-- Job Card 6 (Google) -->
                    <div class="bg-white border border-slate-200/90 rounded-[2rem] p-6 lg:p-7 shadow-[0_10px_30px_-5px_rgba(0,0,0,0.03)] hover:shadow-xl hover:border-slate-400 transition-all duration-300 flex flex-col justify-between space-y-5 relative overflow-hidden group">
                        
                        <!-- Top Row: Company Logo, Info & Bookmark -->
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-white border border-slate-200/80 text-red-500 font-bold text-xs flex items-center justify-center shrink-0 shadow-2xs">
                                    G
                                </div>
                                <div class="space-y-0.5">
                                    <h4 class="text-sm font-bold text-slate-900 leading-tight">Google</h4>
                                    <span class="text-xs text-slate-400 font-medium block">Northam Office</span>
                                </div>
                            </div>
                            <button class="w-9 h-9 rounded-full bg-white border border-slate-200/80 flex items-center justify-center text-slate-400 hover:text-slate-700 shadow-2xs transition-colors shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/></svg>
                            </button>
                        </div>

                        <!-- Job Title -->
                        <div>
                            <h3 class="text-xl font-bold text-slate-900 tracking-tight">Lead UI/UX Designer</h3>
                        </div>

                        <!-- Tags Row -->
                        <div class="flex flex-wrap items-center gap-3 text-xs font-semibold text-[#0F172B]">
                            <span>Full Time</span>
                            <span>Remote</span>
                            <span>Part Time</span>
                        </div>

                        <!-- Bottom Row -->
                        <div class="pt-2 flex items-end justify-between border-t border-slate-100">
                            <div class="space-y-1.5">
                                <div class="text-xs font-bold text-slate-900">$90K-$110K <span class="text-[10px] text-slate-500 font-normal">/ Year</span></div>
                                <div class="flex items-center gap-1">
                                    <div class="flex -space-x-2">
                                        <img src="{{ asset('images/avatars/funmi.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                        <img src="{{ asset('images/avatars/babajide.png') }}" class="w-6 h-6 rounded-full object-cover ring-2 ring-white" />
                                    </div>
                                    <span class="w-5 h-5 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">+</span>
                                </div>
                            </div>

                            <a href="{{ url('/dashboard/messages') }}" class="text-slate-900 font-bold text-xs hover:text-sky-600 flex items-center gap-1 transition-colors group-hover:translate-x-0.5">
                                <span>Apply</span>
                                <span>→</span>
                            </a>
                        </div>

                    </div>

                </div>

                <!-- Bottom Floating Deep Blue View More Button -->
                <div class="pt-4">
                    <a href="{{ url('/talent') }}" class="inline-flex items-center justify-center px-9 py-3.5 text-sm font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-lg shadow-slate-900/25 hover:shadow-slate-900/40 hover:scale-105 active:scale-95 transition-all text-center mx-auto">
                        View More
                    </a>
                </div>

            </section>

            <!-- Section: Built for Job Seekers & Employers (Dual Tinted Cards) -->
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Card 1: Job Seekers -->
                    <div class="bg-[#EEF4FF] border border-blue-100 rounded-[2.5rem] p-8 sm:p-10 shadow-sm space-y-6 flex flex-col justify-between">
                        <div class="space-y-3">
                            <span class="text-sky-600 text-xs font-extrabold uppercase tracking-widest block">FOR TALENT & TUTORS</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Built for Job Seekers & Tutors</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Connect directly with parents and households looking for private tutors, electricians, carpenters, and artisans near you.
                            </p>
                        </div>

                        <!-- Card Visual Graphic -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-blue-100 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#0F172B] text-white flex items-center justify-center font-bold text-sm">
                                    🔍
                                </div>
                                <div class="text-left">
                                    <span class="text-xs font-bold text-slate-900 block">Search Opportunities</span>
                                    <span class="text-[10px] text-slate-500">100+ new jobs today</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-sky-700 bg-sky-50 px-3 py-1 rounded-full">Active</span>
                        </div>

                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="inline-flex items-center justify-center px-7 py-3 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-md transition-all self-start">
                            Create Talent Profile →
                        </a>
                    </div>

                    <!-- Card 2: Employers -->
                    <div class="bg-slate-100/80 border border-slate-200/90 rounded-[2.5rem] p-8 sm:p-10 shadow-sm space-y-6 flex flex-col justify-between">
                        <div class="space-y-3">
                            <span class="text-sky-600 text-xs font-extrabold uppercase tracking-widest block">FOR PARENTS & CLIENTS</span>
                            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Built for Employers & Parents</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Post custom tutoring tasks or trade projects to receive bids from verified local professionals backed by CSISS standards.
                            </p>
                        </div>

                        <!-- Card Visual Graphic -->
                        <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-[#0F172B] text-white flex items-center justify-center font-bold text-sm">
                                    📋
                                </div>
                                <div class="text-left">
                                    <span class="text-xs font-bold text-slate-900 block">Post Custom Task</span>
                                    <span class="text-[10px] text-slate-500">Receive bids in 15 mins</span>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-sky-800 bg-sky-100 px-3 py-1 rounded-full">Easy Post</span>
                        </div>

                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="inline-flex items-center justify-center px-7 py-3 text-xs font-bold text-white bg-[#0F172B] hover:bg-slate-800 rounded-full shadow-md transition-all self-start">
                            Post An Opportunity →
                        </a>
                    </div>

                </div>
            </section>

            <!-- Section: What Our Users Say (Skill Marketplace 5-Card Testimonial Grid matching Reference Screenshot) -->
            <section id="testimonials" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14 text-center space-y-14 sm:space-y-16">
                
                <div class="space-y-2 max-w-2xl mx-auto">
                    <span class="text-sky-600 text-xs font-extrabold uppercase tracking-widest block">
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
                            <div class="flex items-center gap-1 text-amber-400">★★★★★</div>
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
                            <div class="flex items-center gap-1 text-amber-400">★★★★★</div>
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
                    <div class="bg-gradient-to-b from-[#0F172B] via-slate-900 to-sky-950 text-white rounded-[2.5rem] p-8 shadow-xl flex flex-col justify-between relative overflow-hidden border border-slate-800 min-h-[380px]">
                        <div class="space-y-3 relative z-10">
                            <span class="px-3 py-1 rounded-full bg-white/10 text-sky-200 text-[10px] font-bold border border-white/20">FEATURED TUTOR STORY</span>
                            <div class="flex items-center gap-1 text-amber-300 pt-2">★★★★★</div>
                            <h4 class="text-xl font-black text-white leading-snug">
                                "Skill Marketplace helped me build a full-time tutoring practice safely."
                            </h4>
                            <p class="text-slate-300 text-xs leading-relaxed">
                                "Protected phone numbers until clients accept gave me total safety. I now tutor 5 students weekly across Abuja."
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-white/20 relative z-10">
                            <img src="{{ asset('images/avatars/zainab.png') }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-white" />
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
                            <div class="flex items-center gap-1 text-amber-400">★★★★★</div>
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
                            <div class="flex items-center gap-1 text-amber-400">★★★★★</div>
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
                                Have questions about hiring verified tutors or listing your trade skills on Skill Marketplace — Powered by CSISS? Find clear answers below.
                            </p>
                        </div>

                        <!-- Accordion Rows -->
                        <div class="space-y-3">
                            
                            <!-- FAQ 1 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
                                <button @click="activeFaq = (activeFaq === 1 ? null : 1)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-sky-600 transition-colors">
                                    <span>What is Skill Marketplace — Powered by CSISS?</span>
                                    <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center text-base font-bold shrink-0 ml-2" x-text="activeFaq === 1 ? '−' : '+'"></span>
                                </button>
                                <div x-show="activeFaq === 1" x-collapse class="px-5 pb-4 text-xs text-slate-500 leading-relaxed font-normal pt-1 border-t border-slate-100">
                                    Skill Marketplace is Nigeria's premier digital directory connecting parents, households, and businesses with verified academic tutors (for WAEC, JAMB, primary & secondary subjects) and skilled trade artisans (electricians, plumbers, carpenters, technicians) near them.
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
                                    Yes! Joining Skill Marketplace as a talent or tutor is completely free. You can create a detailed profile showcasing your subjects, skills, past work, location, and hourly or monthly rates.
                                </div>
                            </div>

                            <!-- FAQ 5 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-2xs">
                                <button @click="activeFaq = (activeFaq === 5 ? null : 5)" class="w-full px-5 py-4 flex items-center justify-between text-left font-bold text-xs sm:text-sm text-slate-900 hover:text-sky-600 transition-colors">
                                    <span>What locations in Nigeria are covered by the marketplace?</span>
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
                        <span class="px-5 py-1.5 rounded-full text-xs font-semibold bg-white border border-slate-200 text-[#0F172B] shadow-2xs inline-block">
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
                            <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="bg-[#0F172B] hover:bg-slate-800 text-white px-6 py-2.5 rounded-full text-xs font-bold shadow-md hover:shadow-lg transition-all shrink-0">
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
                            <div class="w-8 h-8 rounded-full bg-[#0F172B] text-white font-bold flex items-center justify-center text-sm shadow-xs">
                                ⚡
                            </div>
                            <span class="text-xl font-bold text-slate-900 tracking-tight">Skill Marketplace</span>
                        </div>

                        <p class="text-xs text-slate-500 font-normal leading-relaxed max-w-xs">
                            Your trusted partner in finding the perfect career opportunity.
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
                        © 2026 Skill Marketplace — Powered by CSISS. All rights reserved.
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
                Skill Marketplace
            </div>

        </footer>

        @livewireScripts
    </body>
</html>
