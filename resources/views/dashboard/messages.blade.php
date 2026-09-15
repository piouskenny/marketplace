<x-dashboard-layout 
    title="Messages & Direct Chat — {{ config('app.name', 'Skill Marketplace') }}"
    active="messages"
    xData="messagesApp()"
    xInit="setTimeout(() => pageLoading = false, 350); $nextTick(() => { scrollToBottom(); initRealtimeEcho(); })"
>
    <x-slot name="head">
        <script>
            window.conversationsData = {!! json_encode($conversations ?? []) !!};
            window.initialActiveId = {!! request('conn_id') ? json_encode('conn_' . request('conn_id')) : (isset($conversations[0]['id']) ? json_encode($conversations[0]['id']) : 101) !!};
            window.currentUserId = {!! json_encode(auth()->id()) !!};

            window.messagesApp = function() {
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
                    reviewModalOpen: false,
                    reviewRating: 5,
                    reviewComment: '',
                    reviewSubmitting: false,
                    reviewSuccess: false,
                    reviewError: '',
                    selectedApplicant: null,
                    currentUserId: window.currentUserId,
                    activeConversationId: window.initialActiveId,
                    conversations: window.conversationsData || [],
                    searchQuery: '',
                    newMessageText: '',
                    activeChannelName: null,
                    subscribedToUserChannel: false,

                    initRealtimeEcho: function() {
                        var self = this;
                        if (!window.Echo) return;

                        if (self.currentUserId && !self.subscribedToUserChannel) {
                            self.subscribedToUserChannel = true;
                            window.Echo.private('user.' + self.currentUserId)
                                .listen('.notification.created', function(n) {
                                    self.handleIncomingUserNotification(n);
                                })
                                .listen('NotificationCreated', function(n) {
                                    self.handleIncomingUserNotification(n);
                                });
                        }

                        var conv = self.activeConversation;
                        if (!conv || !conv.db_conversation_id) return;

                        var targetChannel = 'conversation.' + conv.db_conversation_id;
                        if (self.activeChannelName === targetChannel) return;

                        if (self.activeChannelName) {
                            window.Echo.leave(self.activeChannelName);
                        }

                        self.activeChannelName = targetChannel;
                        window.Echo.private(targetChannel)
                            .listen('.message.sent', function(e) {
                                self.handleIncomingBroadcast(e);
                            })
                            .listen('MessageSent', function(e) {
                                self.handleIncomingBroadcast(e);
                            });
                    },

                    handleIncomingUserNotification: function(n) {
                        if (!n) return;
                        var self = this;

                        if (n.type === 'new_message') {
                            var conv = self.conversations.find(function(c) {
                                return (n.conversation_id && (String(c.db_conversation_id) === String(n.conversation_id) || String(c.id) === 'db_conv_' + n.conversation_id)) ||
                                       (n.connection_request_id && (String(c.connection_id) === String(n.connection_request_id) || String(c.id) === 'conn_' + n.connection_request_id)) ||
                                       (n.connection_id && (String(c.connection_id) === String(n.connection_id) || String(c.id) === 'conn_' + n.connection_id));
                            });

                            if (conv) {
                                if (n.conversation_id && !conv.db_conversation_id) {
                                    conv.db_conversation_id = n.conversation_id;
                                    self.initRealtimeEcho();
                                }

                                if (!conv.messages) conv.messages = [];
                                var msgId = n.message_id || Date.now();
                                var exists = conv.messages.some(function(m) {
                                    return String(m.id) === String(msgId) || (m.text === n.message && Date.now() - (m.id || 0) < 15000);
                                });

                                if (!exists) {
                                    conv.messages.push({
                                        id: msgId,
                                        sender: 'them',
                                        text: n.message || 'New message received',
                                        time: 'Just now'
                                    });

                                    if (String(self.activeConversationId) !== String(conv.id)) {
                                        conv.unread = (conv.unread || 0) + 1;
                                    }
                                    conv.last_time = 'Just now';

                                    if (String(self.activeConversationId) === String(conv.id)) {
                                        self.$nextTick(function() { self.scrollToBottom(); });
                                    }
                                }
                            }
                        } else if (n.connection_request_id || n.connection_id || n.type) {
                            var connReqId = n.connection_request_id || n.connection_id;
                            var conv = self.conversations.find(function(c) {
                                return (connReqId && (String(c.connection_id) === String(connReqId) || String(c.id) === 'conn_' + connReqId)) ||
                                       (n.conversation_id && String(c.db_conversation_id) === String(n.conversation_id));
                            });

                            if (conv) {
                                if (n.type === 'connection_accepted') {
                                    conv.status = 'accepted';
                                } else if (n.type === 'connection_declined') {
                                    conv.status = 'declined';
                                } else if (n.type === 'connection_activated') {
                                    conv.status = 'connected';
                                    if (n.conversation_id && !conv.db_conversation_id) {
                                        conv.db_conversation_id = n.conversation_id;
                                    }
                                    self.initRealtimeEcho();
                                }
                                self.$nextTick(function() { self.scrollToBottom(); });
                            } else {
                                window.location.reload();
                            }
                        }
                    },

                    handleIncomingBroadcast: function(e) {
                        if (!e) return;
                        var self = this;

                        var conv = self.conversations.find(function(c) {
                            return (e.conversation_id && (String(c.db_conversation_id) === String(e.conversation_id) || String(c.id) === 'db_conv_' + e.conversation_id)) ||
                                   (e.connection_id && (String(c.connection_id) === String(e.connection_id) || String(c.id) === 'conn_' + e.connection_id)) ||
                                   (e.connection_request_id && (String(c.connection_id) === String(e.connection_request_id) || String(c.id) === 'conn_' + e.connection_request_id));
                        });

                        if (!conv) return;

                        if (e.conversation_id && !conv.db_conversation_id) {
                            conv.db_conversation_id = e.conversation_id;
                        }

                        if (!conv.messages) conv.messages = [];

                        // Prevent duplicate rendering of sender's own message
                        var existing = conv.messages.find(function(m) {
                            return String(m.id) === String(e.id) || (m.text === e.body && m.sender === 'me' && Date.now() - (m.id || 0) < 15000);
                        });

                        if (existing) {
                            existing.id = e.id;
                            existing.time = e.time_formatted || existing.time;
                        } else {
                            var isMe = String(e.sender_id) === String(self.currentUserId);
                            conv.messages.push({
                                id: e.id,
                                sender: isMe ? 'me' : 'them',
                                text: e.body,
                                time: e.time_formatted || 'Just now'
                            });

                            if (String(self.activeConversationId) !== String(conv.id)) {
                                conv.unread = (conv.unread || 0) + 1;
                            }
                        }

                        conv.last_time = e.time_formatted || 'Just now';

                        if (String(self.activeConversationId) === String(conv.id)) {
                            self.$nextTick(function() {
                                self.scrollToBottom();
                            });
                        }
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
                            return (c.name && c.name.toLowerCase().indexOf(q) !== -1) || 
                                   (c.title && c.title.toLowerCase().indexOf(q) !== -1) ||
                                   (c.category && c.category.toLowerCase().indexOf(q) !== -1);
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
                            self.initRealtimeEcho();
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
                        if (!conv.messages) conv.messages = [];
                        conv.messages.push(tempMsg);
                        conv.last_time = 'Just now';
                        this.newMessageText = '';

                        var self = this;
                        this.$nextTick(function() {
                            self.scrollToBottom();
                        });

                        var targetUrl = null;
                        var rawConnId = conv.connection_id || conv.id;
                        if (conv.db_conversation_id) {
                            targetUrl = '{{ url('/conversations') }}/' + conv.db_conversation_id + '/messages';
                        } else if (rawConnId) {
                            var cleanConnId = String(rawConnId).replace('conn_', '');
                            targetUrl = '{{ url('/connections') }}/' + cleanConnId + '/messages';
                        }

                        if (targetUrl) {
                            fetch(targetUrl, {
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
                                    if (data.message.conversation_id && !conv.db_conversation_id) {
                                        conv.db_conversation_id = data.message.conversation_id;
                                        self.initRealtimeEcho();
                                    }
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

                    openReviewModal: function(conv) {
                        this.reviewRating = 5;
                        this.reviewComment = '';
                        this.reviewError = '';
                        this.reviewSuccess = false;
                        this.reviewModalOpen = true;
                    },

                    submitReview: function() {
                        var self = this;
                        var conv = self.activeConversation;
                        if (!conv) return;
                        var connId = conv.connection_id || conv.id;
                        if (typeof connId === 'string' && connId.indexOf('conn_') === 0) {
                            connId = connId.replace('conn_', '');
                        }
                        self.reviewSubmitting = true;
                        self.reviewError = '';

                        fetch('{{ url('/connections') }}/' + connId + '/review', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                rating: self.reviewRating,
                                comment: self.reviewComment
                            })
                        })
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            self.reviewSubmitting = false;
                            if (data.success) {
                                self.reviewSuccess = true;
                                setTimeout(function() {
                                    self.reviewModalOpen = false;
                                }, 1500);
                            } else {
                                self.reviewError = data.message || 'Failed to submit review.';
                            }
                        })
                        .catch(function(err) {
                            self.reviewSubmitting = false;
                            self.reviewError = 'An error occurred while submitting your review.';
                        });
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
            };
        </script>
    </x-slot>

    <!-- Main Dashboard Workspace Container -->
    <main class="flex-1 min-w-0 flex flex-col space-y-4 h-[calc(100vh-2rem)] sm:h-[calc(100vh-3rem)] overflow-hidden">
        
        <!-- Top Header Bar -->
        <header class="bg-white border border-slate-200/80 rounded-2xl px-4 sm:px-6 py-3 flex items-center justify-between gap-4 shrink-0 shadow-xs">
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

                <h1 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">Direct Messages</h1>
            </div>

            <div class="flex items-center gap-3">
                <!-- Notifications Popover Bell Icon -->
                <div class="relative">
                    <button @click="notificationsOpen = !notificationsOpen" class="w-9 h-9 rounded-xl border border-slate-200/80 bg-slate-50 flex items-center justify-center text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors relative cursor-pointer" title="View notifications">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="w-2 h-2 rounded-full bg-sky-500 absolute top-2 right-2 ring-2 ring-white"></span>
                    </button>

                    <!-- Notifications Dropdown Panel -->
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
                    
        <!-- 2-Column Messaging Workspace Card -->
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
                                    x-text="c.messages && c.messages.length > 0 ? c.messages[c.messages.length - 1].text : 'No messages'"
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
                                Select a conversation from the sidebar or submit an application to start messaging.
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
                                    <div class="flex items-center gap-2 flex-wrap">
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
                                    <div class="flex items-center gap-2 text-[11px] text-slate-500 pt-0.5">
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

                            <!-- Incoming Connection Offer Card -->
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

                            <!-- Sent Connection Request Pending Card -->
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

                            <!-- Payment Pending Card -->
                            <template x-if="activeConversation.status === 'accepted'">
                                <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 text-emerald-950 space-y-3 mb-3 shadow-xs">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                🎉
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-900">Application Approved! Connection Fee Required</h4>
                                                <p class="text-[11px] text-slate-600 font-normal">
                                                    Connection request with <strong x-text="activeConversation.name"></strong> is approved! Complete the ₦1,000 platform connection fee to unlock direct chat messaging and private phone details.
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

                            <!-- Connected Success Header Card -->
                            <template x-if="activeConversation.status === 'connected'">
                                <div class="bg-emerald-50/80 border border-emerald-200 rounded-2xl p-3.5 text-emerald-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-3 shadow-2xs">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                            ✓
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold text-emerald-950">Direct Connection Active</h4>
                                            <p class="text-[11px] text-emerald-800 font-normal">Messaging and direct contact details unlocked.</p>
                                        </div>
                                    </div>
                                    <button 
                                        @click="openReviewModal(activeConversation)"
                                        class="px-3 py-1.5 rounded-xl bg-white hover:bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs font-semibold shadow-2xs flex items-center gap-1.5 transition-colors cursor-pointer shrink-0"
                                    >
                                        <span>★ Leave Review</span>
                                    </button>
                                </div>
                            </template>

                            <!-- Messages History Feed -->
                            <template x-for="msg in (activeConversation.messages || [])" :key="msg.id">
                                <div 
                                    class="flex flex-col"
                                    :class="msg.sender === 'me' ? 'items-end' : 'items-start'"
                                >
                                    <div 
                                        class="max-w-[85%] sm:max-w-[70%] p-3 sm:p-3.5 rounded-2xl text-xs sm:text-sm leading-relaxed shadow-2xs"
                                        :class="msg.sender === 'me' ? 'bg-[#0F172B] text-white rounded-br-xs' : 'bg-white border border-slate-200 text-slate-800 rounded-bl-xs'"
                                    >
                                        <p class="whitespace-pre-line" x-text="msg.text"></p>
                                        <div 
                                            class="text-[10px] mt-1.5 flex items-center gap-1"
                                            :class="msg.sender === 'me' ? 'text-slate-400 justify-end' : 'text-slate-400 justify-start'"
                                        >
                                            <span x-text="msg.time"></span>
                                            <template x-if="msg.sender === 'me'">
                                                <svg class="w-3 h-3 text-emerald-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input Bar -->
                        <div class="p-3 sm:p-4 border-t border-slate-200/80 bg-white shrink-0">
                            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                                <input 
                                    type="text" 
                                    x-model="newMessageText" 
                                    placeholder="Type a message..." 
                                    class="flex-1 bg-slate-50 border border-slate-200 focus:bg-white focus:border-slate-800 rounded-xl px-4 py-2.5 text-xs sm:text-sm font-normal text-slate-900 outline-none transition-all"
                                    :disabled="activeConversation.status !== 'connected'"
                                />
                                <button 
                                    type="submit" 
                                    class="bg-[#0F172B] hover:bg-slate-800 disabled:opacity-50 text-white font-semibold px-4 py-2.5 rounded-xl text-xs sm:text-sm shadow-xs flex items-center gap-2 transition-colors cursor-pointer shrink-0"
                                    :disabled="!newMessageText.trim() || activeConversation.status !== 'connected'"
                                >
                                    <span>Send</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </main>

    <!-- Modal 1: Applicant Profile Drawer/Modal -->
    <div 
        x-show="applicantModalOpen" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 overflow-y-auto"
        style="display: none;"
    >
        <div 
            @click.outside="applicantModalOpen = false"
            class="bg-white border border-slate-200/90 rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden space-y-0 transform transition-all"
        >
            <template x-if="selectedApplicant">
                <div>
                    <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img :src="selectedApplicant.avatar" :alt="selectedApplicant.name" class="w-12 h-12 rounded-full object-cover border-2 border-white/20" />
                            <div>
                                <h3 class="text-base font-bold" x-text="selectedApplicant.name"></h3>
                                <p class="text-xs text-slate-300 font-normal" x-text="selectedApplicant.title"></p>
                            </div>
                        </div>
                        <button @click="applicantModalOpen = false" class="text-slate-400 hover:text-white p-1 rounded-lg transition-colors cursor-pointer">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200" x-text="selectedApplicant.rating || '5.0 ★'"></span>
                            <span class="text-xs text-slate-500" x-text="selectedApplicant.location"></span>
                        </div>

                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Biography & Experience</h4>
                            <p class="text-xs text-slate-700 leading-relaxed" x-text="selectedApplicant.bio"></p>
                        </div>

                        <template x-if="selectedApplicant.skills">
                            <div class="space-y-1.5 pt-2 border-t border-slate-100">
                                <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Skills & Verification</h4>
                                <div class="flex flex-wrap gap-1.5">
                                    <template x-for="skill in selectedApplicant.skills" :key="skill">
                                        <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200" x-text="skill"></span>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                            <button @click="applicantModalOpen = false" class="px-4 py-2 rounded-xl bg-[#0F172B] text-white text-xs font-semibold hover:bg-slate-800 transition-colors cursor-pointer">
                                Close Profile
                            </button>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal 2: Connection Fee Payment Modal -->
    <div 
        x-show="paymentModalOpen" 
        x-transition
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.outside="paymentModalOpen = false"
            class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-5"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm">
                        💳
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Unlock Connection & Direct Chat</h3>
                </div>
                <button @click="paymentModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="space-y-3">
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pay the <strong>₦1,000 platform connection fee</strong> via Paystack to activate your direct line, messaging history, and contact details.
                </p>
                
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-2">
                    <div class="flex justify-between text-xs font-semibold text-slate-700">
                        <span>Platform Connection Fee</span>
                        <span class="text-slate-900 font-bold">₦1,000.00</span>
                    </div>
                    <div class="flex justify-between text-[11px] text-slate-500">
                        <span>Payment Method</span>
                        <span>Paystack Checkout / Card / Transfer</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button @click="paymentModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Cancel
                </button>
                <button 
                    @click="payConnectionFee(activeConversation)" 
                    class="px-5 py-2.5 rounded-xl bg-[#0F172B] hover:bg-slate-800 text-white text-xs font-bold shadow-xs flex items-center gap-2 transition-colors cursor-pointer"
                    :disabled="payingConnection"
                >
                    <span x-text="payingConnection ? 'Processing...' : 'Pay ₦1,000 Now →'"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 3: Review Modal -->
    <div 
        x-show="reviewModalOpen" 
        x-transition
        class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4"
        style="display: none;"
    >
        <div 
            @click.outside="reviewModalOpen = false"
            class="bg-white border border-slate-200 rounded-2xl shadow-2xl max-w-md w-full p-6 space-y-4"
        >
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Leave a Review</h3>
                <button @click="reviewModalOpen = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <template x-if="reviewSuccess">
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs p-4 rounded-xl text-center space-y-1">
                    <p class="font-bold">Thank You!</p>
                    <p>Your review has been submitted successfully.</p>
                </div>
            </template>

            <template x-if="!reviewSuccess">
                <div class="space-y-4">
                    <template x-if="reviewError">
                        <div class="bg-rose-50 border border-rose-200 text-rose-800 text-xs p-3 rounded-xl" x-text="reviewError"></div>
                    </template>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Rating</label>
                        <div class="flex items-center gap-2">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button 
                                    type="button" 
                                    @click="reviewRating = star"
                                    class="text-2xl transition-transform hover:scale-110 cursor-pointer"
                                    :class="star <= reviewRating ? 'text-amber-400' : 'text-slate-300'"
                                >★</button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Comment / Feedback</label>
                        <textarea 
                            x-model="reviewComment" 
                            rows="3" 
                            placeholder="Share your experience working with this client or service provider..." 
                            class="w-full bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs outline-none focus:border-slate-800 focus:bg-white transition-all"
                        ></textarea>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button @click="reviewModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button 
                            @click="submitReview()" 
                            class="px-5 py-2 rounded-xl bg-[#0F172B] hover:bg-slate-800 text-white text-xs font-bold transition-colors cursor-pointer"
                            :disabled="reviewSubmitting"
                        >
                            <span x-text="reviewSubmitting ? 'Submitting...' : 'Submit Review'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

</x-dashboard-layout>
