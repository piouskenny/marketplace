<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Customer Setup — {{ config('app.name', 'Skill Marketplace') }}</title>

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
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white shadow-md">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                    </div>
                    <span class="font-extrabold text-base text-slate-900">Skill Marketplace</span>
                </a>
                <a href="{{ route('onboarding') }}" class="text-xs font-bold text-slate-600 hover:text-sky-700">
                    ← Back to step 1
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-xl bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-6">
                
                <div class="space-y-2">
                    <span class="text-xs font-extrabold text-sky-700 uppercase tracking-wider bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                        Customer Profile Setup
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Complete your hiring profile
                    </h1>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        Please provide your contact details so verified professionals and tutors can reach you after a connection is activated.
                    </p>
                </div>

                <!-- Form -->
                <form action="{{ url('/onboarding/client') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Location Field -->
                    <div>
                        <label for="location" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            City / Location <span class="text-rose-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="location" 
                            name="location" 
                            required
                            value="{{ old('location', $user->location ?? 'Nigeria') }}"
                            placeholder="e.g. Ikeja, Lagos or Garki, Abuja"
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                        />
                    </div>

                    <!-- Phone Field -->
                    <div>
                        <label for="phone" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                            Phone Number <span class="text-slate-500 font-medium text-xs">(Kept private until connected)</span>
                        </label>
                        <input 
                            type="text" 
                            id="phone" 
                            name="phone" 
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="e.g. +234 801 234 5678"
                            class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                        />
                    </div>

                    <!-- Privacy Note -->
                    <div class="bg-sky-50 border border-sky-200 rounded-2xl p-4 flex items-start gap-3">
                        <span class="text-lg">🔒</span>
                        <p class="text-xs text-sky-950 font-medium leading-relaxed">
                            Your phone number and direct contact details will <strong>never</strong> be shown publicly. They are only shared when you accept or activate a connection request.
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl shadow-md transition-all active:scale-98 cursor-pointer"
                        >
                            Complete Setup & Enter Dashboard →
                        </button>
                    </div>
                </form>

            </div>
        </main>

        <!-- Footer -->
        <footer class="relative z-10 w-full py-4 text-center text-xs text-slate-500 border-t border-slate-200">
            &copy; {{ date('Y') }} Skill Marketplace® Global LLC. All rights reserved.
        </footer>

        @livewireScripts
    </body>
</html>
