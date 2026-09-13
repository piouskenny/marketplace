<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ sidebarOpen: false }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Edit Profile — {{ config('app.name', 'Skill Marketplace') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-50/90 font-sans antialiased text-slate-900 min-h-full selection:bg-sky-500 selection:text-white">

        <!-- Abstract Light Net Background Pattern -->
        <div class="fixed inset-0 pointer-events-none opacity-[0.05] bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:32px_32px] z-0"></div>

        <!-- Main Wrapper Container -->
        <div class="relative z-10 w-full px-3 sm:px-6 lg:px-8 py-4 sm:py-6 flex gap-6 min-h-screen">

            <!-- Sidebar Navigation -->
            <aside class="w-80 lg:w-72 xl:w-80 shrink-0 bg-white/95 backdrop-blur-xl border border-sky-100 rounded-3xl p-5 shadow-xl hidden lg:flex flex-col justify-between h-[calc(100vh-3rem)] sticky top-6">
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-sky-100">
                        <a href="/" class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-sky-400 to-blue-600 flex items-center justify-center text-white font-bold">
                                🎓
                            </div>
                            <span class="font-extrabold text-base text-slate-900">Skill Marketplace</span>
                        </a>
                    </div>
                    
                    <nav class="space-y-2">
                        <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl text-slate-700 hover:bg-sky-50 font-bold text-sm">
                            ← Back to Dashboard
                        </a>
                        <a href="{{ url('/profile/edit') }}" class="flex items-center gap-3 px-4 py-3 rounded-2xl bg-sky-600 text-white font-extrabold text-sm shadow-md">
                            Profile Settings & Services
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Form Container -->
            <main class="flex-1 min-w-0 space-y-6 max-w-4xl">
                
                <div class="bg-white border-2 border-slate-200 rounded-3xl p-6 sm:p-10 shadow-xs space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-5">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">Profile & Service Settings</h1>
                            <p class="text-sm text-slate-600 font-medium">Update your account details, professional listing, and tutoring preferences.</p>
                        </div>
                        <a href="{{ url('/dashboard') }}" class="lg:hidden text-xs font-bold text-sky-700 bg-sky-50 px-3 py-2 rounded-xl">
                            ← Dashboard
                        </a>
                    </div>

                    @if (session('status'))
                        <div class="bg-sky-50 border-2 border-sky-300 text-sky-950 text-sm font-extrabold rounded-2xl p-4">
                            ✓ {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ url('/profile/edit') }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Basic Account Details -->
                        <div class="space-y-4">
                            <h3 class="text-base font-extrabold text-slate-900 uppercase tracking-wider text-sky-700">1. Account Information</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-900 mb-1">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium" />
                                </div>
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-900 mb-1">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-900 mb-1">Location / City</label>
                                <input type="text" name="location" value="{{ old('location', $user->location) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium" />
                            </div>
                        </div>

                        <!-- Section 2: Professional Profile Details -->
                        <div class="space-y-4 pt-4 border-t border-slate-200">
                            <h3 class="text-base font-extrabold text-slate-900 uppercase tracking-wider text-sky-700">2. Professional Listing Details</h3>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-900 mb-1">Primary Category</label>
                                <select name="category_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ optional($user->professionalProfile)->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-900 mb-1">Display Name / Title</label>
                                    <input type="text" name="display_name" value="{{ old('display_name', optional($user->professionalProfile)->display_name ?? $user->name) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium" />
                                </div>
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-900 mb-1">Years of Experience</label>
                                    <input type="number" name="years_of_experience" value="{{ old('years_of_experience', optional($user->professionalProfile)->years_of_experience ?? 1) }}" required class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3.5 py-2.5 text-sm font-medium" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-900 mb-1">Service Biography / Summary</label>
                                <textarea name="bio" rows="3" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-3 text-sm font-medium">{{ old('bio', optional($user->professionalProfile)->bio) }}</textarea>
                            </div>
                        </div>

                        <!-- Section 3: Academic Tutoring Metadata -->
                        <div class="space-y-4 pt-4 border-t border-slate-200">
                            <h3 class="text-base font-extrabold text-slate-900 uppercase tracking-wider text-sky-700">3. Academic Tutoring Specialization</h3>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-900 mb-1.5">Subjects Taught</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 border border-slate-200 rounded-xl p-3">
                                    @php
                                        $selectedSubjects = optional(optional($user->professionalProfile)->educationProfile)->subjects->pluck('id')->toArray() ?? [];
                                    @endphp
                                    @foreach($subjects as $sub)
                                        <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                            <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" {{ in_array($sub->id, $selectedSubjects) ? 'checked' : '' }} class="accent-sky-600 rounded" />
                                            <span>{{ $sub->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-extrabold text-slate-900 mb-1.5">Target Education Levels</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 border border-slate-200 rounded-xl p-3">
                                    @php
                                        $selectedLevels = optional(optional($user->professionalProfile)->educationProfile)->educationLevels->pluck('id')->toArray() ?? [];
                                    @endphp
                                    @foreach($levels as $lvl)
                                        <label class="flex items-center gap-2 text-xs font-bold text-slate-800 cursor-pointer">
                                            <input type="checkbox" name="level_ids[]" value="{{ $lvl->id }}" {{ in_array($lvl->id, $selectedLevels) ? 'checked' : '' }} class="accent-sky-600 rounded" />
                                            <span>{{ $lvl->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-extrabold text-sm py-3 px-8 rounded-2xl shadow-md transition-all">
                                Save Profile Changes →
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>

        @livewireScripts
    </body>
</html>
