<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Log In — {{ config('app.name', 'Skill Marketplace') }}</title>

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
                    <span class="text-slate-900 font-extrabold text-base tracking-tight">Skill Marketplace</span>
                </a>

                <!-- Right Helper Link -->
                <div class="text-xs sm:text-sm font-medium text-slate-600">
                    Don't have an account? 
                    <a href="{{ url('/register') }}" class="text-sky-600 font-bold hover:underline ml-1">
                        Sign Up
                    </a>
                </div>
            </div>
        </header>

        <!-- Main Login Container -->
        <main class="relative z-10 flex-1 flex flex-col justify-center items-center px-4 py-12">
            <div class="w-full max-w-md bg-white border border-slate-200/90 rounded-3xl p-7 sm:p-10 shadow-lg shadow-slate-900/5">
                
                <!-- Heading -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight text-center mb-8 leading-tight">
                    Log in to Skill Marketplace
                </h1>

                <!-- Google Social Auth Button Only -->
                <div class="mb-6">
                    <a href="{{ url('/auth/google') }}" class="w-full flex items-center justify-center gap-3 px-4 py-3.5 border border-slate-200 hover:border-sky-300 bg-white hover:bg-sky-50/50 rounded-full text-sm font-extrabold text-slate-800 transition-all shadow-xs group cursor-pointer">
                        <svg class="w-5 h-5 bg-white rounded-full p-0.5" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Continue with Google</span>
                    </a>
                </div>

                <!-- Divider -->
                <div class="relative flex items-center justify-center my-6">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-white px-3 text-xs font-semibold text-slate-400 uppercase tracking-widest absolute">or</span>
                </div>

                <!-- Login Form -->
                <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-800 mb-1.5">Username or Email address</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            placeholder="Username or email" 
                            class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl px-3.5 py-2.5 outline-none transition-all"
                        />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-800">Password</label>
                            <a href="#" class="text-xs font-semibold text-sky-600 hover:underline">Forgot password?</a>
                        </div>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                placeholder="Enter password" 
                                class="w-full bg-white border border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-500/10 text-slate-900 text-sm rounded-xl pl-3.5 pr-10 py-2.5 outline-none transition-all"
                            />
                            <button type="button" onclick="const p = document.getElementById('password'); p.type = p.type === 'password' ? 'text' : 'password';" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Keep Logged In Checkbox -->
                    <div class="flex items-center gap-2.5 py-1">
                        <input 
                            type="checkbox" 
                            id="remember" 
                            name="remember" 
                            checked 
                            class="w-4 h-4 accent-sky-600 rounded border-slate-300 focus:ring-sky-500"
                        />
                        <label for="remember" class="text-xs text-slate-600 cursor-pointer font-medium">
                            Keep me logged in on this device
                        </label>
                    </div>

                    <!-- Submit CTA Button (Blue Accent) -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full bg-sky-600 hover:bg-sky-500 text-white font-extrabold text-sm sm:text-base py-3.5 px-6 rounded-full shadow-md shadow-sky-600/20 transition-all active:scale-98 cursor-pointer"
                        >
                            Log In
                        </button>
                    </div>
                </form>

                <!-- Footer Switch Link -->
                <div class="mt-8 pt-6 border-t border-slate-100 text-center text-xs sm:text-sm font-medium text-slate-600">
                    Don't have an account? 
                    <a href="{{ url('/register') }}" class="text-sky-600 font-bold hover:underline ml-1">
                        Sign Up
                    </a>
                </div>

            </div>
        </main>

        <!-- Minimal Footer -->
        <footer class="relative z-10 w-full py-6 text-center text-xs text-slate-500 border-t border-slate-100 bg-white/60 backdrop-blur-md">
            &copy; {{ date('Y') }} Skill Marketplace® Global LLC. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
