<x-dashboard-layout 
    title="Messages & Direct Chat — {{ config('app.name', 'Skill Marketplace') }}"
    active="messages"
    xData="messagesApp({{ json_encode($conversations ?? []) }})"
    xInit="setTimeout(() => pageLoading = false, 350); $nextTick(() => scrollToBottom()); startRealtimePolling()"
>

@push('scripts')
        <script>
            function messagesApp(conversationsData) {
                return {
                    pageLoading: true,
                    sidebarOpen: false,
                    sidebarCollapsed: localStorage.getItem('sidebar_collapsed') === 'true',
                    toggleSidebar: function() {
                        this.sidebarCollapsed = !this.sidebarCollapsed;
                        localStorage.setItem('sidebar_collapsed', this.sidebarCollapsed);
                    },
                    notificationsOpen: false,
                    profileModalOpen: false,
                    applicantModalOpen: false,
                    paymentModalOpen: false,
                    payingConnection: false,
                    selectedApplicant: null,
                    activeConversationId: {!! request('conn_id') ? json_encode('conn_' . request('conn_id')) : (isset($conversations[0]['id']) ? json_encode($conversations[0]['id']) : 101) !!},
                    conversations: conversationsData,
                    searchQuery: '',
                    newMessageText: '',

                    openApplicantProfile: function(conv) {
                        if (conv && conv.applicant_profile) {
                            this.selectedApplicant = conv.applicant_profile;
                            this.applicantModalOpen = true;
                        } else if (conv) {
                            this.selectedApplicant = {
                                name: conv.name,
                                avatar: conv.avatar,
                                title: conv.title,
                                category: conv.category,
                                location: conv.location,
                                bio: 'Applicant for ' + conv.title + '.',
                                skills: [conv.category, 'Verified Applicant'],
                                rating: '5.0 ★ (New Applicant)',
                                verified: true
                            };
                            this.applicantModalOpen = true;
                        }
                    },

                    acceptConnection: function(conv) {
                        var self = this;
                        conv.status = 'accepted';
                        fetch('{{ url('/connections') }}/' + (conv.connection_id || conv.id) + '/accept', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(function(res) { return res.json(); })
                        .then(function() {
                            conv.status = 'accepted';
                            self.$nextTick(function() { self.scrollToBottom(); });
                        })
                        .catch(function() {
                            conv.status = 'accepted';
                        });
                    },

                    rejectConnection: function(conv) {
                        var self = this;
                        conv.status = 'declined';
                        fetch('{{ url('/connections') }}/' + (conv.connection_id || conv.id) + '/reject', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(function(res) { return res.json(); })
                        .then(function() {
                            conv.status = 'declined';
                        })
                        .catch(function() {
                            conv.status = 'declined';
                        });
                    },

                    payConnectionFee: function(conv) {
                        var self = this;
                        self.payingConnection = true;
                        var connId = conv.connection_id || conv.id;
                        if (typeof connId === 'string' && connId.indexOf('conn_') === 0) {
                            connId = connId.replace('conn_', '');
                        }
                        fetch('{{ url('/connections') }}/' + connId + '/pay', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            self.payingConnection = false;
                            conv.status = 'connected';
                            self.paymentModalOpen = false;
                            self.$nextTick(function() { self.scrollToBottom(); });
                        })
                        .catch(function() {
                            self.payingConnection = false;
                            conv.status = 'connected';
                            self.paymentModalOpen = false;
                        });
                    },

                    get activeConversation() {
                        if (!this.conversations || this.conversations.length === 0) return null;
                        var self = this;
                        var found = this.conversations.find(function(c) { return String(c.id) === String(self.activeConversationId); });
                        return found || this.conversations[0] || null;
                    },

                    get filteredConversations() {
                        if (!this.searchQuery.trim()) return this.conversations;
                        var q = this.searchQuery.toLowerCase();
                        return this.conversations.filter(function(c) {
                            return c.name.toLowerCase().indexOf(q) !== -1 || 
                                   c.title.toLowerCase().indexOf(q) !== -1 ||
                                   c.category.toLowerCase().indexOf(q) !== -1;
                        });
                    },

                    selectConversation: function(id) {
                        this.activeConversationId = id;
                        var conv = this.conversations.find(function(c) { return String(c.id) === String(id); });
                        if (conv) {
                            conv.unread = 0;
                            if (conv.db_conversation_id) {
                                fetch('{{ url('/conversations') }}/' + conv.db_conversation_id + '/read', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                }).catch(function() {});
                            }
                        }
                        var self = this;
                        this.$nextTick(function() {
                            self.scrollToBottom();
                        });
                    },

                    sendMessage: function() {
                        var text = this.newMessageText.trim();
                        if (!text) return;
                        var conv = this.activeConversation;
                        if (!conv) return;

                        var now = new Date();
                        var hours = now.getHours();
                        var minutes = now.getMinutes().toString().padStart(2, '0');
                        var ampm = hours >= 12 ? 'PM' : 'AM';
                        var formattedHours = (hours % 12 || 12).toString();
                        var timeString = formattedHours + ':' + minutes + ' ' + ampm;

                        var tempMsg = {
                            id: Date.now(),
                            sender: 'me',
                            text: text,
                            time: timeString
                        };
                        conv.messages.push(tempMsg);
                        conv.last_time = 'Just now';
                        this.newMessageText = '';

                        var self = this;
                        this.$nextTick(function() {
                            self.scrollToBottom();
                        });

                        if (conv.db_conversation_id) {
                            fetch('{{ url('/conversations') }}/' + conv.db_conversation_id + '/messages', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ body: text })
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                if (data.success && data.message) {
                                    tempMsg.id = data.message.id;
                                    tempMsg.time = data.message.time_formatted;
                                }
                            })
                            .catch(function(err) {});
                        }
                    },

                    scrollToBottom: function() {
                        var container = this.$refs.messageFeed;
                        if (container) {
                            container.scrollTop = container.scrollHeight;
                        }
                    },

                    startRealtimePolling: function() {
                        var self = this;
                        setInterval(function() {
                            fetch('{{ url('/connections/status') }}')
                                .then(function(res) { return res.json(); })
                                .then(function(data) {
                                    if (data && data.connections) {
                                        data.connections.forEach(function(item) {
                                            var conv = self.conversations.find(function(c) {
                                                return String(c.connection_id || c.id) === String(item.id) || String(c.id) === 'conn_' + item.id;
                                            });
                                            if (conv && conv.status !== item.status) {
                                                conv.status = item.status;
                                                if (item.status === 'accepted' || item.status === 'connected') {
                                                    self.$nextTick(function() { self.scrollToBottom(); });
                                                }
                                            }
                                        });
                                    }
                                })
                                .catch(function(err) {});
                        }, 2500);
                    }
                };
            }
        </script>
@endpush
    <!-- Main Dashboard Workspace -->
    <main class="flex-1 min-w-0 flex flex-col space-y-4 h-[calc(100vh-2rem)] sm:h-[calc(100vh-3rem)] overflow-hidden">
        
        <!-- Top Header Bar -->
        <header class="bg-white border border-slate-200/80 rounded-2xl px-4 sm:px-6 py-3 flex items-center justify-between gap-4 shrink-0 shadow-xs">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 cursor-pointer" aria-label="Open navigation menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Direct Messages</h1>
            </div>

            <div class="flex items-center gap-3">
                <!-- Notifications Popover Bell Icon -->
                <div class="relative" x-data="{ notificationsOpen: false }">
                    <button @click="notificationsOpen = !notificationsOpen" class="w-9 h-9 rounded-xl border border-slate-200/80 bg-slate-50 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors relative cursor-pointer" title="View notifications">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="w-2 h-2 rounded-full bg-sky-500 absolute top-2 right-2 ring-2 ring-white"></span>
                    </button>

                    <!-- Notifications Popup Dropdown Panel -->
                    <div 
                        x-show="notificationsOpen" 
                        @click.outside="notificationsOpen = false"
                        x-transition
                        class="absolute right-0 mt-2 w-80 sm:w-96 bg-white border border-slate-200/90 rounded-2xl shadow-xl z-50 overflow-hidden space-y-0"
                        style="display: none;"
                    >
                        <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900">Notifications</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172B] text-white">2 New</span>
                            </div>
                            <button @click="notificationsOpen = false" class="text-xs text-slate-400 hover:text-slate-600 font-medium cursor-pointer">Close</button>
                        </div>

                        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
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
        </header>

        @if (session('status'))
            <div class="bg-emerald-600 text-white px-4 sm:px-6 py-2.5 shrink-0 flex items-center justify-between text-xs sm:text-sm font-semibold rounded-2xl shadow-xs">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-200 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif
                    
                    <!-- Wireframe 2-Column Chat & Messaging Workspace Card -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden flex flex-col md:flex-row flex-1 min-h-0">

                        <!-- Left Column: Messages Sidebar (Threads List) -->
                        <div class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200/80 flex flex-col bg-slate-50/50 shrink-0">
                            
                            <!-- Search & Title Header -->
                            <div class="p-4 border-b border-slate-200/80 space-y-3 bg-white">
                                <div class="flex items-center justify-between">
                                    <h2 class="text-xl font-bold text-slate-900 tracking-tight">Messages</h2>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#0F172B] text-white" x-text="conversations.length + ' Chats'"></span>
                                </div>
                                <div class="relative">
                                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                    <input 
                                        type="text" 
                                        x-model="searchQuery" 
                                        placeholder="Search conversations..." 
                                        class="w-full pl-9 pr-3 py-2 rounded-xl text-xs bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-slate-900 transition-all"
                                    />
                                </div>
                            </div>

                            <!-- Conversation List -->
                            <div class="divide-y divide-slate-100 overflow-y-auto flex-1">
                                <template x-for="c in filteredConversations" :key="c.id">
                                    <button 
                                        @click="selectConversation(c.id)"
                                        class="w-full p-3.5 flex items-start gap-3 transition-colors text-left cursor-pointer relative"
                                        :class="activeConversationId === c.id ? 'bg-white shadow-2xs border-l-4 border-[#0F172B]' : 'hover:bg-white/80'"
                                    >
                                        <div class="relative shrink-0">
                                            <img :src="c.avatar" :alt="c.name" class="w-10 h-10 rounded-full object-cover border border-slate-200" />
                                            <span 
                                                x-show="c.online" 
                                                class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white absolute bottom-0 right-0"
                                            ></span>
                                        </div>

                                        <div class="flex-1 min-w-0 space-y-0.5">
                                            <div class="flex items-center justify-between gap-1">
                                                <h4 class="text-xs font-bold text-slate-900 truncate" x-text="c.name"></h4>
                                                <span class="text-[10px] text-slate-400 font-medium shrink-0" x-text="c.last_time"></span>
                                            </div>
                                            <div class="flex items-center gap-1.5 pt-0.5">
                                                <template x-if="c.is_incoming && c.status === 'pending'">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 shrink-0">
                                                        Incoming Request
                                                    </span>
                                                </template>
                                                <template x-if="!c.is_incoming && c.status === 'pending'">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                                                        Pending Acceptance
                                                    </span>
                                                </template>
                                                <template x-if="!c.is_incoming && c.status === 'accepted'">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                                                        Payment Pending
                                                    </span>
                                                </template>
                                                <template x-if="c.status === 'connected'">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                                                        Connected
                                                    </span>
                                                </template>
                                                <template x-if="c.status === 'declined'">
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-200 text-slate-700 border border-slate-300 shrink-0">
                                                        Declined
                                                    </span>
                                                </template>
                                                <p class="text-[11px] text-slate-500 font-normal truncate flex-1" x-text="c.title"></p>
                                            </div>
                                            <p 
                                                class="text-xs truncate pt-0.5" 
                                                :class="c.unread > 0 ? 'font-semibold text-slate-900' : 'text-slate-500 font-normal'"
                                                x-text="c.messages.length > 0 ? c.messages[c.messages.length - 1].text : 'No messages'"
                                            ></p>
                                        </div>
                                        <span 
                                            x-show="c.unread > 0" 
                                            class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white shrink-0 self-center"
                                            x-text="c.unread"
                                        ></span>
                                    </button>
                                </template>

                                <template x-if="filteredConversations.length === 0">
                                    <div class="p-8 text-center space-y-2">
                                        <div class="w-10 h-10 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-lg">
                                            💬
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">No active chats</p>
                                        <p class="text-[11px] text-slate-400 font-normal">Accepted connection requests will appear here.</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Right Column: Active Chat Feed Workspace -->
                        <div class="flex-1 flex flex-col min-w-0 bg-white">
                            
                            <!-- Empty Conversations State -->
                            <template x-if="!activeConversation">
                                <div class="flex-1 flex flex-col items-center justify-center p-8 text-center space-y-4 bg-slate-50/40">
                                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-3xl shadow-2xs">
                                        💬
                                    </div>
                                    <div class="max-w-sm space-y-1.5">
                                        <h3 class="text-base font-bold text-slate-900">No Active Conversation Selected</h3>
                                        <p class="text-xs text-slate-500 leading-relaxed">
                                            You do not have any active chat selected. Submit an application or accept a connection request to start messaging.
                                        </p>
                                    </div>
                                    <a 
                                        href="{{ url('/dashboard/talent') }}" 
                                        class="bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-xs py-2.5 px-4 rounded-xl shadow-xs inline-flex items-center gap-2 transition-colors cursor-pointer"
                                    >
                                        <span>Find Talent & Tutors →</span>
                                    </a>
                                </div>
                            </template>

                            <!-- Active Conversation Workspace -->
                            <template x-if="activeConversation">
                                <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden">
                                    <!-- Chat Active User Header -->
                                    <div class="px-5 py-3.5 border-b border-slate-200/80 flex items-center justify-between gap-4 bg-white shrink-0">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="relative shrink-0">
                                        <img :src="activeConversation.avatar" :alt="activeConversation.name" class="w-10 h-10 rounded-full object-cover border border-slate-200" />
                                        <span x-show="activeConversation.online" class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white absolute bottom-0 right-0"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <h3 class="text-sm font-bold text-slate-900 truncate" x-text="activeConversation.name"></h3>
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200" x-text="activeConversation.category"></span>
                                            <template x-if="activeConversation.is_incoming && activeConversation.status === 'pending'">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                    [Incoming Request]
                                                </span>
                                            </template>
                                            <template x-if="!activeConversation.is_incoming && activeConversation.status === 'pending'">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    [Pending Acceptance]
                                                </span>
                                            </template>
                                            <template x-if="!activeConversation.is_incoming && activeConversation.status === 'accepted'">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    [Payment Pending]
                                                </span>
                                            </template>
                                            <template x-if="activeConversation.status === 'connected'">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    [Connected]
                                                </span>
                                            </template>
                                            <template x-if="activeConversation.status === 'declined'">
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700 border border-slate-300">
                                                    [Declined]
                                                </span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                            <span x-text="activeConversation.title" class="truncate"></span>
                                            <span>•</span>
                                            <span x-text="activeConversation.location"></span>
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    @click="openApplicantProfile(activeConversation)" 
                                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold transition-colors cursor-pointer shrink-0"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>View Profile</span>
                                </button>
                            </div>

                            <!-- Messages History Feed Scroll Container -->
                            <div x-ref="messageFeed" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 bg-slate-50/30">
                                
                                <div class="text-center py-2">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-semibold bg-slate-200/70 text-slate-600">
                                        End-to-end encrypted direct connection
                                    </span>
                                </div>

                                <template x-if="activeConversation.is_incoming && activeConversation.status === 'pending'">
                                    <div class="bg-indigo-50 border border-indigo-200/90 rounded-2xl p-4 text-indigo-950 space-y-3 mb-3 shadow-xs">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-[#0F172B] text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                    📩
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-bold text-slate-900">Incoming Application & Connection Offer</h4>
                                                    <p class="text-[11px] text-slate-600 font-normal">
                                                        <strong x-text="activeConversation.name"></strong> applied to your opportunity posting. Review profile and accept or decline.
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 shrink-0">
                                                Pending Approval
                                            </span>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 pt-2 border-t border-indigo-100">
                                            <button 
                                                @click="openApplicantProfile(activeConversation)"
                                                class="px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 border border-slate-200 text-slate-800 text-xs font-semibold shadow-2xs flex items-center gap-1.5 transition-colors cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                <span>View Requester Profile</span>
                                            </button>
                                            <button 
                                                @click="acceptConnection(activeConversation)"
                                                class="px-4 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-xs flex items-center gap-1.5 transition-colors cursor-pointer"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                <span>Accept Offer & Unlock Connection</span>
                                            </button>
                                            <button 
                                                @click="rejectConnection(activeConversation)"
                                                class="px-3.5 py-1.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-semibold flex items-center gap-1.5 transition-colors cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>Decline Offer</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="!activeConversation.is_incoming && activeConversation.status === 'pending'">
                                    <div class="bg-amber-50 border border-amber-200/80 rounded-2xl p-4 text-amber-900 space-y-1 mb-3">
                                        <div class="flex items-center gap-2 font-bold text-xs">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Application & Connection Request Sent (Pending Approval)</span>
                                        </div>
                                        <p class="text-xs text-amber-700 font-normal leading-relaxed">
                                            Your application note has been delivered to the job owner. Direct chat messaging will unlock as soon as the job owner approves your connection request and connection fee is completed.
                                        </p>
                                    </div>
                                </template>

                                <template x-if="!activeConversation.is_incoming && activeConversation.status === 'accepted'">
                                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-emerald-950 space-y-3 mb-3 shadow-xs">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                    🎉
                                                </div>
                                                <div>
                                                    <h4 class="text-xs font-bold text-slate-900">Application Approved! Connection Fee Required</h4>
                                                    <p class="text-[11px] text-slate-600 font-normal">
                                                        <strong x-text="activeConversation.name"></strong> accepted your application connection request! Pay the ₦1,000 platform connection fee to unlock direct chat messaging and phone details.
                                                    </p>
                                                </div>
                                            </div>
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                                                Payment Pending
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 pt-2 border-t border-emerald-100">
                                            <button 
                                                @click="paymentModalOpen = true"
                                                class="px-4 py-2 rounded-xl bg-[#0F172B] hover:bg-slate-800 text-white text-xs font-bold shadow-xs flex items-center gap-2 transition-colors cursor-pointer"
                                            >
                                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 8v2m0-6c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                <span>Pay ₦1,000 Connection Fee & Unlock Chat</span>
                                            </button>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="activeConversation.status === 'connected'">
                                    <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-3.5 text-emerald-950 flex items-center gap-3 mb-3 shadow-2xs">
                                        <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            ✓
                                        </div>
                                        <div class="text-xs flex-1">
                                            <span class="font-bold text-slate-900">Connection Active & Verified!</span>
                                            <span class="text-slate-600 block text-[11px]">Direct chat messaging and contact details are fully unlocked.</span>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="activeConversation.status === 'declined'">
                                    <div class="bg-slate-100 border border-slate-200 rounded-2xl p-4 text-slate-700 text-center space-y-1 mb-3">
                                        <div class="text-xs font-bold text-slate-800">Connection Offer Declined</div>
                                        <p class="text-[11px] text-slate-500 font-normal">This connection request was declined.</p>
                                    </div>
                                </template>

                                <template x-for="m in activeConversation.messages" :key="m.id">
                                    <div 
                                        class="flex flex-col space-y-1"
                                        :class="m.sender === 'me' ? 'items-end' : 'items-start'"
                                    >
                                        <div 
                                            class="p-3.5 text-xs sm:text-sm leading-relaxed max-w-md shadow-2xs"
                                            :class="m.sender === 'me' ? 'bg-[#0F172B] text-white rounded-2xl rounded-tr-xs' : 'bg-white border border-slate-200 text-slate-900 rounded-2xl rounded-tl-xs'"
                                        >
                                            <p x-text="m.text"></p>
                                        </div>
                                        <div class="flex items-center gap-1 px-1 text-[10px] text-slate-400">
                                            <span x-text="m.time"></span>
                                            <span x-show="m.sender === 'me'" class="text-sky-400 font-bold">✓✓</span>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Bottom Input Workspace -->
                            <div class="p-3 sm:p-4 bg-white border-t border-slate-200/80 space-y-3 shrink-0">
                                
                                <template x-if="activeConversation.status === 'pending'">
                                    <div class="bg-slate-100/90 border border-slate-200 rounded-2xl p-4 text-center space-y-1">
                                        <div class="inline-flex items-center gap-2 text-slate-800 font-bold text-xs">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0H10m12-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Messaging Disabled Until Connection Request is Accepted</span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 font-normal max-w-md mx-auto">
                                            This connection request is currently pending approval by <strong x-text="activeConversation.name"></strong>. Direct messaging will unlock once approved and connection fee is paid.
                                        </p>
                                    </div>
                                </template>

                                <template x-if="!activeConversation.is_incoming && activeConversation.status === 'accepted'">
                                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 text-center space-y-2">
                                        <div class="inline-flex items-center gap-2 text-amber-900 font-bold text-xs">
                                            <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m0 0v2m0-2h2m-2 0H10m12-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Messaging Locked — ₦1,000 Connection Fee Required</span>
                                        </div>
                                        <p class="text-[11px] text-amber-700 font-normal max-w-md mx-auto">
                                            <strong x-text="activeConversation.name"></strong> has approved your connection request! Complete the ₦1,000 connection payment to send messages.
                                        </p>
                                        <button 
                                            @click="paymentModalOpen = true"
                                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#0F172B] text-white font-bold text-xs shadow-xs hover:bg-slate-800 transition-colors cursor-pointer"
                                        >
                                            <span>Pay ₦1,000 Fee Now</span>
                                        </button>
                                    </div>
                                </template>

                                <template x-if="activeConversation.status === 'connected' || activeConversation.status === undefined || (activeConversation.is_incoming && activeConversation.status === 'accepted')">
                                    <!-- Multi-line Text Area Input -->
                                    <div class="relative bg-slate-50 border border-slate-200 rounded-2xl p-3 focus-within:bg-white focus-within:border-slate-400 focus-within:ring-2 focus-within:ring-slate-900 transition-all">
                                        <textarea 
                                            x-model="newMessageText"
                                            @keydown.enter.prevent="sendMessage()"
                                            placeholder="Send a message..." 
                                            rows="2"
                                            class="w-full bg-transparent text-xs sm:text-sm text-slate-900 placeholder-slate-400 focus:outline-none resize-none"
                                        ></textarea>

                                        <!-- Wireframe Bottom Toolbar Bar inside Input Card -->
                                        <div class="flex items-center justify-between pt-2 border-t border-slate-200/60 mt-1">
                                            
                                            <!-- Left Formatting Tools: Aa, Paperclip Attachment, Settings -->
                                            <div class="flex items-center gap-1 text-slate-400">
                                                <button type="button" class="p-1.5 rounded-lg hover:text-slate-700 hover:bg-slate-200/60 transition-colors cursor-pointer" title="Format Text (Aa)">
                                                    <span class="text-xs font-bold font-serif">Aa</span>
                                                </button>
                                                <button type="button" class="p-1.5 rounded-lg hover:text-slate-700 hover:bg-slate-200/60 transition-colors cursor-pointer" title="Attach Document / Photo">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                </button>
                                                <button type="button" class="p-1.5 rounded-lg hover:text-slate-700 hover:bg-slate-200/60 transition-colors cursor-pointer" title="Quick Options">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                                                </button>
                                            </div>

                                            <!-- Right Send Button Action -->
                                            <button 
                                                @click="sendMessage()"
                                                class="bg-[#0F172B] hover:bg-slate-800 text-white text-xs font-semibold px-4 py-2 rounded-xl flex items-center gap-2 transition-colors cursor-pointer shadow-xs"
                                            >
                                                <span>Send</span>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                    </div>
                </main>

        <!-- Slide-Over User Profile Card Drawer Modal -->
        <div 
            x-show="profileModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-hidden" 
            style="display: none;"
        >
            <div @click="profileModalOpen = false" class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-md bg-white border-l border-slate-200 shadow-2xl p-6 flex flex-col justify-between overflow-y-auto relative z-10">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <h3 class="text-base font-bold text-slate-900">Profile Overview</h3>
                            <button @click="profileModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- User Info Header -->
                        <div class="flex items-center gap-4">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-xs" />
                            @else
                                <div class="w-16 h-16 rounded-2xl bg-[#0F172B] text-white font-bold flex items-center justify-center text-xl shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="space-y-1">
                                <h4 class="text-lg font-bold text-slate-900 leading-tight">{{ $user->name }}</h4>
                                <p class="text-xs text-slate-500 font-medium">{{ $user->email }}</p>
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                                    ✓ Verified Account
                                </span>
                            </div>
                        </div>

                        <!-- User Details List -->
                        <div class="space-y-3 bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-xs">
                            <div class="flex justify-between py-1 border-b border-slate-200/60">
                                <span class="text-slate-500 font-normal">Phone Contact</span>
                                <span class="text-slate-900 font-semibold">{{ $user->phone ?? 'Not provided' }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-slate-200/60">
                                <span class="text-slate-500 font-normal">Primary Location</span>
                                <span class="text-slate-900 font-semibold">{{ $user->location ?? 'Nigeria' }}</span>
                            </div>
                        </div>

                        <a href="{{ url('/profile/edit') }}" class="w-full bg-[#0F172B] hover:bg-slate-800 text-white font-semibold text-xs py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span>Edit Full Settings</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Requester / Applicant Profile Drawer Modal -->
        <div 
            x-show="applicantModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 overflow-hidden" 
            style="display: none;"
        >
            <div @click="applicantModalOpen = false" class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div class="w-screen max-w-md bg-white border-l border-slate-200 shadow-2xl p-6 flex flex-col justify-between overflow-y-auto relative z-10">
                    <template x-if="selectedApplicant">
                        <div class="space-y-6">
                            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                                <h3 class="text-base font-bold text-slate-900">Requester Profile Overview</h3>
                                <button @click="applicantModalOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>

                            <!-- Applicant Identity Header -->
                            <div class="flex items-start gap-4">
                                <img :src="selectedApplicant.avatar" :alt="selectedApplicant.name" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shrink-0 shadow-xs" />
                                <div class="space-y-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-lg font-bold text-slate-900 leading-tight" x-text="selectedApplicant.name"></h4>
                                        <span x-show="selectedApplicant.verified" class="text-sky-500 font-bold text-sm" title="Verified Talent">✓</span>
                                    </div>
                                    <p class="text-xs text-slate-600 font-semibold" x-text="selectedApplicant.title"></p>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                        <span x-text="selectedApplicant.category" class="px-2 py-0.5 rounded-md bg-slate-100 font-semibold"></span>
                                        <span>•</span>
                                        <span x-text="selectedApplicant.location"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Rating & Trust Badge -->
                            <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Rating & Reviews</span>
                                    <span class="text-sm font-bold text-slate-900" x-text="selectedApplicant.rating"></span>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Identity Verified
                                </span>
                            </div>

                            <!-- Bio & Overview -->
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Professional Bio</h4>
                                <p class="text-xs sm:text-sm text-slate-700 font-normal leading-relaxed bg-slate-50 border border-slate-200/80 rounded-xl p-3.5" x-text="selectedApplicant.bio"></p>
                            </div>

                            <!-- Verified Skills -->
                            <template x-if="selectedApplicant.skills && selectedApplicant.skills.length">
                                <div class="space-y-2">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Verified Skills & Expertise</h4>
                                    <div class="flex flex-wrap gap-1.5">
                                        <template x-for="skill in selectedApplicant.skills" :key="skill">
                                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-100 text-slate-800 border border-slate-200" x-text="skill"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- Education -->
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Education & Certification</h4>
                                <p class="text-xs text-slate-700 font-medium bg-slate-50 border border-slate-200/80 rounded-xl p-3" x-text="selectedApplicant.education"></p>
                            </div>

                            <!-- Action Accept / Reject directly from drawer -->
                            <template x-if="activeConversation.is_incoming && activeConversation.status === 'pending'">
                                <div class="pt-4 border-t border-slate-100 flex items-center gap-3">
                                    <button 
                                        @click="acceptConnection(activeConversation); applicantModalOpen = false;"
                                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-3 px-4 rounded-xl flex items-center justify-center gap-2 transition-colors cursor-pointer shadow-xs"
                                    >
                                        <span>Accept Offer & Connect</span>
                                    </button>
                                    <button 
                                        @click="rejectConnection(activeConversation); applicantModalOpen = false;"
                                        class="px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs py-3 rounded-xl transition-colors cursor-pointer"
                                    >
                                        <span>Decline</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Payment Checkout Modal -->
        <div 
            x-show="paymentModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4" 
            style="display: none;"
        >
            <div @click="paymentModalOpen = false" class="fixed inset-0 bg-slate-950/50 backdrop-blur-xs"></div>

            <div class="relative bg-white border border-slate-200 rounded-3xl shadow-2xl w-full max-w-md p-6 space-y-6 z-10">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#0F172B] text-white flex items-center justify-center font-bold text-sm">
                            💳
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Unlock Direct Connection</h3>
                            <p class="text-[11px] text-slate-500 font-medium">Platform Connection & Verification Fee</p>
                        </div>
                    </div>
                    <button @click="paymentModalOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Summary Breakdown Card -->
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-normal">Opportunity Posting</span>
                        <span class="text-slate-900 font-bold truncate max-w-[200px]" x-text="activeConversation.title"></span>
                    </div>
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-slate-500 font-normal">Job Owner / Client</span>
                        <span class="text-slate-900 font-bold" x-text="activeConversation.name"></span>
                    </div>
                    <div class="border-t border-slate-200/70 pt-2 flex justify-between items-center">
                        <span class="text-xs font-bold text-slate-700">Platform Connection Fee</span>
                        <span class="text-lg font-extrabold text-emerald-600">₦1,000 NGN</span>
                    </div>
                </div>

                <!-- Payment Method Tabs Demo -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-700 block">Select Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-3 border-2 border-[#0F172B] bg-slate-50 rounded-xl text-center cursor-pointer">
                            <span class="text-lg block">💳</span>
                            <span class="text-[10px] font-bold text-slate-900 block">Card</span>
                        </div>
                        <div class="p-3 border border-slate-200 hover:bg-slate-50 rounded-xl text-center cursor-pointer">
                            <span class="text-lg block">🏦</span>
                            <span class="text-[10px] font-semibold text-slate-600 block">Bank Transfer</span>
                        </div>
                        <div class="p-3 border border-slate-200 hover:bg-slate-50 rounded-xl text-center cursor-pointer">
                            <span class="text-lg block">📱</span>
                            <span class="text-[10px] font-semibold text-slate-600 block">USSD Code</span>
                        </div>
                    </div>
                </div>

                <!-- Guarantee Note -->
                <div class="flex items-center gap-2 text-[11px] text-slate-500 bg-emerald-50 border border-emerald-200/80 rounded-xl p-3">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>Instant payment confirmation with SSL 256-bit encryption.</span>
                </div>

                <!-- Pay Button -->
                <button 
                    @click="payConnectionFee(activeConversation)"
                    :disabled="payingConnection"
                    class="w-full bg-[#0F172B] hover:bg-slate-800 text-white font-bold text-sm py-3.5 px-4 rounded-xl flex items-center justify-center gap-2 transition-all cursor-pointer shadow-md disabled:opacity-50"
                >
                    <svg x-show="payingConnection" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span x-text="payingConnection ? 'Processing Payment...' : 'Pay ₦1,000 & Unlock Direct Chat'"></span>
                </button>
            </div>
        </div>

</x-dashboard-layout>
