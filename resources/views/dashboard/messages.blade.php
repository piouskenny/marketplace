<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Messages & Direct Chat — {{ config('app.name', 'Skill Marketplace') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body 
        class="bg-slate-50 font-sans antialiased text-slate-900 min-h-full flex flex-col selection:bg-slate-900 selection:text-white"
        x-data="{
            sidebarOpen: false,
            notificationsOpen: false,
            profileModalOpen: false,
            activeConversationId: 101,
            conversations: {{ json_encode($conversations) }},
            searchQuery: '',
            newMessageText: '',

            get activeConversation() {
                return this.conversations.find(c => c.id === this.activeConversationId) || this.conversations[0];
            },

            get filteredConversations() {
                if (!this.searchQuery.trim()) return this.conversations;
                const q = this.searchQuery.toLowerCase();
                return this.conversations.filter(c => 
                    c.name.toLowerCase().includes(q) || 
                    c.title.toLowerCase().includes(q) ||
                    c.category.toLowerCase().includes(q)
                );
            },

            selectConversation(id) {
                this.activeConversationId = id;
                const conv = this.conversations.find(c => c.id === id);
                if (conv) conv.unread = 0;
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },

            sendMessage() {
                if (!this.newMessageText.trim()) return;
                const conv = this.activeConversation;
                if (!conv) return;

                const now = new Date();
                const hours = now.getHours();
                const minutes = now.getMinutes().toString().padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                const formattedHours = (hours % 12 || 12).toString();
                const timeString = `${formattedHours}:${minutes} ${ampm}`;

                conv.messages.push({
                    id: Date.now(),
                    sender: 'me',
                    text: this.newMessageText.trim(),
                    time: timeString
                });

                conv.last_time = 'Just now';
                this.newMessageText = '';

                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },

            scrollToBottom() {
                const container = this.$refs.messageFeed;
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        }"
        x-init="$nextTick(() => scrollToBottom())"
    >

        <!-- Main Responsive Layout Wrapper -->
        <div class="flex min-h-screen bg-slate-50">

            <!-- Sidebar Navigation (Desktop Fixed & Mobile Slide-over) -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between transition-transform duration-300 transform lg:translate-x-0 lg:static lg:z-auto"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <div class="p-5 space-y-6 flex-1 overflow-y-auto">
                    <!-- Brand Logo Header -->
                    <div class="flex items-center justify-between">
                        <a href="{{ url('/dashboard') }}" class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-[#0F172B] text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2L3 7v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V7l-9-5z"/></svg>
                            </div>
                            <div>
                                <span class="text-slate-900 font-extrabold text-base tracking-tight block leading-tight">Skill Marketplace</span>
                                <span class="text-[10px] text-slate-500 font-medium block">Dashboard Workspace</span>
                            </div>
                        </a>
                        <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Navigation Links Grouped -->
                    <div class="space-y-4">
                        <!-- Group 1: Navigation -->
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Navigation</span>
                            <nav class="space-y-0.5">
                                <!-- Home Dashboard -->
                                <a href="{{ url('/dashboard') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M3 9.5L12 3l9 6.5V20a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V9.5z"/></svg>
                                        <span>Overview</span>
                                    </div>
                                </a>

                                <!-- Browse Directory / Find Talent -->
                                <a href="{{ url('/dashboard/talent') }}" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                                        <span>Find Talent</span>
                                    </div>
                                </a>
                            </nav>
                        </div>

                        <!-- Group 2: Work & Requests -->
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Workplace</span>
                            <nav class="space-y-0.5">
                                <!-- Messages & Requests (ACTIVE PAGE) -->
                                <a href="{{ url('/dashboard/messages') }}" class="flex items-center justify-between px-3 py-2 rounded-xl bg-[#0F172B] text-white text-xs font-normal transition-colors shadow-xs">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        <span>Messages</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-400 text-slate-900">2</span>
                                </a>
                            </nav>
                        </div>

                        <!-- Group 3: Settings -->
                        <div>
                            <span class="px-3 text-[10px] font-normal text-slate-500 uppercase tracking-wider block mb-1">Account</span>
                            <nav class="space-y-0.5">
                                <button @click="profileModalOpen = true; sidebarOpen = false" class="w-full flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors text-left cursor-pointer">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                        <span>View Profile Card</span>
                                    </div>
                                </button>
                                <a href="{{ url('/profile/edit') }}" @click="sidebarOpen = false" class="flex items-center justify-between px-3 py-2 rounded-xl text-[#000000] hover:bg-slate-100 text-xs font-normal transition-colors">
                                    <div class="flex items-center gap-2.5">
                                        <svg class="w-4 h-4 text-[#000000]" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        <span>Edit Settings</span>
                                    </div>
                                </a>
                            </nav>
                        </div>
                    </div>
                </div>

                <!-- Bottom User Card Trigger & Logout -->
                <div class="p-4 border-t border-slate-200/80 space-y-3 shrink-0">
                    <button @click="profileModalOpen = true" class="w-full bg-slate-50 hover:bg-slate-100 border border-slate-200/80 rounded-xl p-3 flex items-center justify-between gap-3 text-left transition-colors cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200 shrink-0" />
                            @else
                                <div class="w-8 h-8 rounded-lg bg-[#0F172B] text-white font-bold flex items-center justify-center text-xs shrink-0">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h5 class="text-xs font-semibold text-slate-900 truncate">{{ $user->name ?? 'User' }}</h5>
                                <span class="text-[11px] text-slate-500 font-normal block truncate">Verified Profile</span>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <form action="{{ url('/logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full flex items-center justify-center gap-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 border border-rose-200/60 rounded-xl py-2 transition-colors cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Sign Out Account</span>
                        </button>
                    </form>
                </div>
            </aside>

            <!-- Backdrop for Mobile Sidebar -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-40 lg:hidden" 
                style="display: none;"
            ></div>

            <!-- Main Viewport Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

                <!-- Top Navbar Header -->
                <header class="bg-white border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex items-center justify-between gap-4 sticky top-0 z-30 shrink-0">
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

                <!-- Page Main Section Container -->
                <main class="flex-1 p-3 sm:p-5 overflow-hidden flex flex-col">
                    
                    <!-- Wireframe 2-Column Chat & Messaging Workspace Card -->
                    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xs overflow-hidden flex flex-col md:flex-row flex-1 min-h-[560px]">

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
                                            <p class="text-[11px] text-slate-500 font-normal truncate" x-text="c.title"></p>
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
                            </div>
                        </div>

                        <!-- Right Column: Active Chat Feed Workspace -->
                        <div class="flex-1 flex flex-col min-w-0 bg-white">
                            
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
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-500">
                                            <span x-text="activeConversation.title" class="truncate"></span>
                                            <span>•</span>
                                            <span x-text="activeConversation.location"></span>
                                        </div>
                                    </div>
                                </div>

                                <button 
                                    @click="profileModalOpen = true" 
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

                            <!-- Bottom Input Workspace (Matching Wireframe Image) -->
                            <div class="p-3 sm:p-4 bg-white border-t border-slate-200/80 space-y-3 shrink-0">
                                
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
                        </div>

                    </div>
                </main>
            </div>
        </div>

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

    </body>
</html>
