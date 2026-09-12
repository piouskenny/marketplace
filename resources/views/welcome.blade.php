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

        <!-- Navigation Bar -->
        <header class="absolute top-0 left-0 right-0 z-50 w-full">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-sky-500/20 border border-sky-400/30 flex items-center justify-center backdrop-blur-sm group-hover:border-sky-400 transition-colors">
                        <svg class="w-5 h-5 text-sky-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-white font-bold text-lg tracking-tight">Skill Marketplace</span>
                </a>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#professionals" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Find Professionals</a>
                    <a href="#how-it-works" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">How It Works</a>
                    <a href="#categories" class="text-sm font-medium text-slate-300 hover:text-white transition-colors">Categories</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-4 py-2 text-sm font-semibold text-white bg-sky-600 hover:bg-sky-500 rounded-lg shadow-md shadow-sky-600/20 transition-all">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ Route::has('login') ? route('login') : url('/login') }}" class="px-4 py-2 text-sm font-medium text-slate-200 hover:text-white bg-slate-900/80 hover:bg-slate-800 border border-slate-700/80 rounded-lg transition-all backdrop-blur-sm">
                            Log in
                        </a>
                        <a href="{{ Route::has('register') ? route('register') : url('/register') }}" class="px-4 py-2 text-sm font-semibold text-slate-950 bg-sky-400 hover:bg-sky-300 rounded-lg shadow-md shadow-sky-400/20 transition-all">
                            Sign up
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative min-h-[660px] lg:min-h-[720px] flex flex-col justify-center items-center text-center px-4 pt-28 pb-20 overflow-hidden">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/hero_bg.png') }}" alt="Skilled Artisans" class="w-full h-full object-cover object-center" />
                <div class="absolute inset-0 bg-gradient-to-b from-slate-950/85 via-slate-950/80 to-slate-950"></div>
                <div class="absolute inset-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-sky-900/20 via-transparent to-transparent"></div>
            </div>

            <!-- Content -->
            <div class="relative z-10 max-w-4xl mx-auto flex flex-col items-center">
                <!-- Top Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-900/80 border border-sky-500/40 text-sky-300 text-xs font-semibold backdrop-blur-md mb-6 shadow-lg">
                    <svg class="w-3.5 h-3.5 text-sky-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span>Trusted local talent, one search away</span>
                </div>

                <!-- Main Heading -->
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white tracking-tight leading-[1.15] mb-5">
                    Find Skilled Professionals <br class="hidden sm:inline" />Near You
                </h1>

                <!-- Subtitle -->
                <p class="text-slate-300 text-base sm:text-lg max-w-2xl mb-10 font-normal leading-relaxed">
                    Connect with trusted artisans, freelancers and service providers in your area
                </p>

                <!-- Search Container Card -->
                <div class="w-full max-w-3xl bg-slate-900/90 backdrop-blur-xl border border-slate-800/90 rounded-2xl p-5 sm:p-7 shadow-2xl text-left">
                    <label class="text-[11px] font-bold tracking-widest text-slate-400 uppercase mb-3 block">
                        WHAT DO YOU NEED FROM YOUR LOCAL TEAM?
                    </label>

                    <!-- Search Form -->
                    <form action="#" method="GET" class="relative flex items-center">
                        <div class="relative w-full">
                            <svg class="w-5 h-5 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input 
                                type="text" 
                                name="query" 
                                placeholder="Search for a service, trade or skill" 
                                class="w-full bg-slate-950/90 border border-slate-800 focus:border-sky-500 text-white placeholder-slate-400 text-sm sm:text-base rounded-xl pl-11 pr-32 py-3.5 outline-none transition-all shadow-inner"
                            />
                            <button 
                                type="submit" 
                                class="absolute right-1.5 top-1.2/2 -translate-y-1.2/2 bg-sky-500 hover:bg-sky-400 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition-all shadow-md shadow-sky-500/20"
                            >
                                Search
                            </button>
                        </div>
                    </form>

                    <!-- Quick Filter Tags -->
                    <div class="flex flex-wrap items-center gap-2 mt-4 pt-1">
                        @foreach(['Home repair', 'Cleaning', 'Photography', 'Furniture assembly', 'Pet sitting', 'Moving help'] as $tag)
                            <a href="#categories" class="px-3.5 py-1.5 rounded-full text-xs font-medium bg-slate-800/80 hover:bg-slate-700/80 text-slate-300 border border-slate-700/60 transition-all shadow-2xs hover:text-white">
                                {{ $tag }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 2: How It Works -->
        <section id="how-it-works" class="bg-white py-24 px-4 sm:px-6 lg:px-8 border-b border-slate-100">
            <div class="max-w-6xl mx-auto text-center">
                <span class="text-sky-600 text-xs font-bold uppercase tracking-widest mb-2 block">SIMPLE AND SECURE</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">How it works</h2>
                <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto mb-16 leading-relaxed">
                    From first search to finished project, connect with the right professional in three straightforward steps.
                </p>

                <!-- Steps Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                    <!-- Step 1 -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-7 text-left shadow-xs hover:shadow-md transition-all relative flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center mb-5 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-sky-600 tracking-wider mb-2 block">01</span>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Search</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Tell us what you need and discover skilled professionals near you.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-7 text-left shadow-xs hover:shadow-md transition-all relative flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center mb-5 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-sky-600 tracking-wider mb-2 block">02</span>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Connect</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Review profiles, ratings and send a connection request with confidence.
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="bg-white border border-slate-200/90 rounded-2xl p-7 text-left shadow-xs hover:shadow-md transition-all relative flex flex-col justify-between group">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center mb-5 group-hover:scale-105 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold text-sky-600 tracking-wider mb-2 block">03</span>
                            <h3 class="text-xl font-bold text-slate-900 mb-3">Collaborate</h3>
                            <p class="text-slate-600 text-sm leading-relaxed">
                                Chat in real time, agree on the details and bring your project to life.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Section 3: Popular Categories -->
        <section id="categories" class="bg-slate-50/80 py-24 px-4 sm:px-6 lg:px-8 border-b border-slate-200/70">
            <div class="max-w-6xl mx-auto text-center">
                <span class="text-sky-600 text-xs font-bold uppercase tracking-widest mb-2 block">EXPLORE BY SKILL</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight mb-4">Popular categories</h2>
                <p class="text-slate-600 text-sm sm:text-base max-w-xl mx-auto mb-14 leading-relaxed">
                    Whatever the task, find experienced people ready to help in your area.
                </p>

                <!-- Category Cards Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-left">

                    <!-- 1. Plumbing -->
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

                    <!-- 2. Electrical -->
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

                    <!-- 3. Carpentry -->
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

                    <!-- 4. Painting -->
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

                    <!-- 5. Cleaning -->
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

                    <!-- 6. Tutoring -->
                    <div class="group flex items-center justify-between p-4 bg-white hover:bg-sky-50/40 border border-slate-200/80 rounded-xl transition-all shadow-2xs hover:shadow-sm hover:border-sky-300 cursor-pointer">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg bg-sky-50 group-hover:bg-sky-100 text-sky-600 flex items-center justify-center transition-colors mr-3.5 shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-sky-700 transition-colors">Tutoring</h4>
                                <span class="text-xs text-slate-500 font-medium">874 professionals</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-sky-600 group-hover:translate-x-0.5 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>

                    <!-- 7. Photography -->
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

                    <!-- 8. Web Development -->
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

                </div>
            </div>
        </section>

        <!-- Section 4: Testimonials -->
        <section class="bg-white py-24 px-4 sm:px-6 lg:px-8">
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

        <!-- Footer -->
        <footer class="bg-slate-950 text-slate-400 border-t border-slate-900">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12 flex flex-col md:flex-row items-center md:items-start justify-between gap-8">
                <!-- Left Branding -->
                <div class="flex flex-col items-center md:items-start text-center md:text-left">
                    <a href="/" class="flex items-center gap-2.5 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-sky-500/20 border border-sky-400/30 flex items-center justify-center">
                            <svg class="w-4 h-4 text-sky-400" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" fill="currentColor" fill-opacity="0.2" stroke="currentColor" stroke-width="2"/>
                                <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2"/>
                            </svg>
                        </div>
                        <span class="text-white font-bold text-base tracking-tight">Skill Marketplace</span>
                    </a>
                    <p class="max-w-xs text-xs text-slate-400 leading-relaxed">
                        Helping communities find trusted skills, build relationships and get great work done.
                    </p>
                </div>

                <!-- Footer Navigation -->
                <div class="flex items-center gap-6 sm:gap-8 text-xs font-medium text-slate-300">
                    <a href="#about" class="hover:text-white transition-colors">About</a>
                    <a href="#connect" class="hover:text-white transition-colors">Connect</a>
                    <a href="#terms" class="hover:text-white transition-colors">Terms</a>
                    <a href="#privacy" class="hover:text-white transition-colors">Privacy</a>
                    <a href="#help" class="hover:text-white transition-colors">Help</a>
                </div>

                <!-- Social Icons -->
                <div class="flex items-center gap-3">
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 flex items-center justify-center transition-all">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 flex items-center justify-center transition-all">
                        <svg class="w-3.5 h-3.5 fill-none stroke-currentColor stroke-2" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>
                    <a href="#" class="w-8 h-8 rounded-full bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 flex items-center justify-center transition-all">
                        <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
                    </a>
                </div>
            </div>

            <!-- Copyright Line -->
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-900/80 py-6 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Skill Marketplace. All rights reserved.
            </div>
        </footer>

        @livewireScripts
    </body>
</html>
