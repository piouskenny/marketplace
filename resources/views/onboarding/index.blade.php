<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Select Account Goal — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50/90 font-sans antialiased text-slate-900 min-h-full flex flex-col justify-between selection:bg-sky-500 selection:text-white">

        <!-- Abstract Light Net Background Pattern -->
        <div class="fixed inset-0 pointer-events-none opacity-[0.06] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>
        <div class="fixed inset-0 pointer-events-none opacity-[0.03] [background-image:radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px] z-0"></div>

        <!-- Header -->
        <header class="relative z-10 w-full px-6 lg:px-12 py-5 bg-white/90 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <img src="{{ asset('images/skilllingng_logo.png') }}" alt="{{ config('app.name', 'Skill Link NG') }}" class="w-10 h-10 object-contain rounded-xl shrink-0" />
                    <span class="font-extrabold text-base text-slate-900">Skill Link NG</span>
                </a>
                <span class="text-xs font-bold text-sky-700 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                    Step 1 of 2
                </span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-2xl bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-8">
                
                <!-- Heading -->
                <div class="text-center space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-100 text-sky-800 font-extrabold text-xs">
                        <span>👋 Welcome, {{ $user->name }}!</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                        What would you like to do first?
                    </h1>
                    <p class="text-sm sm:text-base text-slate-600 max-w-lg mx-auto font-medium">
                        Select an option below to set up your account. Don't worry — you can use all features anytime!
                    </p>
                </div>

                <!-- 2 Large Goal Options -->
                <div class="grid grid-cols-1 gap-4">
                    
                    <!-- Option 1: Find Talent / Hire -->
                    <a href="{{ route('onboarding.client') }}" class="group border-2 border-slate-200 hover:border-slate-900 rounded-3xl p-6 bg-slate-50/50 hover:bg-slate-100/50 transition-all flex items-center justify-between gap-4 shadow-2xs">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 border border-slate-300 text-slate-900 flex items-center justify-center font-bold group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 transition-colors shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 group-hover:text-sky-900">
                                    I want to hire professionals or skilled workers
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 font-medium">
                                    Find plumbers, electricians, carpenters, academic tutors and more near you.
                                </p>
                            </div>
                        </div>
                        <span class="w-9 h-9 rounded-xl bg-white border border-slate-300 group-hover:bg-slate-900 group-hover:text-white group-hover:border-slate-900 flex items-center justify-center font-bold text-slate-700 shrink-0 transition-all">
                            →
                        </span>
                    </a>

                    <!-- Option 2: Offer Services -->
                    <a href="{{ route('onboarding.classification') }}" class="group border-2 border-sky-300 hover:border-sky-600 rounded-3xl p-6 bg-sky-50/40 hover:bg-sky-50/70 transition-all flex items-center justify-between gap-4 shadow-2xs">
                        <div class="flex items-center gap-4 min-w-0">
                            <div class="w-14 h-14 rounded-2xl bg-sky-100 border border-sky-300 text-sky-700 flex items-center justify-center font-bold group-hover:bg-sky-600 group-hover:text-white group-hover:border-sky-600 transition-colors shrink-0">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 group-hover:text-sky-900">
                                    I offer services, skills, or academic tutoring
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 font-medium">
                                    List under Business & Professional Services, Teaching & Education, or Skilled Trades & Services — or all three.
                                </p>
                            </div>
                        </div>
                        <span class="w-9 h-9 rounded-xl bg-sky-100 border border-sky-300 group-hover:bg-sky-600 group-hover:text-white group-hover:border-sky-600 flex items-center justify-center font-bold text-sky-700 shrink-0 transition-all">
                            →
                        </span>
                    </a>

                </div>

                <!-- Skip Link -->
                <div class="text-center pt-2">
                    <form action="{{ route('onboarding.skip') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-slate-500 hover:text-slate-700 font-medium underline underline-offset-2 cursor-pointer">
                            Skip for now — I'll set this up later
                        </button>
                    </form>
                </div>

            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full py-4 text-center text-xs text-slate-500 border-t border-slate-200">
            &copy; {{ date('Y') }} Skill Link NG® Global LLC. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
