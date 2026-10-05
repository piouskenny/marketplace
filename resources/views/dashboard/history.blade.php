<x-dashboard-layout 
    title="History & Payments — {{ config('app.name', 'Skill Link NG') }}"
    active="history"
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
        activeTab: '{{ $activeTab ?? 'connections' }}',
        filterStatus: 'all'
    }"
>

    <!-- Main Workspace Container -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

        <!-- Top Navbar Header -->
        <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4 sticky top-0 z-30 shrink-0">
            <div class="flex items-center gap-3">
                <!-- Desktop Sidebar Toggle -->
                <button 
                    @click="toggleSidebar()" 
                    class="hidden lg:flex items-center justify-center p-2 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 hover:text-slate-900 shrink-0 cursor-pointer transition-colors"
                    :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'"
                >
                    <svg class="w-4 h-4 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>

                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 cursor-pointer" aria-label="Open navigation menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">History & Payment Logs</h1>
                    <p class="text-xs text-slate-500 hidden sm:block">Track connection requests, view transaction records, and retry pending payments.</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Navigation shortcut to messages -->
                <a href="{{ url('/dashboard/messages') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs py-2 px-3.5 rounded-xl flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    <span>Messages Workspace</span>
                </a>

                <div class="h-5 w-px bg-slate-200 hidden sm:block"></div>

                <x-header-notifications :userNotifications="$userNotifications ?? []" :unreadCount="$unreadCount ?? 0" />

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

        <!-- Flash Status / Error Messages -->
        @if (session('status'))
            <div class="bg-emerald-600 text-white px-4 sm:px-6 py-3 shrink-0 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-600 text-white px-4 sm:px-6 py-3 shrink-0 flex items-center justify-between text-xs sm:text-sm font-semibold shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Page Body Content -->
        <main class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl w-full mx-auto">

            <!-- Metrics Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Connections -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Total Connections</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 block">{{ $totalConnectionsCount }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>

                <!-- Unlocked & Paid Connections -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Active / Unlocked</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 mt-1 block">{{ $connectedCount }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>

                <!-- Total Amount Spent -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Total Paid Fees</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1 block">₦{{ $totalAmountSpentNaira }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <!-- Pending / Failed Payments -->
                <div class="bg-white border border-slate-200/80 rounded-2xl p-5 shadow-2xs flex items-center justify-between">
                    <div>
                        <span class="text-xs font-medium text-slate-500 block uppercase tracking-wider">Pending / Failed</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-rose-600 mt-1 block">{{ $pendingOrFailedPaymentsCount }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tab Switcher Header -->
            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden">
                <div class="border-b border-slate-100 px-5 py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <!-- Tab Buttons -->
                    <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                        <button 
                            @click="activeTab = 'connections'" 
                            :class="activeTab === 'connections' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                            class="px-4 py-2 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span>Connection History ({{ $totalConnectionsCount }})</span>
                        </button>

                        <button 
                            @click="activeTab = 'payments'" 
                            :class="activeTab === 'payments' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'text-slate-600 font-medium hover:text-slate-900'"
                            class="px-4 py-2 rounded-lg text-xs transition-all cursor-pointer flex items-center gap-2"
                        >
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>Payment & Transaction History ({{ $payments->count() }})</span>
                        </button>
                    </div>

                    <div class="text-xs text-slate-500">
                        Showing all recorded activity for <strong class="text-slate-800">{{ $user->email }}</strong>
                    </div>
                </div>

                <!-- SECTION 1: CONNECTION HISTORY TAB -->
                <div x-show="activeTab === 'connections'" x-transition>
                    @if($connections->isEmpty())
                        <div class="p-12 text-center space-y-4">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <div class="max-w-md mx-auto space-y-1">
                                <h4 class="text-base font-bold text-slate-900">No Connection History Yet</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">You haven't initiated or received any connection requests yet. Explore open jobs or search talent to get started.</p>
                            </div>
                            <div class="flex items-center justify-center gap-3 pt-2">
                                <a href="{{ url('/dashboard/jobs') }}" class="bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors">
                                    Browse Jobs
                                </a>
                                <a href="{{ url('/dashboard/talent') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs transition-colors">
                                    Find Talent
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="divide-y divide-slate-100">
                            @foreach($connections as $conn)
                                @php
                                    $isIncoming = (int) $conn->recipient_id === (int) $user->id;
                                    $partner = $isIncoming ? $conn->initiator : $conn->recipient;
                                    $statusVal = $conn->status instanceof \App\Enums\ConnectionStatus ? $conn->status->value : (string) $conn->status;
                                    $isPaid = $statusVal === 'connected' || $conn->conversation;
                                    $isAccepted = $statusVal === 'accepted';
                                    $isPending = $statusVal === 'pending';
                                    $isPaymentPending = $statusVal === 'payment_pending';
                                    $isDeclined = $statusVal === 'declined';
                                @endphp

                                <div class="p-5 hover:bg-slate-50/50 transition-colors flex flex-col md:flex-row md:items-center justify-between gap-5">
                                    <div class="flex items-start gap-4 flex-1 min-w-0">
                                        <!-- Avatar -->
                                        @if($partner && $partner->avatar_url)
                                            <img src="{{ $partner->avatar_url }}" alt="{{ $partner->name }}" class="w-11 h-11 rounded-2xl object-cover border border-slate-200 shrink-0" />
                                        @else
                                            <div class="w-11 h-11 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-sm shrink-0">
                                                {{ strtoupper(substr($partner->name ?? 'U', 0, 1)) }}
                                            </div>
                                        @endif

                                        <div class="space-y-1 flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $isIncoming ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-blue-100 text-blue-800 border border-blue-200' }}">
                                                    {{ $isIncoming ? 'Incoming Request' : 'Sent Application' }}
                                                </span>

                                                <!-- Status Badge -->
                                                @if($isPaid)
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                                        Connected / Paid (₦1,000)
                                                    </span>
                                                @elseif($isAccepted || $isPaymentPending)
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        Accepted — Awaiting Payment
                                                    </span>
                                                @elseif($isDeclined)
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                        Declined
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                                        Pending Approval
                                                    </span>
                                                @endif

                                                <span class="text-[11px] text-slate-400 font-medium">
                                                    {{ $conn->created_at->diffForHumans() }}
                                                </span>
                                            </div>

                                            <h4 class="text-sm font-bold text-slate-900 tracking-tight">
                                                {{ $partner->name ?? 'User' }} 
                                                <span class="text-slate-400 font-normal">({{ $conn->opportunity ? $conn->opportunity->title : 'Direct Connect & Hire' }})</span>
                                            </h4>

                                            <p class="text-xs text-slate-600 line-clamp-1">
                                                {{ $conn->initial_message ?? 'No note attached.' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Right Action Buttons -->
                                    <div class="flex items-center gap-3 shrink-0 pt-3 md:pt-0 border-t md:border-t-0">
                                        @if($isPaid)
                                            <a href="{{ url('/dashboard/messages?conn_id=' . $conn->id) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-xs flex items-center gap-1.5 transition-colors">
                                                <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                                <span>Open Direct Chat</span>
                                            </a>
                                        @elseif($isAccepted || $isPaymentPending)
                                            @if(!$isIncoming)
                                                <form action="{{ url('/connections/' . $conn->id . '/pay') }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-xl shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer">
                                                        <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        <span>Pay ₦1,000 to Unlock</span>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-slate-500 font-semibold italic">Waiting for applicant payment</span>
                                            @endif
                                        @elseif($isPending && $isIncoming)
                                            <form action="{{ url('/connections/' . $conn->id . '/accept') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-3.5 rounded-xl shadow-xs transition-colors cursor-pointer">
                                                    Accept
                                                </button>
                                            </form>
                                            <form action="{{ url('/connections/' . $conn->id . '/reject') }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 text-xs font-semibold py-2 px-3.5 rounded-xl transition-colors cursor-pointer border border-slate-200">
                                                    Decline
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ url('/dashboard/messages?conn_id=' . $conn->id) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold py-2 px-3.5 rounded-xl transition-colors">
                                                View Request
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- SECTION 2: PAYMENT & TRANSACTION HISTORY TAB -->
                <div x-show="activeTab === 'payments'" x-transition>
                    @if($payments->isEmpty())
                        <div class="p-12 text-center space-y-4">
                            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 border border-amber-100 mx-auto flex items-center justify-center">
                                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="max-w-md mx-auto space-y-1">
                                <h4 class="text-base font-bold text-slate-900">No Payment History Yet</h4>
                                <p class="text-xs text-slate-500 leading-relaxed">You have not completed or attempted any Paystack connection fee payments yet. Once a connection is accepted, you can pay the ₦1,000 fee to unlock direct contact details.</p>
                            </div>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-600 border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200/80 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                        <th class="py-3.5 px-5">Transaction Reference</th>
                                        <th class="py-3.5 px-5">Connection / Purpose</th>
                                        <th class="py-3.5 px-5">Amount</th>
                                        <th class="py-3.5 px-5">Provider</th>
                                        <th class="py-3.5 px-5">Status</th>
                                        <th class="py-3.5 px-5">Date</th>
                                        <th class="py-3.5 px-5 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($payments as $pay)
                                        @php
                                            $payStatus = $pay->status instanceof \App\Enums\PaymentStatus ? $pay->status->value : (string) $pay->status;
                                            $isSuccessful = $payStatus === 'successful';
                                            $isFailed = $payStatus === 'failed';
                                            $isPending = $payStatus === 'pending';
                                            $connReq = $pay->connectionRequest;
                                        @endphp
                                        <tr class="hover:bg-slate-50/50 transition-colors">
                                            <!-- Reference -->
                                            <td class="py-4 px-5 font-mono font-bold text-slate-900">
                                                {{ $pay->reference }}
                                            </td>

                                            <!-- Connection / Purpose -->
                                            <td class="py-4 px-5">
                                                @if($connReq)
                                                    <div class="space-y-0.5">
                                                        <span class="font-bold text-slate-900 block">Connection Fee</span>
                                                        <span class="text-[11px] text-slate-500 block">
                                                            To: {{ $connReq->recipient ? $connReq->recipient->name : 'Recipient' }} 
                                                            ({{ $connReq->opportunity ? $connReq->opportunity->title : 'Direct Hire' }})
                                                        </span>
                                                    </div>
                                                @else
                                                    <span class="font-semibold text-slate-700">Connection Unlock Fee</span>
                                                @endif
                                            </td>

                                            <!-- Amount -->
                                            <td class="py-4 px-5 font-extrabold text-slate-900 text-sm">
                                                ₦{{ number_format($pay->amount / 100, 2) }}
                                            </td>

                                            <!-- Gateway Provider -->
                                            <td class="py-4 px-5 font-semibold text-slate-700">
                                                <span class="inline-flex items-center gap-1">
                                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                                    {{ strtoupper($pay->provider ?? 'Paystack') }}
                                                </span>
                                            </td>

                                            <!-- Status -->
                                            <td class="py-4 px-5">
                                                @if($isSuccessful)
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        Successful
                                                    </span>
                                                @elseif($isFailed)
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                        Failed
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Date -->
                                            <td class="py-4 px-5 text-slate-500">
                                                {{ $pay->created_at ? $pay->created_at->format('M d, Y • h:i A') : 'N/A' }}
                                            </td>

                                            <!-- Action Button (Retry Payment or View Chat) -->
                                            <td class="py-4 px-5 text-right">
                                                @if($isSuccessful && $connReq)
                                                    <a href="{{ url('/dashboard/messages?conn_id=' . $connReq->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 hover:text-emerald-900 bg-emerald-50 hover:bg-emerald-100 px-3 py-1.5 rounded-xl border border-emerald-200 transition-colors">
                                                        <span>View Chat</span>
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                                    </a>
                                                @elseif(($isFailed || $isPending) && $connReq)
                                                    <form action="{{ url('/connections/' . $connReq->id . '/pay') }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 px-3.5 py-1.5 rounded-xl shadow-xs transition-colors cursor-pointer">
                                                            <svg class="w-3.5 h-3.5 text-emerald-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                            <span>Retry Payment</span>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <!-- Profile Slide-Over Drawer Modal -->
    <x-profile-drawer :user="$user" />
</x-dashboard-layout>
