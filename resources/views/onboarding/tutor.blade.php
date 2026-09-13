<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Academic Tutor Details — {{ config('app.name', 'Skill Marketplace') }}</title>

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
                <span class="text-xs font-extrabold text-sky-700 uppercase tracking-wider bg-sky-50 px-3 py-1 rounded-full border border-sky-200">
                    Step 2B: Academic Tutoring Specialization
                </span>
            </div>
        </header>

        <!-- Main Content -->
        <main class="relative z-10 flex-1 flex flex-col items-center justify-center px-4 py-12">
            <div class="w-full max-w-2xl bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xl space-y-6">
                
                <div class="space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 text-sky-700 flex items-center justify-center text-xl font-bold">
                        🎓
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Academic Tutoring Details
                    </h1>
                    <p class="text-sm text-slate-600 font-medium leading-relaxed">
                        Select the subjects you teach, education levels, and teaching mode so parents and students can match with you.
                    </p>
                </div>

                <!-- Form -->
                <form action="{{ url('/onboarding/tutor') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Subjects Checkboxes -->
                    <div>
                        <label class="block text-sm font-extrabold text-slate-900 mb-2">
                            Subjects Taught <span class="text-rose-500">*</span> <span class="text-xs font-normal text-slate-500">(Select all that apply)</span>
                        </label>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 bg-slate-50 border-2 border-slate-200 rounded-2xl p-4">
                            @foreach($subjects as $sub)
                                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-800 cursor-pointer p-1 hover:bg-white rounded-lg transition-colors">
                                    <input 
                                        type="checkbox" 
                                        name="subject_ids[]" 
                                        value="{{ $sub->id }}" 
                                        class="w-4 h-4 accent-sky-600 rounded"
                                    />
                                    <span>{{ $sub->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Target Education Levels Checkboxes -->
                    <div>
                        <label class="block text-sm font-extrabold text-slate-900 mb-2">
                            Target Education Levels <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 bg-slate-50 border-2 border-slate-200 rounded-2xl p-4">
                            @foreach($levels as $lvl)
                                <label class="flex items-center gap-2 text-xs sm:text-sm font-bold text-slate-800 cursor-pointer p-1 hover:bg-white rounded-lg transition-colors">
                                    <input 
                                        type="checkbox" 
                                        name="level_ids[]" 
                                        value="{{ $lvl->id }}" 
                                        class="w-4 h-4 accent-sky-600 rounded"
                                    />
                                    <span>{{ $lvl->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Teaching Mode & Qualifications Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="teaching_mode" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Teaching Mode <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                id="teaching_mode" 
                                name="teaching_mode" 
                                required 
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all cursor-pointer"
                            >
                                <option value="physical">Physical (In-Person Only)</option>
                                <option value="online">Online Only</option>
                                <option value="both" selected>Both Physical & Online</option>
                            </select>
                        </div>

                        <div>
                            <label for="qualifications" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Academic Qualification
                            </label>
                            <input 
                                type="text" 
                                id="qualifications" 
                                name="qualifications" 
                                placeholder="e.g. B.Sc. Mathematics, NCE"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Hourly Tutoring Rate (Optional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="rate_min" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Hourly Tutoring Rate (₦)
                            </label>
                            <input 
                                type="number" 
                                id="rate_min" 
                                name="rate_min" 
                                min="0"
                                placeholder="e.g. 5000"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>
                        <div>
                            <label for="rate_max" class="block text-sm font-extrabold text-slate-900 mb-1.5">
                                Max Rate (Optional ₦)
                            </label>
                            <input 
                                type="number" 
                                id="rate_max" 
                                name="rate_max" 
                                min="0"
                                placeholder="e.g. 10000"
                                class="w-full bg-slate-50 border-2 border-slate-300 focus:border-sky-600 focus:bg-white text-slate-900 text-sm font-medium rounded-2xl px-4 py-3 outline-none transition-all"
                            />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            class="w-full bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm sm:text-base py-4 px-6 rounded-2xl shadow-md transition-all active:scale-98 cursor-pointer"
                        >
                            Complete Tutor Setup & Enter Dashboard →
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
