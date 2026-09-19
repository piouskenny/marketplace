<x-dashboard-layout 
    title="{{ $user->name }} — Profile — {{ config('app.name', 'Skill Marketplace') }}"
    active="talent"
    xData="{ hireModalOpen: false }"
>

    <!-- Main Profile Viewport -->
    <main class="flex-1 min-w-0 space-y-6 max-w-5xl mx-auto">
        
        <!-- Top Header Bar -->
        <header class="bg-white border border-slate-200/80 rounded-2xl p-3.5 sm:px-6 shadow-xs flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
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
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 cursor-pointer" aria-label="Open navigation menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">User Profile Details</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">View qualifications, experience, and contact status.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : url('/dashboard/talent') }}" class="text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 px-3.5 py-2 rounded-xl transition-colors">
                    ← Back
                </a>
                <button @click="profileModalOpen = true" class="flex items-center gap-2 cursor-pointer hover:opacity-80 transition-opacity">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" />
                    @else
                        <div class="w-8 h-8 rounded-lg bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0">
                            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-xs font-semibold text-slate-800 hidden sm:inline">{{ auth()->user()->name ?? 'User' }}</span>
                </button>
            </div>
        </header>

        <!-- Status Alerts -->
        @if (session('status'))
            <div x-data="{ show: true }" x-show="show" x-transition class="bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs sm:text-sm font-medium rounded-2xl p-4 flex items-center justify-between gap-2 shadow-xs">
                <div class="flex items-center gap-2 min-w-0">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    <span class="truncate sm:whitespace-normal">{{ session('status') }}</span>
                </div>
                <button @click="show = false" class="text-emerald-700 hover:text-emerald-950 p-1 rounded-lg transition-colors cursor-pointer shrink-0 ml-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <!-- Profile Hero Card -->
        <div class="bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
                <div class="flex items-center gap-5 min-w-0">
                    <div class="relative shrink-0">
                        @if($user->avatar_url)
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover border border-slate-300 shadow-xs" />
                        @else
                            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-3xl shadow-xs">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                        @endif
                        <span class="w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-white absolute bottom-1 right-1" title="Online User"></span>
                    </div>

                    <div class="space-y-1.5 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h2 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight truncate">{{ $user->name }}</h2>
                            @if($isContactUnlocked)
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    <span>Verified & Connected</span>
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    Marketplace Member
                                </span>
                            @endif
                        </div>

                        <p class="text-xs sm:text-sm font-semibold text-slate-700">
                            {{ $profile->display_name ?? ($profile->category->name ?? 'Skill Marketplace User') }}
                        </p>

                        <div class="flex items-center gap-4 text-xs text-slate-500 flex-wrap pt-0.5">
                            <div class="flex items-center gap-1 font-medium">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 21s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 7.2c0 7.3-8 11.8-8 11.8z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>{{ $user->location ?? 'Nigeria' }}</span>
                            </div>
                            <span>•</span>
                            <div class="flex items-center gap-1 text-amber-600 font-bold">
                                <svg class="w-3.5 h-3.5 fill-amber-400 text-amber-400" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                <span>{{ number_format($rating, 1) }}</span>
                                <span class="text-slate-400 font-normal">({{ $reviews->count() }} Reviews)</span>
                            </div>
                            @if($profile && $profile->years_of_experience)
                                <span>•</span>
                                <span class="font-medium text-slate-600">{{ $profile->years_of_experience }} Years Exp.</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Primary Action Button -->
                <div class="w-full sm:w-auto shrink-0 flex flex-col sm:flex-row gap-3">
                    @if($isOwnProfile)
                        <a href="{{ url('/profile/edit') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-5 rounded-xl shadow-xs transition-colors">
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span>Edit My Profile →</span>
                        </a>
                    @elseif($connection)
                        <a href="{{ url('/dashboard/messages?conn_id=' . $connection->id) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm py-3 px-6 rounded-xl shadow-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span>Go to Chat / Message Now →</span>
                        </a>
                    @else
                        <button @click="hireModalOpen = true" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs sm:text-sm py-3 px-6 rounded-xl shadow-xs transition-colors cursor-pointer">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            <span>Connect & Hire Professional →</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Contact Information Card (Locked / Masked vs Unlocked) -->
            <div class="p-5 rounded-2xl border transition-all {{ $isContactUnlocked ? 'bg-emerald-50/70 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="space-y-1">
                        <div class="flex items-center gap-2">
                            @if($isContactUnlocked)
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <h3 class="text-xs font-bold text-emerald-950 uppercase tracking-wider">Direct Contact Details (Unlocked)</h3>
                            @else
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Contact Details (Masked & Locked)</h3>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 font-normal">
                            @if($isContactUnlocked)
                                You have an active connection with this user. Phone number and email address are fully visible.
                            @else
                                Phone number and email address are hidden. Connect and pay the connection fee to view direct contact details.
                            @endif
                        </p>
                    </div>

                    @if(!$isContactUnlocked && !$isOwnProfile && $connection && ($dbStatus === 'accepted' || in_array($connection->id, session('accepted_connections', []))))
                        <form action="{{ url('/connections/' . $connection->id . '/pay') }}" method="POST">
                            @csrf
                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors cursor-pointer shrink-0">
                                Pay ₦1,000 & Unlock Contact Info →
                            </button>
                        </form>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 mt-4 border-t border-slate-200/60 text-xs">
                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-slate-400 font-medium block text-[11px]">Phone Number</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ $maskedPhone }}</span>
                        </div>
                        @if($isContactUnlocked && $user->phone)
                            <a href="tel:{{ $user->phone }}" class="p-2 rounded-lg bg-emerald-100 text-emerald-800 font-semibold hover:bg-emerald-200 transition-colors">
                                Call Now
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 p-2 rounded-lg bg-slate-100 text-slate-500 text-xs font-medium" title="Locked until connection is paid">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <span>Locked</span>
                            </span>
                        @endif
                    </div>

                    <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-slate-400 font-medium block text-[11px]">Email Address</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block truncate max-w-[200px] sm:max-w-[240px]">{{ $maskedEmail }}</span>
                        </div>
                        @if($isContactUnlocked)
                            <a href="mailto:{{ $user->email }}" class="p-2 rounded-lg bg-emerald-100 text-emerald-800 font-semibold hover:bg-emerald-200 transition-colors">
                                Send Email
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1 p-2 rounded-lg bg-slate-100 text-slate-500 text-xs font-medium" title="Locked until connection is paid">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                <span>Locked</span>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Service Biography -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Service Biography & Overview</h3>
                <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
                    {{ $profile->bio ?? 'No detailed service biography provided yet.' }}
                </div>
            </div>

            <!-- Skills & Categories -->
            @if($profile && $profile->skills && $profile->skills->isNotEmpty())
                <div class="space-y-3 pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Core Skills & Specializations</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($profile->skills as $sk)
                            <span class="px-3 py-1 rounded-xl bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold">
                                {{ $sk->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Tutoring / Academic Options -->
            @if($profile && $profile->educationProfile)
                @php $edu = $profile->educationProfile; @endphp
                <div class="space-y-4 pt-4 border-t border-slate-100">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Academic & Tutoring Specialization</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5">
                            <span class="text-slate-400 font-medium block">Teaching Mode</span>
                            <span class="font-bold text-slate-900 capitalize text-sm mt-0.5 block">
                                {{ $edu->teaching_mode === 'both' ? 'Physical & Online' : $edu->teaching_mode }}
                            </span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5">
                            <span class="text-slate-400 font-medium block">Qualifications</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                {{ $edu->qualifications ?? 'Degree Qualified' }}
                            </span>
                        </div>
                        <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-3.5">
                            <span class="text-slate-400 font-medium block">Subjects Taught</span>
                            <span class="font-bold text-slate-900 text-sm mt-0.5 block">
                                {{ $edu->subjects ? $edu->subjects->pluck('name')->join(', ') : 'All Core Subjects' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ratings & Reviews Section -->
            <div class="space-y-4 pt-6 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Client Reviews & Rating History</h3>
                    <span class="text-xs font-bold text-slate-900">★ {{ number_format($rating, 1) }} / 5.0</span>
                </div>

                @if($reviews->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($reviews as $rev)
                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-bold text-xs text-slate-900">{{ $rev->reviewer->name ?? 'Verified Client' }}</span>
                                        <span class="text-[10px] text-amber-600 font-bold bg-amber-50 border border-amber-200 px-2 py-0.5 rounded-md">
                                            ★ {{ $rev->rating }}.0
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">{{ $rev->created_at ? $rev->created_at->diffForHumans() : '' }}</span>
                                </div>
                                <p class="text-xs text-slate-600 font-normal leading-relaxed">
                                    "{{ $rev->comment }}"
                                </p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-5 text-center text-xs text-slate-500 font-medium">
                        No written reviews submitted yet for this user.
                    </div>
                @endif
            </div>

        </div>

    </main>

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

            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">{{ $user->name }}</h3>
                </div>
                <p class="text-xs text-slate-600 font-medium">{{ $profile->display_name ?? ($profile->category->name ?? 'Service Provider') }}</p>
                <div class="text-[11px] text-slate-500 flex items-center justify-between gap-2 pt-1">
                    <span>{{ $user->location ?? 'Nigeria' }}</span>
                </div>
            </div>

            <div class="bg-sky-50 border border-sky-200 text-sky-950 rounded-xl p-3.5 text-xs space-y-1">
                <div class="flex items-center justify-between font-bold text-slate-900">
                    <span>Connection Fee:</span>
                    <span class="text-sm font-extrabold text-[#0F172B]">₦1,000</span>
                </div>
                <p class="text-[11px] text-slate-600 font-normal leading-relaxed">
                    Note: The ₦1,000 fee is only charged after your connection request is accepted by the expert.
                </p>
            </div>

            <form action="{{ route('connections.hire') }}" method="POST" class="space-y-3">
                @csrf
                <input type="hidden" name="recipient_id" value="{{ $user->id }}" />

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Your Job or Project Brief</label>
                    <textarea 
                        name="brief"
                        rows="3" 
                        placeholder="Describe your tutoring or task requirements (e.g., SS2 Physics tutoring 3 days a week in Ikeja)..."
                        class="w-full bg-slate-50 border border-slate-200 focus:border-slate-800 focus:bg-white rounded-xl p-3 text-xs text-slate-900 font-normal outline-none transition-all"
                    ></textarea>
                </div>

                <button 
                    type="submit"
                    class="w-full bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-3 px-4 rounded-xl shadow-xs transition-colors cursor-pointer"
                >
                    Submit Connection Request →
                </button>
            </form>
        </div>
    </div>

    <!-- Profile Slide-Over Drawer Modal for Top Header Trigger -->
    <x-profile-drawer :user="auth()->user()" />
</x-dashboard-layout>
