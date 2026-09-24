<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Sign Up — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-white font-sans antialiased text-slate-900 min-h-full flex flex-col justify-between relative selection:bg-sky-500 selection:text-white">

        <!-- Abstract White & Light-Black Net Background Pattern -->
        <div class="fixed inset-0 pointer-events-none opacity-[0.06] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>
        <div class="fixed inset-0 pointer-events-none opacity-[0.03] [background-image:radial-gradient(#000_1px,transparent_1px)] [background-size:16px_16px] z-0"></div>
        <div class="fixed inset-0 bg-gradient-to-b from-white/90 via-transparent to-white/90 pointer-events-none z-0"></div>

        <!-- Top Header Navigation -->
        <header class="relative z-10 w-full px-6 lg:px-12 py-6 border-b border-slate-100 bg-white/80 backdrop-blur-md">
            <div class="max-w-6xl mx-auto flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-8 h-8 rounded-full bg-sky-500/10 border border-sky-400/30 flex items-center justify-center backdrop-blur-md group-hover:border-sky-500 transition-colors shadow-xs">
                        <svg class="w-4 h-4 text-sky-600" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z" fill="currentColor" fill-opacity="0.15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="text-slate-900 font-extrabold text-base tracking-tight">Skill Link NG</span>
                </a>

                <!-- Right Helper Link -->
                <div class="text-xs sm:text-sm font-medium text-slate-600">
                    Here to hire talent or tutors? 
                    <a href="{{ url('/register?type=client') }}" class="text-sky-600 font-bold hover:underline ml-1">
                        Join as a Client
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Register Container -->
        <main class="relative z-10 flex-1 flex flex-col justify-center items-center px-4 py-12">
            <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-3xl p-7 sm:p-10 shadow-lg shadow-slate-900/5">
                
                <!-- Heading -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight text-center mb-8 leading-tight">
                    Sign up to find work & services you love
                </h1>

                <!-- Error Messages & Status Banner -->
                @if ($errors->any())
                    <div class="mb-6 bg-rose-50 border border-rose-200/90 text-rose-800 text-xs sm:text-sm font-medium rounded-2xl p-4 space-y-1.5 shadow-2xs">
                        <div class="flex items-center gap-2 font-bold text-rose-950 text-sm">
                            <svg class="w-4.5 h-4.5 text-rose-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>Registration Error</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1 text-rose-700 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('status'))
                    <div class="mb-6 bg-emerald-50 border border-emerald-200/90 text-emerald-800 text-xs sm:text-sm font-medium rounded-2xl p-4 flex items-center gap-2.5 shadow-2xs">
                        <svg class="w-4.5 h-4.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Sign up Form -->
                <form action="{{ url('/register') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Name Fields (2 Columns) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="first_name" class="block text-xs font-bold text-slate-800 mb-1.5">First name</label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                value="{{ old('first_name') }}"
                                required
                                placeholder="e.g. John" 
                                class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl px-3.5 py-2.5 outline-none transition-all"
                            />
                        </div>
                        <div>
                            <label for="last_name" class="block text-xs font-bold text-slate-800 mb-1.5">Last name</label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                value="{{ old('last_name') }}"
                                required
                                placeholder="e.g. Doe" 
                                class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl px-3.5 py-2.5 outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5">Email address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            required
                            placeholder="name@example.com" 
                            class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl px-3.5 py-2.5 outline-none transition-all"
                        />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-800 mb-1.5">Password</label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                placeholder="Password (8 or more characters)" 
                                class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl pl-3.5 pr-10 py-2.5 outline-none transition-all"
                            />
                            <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Country Selection -->
                    <div>
                        <label for="country" class="block text-xs font-bold text-slate-800 mb-1.5">Country</label>
                        <select 
                            id="country" 
                            name="country" 
                            class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl px-3.5 py-2.5 outline-none transition-all cursor-pointer"
                        >
                            <option value="NG" selected>Nigeria</option>
                            <option value="US">United States</option>
                            <option value="GB">United Kingdom</option>
                            <option value="GH">Ghana</option>
                            <option value="KE">Kenya</option>
                            <option value="CA">Canada</option>
                        </select>
                    </div>

                    <!-- Checkbox 1: Emails -->
                    <div class="flex items-start gap-2.5 pt-2">
                        <input 
                            type="checkbox" 
                            id="newsletter" 
                            name="newsletter" 
                            checked 
                            class="w-4 h-4 mt-0.5 accent-sky-600 rounded border-slate-300 focus:ring-sky-500"
                        />
                        <label for="newsletter" class="text-xs text-slate-600 leading-snug cursor-pointer">
                            Send me helpful emails to find rewarding work, service opportunities, and leads.
                        </label>
                    </div>

                    <!-- Checkbox 2: Terms & Conditions -->
                    <div class="flex items-start gap-2.5 pb-2">
                        <input 
                            type="checkbox" 
                            id="terms" 
                            name="terms" 
                            required 
                            class="w-4 h-4 mt-0.5 accent-sky-600 rounded border-slate-300 focus:ring-sky-500"
                        />
                        <label for="terms" class="text-xs text-slate-600 leading-snug cursor-pointer">
                            Yes, I understand and agree to the <a href="#" class="text-sky-600 font-bold hover:underline">Skill Link NG Terms of Service</a>, including the <a href="#" class="text-sky-600 font-bold hover:underline">User Agreement</a> and <a href="#" class="text-sky-600 font-bold hover:underline">Privacy Policy</a>.
                        </label>
                    </div>

                    <!-- Submit CTA Button (Blue Accent) -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full bg-sky-600 hover:bg-sky-500 text-white font-extrabold text-sm sm:text-base py-3.5 px-6 rounded-full shadow-md shadow-sky-600/20 transition-all active:scale-98 cursor-pointer"
                        >
                            Create my account
                        </button>
                    </div>
                </form>

                <!-- Footer Switch Link -->
                <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs sm:text-sm font-medium text-slate-600">
                    Already have an account? 
                    <a href="{{ url('/login') }}" class="text-sky-600 font-bold hover:underline ml-1">
                        Log In
                    </a>
                </div>

            </div>
        </main>

        <!-- Minimal Footer -->
        <footer class="relative z-10 w-full py-6 text-center text-xs text-slate-500 border-t border-slate-100 bg-white/60 backdrop-blur-md">
            &copy; {{ date('Y') }} Skill Link NG® Global LLC. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
