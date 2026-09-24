<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Verify Email Address — {{ config('app.name', 'Skill Link NG') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-950 font-sans antialiased text-slate-900 min-h-full flex items-center justify-center p-4 selection:bg-sky-500 selection:text-white">

        <!-- Background Glow Accent -->
        <div class="fixed inset-0 pointer-events-none opacity-20 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-sky-500 via-transparent to-transparent"></div>

        <div class="relative z-10 w-full max-w-lg bg-white border border-slate-200 rounded-3xl p-8 sm:p-10 shadow-2xl space-y-6">
            
            <!-- Brand Header -->
            <div class="text-center space-y-2">
                <a href="/" class="inline-flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-[#0F172B] text-white flex items-center justify-center font-bold shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                    </div>
                    <span class="text-slate-900 font-extrabold text-xl tracking-tight">Skill Link NG</span>
                </a>
            </div>

            <!-- Email Icon & Heading -->
            <div class="text-center space-y-3 pt-2">
                <div class="w-16 h-16 rounded-3xl bg-sky-50 border border-sky-200 text-sky-600 mx-auto flex items-center justify-center shadow-xs">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Verify Your Email Address</h2>
                <p class="text-sm text-slate-600 leading-relaxed font-medium">
                    Thanks for joining Skill Link NG! We sent a verification link to <strong class="text-slate-900">{{ auth()->user()->email }}</strong>. Please check your inbox and click the verification link to activate your account.
                </p>
            </div>

            <!-- Requirement Alert Banner -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <p class="text-xs text-amber-950 font-bold leading-normal">
                    Email verification is required before you can connect with clients, apply for jobs, or complete your profile.
                </p>
            </div>

            <!-- Status Alert if Resent -->
            @if (session('status') == 'verification-link-sent')
                <div class="bg-emerald-50 border border-emerald-300 text-emerald-950 text-xs font-bold rounded-2xl p-4 text-center">
                    ✓ A new verification link has been sent to your email address!
                </div>
            @endif

            <!-- Resend Button & Sign Out Form -->
            <div class="space-y-3 pt-2">
                <form action="{{ route('verification.send') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full bg-[#0F172B] hover:bg-slate-800 text-white font-extrabold text-sm py-3.5 px-6 rounded-2xl shadow-md transition-all cursor-pointer">
                        Resend Verification Email →
                    </button>
                </form>

                <form action="{{ url('/logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-xs font-bold text-slate-500 hover:text-slate-900 py-2 transition-colors cursor-pointer">
                        Sign Out & Switch Account
                    </button>
                </form>
            </div>

        </div>

    </body>
</html>
