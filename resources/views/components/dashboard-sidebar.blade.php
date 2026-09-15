@props(['active' => null])

@php
    $activeItem = $active ?? match(true) {
        request()->is('dashboard/talent*') => 'talent',
        request()->is('dashboard/my-jobs*') => 'my-jobs',
        request()->is('dashboard/messages*') => 'messages',
        request()->is('profile*') => 'settings',
        default => 'overview',
    };

    $userId = Auth::id();
    $myJobsCount = $userId ? \App\Models\Opportunity::where('user_id', $userId)->whereNull('deleted_at')->count() : 0;
    $unreadMessagesCount = $userId ? \App\Models\ConnectionRequest::where('recipient_id', $userId)
        ->where('status', \App\Enums\ConnectionStatus::Pending)
        ->count() : 0;
@endphp

<aside 
    :class="{
        'translate-x-0': sidebarOpen,
        '-translate-x-full lg:translate-x-0': !sidebarOpen,
        'lg:w-20 lg:p-3': sidebarCollapsed,
        'lg:w-64 xl:w-72 lg:p-5': !sidebarCollapsed
    }"
    class="fixed lg:sticky inset-y-0 lg:top-6 left-0 z-50 lg:z-10 w-72 shrink-0 bg-white border-r lg:border border-slate-200/80 lg:rounded-2xl p-5 shadow-xl lg:shadow-xs flex flex-col justify-between h-full lg:h-[calc(100vh-3rem)] transition-all duration-300 ease-in-out overflow-y-auto no-scrollbar"
>
    <div class="space-y-6">
        
        <!-- Sidebar Header & Logo -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200/80" :class="sidebarCollapsed ? 'lg:justify-center' : ''">
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 group shrink-0" :title="sidebarCollapsed ? 'Skill Marketplace' : ''">
                <div class="w-9 h-9 rounded-xl bg-[#0F172B] flex items-center justify-center text-white shadow-xs group-hover:bg-slate-800 transition-colors shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                </div>
                <div x-show="!sidebarCollapsed" class="transition-all duration-200">
                    <span class="text-slate-900 font-bold text-base tracking-tight block">Skill Marketplace</span>
                    <span class="text-[11px] text-slate-500 font-medium tracking-wide">Member Dashboard</span>
                </div>
            </a>

            <!-- Desktop Sidebar Collapse Toggle Button -->
            <button 
                @click="toggleSidebar()" 
                class="hidden lg:flex items-center justify-center p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer"
                :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
            >
                <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" aria-label="Close menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Primary CTA Button -->
        <div>
            <button @click="if (typeof postModalOpen !== 'undefined') { postModalOpen = true; } else if (typeof createModalOpen !== 'undefined') { createModalOpen = true; } else { window.location.href='{{ url('/dashboard/my-jobs') }}'; } sidebarOpen = false;" 
               class="w-full flex items-center justify-center gap-2 bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm py-2.5 px-3 rounded-xl shadow-xs transition-all cursor-pointer"
               :title="sidebarCollapsed ? 'Post an Opportunity' : ''">
                <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">+ Post Opportunity</span>
            </button>
        </div>

        <!-- Navigation Links Grouped -->
        <div class="space-y-4">
            
            <!-- Group 1: Navigation -->
            <div>
                <span x-show="!sidebarCollapsed" class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Navigation</span>
                <nav class="space-y-0.5">
                    <!-- Home Dashboard -->
                    <a href="{{ url('/dashboard') }}" @click="sidebarOpen = false"
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'justify-between px-3 py-2'"
                       class="flex items-center rounded-xl text-xs font-normal transition-colors {{ $activeItem === 'overview' ? 'bg-[#0F172B] text-white shadow-xs' : 'text-[#000000] hover:bg-slate-100' }}"
                       :title="sidebarCollapsed ? 'Overview' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4.5 h-4.5 {{ $activeItem === 'overview' ? 'text-white' : 'text-[#000000]' }} shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                            <span x-show="!sidebarCollapsed">Overview</span>
                        </div>
                        @if($activeItem === 'overview')
                            <span x-show="!sidebarCollapsed" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>

                    <!-- Find Talent -->
                    <a href="{{ url('/dashboard/talent') }}" @click="sidebarOpen = false" 
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'justify-between px-3 py-2'"
                       class="flex items-center rounded-xl text-xs font-normal transition-colors {{ $activeItem === 'talent' ? 'bg-[#0F172B] text-white shadow-xs' : 'text-[#000000] hover:bg-slate-100' }}"
                       :title="sidebarCollapsed ? 'Find Talent' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4.5 h-4.5 {{ $activeItem === 'talent' ? 'text-white' : 'text-[#000000]' }} shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <span x-show="!sidebarCollapsed">Find Talent</span>
                        </div>
                        @if($activeItem === 'talent')
                            <span x-show="!sidebarCollapsed" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>
                </nav>
            </div>

            <!-- Group 2: Workplace -->
            <div>
                <span x-show="!sidebarCollapsed" class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Workplace</span>
                <nav class="space-y-0.5">
                    <!-- My Job Postings -->
                    <a href="{{ url('/dashboard/my-jobs') }}" @click="sidebarOpen = false" 
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'justify-between px-3 py-2'"
                       class="flex items-center rounded-xl text-xs font-normal transition-colors {{ $activeItem === 'my-jobs' ? 'bg-[#0F172B] text-white shadow-xs' : 'text-[#000000] hover:bg-slate-100' }}"
                       :title="sidebarCollapsed ? 'My Job Postings' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4.5 h-4.5 {{ $activeItem === 'my-jobs' ? 'text-white' : 'text-[#000000]' }} shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            <span x-show="!sidebarCollapsed">My Job Postings</span>
                        </div>
                        @if($myJobsCount > 0)
                            <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-400 text-slate-900">{{ $myJobsCount }}</span>
                        @endif
                    </a>

                    <!-- Messages & Requests -->
                    <a href="{{ url('/dashboard/messages') }}" @click="sidebarOpen = false" 
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'justify-between px-3 py-2'"
                       class="flex items-center rounded-xl text-xs font-normal transition-colors {{ $activeItem === 'messages' ? 'bg-[#0F172B] text-white shadow-xs' : 'text-[#000000] hover:bg-slate-100' }}"
                       :title="sidebarCollapsed ? 'Messages' : ''">
                        <div class="flex items-center gap-2.5 relative">
                            <svg class="w-4.5 h-4.5 {{ $activeItem === 'messages' ? 'text-white' : 'text-[#000000]' }} shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            <span x-show="!sidebarCollapsed">Messages</span>
                            @if($unreadMessagesCount > 0)
                                <span x-show="sidebarCollapsed" class="w-2 h-2 rounded-full bg-emerald-500 absolute -top-1 -right-1"></span>
                            @endif
                        </div>
                        @if($unreadMessagesCount > 0)
                            <span x-show="!sidebarCollapsed" class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-400 text-slate-900">{{ $unreadMessagesCount }}</span>
                        @endif
                    </a>
                </nav>
            </div>

            <!-- Group 3: Settings & Account -->
            <div>
                <span x-show="!sidebarCollapsed" class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Account</span>
                <nav class="space-y-0.5">
                    <button @click="if (typeof profileModalOpen !== 'undefined') { profileModalOpen = true; } else { window.location.href='{{ url('/profile/edit') }}'; } sidebarOpen = false;" 
                            :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'justify-between px-3 py-2'"
                            class="w-full flex items-center rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors text-left cursor-pointer"
                            :title="sidebarCollapsed ? 'View Profile Card' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4.5 h-4.5 text-[#000000] shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span x-show="!sidebarCollapsed">View Profile Card</span>
                        </div>
                    </button>
                    <a href="{{ url('/profile/edit') }}" @click="sidebarOpen = false" 
                       :class="sidebarCollapsed ? 'justify-center px-2 py-2.5' : 'justify-between px-3 py-2'"
                       class="flex items-center rounded-xl text-xs font-normal transition-colors {{ $activeItem === 'settings' ? 'bg-[#0F172B] text-white shadow-xs' : 'text-[#000000] hover:bg-slate-100' }}"
                       :title="sidebarCollapsed ? 'Edit Settings' : ''">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4.5 h-4.5 {{ $activeItem === 'settings' ? 'text-white' : 'text-[#000000]' }} shrink-0" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span x-show="!sidebarCollapsed">Edit Settings</span>
                        </div>
                        @if($activeItem === 'settings')
                            <span x-show="!sidebarCollapsed" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        @endif
                    </a>
                </nav>
            </div>

        </div>
    </div>

    <!-- Sidebar Footer Sign Out -->
    <div class="pt-4 mt-6 border-t border-slate-200/80 shrink-0">
        <form action="{{ url('/logout') }}" method="POST">
            @csrf
            <button type="submit" 
                    :class="sidebarCollapsed ? 'justify-center p-2.5' : 'justify-center px-3 py-2'"
                    class="w-full flex items-center gap-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white hover:bg-slate-100 border border-slate-200 transition-colors cursor-pointer"
                    :title="sidebarCollapsed ? 'Sign Out' : ''">
                <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                <span x-show="!sidebarCollapsed">Sign Out</span>
            </button>
        </form>
    </div>
</aside>
