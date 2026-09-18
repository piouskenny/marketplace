<x-dashboard-layout 
    title="Find Talent & Tutors — Dashboard — {{ config('app.name', 'Skill Marketplace') }}"
    active="talent"
    xData="{ 
        pageLoading: true,
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
        },
        searchQuery: '{{ addslashes($searchQuery) }}', 
        selectedCategory: '{{ addslashes($selectedCategory) }}', 
        selectedLocation: '{{ addslashes($selectedLocation) }}',
        profileModalOpen: false, 
        notificationsOpen: false,
        selectedTalent: null,
        hireModalOpen: false,
        openHireModal(talent) {
            this.selectedTalent = talent;
            this.hireModalOpen = true;
        }
    }"
>

            <!-- Main Dashboard Viewport -->
            <main class="flex-1 min-w-0 space-y-6">
                
                <!-- Top Header Bar -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-3.5 sm:px-6 shadow-xs flex items-center justify-between gap-4">
                    
                    <!-- Desktop Sidebar Collapse Toggle Button -->
                    <button 
                        @click="toggleSidebar()" 
                        class="hidden lg:flex items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shrink-0 cursor-pointer transition-colors"
                        :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                    >
                        <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                        </svg>
                    </button>

                    <!-- Mobile Menu Button -->
                    <button 
                        @click="sidebarOpen = true" 
                        class="lg:hidden flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 font-medium text-xs hover:bg-slate-100 shrink-0 cursor-pointer transition-colors"
                        aria-label="Open navigation menu"
                    >
                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        <span>Menu</span>
                    </button>

                    <!-- Global Search Bar -->
                    <div class="relative flex-1 max-w-lg">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input 
                            type="text" 
                            x-model="searchQuery"
                            placeholder="Search tutors, plumbers, electricians, developers..." 
                            class="w-full bg-slate-50 border border-slate-200 text-sm text-slate-900 font-normal rounded-xl pl-10 pr-4 py-2 outline-none focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all"
                        />
                    </div>

                    <!-- Right Header Icons -->
                    <div class="flex items-center gap-3 shrink-0">
                        
                        <!-- Notifications Popup Trigger Button -->
                        <div class="relative">
                            <button 
                                @click="notificationsOpen = !notificationsOpen" 
                                class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 relative transition-colors cursor-pointer" 
                                title="Notifications"
                            >
                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                                <span class="w-2 h-2 rounded-full bg-sky-500 absolute top-2 right-2 ring-2 ring-white"></span>
                            </button>

                            <!-- Notifications Popup Dropdown Panel -->
                            <div 
                                x-show="notificationsOpen" 
                                @click.outside="notificationsOpen = false"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-200/90 rounded-2xl shadow-xl z-50 overflow-hidden space-y-0"
                                style="display: none;"
                            >
                                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-slate-900">Notifications</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172B] text-white">3 New</span>
                                    </div>
                                    <button @click="notificationsOpen = false" class="text-xs text-slate-400 hover:text-slate-600 font-medium cursor-pointer">Close</button>
                                </div>

                                <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
                                    @if (!$user->hasVerifiedEmail())
                                        <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3">
                                            <div class="flex items-start gap-3 min-w-0">
                                                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 border border-amber-200 mt-0.5">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <div class="flex-1 min-w-0 space-y-0.5">
                                                    <p class="text-xs font-bold text-slate-900">Action Required: Verify Email</p>
                                                    <p class="text-[11px] text-slate-500 font-normal">Please confirm {{ $user->email }} to unlock full access to application requests.</p>
                                                    <span class="text-[10px] text-slate-400 font-medium block">Just now</span>
                                                </div>
                                            </div>
                                            <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors cursor-pointer shrink-0" title="Dismiss notification">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    @endif

                                    <div x-data="{ show: true }" x-show="show" x-transition class="p-3.5 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3">
                                        <div class="flex items-start gap-3 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-200 mt-0.5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div class="flex-1 min-w-0 space-y-0.5">
                                                <p class="text-xs font-bold text-slate-900">Welcome to Skill Marketplace</p>
                                                <p class="text-[11px] text-slate-500 font-normal">Your account is active! Browse opportunities and connect with clients or tutors.</p>
                                                <span class="text-[10px] text-slate-400 font-medium block">10 minutes ago</span>
                                            </div>
                                        </div>
                                        <button @click="show = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-md transition-colors cursor-pointer shrink-0" title="Dismiss notification">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="p-2.5 bg-slate-50/80 border-t border-slate-200/80 text-center">
                                    <span class="text-[11px] text-slate-500 font-medium">All notifications up to date</span>
                                </div>
                            </div>
                        </div>

                        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- Header Profile Button Trigger -->
                        <button @click="profileModalOpen = true" class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" />
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="text-xs font-semibold text-slate-800 hidden sm:inline">{{ $user->name ?? 'User' }}</span>
                        </button>

                    </div>
                </div>

                <!-- Dashboard Page Header Section -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-7 shadow-xs space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-slate-100 text-slate-800 border border-slate-200 text-xs font-semibold mb-1">
                                <svg class="w-3.5 h-3.5 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <span>Talent & Tutor Directory</span>
                            </div>
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">
                                Find Experts & Skilled Professionals
                            </h1>
                            <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                                Browse verified tutors, trade artisans, and digital freelancers. Connect directly for your projects.
                            </p>
                        </div>

                        <!-- Secondary Action -->
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition-colors shrink-0">
                            ← Back to Overview
                        </a>
                    </div>

                    <!-- Search Engine Input & Filter Bar -->
                    <form action="{{ route('dashboard.talent') }}" method="GET" class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                            
                            <!-- Search Input -->
                            <div class="sm:col-span-6 relative">
                                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input 
                                    type="text" 
                                    name="query"
                                    value="{{ $searchQuery }}"
                                    placeholder="Search by name, subject (e.g. Mathematics), or trade skill..." 
                                    class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm text-slate-900 font-normal rounded-xl pl-10 pr-4 py-2.5 outline-none transition-all"
                                />
                            </div>

                            <!-- Category Dropdown -->
                            <div class="sm:col-span-3">
                                <select name="category" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm text-slate-900 font-medium rounded-xl px-3 py-2.5 outline-none transition-all">
                                    <option value="All" {{ $selectedCategory === 'All' ? 'selected' : '' }}>All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->name }}" {{ $selectedCategory === $cat->name ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Location Filter -->
                            <div class="sm:col-span-3">
                                <select name="location" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white text-xs sm:text-sm text-slate-900 font-medium rounded-xl px-3 py-2.5 outline-none transition-all">
                                    <option value="All" {{ $selectedLocation === 'All' ? 'selected' : '' }}>All Locations</option>
                                    <option value="Lagos" {{ $selectedLocation === 'Lagos' ? 'selected' : '' }}>Lagos</option>
                                    <option value="Abuja" {{ $selectedLocation === 'Abuja' ? 'selected' : '' }}>Abuja</option>
                                    <option value="Port Harcourt" {{ $selectedLocation === 'Port Harcourt' ? 'selected' : '' }}>Port Harcourt</option>
                                    <option value="Enugu" {{ $selectedLocation === 'Enugu' ? 'selected' : '' }}>Enugu</option>
                                </select>
                            </div>
                        </div>

                        <!-- Specialized Education & Rating Filters Row -->
                        <div class="bg-slate-900 text-white p-2.5 rounded-xl border border-slate-800 flex flex-wrap items-center gap-2 text-xs">
                            <span class="text-slate-400 font-semibold px-1 text-[11px] uppercase tracking-wider">Tutor Filters:</span>
                            
                            <!-- Subject Filter -->
                            <select name="subject_id" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-sky-400 font-medium">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}" {{ isset($selectedSubject) && $selectedSubject == $sub->id ? 'selected' : '' }}>
                                        🎓 {{ $sub->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Education Level Filter -->
                            <select name="education_level_id" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-sky-400 font-medium">
                                <option value="">All Levels</option>
                                @foreach($educationLevels as $lvl)
                                    <option value="{{ $lvl->id }}" {{ isset($selectedLevel) && $selectedLevel == $lvl->id ? 'selected' : '' }}>
                                        {{ $lvl->name }}
                                    </option>
                                @endforeach
                            </select>

                            <!-- Teaching Mode Filter -->
                            <select name="teaching_mode" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-sky-400 font-medium">
                                <option value="All" {{ isset($selectedTeachingMode) && $selectedTeachingMode === 'All' ? 'selected' : '' }}>All Modes</option>
                                <option value="physical" {{ isset($selectedTeachingMode) && $selectedTeachingMode === 'physical' ? 'selected' : '' }}>Physical (In-Person)</option>
                                <option value="online" {{ isset($selectedTeachingMode) && $selectedTeachingMode === 'online' ? 'selected' : '' }}>Online</option>
                                <option value="both" {{ isset($selectedTeachingMode) && $selectedTeachingMode === 'both' ? 'selected' : '' }}>Physical & Online</option>
                            </select>

                            <!-- Min Rating Filter -->
                            <select name="min_rating" class="bg-slate-800 text-slate-200 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs outline-none focus:border-sky-400 font-medium">
                                <option value="0" {{ isset($selectedMinRating) && $selectedMinRating == 0 ? 'selected' : '' }}>All Ratings</option>
                                <option value="4.5" {{ isset($selectedMinRating) && $selectedMinRating == 4.5 ? 'selected' : '' }}>★ 4.5 & above</option>
                                <option value="4.0" {{ isset($selectedMinRating) && $selectedMinRating == 4.0 ? 'selected' : '' }}>★ 4.0 & above</option>
                                <option value="3.0" {{ isset($selectedMinRating) && $selectedMinRating == 3.0 ? 'selected' : '' }}>★ 3.0 & above</option>
                            </select>

                            @if((isset($selectedSubject) && $selectedSubject) || (isset($selectedLevel) && $selectedLevel) || (isset($selectedTeachingMode) && $selectedTeachingMode !== 'All') || (isset($selectedMinRating) && $selectedMinRating > 0))
                                <a href="{{ route('dashboard.talent') }}" class="text-sky-400 hover:text-sky-300 font-medium text-[11px] underline ml-auto px-1">
                                    Clear Filters
                                </a>
                            @endif
                        </div>

                        <!-- Sub-bar Filter Pills -->
                        <div class="flex items-center justify-between gap-3 flex-wrap pt-1">
                            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                                <a href="{{ route('dashboard.talent', ['category' => 'All', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'All' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                                    All Experts
                                </a>
                                <a href="{{ route('dashboard.talent', ['category' => 'Education & Tutoring', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'Education & Tutoring' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                                    Academic Tutors
                                </a>
                                <a href="{{ route('dashboard.talent', ['category' => 'Home & Technical Services', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'Home & Technical Services' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                                    Home & Technical Trades
                                </a>
                                <a href="{{ route('dashboard.talent', ['category' => 'Creative & Digital Services', 'query' => $searchQuery]) }}" class="{{ $selectedCategory === 'Creative & Digital Services' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200' }} px-3 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0">
                                    Creative & Tech
                                </a>
                            </div>

                            <button type="submit" class="bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs px-5 py-2 rounded-xl transition-colors shadow-xs shrink-0 cursor-pointer">
                                Filter Results
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Talent Cards Grid Section -->
                @if(count($professionals) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($professionals as $pro)
                            <div class="bg-white border border-slate-200/80 hover:border-slate-300 rounded-2xl p-5 shadow-xs hover:shadow-sm transition-all flex flex-col justify-between space-y-4 group">
                                <div class="space-y-3.5">
                                    
                                    <!-- Top Row: Avatar + Name + Rating -->
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="relative shrink-0">
                                                @if($pro->user->avatar_url)
                                                    <img src="{{ $pro->user->avatar_url }}" alt="{{ $pro->user->name }}" class="w-11 h-11 rounded-xl object-cover border border-slate-200" />
                                                @else
                                                    <div class="w-11 h-11 rounded-xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-sm shadow-xs">
                                                        {{ strtoupper(substr($pro->user->name ?? 'P', 0, 1)) }}
                                                    </div>
                                                @endif
                                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0" title="Online & Available"></span>
                                            </div>

                                            <div class="min-w-0">
                                                <h3 class="text-sm font-bold text-slate-900 truncate group-hover:text-sky-900 transition-colors">
                                                    {{ $pro->user->name }}
                                                </h3>
                                                <span class="text-[10px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200/80 px-2 py-0.5 rounded-md inline-block">
                                                    ✓ Verified Expert
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-1 text-xs font-bold text-amber-600 bg-amber-50 border border-amber-200/80 px-2 py-0.5 rounded-lg shrink-0">
                                            <span>★</span>
                                            <span>{{ number_format($pro->average_rating ?? 5.0, 1) }}</span>
                                        </div>
                                    </div>

                                    <!-- Headline / Display Title -->
                                    <div>
                                        <h4 class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                            {{ $pro->display_name }}
                                        </h4>
                                    </div>

                                    <!-- Location & Metadata -->
                                    <div class="flex flex-wrap items-center gap-2.5 text-xs text-slate-500 font-medium">
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

                                    <!-- Skills & Subjects Pills -->
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

                                <!-- Action Button: Connect & Hire -->
                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between gap-3 mt-2">
                                    <span class="text-[11px] text-slate-400 font-medium">Guaranteed Acceptance</span>

                                    <button 
                                        @click="openHireModal({
                                            id: {{ $pro->id }},
                                            name: '{{ addslashes($pro->user->name) }}',
                                            display_name: '{{ addslashes($pro->display_name) }}',
                                            location: '{{ addslashes($pro->location) }}',
                                            category: '{{ addslashes($pro->category->name ?? 'Talent') }}',
                                            hourly_rate: '{{ $pro->hourly_rate ? '₦' . number_format($pro->hourly_rate) . '/hr' : 'Flexible Rate' }}'
                                        })" 
                                        class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors cursor-pointer"
                                    >
                                        Connect & Hire (₦1,000) →
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white border border-slate-200/80 rounded-2xl p-12 text-center space-y-3 max-w-lg mx-auto shadow-xs">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">No matching talent found</h3>
                        <p class="text-xs text-slate-500">Try adjusting your search query or selecting "All Categories".</p>
                        <a href="{{ route('dashboard.talent') }}" class="inline-block text-xs font-semibold text-[#0F172B] hover:underline pt-2">
                            Reset Filters →
                        </a>
                    </div>
                @endif

                <!-- Footer -->
                <footer class="py-6 text-center text-xs text-slate-400 font-normal border-t border-slate-200/80">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Skill Marketplace') }}. All rights reserved.
                </footer>

            </main>
        </div>

        <!-- Profile Details Drawer Popup -->
        <div 
            x-show="profileModalOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="profileModalOpen = false" 
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex justify-end"
            style="display: none;"
        >
            <div 
                @click.stop
                x-show="profileModalOpen"
                x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="translate-x-full"
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="w-full max-w-md bg-white h-full shadow-2xl p-6 flex flex-col justify-between overflow-y-auto no-scrollbar border-l border-slate-200/80"
            >
                <div class="space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <span class="text-sm font-bold text-slate-900">My Profile Card</span>
                        </div>
                        <button @click="profileModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center space-y-3">
                        <div class="relative inline-block">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border border-slate-300 shadow-xs mx-auto" />
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-2xl shadow-xs mx-auto">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="w-4 h-4 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0"></span>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Account Overview</h4>
                        <div class="bg-white border border-slate-200/80 rounded-xl divide-y divide-slate-100 text-xs">
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Full Name</span>
                                <span class="font-semibold text-slate-900">{{ $user->name }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Email Address</span>
                                <span class="font-semibold text-slate-900 truncate max-w-[200px]">{{ $user->email }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Phone Number</span>
                                <span class="font-semibold text-slate-900">{{ $user->phone ?? 'Not specified' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-200/80 space-y-3">
                    <a href="{{ url('/profile/edit') }}" class="w-full flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs transition-colors">
                        <span>Edit Full Profile & Services →</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Connect & Hire Modal Popup -->
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
                <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                    <div class="flex items-center gap-2">
                        <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                        <span class="text-sm font-bold text-slate-900">Connect & Hire Request</span>
                    </div>
                    <button @click="hireModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

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

                <div class="bg-sky-50 border border-sky-200 text-sky-950 rounded-xl p-3.5 text-xs space-y-1">
                    <div class="flex items-center justify-between font-bold text-slate-900">
                        <span>Connection Fee:</span>
                        <span class="text-sm font-extrabold text-[#0F172B]">₦1,000</span>
                    </div>
                    <p class="text-[11px] text-slate-600 font-normal leading-relaxed">
                        Note: The ₦1,000 fee is only charged after your connection request is accepted by the expert.
                    </p>
                </div>

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

</x-dashboard-layout>
