<x-dashboard-layout 
    title="Find Jobs & Opportunities — Dashboard — {{ config('app.name', 'Skill Link NG') }}"
    active="jobs"
    xData="{ 
        pageLoading: true,
        sidebarOpen: false, 
        sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        toggleSidebar() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
        },
        profileModalOpen: false, 
        notificationsOpen: false,
        selectedJob: null,
        applyModalOpen: false,
        openApplyModal(job) {
            this.selectedJob = job;
            this.applyModalOpen = true;
        }
    }"
>

    <!-- Main Dashboard Viewport -->
    <main class="flex-1 min-w-0 space-y-6">
        
        <!-- Top Header Bar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-3.5 sm:px-6 shadow-xs flex items-center justify-between gap-4">
            
            <!-- Desktop Sidebar Collapse Toggle Button & Title -->
            <div class="flex items-center gap-3">
                <button 
                    @click="toggleSidebar()" 
                    class="hidden lg:flex items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shrink-0 cursor-pointer transition-colors"
                    :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                >
                    <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>

                <!-- Mobile Navigation Menu Toggle Button -->
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-600 hover:text-slate-900 p-2 rounded-xl border border-slate-200 bg-slate-50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Explore Job Opportunities</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Find tasks, projects, and tutoring contracts posted by verified clients</p>
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-3">
                <a href="{{ url('/dashboard/my-jobs') }}" class="inline-flex items-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-2.5 px-3.5 rounded-xl shadow-xs transition-colors">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Post an Opportunity</span>
                </a>
            </div>
        </div>

        <!-- Filter Search Engine Bar -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-4 sm:p-5 shadow-xs space-y-4">
            <form action="{{ route('dashboard.jobs') }}" method="GET" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    
                    <!-- Keyword Search -->
                    <div class="relative">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                        <input 
                            type="text" 
                            name="query"
                            value="{{ $query }}"
                            placeholder="Search title, skills..." 
                            class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 text-xs sm:text-sm rounded-xl pl-10 pr-3 py-2.5 outline-none font-medium"
                        />
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select name="category_id" class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 text-xs rounded-xl px-3 py-2.5 outline-none font-medium">
                            <option value="all">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Location Filter -->
                    <div>
                        <input 
                            type="text" 
                            name="location"
                            value="{{ $location }}"
                            placeholder="Location (e.g. Ikeja, Lagos)" 
                            class="w-full bg-slate-50 text-slate-900 border border-slate-200 focus:border-slate-800 text-xs rounded-xl px-3 py-2.5 outline-none font-medium"
                        />
                    </div>

                    <!-- Submit Button -->
                    <div>
                        <button type="submit" class="w-full bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs sm:text-sm py-2.5 px-4 rounded-xl shadow-xs transition-colors cursor-pointer">
                            Filter Opportunities →
                        </button>
                    </div>

                </div>
            </form>
        </div>

        <!-- Job Listings Feed Grid -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                    Available Listings ({{ $opportunities->total() }})
                </span>
                @if($query || $categoryId || $location)
                    <a href="{{ route('dashboard.jobs') }}" class="text-xs font-semibold text-[#2563EB] hover:underline">
                        Reset Filters
                    </a>
                @endif
            </div>

            @if($opportunities->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($opportunities as $opp)
                        @php
                            $isMyOpportunity = ($opp->user_id === $user->id);
                            $hasApplied = in_array($opp->id, $appliedOpportunityIds);
                        @endphp

                        <div class="bg-white border border-slate-200/80 hover:border-blue-400 rounded-2xl p-5 shadow-xs hover:shadow-md transition-all flex flex-col justify-between space-y-4 group">
                            <div class="space-y-3">
                                <!-- Top Row: Category + Posted Date -->
                                <div class="flex items-center justify-between gap-2">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-[#2563EB] border border-blue-100 truncate max-w-[180px]">
                                        {{ $opp->category->name ?? 'General Task' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium shrink-0">
                                        {{ $opp->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <!-- Title -->
                                <h3 class="text-base font-bold text-slate-900 leading-snug group-hover:text-[#2563EB] transition-colors">
                                    {{ $opp->title }}
                                </h3>

                                <!-- Location & Opportunity Type -->
                                <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $opp->location }}
                                    </span>
                                    <span>•</span>
                                    <span class="capitalize px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold text-[10px]">
                                        {{ str_replace('_', ' ', $opp->opportunity_type) }}
                                    </span>
                                </div>

                                <!-- Budget -->
                                <div class="text-xs font-bold text-slate-900 bg-slate-50 p-2 rounded-xl border border-slate-200/60 inline-block">
                                    💰 Budget: 
                                    @if($opp->budget_min || $opp->budget_max)
                                        ₦{{ number_format($opp->budget_min ?? 0) }} {{ $opp->budget_max ? '- ₦' . number_format($opp->budget_max) : '+' }}
                                    @else
                                        Flexible / Negotiable
                                    @endif
                                </div>

                                <!-- Description snippet -->
                                <p class="text-xs text-slate-600 font-normal leading-relaxed line-clamp-3">
                                    {{ $opp->description }}
                                </p>

                                @if($opp->educationDetails && $opp->educationDetails->subject)
                                    <div class="pt-1">
                                        <span class="px-2 py-1 rounded-md text-[11px] font-bold bg-sky-50 text-sky-800 border border-sky-200">
                                            🎓 {{ $opp->educationDetails->subject->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Card Footer Action -->
                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-7 h-7 rounded-full bg-[#0F172B] text-white font-bold text-xs flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($opp->user->name ?? 'C', 0, 1)) }}
                                    </div>
                                    <span class="text-xs text-slate-600 font-medium truncate">
                                        {{ $opp->user->name ?? 'Client' }}
                                    </span>
                                </div>

                                @if($isMyOpportunity)
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                        Your Posting
                                    </span>
                                @elseif($hasApplied)
                                    <span class="px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                                        ✓ Applied
                                    </span>
                                @else
                                    <button 
                                        @click="openApplyModal({
                                            id: {{ $opp->id }},
                                            title: '{{ addslashes($opp->title) }}',
                                            poster_name: '{{ addslashes($opp->user->name ?? 'Employer') }}',
                                            location: '{{ addslashes($opp->location) }}'
                                        })" 
                                        class="inline-flex items-center justify-center bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs py-2 px-3.5 rounded-xl shadow-xs transition-colors shrink-0 cursor-pointer"
                                    >
                                        Apply Now →
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-4">
                    {{ $opportunities->links() }}
                </div>
            @else
                <div class="bg-white border border-slate-200/80 rounded-2xl p-12 text-center space-y-3 shadow-xs">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">No open opportunities found</h3>
                    <p class="text-xs text-slate-500">Check back later or adjust your search filters.</p>
                </div>
            @endif
        </div>

    </main>

    <!-- Application Modal Popup -->
    <div 
        x-show="applyModalOpen" 
        x-transition:enter="transition-opacity ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="applyModalOpen = false" 
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.stop
            x-show="applyModalOpen"
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
                    <div class="w-2.5 h-2.5 rounded-full bg-[#2563EB]"></div>
                    <span class="text-sm font-bold text-slate-900">Apply for Opportunity</span>
                </div>
                <button @click="applyModalOpen = false" class="p-1 text-slate-400 hover:text-slate-700 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Selected Job Summary -->
            <template x-if="selectedJob">
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-1">
                    <h3 class="text-sm font-bold text-slate-900" x-text="selectedJob.title"></h3>
                    <div class="text-[11px] text-slate-500 font-medium flex items-center gap-2">
                        <span>Posted by: <strong class="text-slate-700" x-text="selectedJob.poster_name"></strong></span>
                        <span>•</span>
                        <span x-text="selectedJob.location"></span>
                    </div>
                </div>
            </template>

            <!-- Request Form -->
            <form x-bind:action="'/opportunities/' + (selectedJob ? selectedJob.id : '') + '/apply'" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Application Pitch / Cover Note</label>
                    <textarea 
                        name="note"
                        rows="3" 
                        placeholder="State your qualifications, experience, and availability for this job..."
                        class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-xs text-slate-900 font-normal outline-none transition-all"
                    ></textarea>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-[#2563EB] hover:bg-blue-600 text-white font-bold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-md transition-colors cursor-pointer"
                >
                    Submit Application →
                </button>
            </form>
        </div>
    </div>

</x-dashboard-layout>
