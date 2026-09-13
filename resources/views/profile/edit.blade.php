<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ pageLoading: true, sidebarOpen: false }" x-init="setTimeout(() => pageLoading = false, 350)">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Edit Profile — {{ config('app.name', 'Skill Marketplace') }}</title>

        <!-- Modern Clean Typography (Inter) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Alpine.js -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            }
        </style>
    </head>
    <body class="bg-slate-100/70 font-sans antialiased text-slate-900 min-h-full selection:bg-slate-900 selection:text-white">

        <!-- Skeleton Preloader Overlay -->
        <div 
            x-show="pageLoading" 
            x-transition:leave="transition ease-out duration-300"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-98 pointer-events-none"
            class="fixed inset-0 z-50 bg-slate-100/90 backdrop-blur-md flex p-4 sm:p-6 gap-6 overflow-hidden"
        >
            <!-- Left Sidebar Skeleton -->
            <div class="hidden lg:flex w-64 xl:w-72 shrink-0 bg-white border border-slate-200/80 rounded-2xl p-5 flex-col justify-between h-[calc(100vh-3rem)] space-y-6">
                <div class="space-y-6">
                    <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-200 animate-pulse"></div>
                        <div class="space-y-1.5 flex-1">
                            <div class="h-4 w-28 bg-slate-200 rounded-md animate-pulse"></div>
                            <div class="h-3 w-20 bg-slate-100 rounded-md animate-pulse"></div>
                        </div>
                    </div>
                    <div class="h-10 w-full bg-slate-200 rounded-xl animate-pulse"></div>
                    <div class="space-y-2 pt-2">
                        <div class="h-3 w-16 bg-slate-100 rounded-md mb-2"></div>
                        <div class="h-9 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
                        <div class="h-9 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
                    </div>
                </div>
                <div class="h-14 w-full bg-slate-200/80 rounded-xl animate-pulse"></div>
            </div>

            <!-- Profile Form Skeleton -->
            <div class="flex-1 bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 space-y-6">
                <div class="h-8 w-48 bg-slate-200 rounded-md animate-pulse"></div>
                <div class="h-4 w-72 bg-slate-100 rounded-md animate-pulse"></div>
                <div class="space-y-4 pt-4">
                    <div class="h-12 w-full bg-slate-100 rounded-xl animate-pulse"></div>
                    <div class="h-12 w-full bg-slate-100 rounded-xl animate-pulse"></div>
                    <div class="h-24 w-full bg-slate-100 rounded-xl animate-pulse"></div>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Overlay -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            class="fixed inset-0 bg-slate-900/50 z-40 lg:hidden"
            style="display: none;"
        ></div>

        <!-- Main Wrapper Container -->
        <div class="w-full px-4 sm:px-6 lg:px-8 py-4 sm:py-6 flex gap-6 min-h-screen">

            <!-- Sidebar Navigation -->
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed lg:sticky inset-y-0 lg:top-6 left-0 z-50 lg:z-10 w-72 lg:w-64 xl:w-72 shrink-0 bg-white border-r lg:border border-slate-200/80 lg:rounded-2xl p-5 shadow-xl lg:shadow-xs flex flex-col justify-between h-full lg:h-[calc(100vh-3rem)] transition-transform duration-300 ease-in-out overflow-y-auto no-scrollbar"
            >
                <div class="space-y-6">
                    
                    <!-- Sidebar Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                        <a href="/" class="flex items-center gap-3 group">
                            <div class="w-9 h-9 rounded-xl bg-[#0F172B] flex items-center justify-center text-white shadow-xs group-hover:bg-slate-800 transition-colors">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                            </div>
                            <div>
                                <span class="text-slate-900 font-bold text-base tracking-tight block">Skill Marketplace</span>
                                <span class="text-[11px] text-slate-500 font-medium tracking-wide">Member Dashboard</span>
                            </div>
                        </a>

                        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Close menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Navigation Links -->
                    <div class="space-y-4">
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Navigation</span>
                            <nav class="space-y-0.5">
                                <a href="{{ url('/dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                                        <span>← Back to Overview</span>
                                    </div>
                                </a>
                                <a href="{{ url('/profile/edit') }}" class="flex items-center justify-between px-3 py-2 rounded-xl bg-[#0F172B] text-white text-xs font-normal transition-colors shadow-xs">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        <span>Profile Settings</span>
                                    </div>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- User Badge -->
                <div class="pt-4 mt-6 border-t border-slate-200/80 space-y-3 shrink-0">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" />
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h5 class="text-xs font-semibold text-slate-900 truncate">{{ $user->name ?? 'User' }}</h5>
                                <span class="text-[11px] text-slate-500 font-medium block truncate">Profile Settings</span>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Form Viewport -->
            <main class="flex-1 min-w-0 space-y-6 max-w-4xl">
                
                <!-- Header Card -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 sm:p-6 shadow-xs flex items-center justify-between gap-4">
                    <div>
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Profile & Service Settings</h1>
                        <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">Manage your public listing, profile photo, and tutoring preferences.</p>
                    </div>
                    <a href="{{ url('/dashboard') }}" class="lg:hidden text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 px-3 py-2 rounded-xl transition-colors">
                        ← Dashboard
                    </a>
                </div>

                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-medium rounded-2xl p-4 flex items-center justify-between gap-2 shadow-xs">
                        <div class="flex items-center gap-2 min-w-0">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <span class="truncate sm:whitespace-normal">{{ session('status') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 hover:bg-emerald-100 p-1 rounded-lg transition-colors cursor-pointer shrink-0 ml-3" title="Cancel notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm font-medium rounded-2xl p-4 space-y-1">
                        <div class="font-bold text-rose-950">Please fix the following issues:</div>
                        <ul class="list-disc list-inside space-y-0.5 text-rose-800">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs space-y-8">
                    <form action="{{ url('/profile/edit') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Section 1: Basic Account Information & Photo -->
                        <div class="space-y-4">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">1. Basic Information & Photo</h3>
                            
                            <!-- Profile Photo Upload Card -->
                            <div x-data="{ photoPreview: null }" class="flex flex-col sm:flex-row items-center gap-5 p-4 bg-slate-50 border border-slate-200/80 rounded-xl">
                                <div class="relative shrink-0">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="w-16 h-16 rounded-xl object-cover border border-slate-300 shadow-xs" />
                                    </template>
                                    <template x-if="!photoPreview">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-xl object-cover border border-slate-300 shadow-xs" />
                                        @else
                                            <div class="w-16 h-16 rounded-xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-xl shadow-xs">
                                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif
                                    </template>
                                </div>

                                <div class="space-y-1 flex-1 text-center sm:text-left">
                                    <label class="block text-xs font-semibold text-slate-900">Profile Photo</label>
                                    <p class="text-xs text-slate-500 font-normal">Upload a clear photo (JPG, PNG or WEBP, max 3MB). This will display on your profile and header.</p>
                                    
                                    <div class="pt-1 flex flex-wrap items-center justify-center sm:justify-start gap-2">
                                        <label class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-lg bg-[#0F172B] text-white font-semibold text-xs hover:bg-slate-800 cursor-pointer shadow-xs transition-colors">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                            <span>Upload Photo</span>
                                            <input 
                                                type="file" 
                                                name="avatar" 
                                                accept="image/*" 
                                                class="hidden" 
                                                @change="
                                                    const file = $event.target.files[0];
                                                    if (file) {
                                                        const reader = new FileReader();
                                                        reader.onload = (e) => { photoPreview = e.target.result; };
                                                        reader.readAsDataURL(file);
                                                    }
                                                "
                                            />
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Location / City</label>
                                <input type="text" name="location" value="{{ old('location', $user->location) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                            </div>
                        </div>

                        <!-- Section 2: Professional Profile Details -->
                        <div class="space-y-4 pt-6 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">2. Service & Professional Listing</h3>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Primary Category</label>
                                <select name="category_id" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all">
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ optional($user->professionalProfile)->category_id == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Professional Title / Headline</label>
                                    <input type="text" name="display_name" value="{{ old('display_name', optional($user->professionalProfile)->display_name ?? $user->name) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Years of Experience</label>
                                    <input type="number" name="years_of_experience" value="{{ old('years_of_experience', optional($user->professionalProfile)->years_of_experience ?? 1) }}" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-sm font-medium outline-none transition-all" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Service Biography / Summary</label>
                                <textarea name="bio" rows="3" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-sm font-medium outline-none transition-all">{{ old('bio', optional($user->professionalProfile)->bio) }}</textarea>
                            </div>
                        </div>

                        <!-- Section 3: Academic Tutoring Specialization -->
                        <div class="space-y-4 pt-6 border-t border-slate-100">
                            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">3. Tutoring & Academic Options</h3>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Subjects Taught</label>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 bg-slate-50 border border-slate-200/80 rounded-xl p-3">
                                    @php
                                        $eduProfile = $user->professionalProfile?->educationProfile;
                                        $selectedSubjects = $eduProfile && $eduProfile->subjects ? $eduProfile->subjects->pluck('id')->toArray() : [];
                                    @endphp
                                    @foreach($subjects as $sub)
                                        <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer">
                                            <input type="checkbox" name="subject_ids[]" value="{{ $sub->id }}" {{ in_array($sub->id, $selectedSubjects) ? 'checked' : '' }} class="accent-slate-900 rounded" />
                                            <span>{{ $sub->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-2">Target Education Levels</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 border border-slate-200/80 rounded-xl p-3">
                                    @php
                                        $selectedLevels = $eduProfile && $eduProfile->educationLevels ? $eduProfile->educationLevels->pluck('id')->toArray() : [];
                                    @endphp
                                    @foreach($levels as $lvl)
                                        <label class="flex items-center gap-2 text-xs font-medium text-slate-800 cursor-pointer">
                                            <input type="checkbox" name="level_ids[]" value="{{ $lvl->id }}" {{ in_array($lvl->id, $selectedLevels) ? 'checked' : '' }} class="accent-slate-900 rounded" />
                                            <span>{{ $lvl->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-2.5 px-6 rounded-xl shadow-xs transition-colors cursor-pointer">
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
