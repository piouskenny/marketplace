@props([
    'userNotifications' => [],
    'unreadCount' => 0,
])

@php
    $formattedNotifications = collect($userNotifications)->map(function ($n) {
        if (is_array($n)) return $n;
        $data = is_array($n->data) ? $n->data : [];
        return array_merge([
            'id' => $n->id,
            'type' => $data['type'] ?? 'general',
            'title' => $data['title'] ?? 'Notification',
            'message' => $data['message'] ?? '',
            'url' => $data['url'] ?? '#',
            'icon' => $data['icon'] ?? 'bell',
            'read_at' => $n->read_at ? $n->read_at->toIso8601String() : null,
            'created_at' => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
        ], $data);
    })->values()->toArray();
@endphp

<div 
    x-data="headerNotificationsApp({{ json_encode(auth()->id()) }}, {{ json_encode($unreadCount) }}, {{ json_encode($formattedNotifications) }})"
    x-init="initNotifications()"
    class="relative"
>
    <!-- Notifications Trigger Button -->
    <button 
        @click="notificationsOpen = !notificationsOpen" 
        class="w-9 h-9 rounded-xl border border-slate-200 hover:bg-slate-50 flex items-center justify-center text-slate-600 relative transition-colors cursor-pointer" 
        title="View notifications"
    >
        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="unreadCount > 0" class="w-2.5 h-2.5 rounded-full bg-sky-500 absolute top-1.5 right-1.5 ring-2 ring-white animate-pulse"></span>
    </button>

    <!-- Dropdown Panel -->
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
                <span x-show="unreadCount > 0" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#0F172B] text-white" x-text="unreadCount + ' New'"></span>
            </div>
            <div class="flex items-center gap-2">
                <button x-show="unreadCount > 0" @click="markAllRead()" class="text-[11px] text-sky-600 hover:text-sky-800 font-semibold cursor-pointer">Mark all read</button>
                <button @click="notificationsOpen = false" class="text-xs text-slate-400 hover:text-slate-600 font-medium cursor-pointer">Close</button>
            </div>
        </div>

        <div class="divide-y divide-slate-100 max-h-80 overflow-y-auto">
            <template x-for="n in notifications" :key="n.id">
                <div 
                    @click="clickNotification(n)"
                    class="p-3.5 hover:bg-slate-50 transition-colors flex items-start justify-between gap-3 cursor-pointer group"
                    :class="!n.read_at ? 'bg-sky-50/40' : ''"
                >
                    <div class="flex items-start gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 border mt-0.5"
                             :class="n.type === 'connection_request' ? 'bg-indigo-100 text-indigo-700 border-indigo-200' : (n.type === 'connection_activated' ? 'bg-emerald-100 text-emerald-700 border-emerald-200' : 'bg-sky-100 text-sky-700 border-sky-200')">
                            <template x-if="n.type === 'connection_request'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </template>
                            <template x-if="n.type === 'connection_activated'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                            </template>
                            <template x-if="n.type !== 'connection_request' && n.type !== 'connection_activated'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </template>
                        </div>
                        <div class="flex-1 min-w-0 space-y-0.5">
                            <p class="text-xs font-bold text-slate-900 group-hover:text-sky-700 transition-colors truncate" x-text="n.title"></p>
                            <p class="text-[11px] text-slate-600 font-normal leading-normal" x-text="n.message"></p>
                            <span class="text-[10px] text-slate-400 font-medium block pt-0.5" x-text="n.created_at"></span>
                        </div>
                    </div>
                    <span x-show="!n.read_at" class="w-2 h-2 rounded-full bg-sky-500 shrink-0 mt-1.5"></span>
                </div>
            </template>

            <template x-if="notifications.length === 0">
                <div class="p-6 text-center text-xs text-slate-400 font-normal">
                    No notifications yet
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    if (!window.headerNotificationsApp) {
        window.headerNotificationsApp = function(userId, initialUnreadCount, initialNotifications) {
            return {
                currentUserId: userId,
                notificationsOpen: false,
                unreadCount: Number(initialUnreadCount) || 0,
                notifications: Array.isArray(initialNotifications) ? initialNotifications : [],
                subscribed: false,

                initNotifications: function() {
                    var self = this;
                    self.initRealtimeUserChannel();
                },

                initRealtimeUserChannel: function() {
                    var self = this;
                    if (!self.currentUserId || self.subscribed) return;

                    var attempts = 0;
                    function check() {
                        attempts++;
                        if (window.Echo) {
                            self.subscribed = true;
                            var channelName = 'user.' + self.currentUserId;
                            var channel = window.Echo.private(channelName);

                            var notifHandler = function(n) {
                                console.log('[HEADER NOTIF EVENT]', channelName, n);
                                self.handleIncomingNotification(n);
                            };

                            channel
                                .listen('.notification.created', notifHandler)
                                .listen('notification.created', notifHandler)
                                .listen('NotificationCreated', notifHandler)
                                .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', notifHandler)
                                .listen('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', notifHandler);
                        } else if (attempts < 50) {
                            setTimeout(check, 100);
                        }
                    }
                    check();
                },

                handleIncomingNotification: function(n) {
                    var self = this;
                    if (!n) return;

                    var payload = (n && typeof n === 'object' && n.data) ? n.data : n;
                    var notifId = payload.id || n.id || ('notif_' + Date.now() + '_' + Math.random().toString(36).substr(2, 5));
                    var connReqId = payload.connection_request_id || payload.connection_id || n.connection_request_id || n.connection_id;
                    var type = payload.type || n.type || 'general';

                    var exists = self.notifications.find(function(item) {
                        return (item.id && String(item.id) === String(notifId)) ||
                               (connReqId && item.connection_request_id && String(item.connection_request_id) === String(connReqId) && item.type === type);
                    });

                    if (!exists) {
                        var targetUrl = payload.url || (connReqId ? '{{ url('/dashboard/messages') }}?conn_id=' + connReqId : '#');
                        var newNotif = {
                            id: notifId,
                            type: type,
                            title: payload.title || (type === 'connection_request' ? 'New Connection Request' : 'Notification'),
                            message: payload.message || 'You received a new notification.',
                            url: targetUrl,
                            icon: payload.icon || 'bell',
                            read_at: null,
                            created_at: 'Just now',
                            connection_request_id: connReqId || null
                        };

                        self.notifications.unshift(newNotif);
                        self.unreadCount = (self.unreadCount || 0) + 1;

                        window.dispatchEvent(new CustomEvent('realtime-notification-received', { detail: payload }));
                    }
                },

                clickNotification: function(n) {
                    var self = this;
                    if (!n.read_at && n.id && typeof n.id === 'string' && n.id.indexOf('notif_') !== 0) {
                        fetch('{{ url('/notifications') }}/' + n.id + '/read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            if (data && data.success) {
                                n.read_at = new Date().toISOString();
                                self.unreadCount = Math.max(0, self.unreadCount - 1);
                            }
                        })
                        .catch(function() {});
                    }

                    if (n.url && n.url !== '#') {
                        window.location.href = n.url;
                    }
                },

                markAllRead: function() {
                    var self = this;
                    fetch('{{ route('notifications.read-all') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.success) {
                            self.notifications.forEach(function(item) { item.read_at = new Date().toISOString(); });
                            self.unreadCount = 0;
                        }
                    })
                    .catch(function() {});
                }
            };
        };
    }
</script>
