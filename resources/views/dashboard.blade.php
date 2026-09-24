<x-dashboard-layout 
    title="Dashboard — {{ config('app.name', 'Skill Link NG') }}"
    active="overview"
    xData="{ 
        pageLoading: true, 
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
        },
        searchQuery: '', 
        selectedCategory: 'All', 
        profileModalOpen: false, 
        notificationsOpen: false,
        postModalOpen: false,
        postCategoryIsAcademic: false,
        checkAcademicCategory(event) {
            const selectedText = event.target.options[event.target.selectedIndex].text.toLowerCase();
            this.postCategoryIsAcademic = selectedText.includes('education') || selectedText.includes('tutor');
        },
        pendingApprovalsCount: {{ json_encode($pendingCount ?? 0) }},
        connectedCount: {{ json_encode($connectedCount ?? 0) }},
        jobDetailModalOpen: false,
        selectedJob: null,
        openJobDetails(job) {
            this.selectedJob = job;
            this.jobDetailModalOpen = true;
        }
    }"
    @realtime-notification-received.window="if ($event.detail && ($event.detail.type === 'connection_request' || $event.detail.type === 'connection_request_created')) pendingApprovalsCount++"
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
                            placeholder="Search opportunities, tutors, or services..." 
                            class="w-full bg-slate-50 border border-slate-200 text-sm text-slate-900 font-normal rounded-xl pl-10 pr-4 py-2 outline-none focus:border-slate-800 focus:bg-white focus:ring-2 focus:ring-slate-100 transition-all"
                        />
                    </div>

                    <!-- Right Header Icons: Notifications Dropdown & Profile Avatar Trigger -->
                    <div class="flex items-center gap-3 shrink-0">
                        
                        <x-header-notifications :userNotifications="$userNotifications ?? []" :unreadCount="$unreadCount ?? 0" />

                        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- Header Profile Button Trigger (Opens Profile Details Slide-Over Popup) -->
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

                <!-- Email Verification Required Alert Banner -->
                @if (!$user->hasVerifiedEmail())
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-amber-50 border border-amber-200 text-amber-900 text-sm font-medium rounded-2xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center shrink-0 border border-amber-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-slate-900 font-semibold block text-xs sm:text-sm">Email verification required</span>
                                <span class="text-xs text-slate-600 font-normal">Please verify <strong>{{ $user->email }}</strong> to unlock applications and connections.</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                            <a href="{{ route('verification.notice') }}" class="px-3.5 py-1.5 bg-[#0F172B] hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shrink-0 transition-colors">
                                Verify Email →
                            </a>
                            <button @click="show = false" class="text-amber-700 hover:text-amber-950 hover:bg-amber-100 p-1.5 rounded-lg transition-colors cursor-pointer" title="Cancel notification">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Session Alert Status Banner -->
                @if (session('status'))
                    <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-medium rounded-2xl p-4 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="truncate sm:whitespace-normal">{{ session('status') }}</span>
                        </div>
                        <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 hover:bg-emerald-100/80 p-1 rounded-lg transition-colors cursor-pointer shrink-0 ml-3" title="Cancel notification">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div x-data="{ show: true }" x-show="show" class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-2xl flex items-center justify-between shadow-xs mb-6">
                        <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold">
                            <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                        <button @click="show = false" class="text-red-700 hover:text-red-950 hover:bg-red-100 p-1 rounded-lg transition-colors cursor-pointer shrink-0 ml-3" title="Dismiss">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif

                <!-- Main Grid Layout Container (Left Feed & Right Suggested Job Sidebar) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                    <!-- Left Main Feed Area (Col Span 8) -->
                    <div class="lg:col-span-8 space-y-6">

                        <!-- JobTrack Style Hero Banner with Integrated Search Pill -->
                        <div class="bg-gradient-to-r from-[#0F172B] via-slate-900 to-sky-950 text-white rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-lg space-y-6 border border-slate-800">
                            <!-- Background ambient glow accents -->
                            <div class="absolute -top-16 -right-16 w-64 h-64 bg-sky-500/10 rounded-full blur-3xl pointer-events-none"></div>
                            <div class="absolute -bottom-16 -left-16 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="relative z-10 space-y-2 max-w-2xl">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-sky-200 text-xs font-semibold backdrop-blur-md">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Explore 1,000+ Verified Local Opportunities</span>
                                </div>

                                <h1 class="text-2xl sm:text-4xl font-extrabold text-white tracking-tight leading-tight">
                                    Find your dream job here!
                                </h1>

                                <p class="text-slate-300 text-xs sm:text-sm leading-relaxed font-normal">
                                    Explore the latest job openings, tutoring requests, and technical contracts available today!
                                </p>
                            </div>

                            <!-- Integrated Pill Search Box inside Hero Banner -->
                            <div class="relative z-10 max-w-2xl bg-white/95 backdrop-blur-md rounded-2xl p-2 shadow-2xl flex items-center gap-2 border border-white/40">
                                <svg class="w-5 h-5 text-slate-400 ml-3 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                <input 
                                    type="text" 
                                    x-model="searchQuery"
                                    placeholder="Search Job, Tutor Subject, or Skill..." 
                                    class="w-full bg-transparent text-sm text-slate-900 placeholder-slate-400 font-medium px-2 py-2 outline-none"
                                />
                                <button 
                                    @click="$nextTick(() => { const el = document.getElementById('opportunities-section'); if(el) el.scrollIntoView({ behavior: 'smooth' }); })"
                                    class="bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm px-6 py-2.5 rounded-xl shadow-md transition-all shrink-0 cursor-pointer flex items-center gap-1.5"
                                >
                                    <span>Search</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modern Stats Metrics Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- Metric 1 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Active Connections</span>
                                    <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900 tracking-tight" x-text="connectedCount">{{ $connectedCount ?? 0 }}</div>
                                <span class="text-[11px] text-slate-400 font-normal block"><span x-text="pendingApprovalsCount">{{ $pendingCount ?? 0 }}</span> pending approvals</span>
                            </div>

                            <!-- Metric 2 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Open Opportunities</span>
                                    <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect width="8" height="4" x="8" y="2" rx="1" ry="1"/></svg>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900 tracking-tight">{{ count($opportunities) }}</div>
                                <span class="text-[11px] text-slate-400 font-normal block">Available to connect</span>
                            </div>

                            <!-- Metric 3 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Trust Score</span>
                                    <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    </span>
                                </div>
                                <div class="text-2xl font-bold text-slate-900 tracking-tight">5.0</div>
                                <span class="text-[11px] text-slate-400 font-normal block">Verified rating</span>
                            </div>

                            <!-- Metric 4 -->
                            <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-1.5 hover:border-slate-300 transition-colors">
                                <div class="flex items-center justify-between text-slate-500 text-xs font-medium">
                                    <span>Account Category</span>
                                    <span class="w-8 h-8 rounded-lg bg-slate-50 text-slate-700 border border-slate-200/60 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-slate-700" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </span>
                                </div>
                                <div class="text-sm font-bold text-slate-900 truncate tracking-tight">
                                    {{ $user->professionalProfile->category->name ?? $user->onboarding_intent ?? 'Client / Talent' }}
                                </div>
                                <span class="text-[11px] text-emerald-600 font-medium block">Verified Status</span>
                            </div>

                        </div>

                        <!-- Marketplace Opportunities List Section -->
                        <section id="opportunities-section" class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-7 shadow-xs space-y-6">
                            
                            <!-- Section Header -->
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-200/80 pb-5">
                                <div>
                                    <h2 class="text-lg sm:text-xl font-bold text-slate-900 tracking-tight">
                                        Live Opportunities & Jobs
                                    </h2>
                                    <p class="text-xs sm:text-sm text-slate-500 font-normal mt-0.5">
                                        Browse requests posted by clients and parents. Filter by category or search by keywords.
                                    </p>
                                </div>

                                <!-- Post CTA -->
                                <button @click="postModalOpen = true" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl transition-colors shadow-xs shrink-0 cursor-pointer">
                                    + Post New Job
                                </button>
                            </div>

                            <!-- Search Box & Filter Tabs -->
                            <div class="space-y-3.5">
                                
                                <!-- Category Filter Pills -->
                                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
                                    <button 
                                        @click="selectedCategory = 'All'" 
                                        :class="selectedCategory === 'All' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                                    >
                                        All Categories
                                    </button>
                                    <button 
                                        @click="selectedCategory = 'Academic Tutoring'" 
                                        :class="selectedCategory === 'Academic Tutoring' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                                    >
                                        Academic Tutoring
                                    </button>
                                    <button 
                                        @click="selectedCategory = 'Home & Technical'" 
                                        :class="selectedCategory === 'Home & Technical' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                                    >
                                        Home & Technical
                                    </button>
                                    <button 
                                        @click="selectedCategory = 'Creative & Digital'" 
                                        :class="selectedCategory === 'Creative & Digital' ? 'bg-[#0F172B] text-white border-[#0F172B]' : 'bg-white text-slate-600 hover:bg-slate-50 border-slate-200'"
                                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold border transition-colors shrink-0 cursor-pointer"
                                    >
                                        Creative & Digital
                                    </button>
                                </div>

                            </div>

                            <!-- Opportunities List Grid -->
                            <div class="space-y-4">
                                @foreach($opportunities as $opp)
                                    <div 
                                        x-show="(selectedCategory === 'All' || selectedCategory === '{{ $opp['category'] }}') && ('{{ strtolower($opp['title'] . ' ' . $opp['description'] . ' ' . $opp['location']) }}'.includes(searchQuery.toLowerCase()))"
                                        class="border border-slate-200/80 hover:border-slate-300 rounded-xl p-5 bg-white hover:shadow-xs transition-all space-y-3 group"
                                    >
                                        <!-- Top Info Line -->
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <span class="px-2.5 py-0.5 rounded-md text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/60">
                                                    {{ $opp['category'] }}
                                                </span>
                                                <span class="text-xs text-slate-400 font-normal">• {{ $opp['time_ago'] }}</span>
                                            </div>
                                            <div class="text-base font-bold text-slate-900">
                                                {{ $opp['budget'] }}
                                            </div>
                                        </div>

                                        <!-- Title & Description -->
                                        <div class="space-y-1">
                                            <h3 class="text-base font-semibold text-slate-900 group-hover:text-sky-700 transition-colors">
                                                {{ $opp['title'] }}
                                            </h3>
                                            <p class="text-xs sm:text-sm text-slate-600 font-normal leading-relaxed">
                                                {{ $opp['description'] }}
                                            </p>
                                        </div>

                                        <!-- Metadata & Action -->
                                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 border-t border-slate-100">
                                            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 font-normal">
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                                    {{ $opp['location'] }}
                                                </span>
                                                <span>•</span>
                                                <span class="flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                                    {{ $opp['type'] }}
                                                </span>
                                            </div>

                                            <!-- Action Button -->
                                            @if (!empty($opp['is_own']) || (isset($opp['user_id']) && $opp['user_id'] === Auth::id()))
                                                <div class="inline-flex items-center gap-2">
                                                    <span class="px-2.5 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-[11px] font-bold">
                                                        Your Posting
                                                    </span>
                                                    <button @click="openJobDetails({{ json_encode($opp) }})" class="inline-flex items-center justify-center bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs py-2 px-3 rounded-xl transition-colors cursor-pointer">
                                                        View Details
                                                    </button>
                                                </div>
                                            @else
                                                <button @click="openJobDetails({{ json_encode($opp) }})" class="inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2 px-4 rounded-xl transition-colors shadow-xs cursor-pointer">
                                                    Apply & Connect →
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </section>

                    </div>

                    <!-- Right Sidebar Area: Suggested Job Column (Col Span 4 - Aligned to Top Level with Hero Banner) -->
                    <div class="lg:col-span-4 space-y-5">
                        
                        <!-- Suggested Jobs Compact Container -->
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-xs space-y-4">
                            <!-- Container Header -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900 tracking-tight">Suggested Jobs</h3>
                                </div>
                                <a href="#opportunities-section" class="text-xs text-sky-600 hover:text-sky-800 font-semibold transition-colors">
                                    View All
                                </a>
                            </div>

                            <!-- Job Items List -->
                            <div class="divide-y divide-slate-100">
                                @forelse($suggestedJobs as $job)
                                    <div 
                                        @click="openJobDetails({{ json_encode($job['raw_opp']) }})"
                                        class="py-3.5 first:pt-0 last:pb-0 flex items-center justify-between gap-3 group cursor-pointer hover:bg-slate-50/80 p-2 rounded-xl transition-colors"
                                    >
                                        <div class="flex items-center gap-3 min-w-0">
                                            @if(!empty($job['avatar']))
                                                <img src="{{ $job['avatar'] }}" alt="{{ $job['client_name'] }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200 shrink-0 shadow-2xs group-hover:scale-105 transition-transform" />
                                            @else
                                                <div class="w-9 h-9 rounded-xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-2xs group-hover:scale-105 transition-transform">
                                                    {{ $job['user_initial'] }}
                                                </div>
                                            @endif
                                            <div class="min-w-0 space-y-1">
                                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-sky-700 transition-colors truncate">
                                                    {{ $job['title'] }}
                                                </h4>
                                                <div class="flex flex-wrap items-center gap-1.5 text-[11px] text-slate-500 font-medium truncate">
                                                    <span>{{ $job['client_name'] }}</span>
                                                    <span>•</span>
                                                    <span class="text-slate-800 font-semibold">{{ $job['budget'] }}</span>
                                                </div>
                                                @if(!empty($job['match_reason']))
                                                    <div class="pt-0.5">
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-sky-50 text-sky-700 text-[10px] font-semibold border border-sky-200/60">
                                                            <svg class="w-3 h-3 text-sky-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                            {{ $job['match_reason'] }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <button 
                                            @click.stop="openJobDetails({{ json_encode($job['raw_opp']) }})" 
                                            class="w-7 h-7 rounded-lg bg-slate-50 group-hover:bg-[#0F172B] group-hover:text-white text-slate-600 border border-slate-200/60 flex items-center justify-center transition-colors shrink-0 cursor-pointer" 
                                            title="View details & connect"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                        </button>
                                    </div>
                                @empty
                                    <div class="py-4 text-center text-xs text-slate-400 font-normal">
                                        No suggested jobs matching your profile yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Quick Post Custom Job Promo Box -->
                        <div class="bg-slate-50/80 border border-slate-200/80 rounded-2xl p-5 text-center space-y-3 shadow-2xs">
                            <div class="w-10 h-10 rounded-xl bg-[#0F172B] text-white flex items-center justify-center mx-auto shadow-xs">
                                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 18v-1.5m0-10.5a6 6 0 00-6 6c0 2.22 1.21 4.156 3 5.196V18a1 1 0 001 1h4a1 1 0 001-1v-1.304A6.002 6.002 0 0018 12a6 6 0 00-6-6z"/></svg>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-900">Need a Specific Skill or Tutor?</h4>
                                <p class="text-[11px] text-slate-500 font-normal leading-relaxed">
                                    Post a custom opportunity to receive bids from verified local professionals.
                                </p>
                            </div>
                            <button @click="postModalOpen = true" class="w-full inline-flex items-center justify-center bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2.5 px-4 rounded-xl transition-all shadow-xs cursor-pointer">
                                + Post Custom Job
                            </button>
                        </div>

                    </div>

                </div>


                <!-- Clean Footer -->
                <footer class="py-6 text-center text-xs text-slate-400 font-normal border-t border-slate-200/80">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Skill Link NG') }}. All rights reserved.
                </footer>

            </main>
        </div>

        <!-- Profile Details Slide-Over Drawer Popup -->
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
            <!-- Drawer Body -->
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
                    <!-- Drawer Header -->
                    <div class="flex items-center justify-between pb-4 border-b border-slate-200/80">
                        <div class="flex items-center gap-2">
                            <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div>
                            <span class="text-sm font-bold text-slate-900">My Profile Card</span>
                        </div>
                        <button @click="profileModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Profile Avatar Card -->
                    <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 text-center space-y-3">
                        <div class="relative inline-block">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-2xl object-cover border border-slate-300 shadow-xs mx-auto" />
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-2xl shadow-xs mx-auto">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <span class="w-4 h-4 rounded-full bg-emerald-500 border-2 border-white absolute bottom-0 right-0" title="Online & Active"></span>
                        </div>

                        <div>
                            <h3 class="text-base font-bold text-slate-900">{{ $user->name }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                        </div>

                        <div class="pt-1 flex justify-center">
                            @if($completionPercentage == 100)
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    ✓ Verified Profile (100%)
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-200 text-slate-800 border border-slate-300">
                                    Profile {{ $completionPercentage }}% Complete
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Details Table -->
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
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Location</span>
                                <span class="font-semibold text-slate-900">{{ $user->location ?? 'Nigeria' }}</span>
                            </div>
                            <div class="p-3 flex justify-between items-center">
                                <span class="text-slate-500 font-medium">Primary Category</span>
                                <span class="font-semibold text-slate-900">{{ $user->professionalProfile->category->name ?? $user->onboarding_intent ?? 'Client / Talent' }}</span>
                            </div>
                        </div>

                        @if($user->professionalProfile && $user->professionalProfile->bio)
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Service Biography</h4>
                                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3 text-xs text-slate-700 leading-relaxed font-normal">
                                    {{ $user->professionalProfile->bio }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Footer Action Buttons -->
                <div class="pt-6 border-t border-slate-200/80 space-y-3">
                    <a href="{{ url('/profile/edit') }}" class="w-full flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs transition-colors">
                        <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        <span>Edit Full Profile & Services →</span>
                    </a>

                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 transition-colors cursor-pointer">
                            <span>Sign Out Account</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Post Opportunity Modal Dialog -->
        <div 
            x-show="postModalOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
            style="display: none;"
        >
            <div 
                @click.away="postModalOpen = false"
                x-show="postModalOpen"
                x-transition:enter="transition ease-out duration-250 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden my-8"
            >
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-[#0F172B] text-white flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-white">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-white tracking-tight">Post a New Opportunity</h3>
                            <p class="text-[11px] text-slate-300 font-normal">Connect with verified tutors, artisans, and professionals</p>
                        </div>
                    </div>
                    <button @click="postModalOpen = false" class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer" aria-label="Close modal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form action="{{ route('opportunities.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    <!-- Title & Category Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1 sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700">Opportunity Title <span class="text-rose-500">*</span></label>
                            <input type="text" name="title" required placeholder="e.g. SS2 Physics & Math Tutor Needed or Electrician for Rewiring" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition-all" />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-700">Service Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" required @change="checkAcademicCategory($event)" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition-all">
                                <option value="" disabled selected>Select Category...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-700">Opportunity Type <span class="text-rose-500">*</span></label>
                            <select name="opportunity_type" required class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition-all">
                                <option value="Physical In-Person">Physical In-Person</option>
                                <option value="Online / Remote">Online / Remote</option>
                                <option value="One-Off Contract">One-Off Contract</option>
                                <option value="Weekly Tutoring">Weekly Tutoring</option>
                                <option value="Freelance Gig">Freelance Gig</option>
                            </select>
                        </div>
                    </div>

                    <!-- Dynamic Academic Tutoring Fields (Displayed when Education category is picked) -->
                    <div x-show="postCategoryIsAcademic" x-transition class="p-4 bg-sky-50/80 border border-sky-100 rounded-xl space-y-4">
                        <div class="flex items-center gap-2 text-xs font-bold text-sky-900 border-b border-sky-200/60 pb-2">
                            <svg class="w-4 h-4 text-sky-700" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                            Academic Tutoring Specific Details
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-700">Subject</label>
                                <select name="subject_id" class="w-full bg-white border border-slate-200 focus:border-slate-800 rounded-lg px-2.5 py-2 text-xs font-medium outline-none">
                                    <option value="">Select Subject...</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-700">Target Level</label>
                                <select name="education_level_id" class="w-full bg-white border border-slate-200 focus:border-slate-800 rounded-lg px-2.5 py-2 text-xs font-medium outline-none">
                                    <option value="">Select Level...</option>
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-[11px] font-semibold text-slate-700">Teaching Mode</label>
                                <select name="teaching_mode" class="w-full bg-white border border-slate-200 focus:border-slate-800 rounded-lg px-2.5 py-2 text-xs font-medium outline-none">
                                    <option value="physical">Physical (In-Person)</option>
                                    <option value="online">Online Virtual</option>
                                    <option value="both">Both Options</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Location & Budget Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-700">Location / Area <span class="text-rose-500">*</span></label>
                            <input type="text" name="location" required placeholder="e.g. Ikeja, Lagos or Garki, Abuja" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition-all" />
                        </div>

                        <div class="space-y-1">
                            <label class="block text-xs font-semibold text-slate-700">Budget / Rate (₦)</label>
                            <input type="text" name="budget" placeholder="e.g. ₦15,000 / week or ₦45,000" class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl px-3.5 py-2.5 text-xs sm:text-sm font-medium outline-none transition-all" />
                        </div>
                    </div>

                    <!-- Detailed Description -->
                    <div class="space-y-1">
                        <label class="block text-xs font-semibold text-slate-700">Description & Requirements <span class="text-rose-500">*</span></label>
                        <textarea name="description" rows="3" required placeholder="Describe what you are looking for, schedule requirements, or specific experience needed..." class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-xs sm:text-sm font-medium outline-none transition-all"></textarea>
                    </div>

                    <!-- Actions Bar -->
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="postModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-2.5 px-6 rounded-xl shadow-xs transition-colors cursor-pointer flex items-center gap-2">
                            <span>Publish Opportunity →</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Job Details Pop-Up Modal -->
        <div 
            x-show="jobDetailModalOpen" 
            x-transition:enter="transition-opacity ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6"
            style="display: none;"
        >
            <div 
                @click.away="jobDetailModalOpen = false"
                x-show="jobDetailModalOpen"
                x-transition:enter="transition ease-out duration-250 transform"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150 transform"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="bg-white border border-slate-200 rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden my-8"
            >
                <template x-if="selectedJob">
                    <div>
                        <!-- Modal Header -->
                        <div class="px-6 py-4 bg-[#0F172B] text-white flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <span class="px-2.5 py-0.5 rounded-md text-xs font-semibold bg-white/10 text-sky-200 border border-white/20">
                                    <span x-text="selectedJob.category"></span>
                                </span>
                                <span class="text-xs text-slate-300">• Posted <span x-text="selectedJob.time_ago"></span></span>
                            </div>
                            <button @click="jobDetailModalOpen = false" class="text-slate-300 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors cursor-pointer" aria-label="Close modal">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="p-6 space-y-6">
                            <!-- Title & Budget -->
                            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 pb-4 border-b border-slate-100">
                                <div class="space-y-1 min-w-0">
                                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 leading-tight" x-text="selectedJob.title"></h2>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                            <span x-text="selectedJob.location"></span>
                                        </span>
                                        <span>•</span>
                                        <span class="flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                            <span x-text="selectedJob.type"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-lg sm:text-xl font-extrabold text-emerald-600 bg-emerald-50 px-3.5 py-1.5 rounded-xl border border-emerald-200/60 shrink-0">
                                    <span x-text="selectedJob.budget"></span>
                                </div>
                            </div>

                            <!-- Full Description -->
                            <div class="space-y-2">
                                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Opportunity Description & Requirements</h3>
                                <p class="text-xs sm:text-sm text-slate-700 font-normal leading-relaxed bg-slate-50/80 border border-slate-200/80 rounded-xl p-4" x-text="selectedJob.description"></p>
                            </div>

                            <!-- Tags / Metadata -->
                            <template x-if="selectedJob.tags && selectedJob.tags.length">
                                <div class="space-y-2">
                                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Key Details & Tags</h3>
                                    <div class="flex flex-wrap gap-2">
                                        <template x-for="tag in selectedJob.tags" :key="tag">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200/80" x-text="tag"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Application Form Action / Owner Lock Notice -->
                            <template x-if="selectedJob.is_own || selectedJob.user_id === {{ Auth::id() }}">
                                <div class="pt-4 border-t border-slate-100 space-y-4">
                                    <div class="bg-amber-50 border border-amber-200/90 rounded-2xl p-4 text-amber-900 space-y-1.5 shadow-xs">
                                        <div class="flex items-center gap-2 font-bold text-xs">
                                            <svg class="w-4.5 h-4.5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                            <span>You Created This Opportunity Posting</span>
                                        </div>
                                        <p class="text-xs text-amber-700 font-normal leading-relaxed">
                                            You cannot submit an application or connect to an opportunity that you posted yourself.
                                        </p>
                                    </div>
                                    <div class="flex items-center justify-end">
                                        <button type="button" @click="jobDetailModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors cursor-pointer">
                                            Close Modal
                                        </button>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!selectedJob.is_own && selectedJob.user_id !== {{ Auth::id() }}">
                                <form :action="'{{ url('/opportunities') }}/' + selectedJob.id + '/apply'" method="POST" class="pt-4 border-t border-slate-100 space-y-4">
                                    @csrf
                                    <input type="hidden" name="job_title" :value="selectedJob.title" />
                                    <input type="hidden" name="job_category" :value="selectedJob.category" />

                                    <div class="space-y-1.5">
                                        <label class="block text-xs font-semibold text-slate-700">Cover Note / Application Message (Optional)</label>
                                        <textarea name="note" rows="2" placeholder="Introduce yourself, mention your experience, or state your availability..." class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-xs sm:text-sm font-medium outline-none transition-all"></textarea>
                                    </div>

                                    <div class="flex items-center justify-between gap-3 pt-2">
                                        <div class="text-[11px] text-slate-500 font-normal">
                                            <span class="font-bold text-slate-700">Free to Apply:</span> Connection request sits as pending until accepted by poster.
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button type="button" @click="jobDetailModalOpen = false" class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors cursor-pointer">
                                                Close
                                            </button>
                                            <button type="submit" class="bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-2.5 px-5 rounded-xl shadow-xs transition-colors cursor-pointer flex items-center gap-2">
                                                <span>Submit Application & Connect →</span>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Profile Slide-Over Drawer Modal -->
            <x-profile-drawer :user="$user" :completionPercentage="$completionPercentage" />
</x-dashboard-layout>

