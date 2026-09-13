<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth" x-data="{ 
    selectedTalent: null, 
    hireModalOpen: false,
    openHireModal(talent) {
        this.selectedTalent = talent;
        this.hireModalOpen = true;
    }
}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Find Talent & Tutors — {{ config('app.name', 'Skill Marketplace') }}</title>

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
    <body class="bg-slate-100/70 font-sans antialiased text-slate-900 min-h-full selection:bg-slate-900 selection:text-white flex flex-col justify-between">

        <div>
            <!-- Header Navigation -->
            <header class="bg-white border-b border-slate-200/80 sticky top-0 z-30 shadow-xs">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between gap-4">
                    
                    <!-- Brand Logo -->
                    <a href="/" class="flex items-center gap-2.5 group">
                        <div class="w-9 h-9 rounded-xl bg-[#0F172B] flex items-center justify-center text-white shadow-xs group-hover:bg-slate-800 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                        </div>
                        <div>
                            <span class="text-slate-900 font-bold text-base tracking-tight block">Skill Marketplace</span>
                            <span class="text-[11px] text-slate-500 font-medium tracking-wide">Find Talent & Tutors</span>
                        </div>
                    </a>

                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-6 text-xs font-semibold text-slate-600">
                        <a href="{{ url('/') }}" class="hover:text-slate-900 transition-colors">Home</a>
                        <a href="{{ url('/talent') }}" class="text-[#0F172B] font-bold">Find Talent</a>
                        <a href="{{ url('/dashboard') }}" class="hover:text-slate-900 transition-colors">Opportunities & Jobs</a>
                    </nav>

                    <!-- Auth Quick Action -->
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                                <span>Go to Dashboard</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900 px-3 py-2">Log In</a>
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-4 rounded-xl shadow-xs transition-colors">
                                Sign Up Free
                            </a>
                        @endauth
                    </div>
                </div>
            </header>

            <!-- Hero Search Engine Banner -->
            <section class="bg-[#0F172B] text-white py-10 sm:py-14 px-4 sm:px-6 lg:px-8 shadow-sm">
                <div class="max-w-4xl mx-auto text-center space-y-4">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-xs font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Verified Experts, Tutors & Artisans</span>
                    </div>

                    <h1 class="text-2xl sm:text-4xl font-bold tracking-tight text-white">
                        Find Verified Local Talent & Private Tutors
                    </h1>

                    <p class="text-slate-300 text-xs sm:text-base max-w-2xl mx-auto font-normal leading-relaxed">
                        Search qualified academic tutors, electricians, plumbers, developers, and trade professionals across Nigeria.
                    </p>

                    <!-- Search Filter Form -->
                    <form action="{{ route('talent.index') }}" method="GET" class="pt-2">
                        <div class="bg-white p-2.5 sm:p-3 rounded-2xl shadow-xl flex flex-col sm:flex-row items-center gap-2.5 border border-slate-200">
                            <!-- Keyword Input -->
                            <div class="relative flex-1 w-full">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input 
                                    type="text" 
                                    name="query"
                                    value="{{ $searchQuery }}"
                                    placeholder="Search by name, skill (e.g. Mathematics, Plumbing)..." 
                                    class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm rounded-xl pl-10 pr-3 py-2.5 outline-none transition-all"
                                />
                            </div>

                            <!-- Category Dropdown -->
                            <div class="w-full sm:w-48 shrink-0">
                                <select name="category" class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm rounded-xl px-3 py-2.5 outline-none transition-all font-medium">
                                    <option value="All" {{ $selectedCategory === 'All' ? 'selected' : '' }}>All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}" {{ $selectedCategory === $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location Dropdown -->
                            <div class="w-full sm:w-40 shrink-0">
                                <select name="location" class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm rounded-xl px-3 py-2.5 outline-none transition-all font-medium">
                                    <option value="All" {{ $selectedLocation === 'All' ? 'selected' : '' }}>All Locations</option>
                                    <option value="Lagos" {{ $selectedLocation === 'Lagos' ? 'selected' : '' }}>Lagos</option>
                                    <option value="Abuja" {{ $selectedLocation === 'Abuja' ? 'selected' : '' }}>Abuja</option>
                                    <option value="Port Harcourt" {{ $selectedLocation === 'Port Harcourt' ? 'selected' : '' }}>Port Harcourt</option>
                                    <option value="Enugu" {{ $selectedLocation === 'Enugu' ? 'selected' : '' }}>Enugu</option>
                                </select>
                            </div>

                            <!-- Search Button -->
                            <button type="submit" class="w-full sm:w-auto bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-xs transition-colors shrink-0 cursor-pointer">
                                Search
                            </button>
                        </div>
                    </form>
                </div>
            </section>

            <!-- Main Content Container -->
            <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
                
                <!-- Category Filter Pills Bar -->
                <div class="flex items-center justify-between gap-4 flex-wrap border-b border-slate-200/80 pb-4">
                    <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                        <a href="{{ route('talent.index', ['category' => 'All', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'All' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                            All Experts
                        </a>
                        <a href="{{ route('talent.index', ['category' => 'Education & Tutoring', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'Education & Tutoring' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                            Academic Tutors
                        </a>
                        <a href="{{ route('talent.index', ['category' => 'Home & Technical Services', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'Home & Technical Services' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                            Home & Technical Trades
                        </a>
                        <a href="{{ route('talent.index', ['category' => 'Creative & Digital Services', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'Creative & Digital Services' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                            Creative & Tech
                        </a>
                    </div>

                    <div class="text-xs font-semibold text-slate-500 shrink-0">
                        Showing <strong class="text-slate-900">{{ count($professionals) }}</strong> verified profile{{ count($professionals) === 1 ? '' : 's' }}
                    </div>
                </div>

                <!-- Talent Grid Cards -->
                @if(count($professionals) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($professionals as $pro)
                            <div class="bg-white border border-slate-200/80 hover:border-slate-300 rounded-2xl p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between space-y-4 group">
                                <div class="space-y-3.5">
                                    <!-- Top Row: Avatar + Name + Rating -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="relative shrink-0">
                                                @if($pro->user->avatar_url)
                                                    <img src="{{ $pro->user->avatar_url }}" alt="{{ $pro->user->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200" />
                                                @else
                                                    <div class="w-12 h-12 rounded-xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-base shadow-xs">
                                                        {{ strtoupper(substr($pro->user->name ?? 'P', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="w-3 h-3 rounded-full bg-emerald-500 border-2 border-white absolute -bottom-0.5 -right-0.5" title="Available"></span>
                                            </div>

                                            <div class="min-w-0">
                                                <h3 class="text-sm font-bold text-slate-900 truncate group-hover:text-sky-900 transition-colors">
                                                    {{ $pro->user->name }}
                                                </h3>
                                                <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-md inline-block mt-0.5">
                                                    ✓ Verified Expert
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Rating Badge -->
                                        <div class="flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/80 px-2 py-1 rounded-lg shrink-0">
                                            <span>★</span>
                                            <span>{{ number_format($pro->average_rating ?? 5.0, 1) }}</span>
                                        </div>
                                    </div>

                                    <!-- Headline / Display Name -->
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                            {{ $pro->display_name }}
                                        </h4>
                                    </div>

                                    <!-- Location & Experience Metadata -->
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-medium pt-0.5">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            {{ $pro->location }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $pro->years_of_experience }} yrs exp</span>
                                        @if($pro->hourly_rate)
                                            <span>•</span>
                                            <span class="font-bold text-slate-900">₦{{ number_format($pro->hourly_rate) }}/hr</span>
                                        @endif
                                    </div>

                                    <!-- Bio Snippet -->
                                    <p class="text-xs text-slate-600 font-normal leading-relaxed line-clamp-2">
                                        {{ $pro->bio }}
                                    </p>

                                    <!-- Skills / Subjects Pills -->
                                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                        @foreach($pro->skills->take(4) as $sk)
                                            <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                                {{ $sk->name }}
                                            </span>
                                        @endforeach

                                        @if($pro->educationProfile && $pro->educationProfile->subjects)
                                            @foreach($pro->educationProfile->subjects->take(2) as $sb)
                                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-sky-50 text-sky-800 border border-sky-200/60">
                                                    🎓 {{ $sb->name }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Row: Connect & Hire Button -->
                                <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3 mt-2">
                                    <span class="text-[11px] text-slate-400 font-medium">Acceptance Guaranteed</span>

                                    <button 
                                        @click="openHireModal({
                                            id: {{ $pro->id }},
                                            name: '{{ addslashes($pro->user->name) }}',
                                            display_name: '{{ addslashes($pro->display_name) }}',
                                            location: '{{ addslashes($pro->location) }}',
                                            category: '{{ addslashes($pro->category->name ?? 'Talent') }}',
                                            hourly_rate: '{{ $pro->hourly_rate ? '₦' . number_format($pro->hourly_rate) . '/hr' : 'Flexible Rate' }}'
                                        })" 
                                        class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors cursor-pointer"
                                    >
                                        Connect & Hire (₦1,000) →
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-12 text-center space-y-3 max-w-lg mx-auto my-8 shadow-xs">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No matching talent found</h3>
                        <p class="text-xs text-slate-500">Try loosening your search terms or choosing "All Categories" to see available experts.</p>
                        <a href="{{ route('talent.index') }}" class="inline-block text-xs font-semibold text-[#0F172B] hover:underline pt-2">
                            Reset Filters →
                        </a>
                    </div>
                @endif

            </main>
        </div>

        <!-- Footer -->
        <footer class="py-6 text-center text-xs text-slate-400 font-normal border-t border-slate-200/80 bg-white">
            &copy; {{ date('Y') }} {{ config('app.name', 'Skill Marketplace') }}. All rights reserved.
        </footer>

        <!-- Connect & Hire Modal Popup (Slide-up / Modal) -->
        <div 
            x-show="hireModalOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="hireModalOpen = false" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4"
            style="display: none;"
        >
            <div 
                @click.stop
                x-show="hireModalOpen"
                x-transition:enter="transition transform ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition transform ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-6 border border-slate-200 space-y-5"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        <span class="text-sm font-bold text-slate-900">Connect & Hire Request</span>
                    </div>
                    <button @click="hireModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Selected Talent Summary -->
                <template x-if="selectedTalent">
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900" x-text="selectedTalent.name"></h3>
                            <span class="text-xs font-bold text-slate-900 bg-white border border-slate-200 px-2 py-0.5 rounded-md" x-text="selectedTalent.hourly_rate"></span>
                        </div>
                        <p class="text-xs text-slate-600 font-medium" x-text="selectedTalent.display_name"></p>
                        <div class="text-[11px] text-slate-400 flex items-center gap-2">
                            <span x-text="selectedTalent.category"></span>
                            <span>•</span>
                            <span x-text="selectedTalent.location"></span>
                        </div>
                    </div>
                </template>

                <!-- Fee Summary Box -->
                <div class="bg-sky-50 border border-sky-200 text-sky-950 rounded-xl p-3.5 text-xs space-y-1">
                    <div class="flex items-center justify-between font-bold text-slate-900">
                        <span>Connection Fee:</span>
                        <span class="text-sm font-extrabold text-[#0F172B]">₦1,000</span>
                    </div>
                    <p class="text-[11px] text-slate-600 font-normal leading-relaxed">
                        Note: The ₦1,000 fee is only charged after your connection request is accepted by the expert.
                    </p>
                </div>

                <!-- Request Form -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Your Job or Project Brief</label>
                        <textarea 
                            rows="3" 
                            placeholder="Describe your tutoring or task requirements (e.g., SS2 Physics tutoring 3 days a week in Ikeja)..."
                            class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-xs text-slate-900 font-normal outline-none transition-all"
                        ></textarea>
                    </div>

                    <button 
                        @click="alert('Connection request sent! You will be notified when accepted.'); hireModalOpen = false;"
                        class="w-full bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs transition-colors cursor-pointer"
                    >
                        Submit Connection Request (₦1,000) →
                    </button>
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>
