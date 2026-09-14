<x-dashboard-layout 
    title="My Job Postings — {{ config('app.name', 'Skill Marketplace') }}"
    active="my-jobs"
    xData="{
        pageLoading: true,
        sidebarOpen: false,
        sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        toggleSidebar: function() {
            this.sidebarCollapsed = !this.sidebarCollapsed;
            localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
        },
        notificationsOpen: false,
        profileModalOpen: false,
        createModalOpen: false,
        deleteModalOpen: false,
        opportunityToDelete: null,
        searchQuery: '',
        filterStatus: 'all',
        isAcademicCategory: false,
        confirmDelete: function(opp) {
            this.opportunityToDelete = opp;
            this.deleteModalOpen = true;
        },
        handleCategoryChange: function(event) {
            var selectedOption = event.target.options[event.target.selectedIndex];
            var categoryName = selectedOption ? selectedOption.getAttribute('data-name') || '' : '';
            categoryName = categoryName.toLowerCase();
            this.isAcademicCategory = categoryName.indexOf('education') !== -1 || categoryName.indexOf('tutor') !== -1;
        }
    }"
>

            <!-- Main Workspace Container -->
            <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

                <!-- Top Navbar Header -->
                <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4 sticky top-0 z-30 shrink-0">
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 cursor-pointer" aria-label="Open navigation menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <div>
                            <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">My Job Postings</h1>
                            <p class="text-xs text-slate-500 hidden sm:block">Manage your published opportunities and track candidate applications.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <!-- Action: Post New Job -->
                        <button 
                            @click="createModalOpen = true"
                            class="bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs py-2 px-3.5 rounded-xl shadow-xs flex items-center gap-1.5 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            <span>+ Post New Job</span>
                        </button>

                        <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                        <!-- Profile Avatar Button -->
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
                </header>

                @if (session('status'))
                    <div class="bg-emerald-600 text-white px-4 sm:px-6 py-3 shrink-0 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('status') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Page Body Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

                    <!-- Metrics Summary Banner Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Total Job Postings</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 block">{{ $myOpportunities->count() }}</span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center text-xl shrink-0">
                                📋
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Active Open Postings</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1 block">
                                    {{ $myOpportunities->filter(fn($o) => ($o->status->value ?? $o->status) === 'open')->count() }}
                                </span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
                                🟢
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                            <div>
                                <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Total Applications Received</span>
                                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 block">
                                    {{ $myOpportunities->sum('connection_requests_count') }}
                                </span>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xl shrink-0">
                                👥
                            </div>
                        </div>
                    </div>

                    <!-- Postings List Section Card -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden space-y-0">
                        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Your Published Opportunities</h3>
                                <p class="text-xs text-slate-500 font-normal">View details, monitor applicants, or delete postings.</p>
                            </div>

                            <button 
                                @click="createModalOpen = true"
                                class="bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs py-2 px-4 rounded-xl shadow-xs inline-flex items-center gap-1.5 transition-colors cursor-pointer"
                            >
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                                <span>Post Opportunity</span>
                            </button>
                        </div>

                        @if($myOpportunities->isEmpty())
                            <div class="p-12 text-center space-y-4">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-3xl">
                                    📂
                                </div>
                                <div class="max-w-md mx-auto space-y-1">
                                    <h4 class="text-base font-bold text-slate-900">No Job Postings Yet</h4>
                                    <p class="text-xs text-slate-500 leading-relaxed">You have not created any opportunity postings yet. Click below to publish your first gig or tutoring request.</p>
                                </div>
                                <button 
                                    @click="createModalOpen = true"
                                    class="bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-5 rounded-xl shadow-xs inline-flex items-center gap-2 transition-colors cursor-pointer"
                                >
                                    <span>+ Post First Job</span>
                                </button>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach($myOpportunities as $opp)
                                    <div class="p-5 hover:bg-slate-50/50 transition-colors flex flex-col lg:flex-row lg:items-center justify-between gap-5">
                                        
                                        <!-- Left Info Area -->
                                        <div class="space-y-2 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                    {{ $opp->category->name ?? 'General Service' }}
                                                </span>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    ● {{ ucfirst($opp->status->value ?? $opp->status) }}
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-medium">
                                                    Posted {{ $opp->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <h4 class="text-base font-bold text-slate-900 tracking-tight leading-snug">
                                                {{ $opp->title }}
                                            </h4>

                                            <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                                {{ $opp->description }}
                                            </p>

                                            <!-- Metadata Badges -->
                                            <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500 pt-1">
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                                    <span>{{ $opp->location }}</span>
                                                </div>
                                                <div class="flex items-center gap-1.5">
                                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m46 0H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2z"/></svg>
                                                    <span>{{ $opp->opportunity_type }}</span>
                                                </div>
                                                @if($opp->budget_min || $opp->budget_max)
                                                    <div class="flex items-center gap-1.5 text-emerald-700 font-bold">
                                                        <span>₦{{ number_format($opp->budget_min) }} @if($opp->budget_max) - ₦{{ number_format($opp->budget_max) }} @endif</span>
                                                    </div>
                                                @endif
                                            </div>

                                            @if($opp->educationDetails)
                                                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                                                    @if($opp->educationDetails->subject)
                                                        <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 border border-indigo-100 text-[10px] font-semibold">
                                                            Subject: {{ $opp->educationDetails->subject->name }}
                                                        </span>
                                                    @endif
                                                    @if($opp->educationDetails->educationLevel)
                                                        <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-100 text-[10px] font-semibold">
                                                            Grade: {{ $opp->educationDetails->educationLevel->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Right Action & Counter Buttons -->
                                        <div class="flex items-center gap-3 shrink-0 border-t lg:border-t-0 pt-3 lg:pt-0">
                                            <!-- Applicant Count Badge -->
                                            <a href="{{ url('/dashboard/messages') }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold flex items-center gap-1.5 transition-colors" title="View Applicants">
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                                <span>{{ $opp->connection_requests_count }} {{ Str::plural('Applicant', $opp->connection_requests_count) }}</span>
                                            </a>

                                            <!-- Delete Button Triggering Confirmation Modal -->
                                            <button 
                                                @click="confirmDelete({ id: {{ $opp->id }}, title: '{{ addslashes($opp->title) }}' })"
                                                class="p-2 rounded-xl text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-rose-200/80 transition-colors cursor-pointer"
                                                title="Delete Job Posting"
                                            >
                                                <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </main>
            </div>
        </div>

        <!-- Delete Confirmation Modal Dialog -->
        <div 
            x-show="deleteModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" 
            style="display: none;"
        >
            <div @click="deleteModalOpen = false" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs"></div>

            <div class="relative bg-white border border-slate-200 rounded-3xl shadow-2xl w-full max-w-md p-6 space-y-6 z-10">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0 text-lg">
                        ⚠️
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Delete Job Posting</h3>
                        <p class="text-xs text-slate-500">Confirm permanent removal</p>
                    </div>
                </div>

                <div class="space-y-2 text-xs text-slate-600">
                    <p>Are you sure you want to delete the opportunity posting <strong class="text-slate-900" x-text="opportunityToDelete ? opportunityToDelete.title : ''"></strong>?</p>
                    <div class="bg-rose-50 border border-rose-200 rounded-xl p-3 text-rose-900 font-normal">
                        This action cannot be undone. The posting and all related application requests will be removed.
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button 
                        @click="deleteModalOpen = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors cursor-pointer"
                    >
                        Cancel
                    </button>

                    <form 
                        :action="opportunityToDelete ? '{{ url('/opportunities') }}/' + opportunityToDelete.id : '#'" 
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer"
                        >
                            Delete Posting
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Job Creation Modal -->
        <div 
            x-show="createModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6" 
            style="display: none;"
        >
            <div @click="createModalOpen = false" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs"></div>

            <div class="relative bg-white border border-slate-200 rounded-3xl shadow-2xl w-full max-w-2xl p-6 sm:p-8 space-y-6 z-10 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Post a New Opportunity</h3>
                        <p class="text-xs text-slate-500 font-medium">Publish a gig or academic tutoring opportunity to connect with verified talent.</p>
                    </div>
                    <button @click="createModalOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ url('/opportunities') }}" method="POST" class="space-y-5 text-xs">
                    @csrf
                    
                    <div class="space-y-1.5">
                        <label for="title" class="font-bold text-slate-700">Opportunity Title <span class="text-rose-500">*</span></label>
                        <input type="text" id="title" name="title" required placeholder="e.g. SS2 Mathematics & Physics Tutor Needed" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900 outline-none transition-all" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="category_id" class="font-bold text-slate-700">Service Category <span class="text-rose-500">*</span></label>
                            <select id="category_id" name="category_id" required @change="handleCategoryChange($event)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900 outline-none transition-all bg-white">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" data-name="{{ $cat->name }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label for="opportunity_type" class="font-bold text-slate-700">Engagement Type <span class="text-rose-500">*</span></label>
                            <select id="opportunity_type" name="opportunity_type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900 outline-none transition-all bg-white">
                                <option value="Contract / Tutoring">Contract / Tutoring</option>
                                <option value="Freelance Gig">Freelance Gig</option>
                                <option value="Full-time">Full-time</option>
                                <option value="Part-time">Part-time</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="location" class="font-bold text-slate-700">Location / City <span class="text-rose-500">*</span></label>
                            <input type="text" id="location" name="location" required placeholder="e.g. Ikeja, Lagos or Remote" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900 outline-none transition-all" />
                        </div>

                        <div class="space-y-1.5">
                            <label for="budget" class="font-bold text-slate-700">Budget Range / Pay Rate</label>
                            <input type="text" id="budget" name="budget" placeholder="e.g. ₦15,000 / week or ₦50,000" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900 outline-none transition-all" />
                        </div>
                    </div>

                    <!-- Dynamic Academic Tutoring Details Expansion -->
                    <div x-show="isAcademicCategory" x-transition class="bg-indigo-50/60 border border-indigo-100 rounded-2xl p-4 space-y-4">
                        <span class="text-xs font-bold text-indigo-900 block">Academic Tutoring Parameters</span>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="space-y-1">
                                <label for="subject_id" class="font-semibold text-slate-700 text-[11px]">Subject</label>
                                <select id="subject_id" name="subject_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                                    <option value="">Select Subject</option>
                                    @foreach($subjects as $sub)
                                        <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="education_level_id" class="font-semibold text-slate-700 text-[11px]">Grade / Level</label>
                                <select id="education_level_id" name="education_level_id" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                                    <option value="">Select Grade Level</option>
                                    @foreach($levels as $lvl)
                                        <option value="{{ $lvl->id }}">{{ $lvl->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label for="teaching_mode" class="font-semibold text-slate-700 text-[11px]">Mode</label>
                                <select id="teaching_mode" name="teaching_mode" class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-xs">
                                    <option value="both">Physical & Online</option>
                                    <option value="physical">Physical In-Person</option>
                                    <option value="online">Online Virtual</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label for="description" class="font-bold text-slate-700">Detailed Description & Requirements <span class="text-rose-500">*</span></label>
                        <textarea id="description" name="description" required rows="4" placeholder="Describe the job responsibilities, schedule preferences, and qualifications needed..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900 outline-none transition-all resize-none"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-3">
                        <button type="button" @click="createModalOpen = false" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold transition-colors cursor-pointer">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#0F172B] hover:bg-slate-800 text-white font-bold shadow-xs transition-colors cursor-pointer">
                            Publish Opportunity
                        </button>
                    </div>
                </form>
            </div>
</x-dashboard-layout>
