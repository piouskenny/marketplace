<x-dashboard-layout 
    title="Messages & Direct Chat — {{ config('app.name', 'Skill Marketplace') }}"
    active="messages"
    xData="messagesApp()"
    xInit="init()"
>
    <x-slot name="head">
        <script>
            window.conversationsData = {!! json_encode($conversations ?? []) !!};
            window.initialActiveId = {!! request('conn_id') ? json_encode('conn_' . request('conn_id')) : (isset($conversations[0]['id']) ? json_encode($conversations[0]['id']) : 'null') !!};
            window.currentUserId = {!! json_encode(auth()->id()) !!};

            window.messagesApp = function() {
                console.log('[RT-DIAG BOOT] messagesApp created');
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
                    subscribedChannels: {},

                    init: function() {
                        console.log('[RT-DIAG BOOT] Alpine init called');
                        console.log('[RT-DIAG BOOT] currentUserId:', this.currentUserId);
                        console.log('[RT-DIAG BOOT] conversations:', this.conversations ? this.conversations.map(function(c) { return { id: c.id, conn_id: c.connection_id, db_conv_id: c.db_conversation_id, status: c.status }; }) : []);
                        
                        var self = this;
                        setTimeout(function() { self.pageLoading = false; }, 350);

                        if (window.innerWidth < 768 && !{!! json_encode((bool)request('conn_id')) !!}) {
                            self.activeConversationId = null;
                        }

                        console.log('[RT-DIAG BOOT] calling ensureEchoSubscribed');
                        self.ensureEchoSubscribed();
                        self.startRealtimePolling();
                    },

                    ensureEchoSubscribed: function() {
                        var self = this;
                        var attempts = 0;
                        function check() {
                            attempts++;
                            if (window.Echo) {
                                console.log('[RT-DIAG BOOT] window.Echo is ready (attempt ' + attempts + '). Executing initRealtimeEcho()...');
                                self.$nextTick(function() {
                                    self.scrollToBottom();
                                    self.initRealtimeEcho();
                                });
                            } else if (attempts < 50) {
                                console.log('[RT-DIAG BOOT] window.Echo not ready yet. Retrying in 100ms...');
                                setTimeout(check, 100);
                            } else {
                                console.error('[RT-DIAG BOOT ERROR] window.Echo failed to load within 5 seconds!');
                            }
                        }
                        check();
                    },

                    formatTime: function(t) {
                        if (!t) return 'Just now';
                        if (typeof t === 'string' && (t.indexOf('T') !== -1 || t.indexOf('-') !== -1 || t.indexOf(':') !== -1)) {
                            var d = new Date(t);
                            if (!isNaN(d.getTime())) {
                                return d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit', hour12: true });
                            }
                        }
                        return t;
                    },

                    sortConversations: function() {
                        if (!this.conversations || this.conversations.length <= 1) return;
                        this.conversations.sort(function(a, b) {
                            var tsA = Number(a.updated_timestamp) || 0;
                            var tsB = Number(b.updated_timestamp) || 0;
                            return tsB - tsA;
                        });
                    },

                    initRealtimeEcho: function() {
                        var self = this;
                        console.log('[RT-DIAG] initRealtimeEcho() called. UserID:', self.currentUserId, 'Conversations:', self.conversations ? self.conversations.map(function(c) { return { id: c.id, conn_id: c.connection_id, db_conv_id: c.db_conversation_id, status: c.status }; }) : []);

                        if (!window.Echo) {
                            console.warn('[RT-DIAG] window.Echo is undefined!');
                            return;
                        }

                        if (window.Pusher) {
                            window.Pusher.logToConsole = true;
                        }

                        if (window.Echo.connector && window.Echo.connector.pusher && !window._pusherStateBound) {
                            window._pusherStateBound = true;
                            window.Echo.connector.pusher.connection.bind('state_change', function(states) {
                                console.log('[RT-DIAG Pusher State Change]', states.previous, '->', states.current);
                            });
                            window.Echo.connector.pusher.connection.bind('error', function(err) {
                                console.error('[RT-DIAG Pusher Connection Error]', err);
                            });
                        }

                        if (!self.subscribedChannels) self.subscribedChannels = {};

                        if (self.currentUserId && !self.subscribedToUserChannel) {
                            self.subscribedToUserChannel = true;
                            var userChannelName = 'user.' + self.currentUserId;
                            console.log('[RT-DIAG] Attempting subscription to user channel:', userChannelName);
                            var userChannel = window.Echo.private(userChannelName);
                            
                            if (typeof userChannel.subscribed === 'function') {
                                userChannel.subscribed(function() {
                                    console.log('[RT-DIAG SUCCESS] Subscribed to user channel:', userChannelName);
                                });
                            }
                            if (typeof userChannel.error === 'function') {
                                userChannel.error(function(err) {
                                    console.error('[RT-DIAG ERROR] Subscription error on user channel:', userChannelName, err);
                                });
                            }

                            var notifHandler = function(n) {
                                console.log('[RT-DIAG EVENT] Incoming user notification event received on', userChannelName, n);
                                self.handleIncomingUserNotification(n);
                            };

                            if (typeof userChannel.notification === 'function') {
                                userChannel.notification(notifHandler);
                            }

                            userChannel
                                .listen('.notification.created', notifHandler)
                                .listen('notification.created', notifHandler)
                                .listen('NotificationCreated', notifHandler)
                                .listen('.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', notifHandler)
                                .listen('Illuminate\\Notifications\\Events\\BroadcastNotificationCreated', notifHandler);
                        }

                        if (self.conversations && self.conversations.length > 0) {
                            self.conversations.forEach(function(conv) {
                                if (conv.db_conversation_id) {
                                    var targetChannel = 'conversation.' + conv.db_conversation_id;
                                    if (!self.subscribedChannels[targetChannel]) {
                                        self.subscribedChannels[targetChannel] = true;
                                        console.log('[RT-DIAG] Attempting subscription to conversation channel:', targetChannel, 'for conv:', conv.id);
                                        
                                        var convChannel = window.Echo.private(targetChannel);
                                        
                                        if (typeof convChannel.subscribed === 'function') {
                                            convChannel.subscribed(function() {
                                                console.log('[RT-DIAG SUCCESS] Subscribed to conversation channel:', targetChannel);
                                            });
                                        }
                                        if (typeof convChannel.error === 'function') {
                                            convChannel.error(function(err) {
                                                console.error('[RT-DIAG ERROR] Subscription error on conversation channel:', targetChannel, err);
                                            });
                                        }

                                        var msgHandler = function(e) {
                                            console.log('[RT-DIAG EVENT] MessageSent event received on channel', targetChannel, e);
                                            self.handleIncomingBroadcast(e);
                                        };

                                        var readHandler = function(e) {
                                            console.log('[RT-DIAG EVENT] MessagesRead event received on channel', targetChannel, e);
                                            self.handleIncomingReadReceipt(e);
                                        };

                                        convChannel
                                            .listen('.message.sent', msgHandler)
                                            .listen('message.sent', msgHandler)
                                            .listen('MessageSent', msgHandler)
                                            .listen('.messages.read', readHandler)
                                            .listen('messages.read', readHandler)
                                            .listen('MessagesRead', readHandler);
                                    } else {
                                        console.log('[RT-DIAG] Already subscribed to channel:', targetChannel);
                                    }
                                } else {
                                    console.log('[RT-DIAG SKIP] Conversation', conv.id, 'has NO db_conversation_id yet.');
                                }
                            });
                        }
                    },

                    handleIncomingReadReceipt: function(e) {
                        console.log('[RT-DIAG ENTRY] handleIncomingReadReceipt()', e);
                        if (!e || !e.conversation_id) return;
                        var self = this;
                        var conv = self.conversations.find(function(c) {
                            return (e.conversation_id && (String(c.db_conversation_id) === String(e.conversation_id) || String(c.id) === 'db_conv_' + e.conversation_id)) ||
                                   (e.connection_id && (String(c.connection_id) === String(e.connection_id) || String(c.id) === 'conn_' + e.connection_id));
                        });

                        if (!conv) {
                            console.warn('[RT-DIAG READ RECEIPT NO MATCH]', e);
                            return;
                        }

                        var readAt = e.read_at || new Date().toISOString();
                        var messageIds = e.message_ids || [];

                        if (conv.messages && conv.messages.length > 0) {
                            conv.messages.forEach(function(m) {
                                if (m.sender === 'me') {
                                    if (messageIds.length === 0 || messageIds.map(String).indexOf(String(m.id)) !== -1) {
                                        m.read_at = readAt;
                                        m.pending = false;
                                    }
                                }
                            });
                        }
                    },

                    handleIncomingUserNotification: function(n) {
                        console.log('[RT-DIAG ENTRY] handleIncomingUserNotification()', n);
                        if (!n) return;
                        var self = this;

                        var payload = (n && typeof n === 'object' && n.data) ? n.data : n;
                        var type = payload.type || n.type;
                        var messageText = payload.message || n.message;
                        var convId = payload.conversation_id || n.conversation_id;
                        var connReqId = payload.connection_request_id || payload.connection_id || n.connection_request_id || n.connection_id;
                        var msgTime = payload.created_at || payload.time_formatted || n.created_at || new Date().toISOString();

                        console.log('[RT-DIAG NOTIF PARSED]', { type: type, messageText: messageText, convId: convId, connReqId: connReqId });

                        if (type === 'messages_read' || type === 'messages.read') {
                            self.handleIncomingReadReceipt(payload);
                            return;
                        }

                        if (type === 'connection_request' || type === 'connection_request_created') {
                            console.log('[RT-DIAG CONNECTION REQUEST NOTIF]', payload);
                            var existingConn = self.conversations.find(function(c) {
                                return connReqId && (String(c.connection_id) === String(connReqId) || String(c.id) === 'conn_' + connReqId);
                            });

                            if (!existingConn && connReqId) {
                                var newConnItem = {
                                    id: 'conn_' + connReqId,
                                    connection_id: Number(connReqId),
                                    db_conversation_id: convId || null,
                                    is_incoming: true,
                                    name: payload.initiator_name || payload.title || 'New Applicant',
                                    title: payload.opportunity_title || payload.title || 'Opportunity Connection Request',
                                    avatar: (payload.applicant_profile && payload.applicant_profile.avatar) ? payload.applicant_profile.avatar : '/images/avatars/babajide.png',
                                    online: true,
                                    location: (payload.applicant_profile && payload.applicant_profile.location) ? payload.applicant_profile.location : 'Lagos, Nigeria',
                                    category: payload.category || 'General Service',
                                    unread: 1,
                                    last_time: self.formatTime(msgTime),
                                    updated_timestamp: Math.floor(Date.now() / 1000),
                                    status: 'pending',
                                    applicant_profile: payload.applicant_profile || null,
                                    messages: [
                                        {
                                            id: 1,
                                            sender: 'them',
                                            text: payload.initial_message || payload.message || 'Application & Connection Request Submitted.',
                                            time: self.formatTime(msgTime)
                                        }
                                    ]
                                };
                                self.conversations.unshift(newConnItem);
                                self.sortConversations();
                                if (!self.activeConversationId) {
                                    self.activeConversationId = newConnItem.id;
                                }
                            }
                            return;
                        }

                        if (type === 'new_message' || (messageText && type !== 'connection_accepted' && type !== 'connection_declined' && type !== 'connection_activated')) {
                            var conv = self.conversations.find(function(c) {
                                return (convId && (String(c.db_conversation_id) === String(convId) || String(c.id) === 'db_conv_' + convId)) ||
                                       (connReqId && (String(c.connection_id) === String(connReqId) || String(c.id) === 'conn_' + connReqId));
                            });

                            if (conv) {
                                console.log('[RT-DIAG NOTIF MATCH FOUND] Matched conversation:', conv.id, 'db_conv_id:', conv.db_conversation_id);
                                if (convId && !conv.db_conversation_id) {
                                    console.log('[RT-DIAG NOTIF] Setting db_conversation_id =', convId, 'and re-running initRealtimeEcho()');
                                    conv.db_conversation_id = convId;
                                    self.initRealtimeEcho();
                                }

                                if (!conv.messages) conv.messages = [];
                                var dbId = payload.message_id || n.message_id;
                                var clientMsgId = payload.client_msg_id || n.client_msg_id;

                                var existingById = dbId ? conv.messages.find(function(m) { return String(m.id) === String(dbId); }) : null;
                                var existingByClient = clientMsgId ? conv.messages.find(function(m) { return m.client_msg_id && String(m.client_msg_id) === String(clientMsgId); }) : null;
                                var existing = existingById || existingByClient;

                                console.log('[RT-DIAG NOTIF DEDUP CHECK]', { dbId: dbId, clientMsgId: clientMsgId, existingById: !!existingById, existingByClient: !!existingByClient, existing: !!existing });

                                if (existing) {
                                    console.log('[RT-DIAG NOTIF DEDUP] Updating existing message:', existing.id);
                                    if (dbId) existing.id = dbId;
                                    existing.pending = false;
                                    existing.time = msgTime;
                                } else {
                                    console.log('[RT-DIAG NOTIF DEDUP] Appending NEW message to conversation:', conv.id);
                                    conv.messages.push({
                                        id: dbId || Date.now(),
                                        client_msg_id: clientMsgId,
                                        sender: 'them',
                                        text: messageText || 'New message received',
                                        time: msgTime,
                                        read_at: null,
                                        pending: false
                                    });

                                    if (String(self.activeConversationId) !== String(conv.id)) {
                                        conv.unread = (conv.unread || 0) + 1;
                                    }
                                }

                                conv.last_time = self.formatTime(msgTime);
                                conv.updated_timestamp = Math.floor(Date.now() / 1000);
                                self.sortConversations();

                                if (String(self.activeConversationId) === String(conv.id)) {
                                    self.$nextTick(function() { self.scrollToBottom(); });
                                }
                            } else {
                                console.warn('[RT-DIAG NOTIF NO MATCH] Could not find conversation matching convId:', convId, 'connReqId:', connReqId, 'in state:', self.conversations.map(function(c) { return { id: c.id, conn_id: c.connection_id, db_conv_id: c.db_conversation_id }; }));
                                self.initRealtimeEcho();
                            }
                        } else if (connReqId || type) {
                            var conv = self.conversations.find(function(c) {
                                return (connReqId && (String(c.connection_id) === String(connReqId) || String(c.id) === 'conn_' + connReqId)) ||
                                       (convId && String(c.db_conversation_id) === String(convId));
                            });

                            if (conv) {
                                console.log('[RT-DIAG STATUS EVENT MATCH]', type, 'conv:', conv.id);
                                if (type === 'connection_accepted') {
                                    conv.status = 'accepted';
                                } else if (type === 'connection_declined') {
                                    conv.status = 'declined';
                                } else if (type === 'connection_activated') {
                                    conv.status = 'connected';
                                    if (convId && !conv.db_conversation_id) {
                                        conv.db_conversation_id = convId;
                                    }
                                    self.initRealtimeEcho();
                                }
                                conv.updated_timestamp = Math.floor(Date.now() / 1000);
                                self.sortConversations();
                                self.$nextTick(function() { self.scrollToBottom(); });
                            } else {
                                console.warn('[RT-DIAG STATUS EVENT NO MATCH]', type, 'connReqId:', connReqId, 'convId:', convId);
                            }
                        }
                    },

                    handleIncomingBroadcast: function(e) {
                        console.log('[RT-DIAG ENTRY] handleIncomingBroadcast()', e);
                        if (!e) return;
                        var self = this;

                        var conv = self.conversations.find(function(c) {
                            return (e.conversation_id && (String(c.db_conversation_id) === String(e.conversation_id) || String(c.id) === 'db_conv_' + e.conversation_id)) ||
                                   (e.connection_id && (String(c.connection_id) === String(e.connection_id) || String(c.id) === 'conn_' + e.connection_id)) ||
                                   (e.connection_request_id && (String(c.connection_id) === String(e.connection_request_id) || String(c.id) === 'conn_' + e.connection_request_id));
                        });

                        if (!conv) {
                            console.warn('[RT-DIAG BROADCAST NO MATCH] Received broadcast for convId:', e.conversation_id, 'connId:', e.connection_id, 'but no matching conversation found in state! State contains:', self.conversations.map(function(c) { return { id: c.id, conn_id: c.connection_id, db_conv_id: c.db_conversation_id }; }));
                            return;
                        }

                        console.log('[RT-DIAG BROADCAST MATCH FOUND] Matched conv:', conv.id, 'db_conv_id:', conv.db_conversation_id);

                        if (e.conversation_id && !conv.db_conversation_id) {
                            console.log('[RT-DIAG BROADCAST] Setting db_conversation_id =', e.conversation_id, 'and re-running initRealtimeEcho()');
                            conv.db_conversation_id = e.conversation_id;
                            self.initRealtimeEcho();
                        }

                        if (!conv.messages) conv.messages = [];

                        var msgTime = e.created_at || e.time_formatted || new Date().toISOString();
                        var dbId = e.id;
                        var clientMsgId = e.client_msg_id;

                        var existingById = dbId ? conv.messages.find(function(m) { return String(m.id) === String(dbId); }) : null;
                        var existingByClient = clientMsgId ? conv.messages.find(function(m) { return m.client_msg_id && String(m.client_msg_id) === String(clientMsgId); }) : null;
                        var existing = existingById || existingByClient;

                        console.log('[RT-DIAG BROADCAST DEDUP CHECK]', { dbId: dbId, clientMsgId: clientMsgId, existingById: !!existingById, existingByClient: !!existingByClient, existing: !!existing });

                        if (existing) {
                            console.log('[RT-DIAG BROADCAST DEDUP] Updating existing message:', existing.id);
                            if (dbId) existing.id = dbId;
                            existing.pending = false;
                            existing.time = msgTime;
                        } else {
                            var isMe = String(e.sender_id) === String(self.currentUserId);
                            console.log('[RT-DIAG BROADCAST DEDUP] Appending NEW message from', isMe ? 'me' : 'them', 'to conv:', conv.id);
                            conv.messages.push({
                                id: dbId,
                                client_msg_id: clientMsgId,
                                sender: isMe ? 'me' : 'them',
                                text: e.body,
                                time: msgTime,
                                pending: false
                            });

                            if (String(self.activeConversationId) !== String(conv.id)) {
                                conv.unread = (conv.unread || 0) + 1;
                            }
                        }

                        conv.last_time = self.formatTime(msgTime);
                        conv.updated_timestamp = Math.floor(Date.now() / 1000);
                        self.sortConversations();

                        if (String(self.activeConversationId) === String(conv.id)) {
                            self.$nextTick(function() {
                                self.scrollToBottom();
                            });
                        }
                    },

                    get unreadConversationsCount() {
                        if (!this.conversations) return 0;
                        return this.conversations.filter(function(c) { return Number(c.unread) > 0; }).length;
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
                            var connId = conv.connection_id || (typeof id === 'string' ? id.replace('conn_', '') : id);
                            fetch('{{ url('/connections') }}/' + connId + '/read', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            }).catch(function() {});

                            if (conv.db_conversation_id) {
                                fetch('{{ url('/conversations') }}/' + conv.db_conversation_id + '/read', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                }).catch(function() {});
                                this.fetchActiveConversationMessages();
                            }
                        }
                        var self = this;
                        this.$nextTick(function() {
                            self.scrollToBottom();
                            self.initRealtimeEcho();
                        });
                    },

                    deleteConversation: function(conv) {
                        if (!conv) return;
                        if (!confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
                            return;
                        }
                        var self = this;
                        var connId = conv.connection_id || (typeof conv.id === 'string' ? conv.id.replace('conn_', '') : conv.id);
                        var targetUrl = '{{ url('/connections') }}/' + connId + '/chat';

                        fetch(targetUrl, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            if (data.success) {
                                self.conversations = self.conversations.filter(function(c) {
                                    return String(c.id) !== String(conv.id);
                                });
                                if (String(self.activeConversationId) === String(conv.id)) {
                                    self.activeConversationId = (self.conversations.length > 0 && window.innerWidth >= 768) ? self.conversations[0].id : null;
                                }
                            }
                        })
                        .catch(function(err) {
                            console.error('[DELETE CHAT ERROR]', err);
                        });
                    },

                    sendMessage: function() {
                        var text = this.newMessageText.trim();
                        if (!text) return;
                        var conv = this.activeConversation;
                        if (!conv) return;

                        var clientMsgId = 'cmsg_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                        var isoNow = new Date().toISOString();
                        var tempMsg = {
                            id: clientMsgId,
                            client_msg_id: clientMsgId,
                            sender: 'me',
                            text: text,
                            time: isoNow,
                            pending: true
                        };
                        if (!conv.messages) conv.messages = [];
                        conv.messages.push(tempMsg);
                        conv.last_time = this.formatTime(isoNow);
                        conv.updated_timestamp = Math.floor(Date.now() / 1000);
                        this.sortConversations();
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
                                body: JSON.stringify({ body: text, client_msg_id: clientMsgId })
                            })
                            .then(function(res) { return res.json(); })
                            .then(function(data) {
                                if (data.success && data.message) {
                                    tempMsg.id = data.message.id;
                                    tempMsg.pending = false;
                                    tempMsg.time = data.message.time_formatted || data.message.created_at || tempMsg.time;
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
                            if (data.conversation_id) {
                                conv.db_conversation_id = data.conversation_id;
                            }
                            self.paymentModalOpen = false;
                            self.$nextTick(function() {
                                self.scrollToBottom();
                                self.initRealtimeEcho();
                            });
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
            <div 
                class="w-full md:w-80 lg:w-96 border-b md:border-b-0 md:border-r border-slate-200/80 flex-col bg-slate-50/50 shrink-0"
                :class="activeConversationId ? 'hidden md:flex' : 'flex'"
            >
                
                <!-- Search & Title Header -->
                <div class="p-4 border-b border-slate-200/80 space-y-3 bg-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-slate-900 tracking-tight">Messages</h2>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#0F172B] text-white" x-text="unreadConversationsCount > 0 ? unreadConversationsCount + ' Unread Chats' : conversations.length + ' Chats'"></span>
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
                            class="w-full p-3.5 flex items-start gap-3 transition-colors text-left cursor-pointer relative group"
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
                                            Payment Required
                                        </span>
                                    </template>
                                    <template x-if="c.is_incoming && c.status === 'accepted'">
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200 shrink-0">
                                            Awaiting Payment
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
                            <div class="flex flex-col items-end gap-1.5 shrink-0 self-center">
                                <span 
                                    x-show="c.unread > 0" 
                                    class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500 text-white"
                                    x-text="c.unread"
                                ></span>
                                <button 
                                    @click.stop="deleteConversation(c)"
                                    class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 opacity-0 group-hover:opacity-100 transition-opacity"
                                    title="Delete chat"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
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
            <div 
                class="flex-1 flex-col min-w-0 bg-white"
                :class="activeConversationId ? 'flex' : 'hidden md:flex'"
            >
                
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
                        <div class="px-3.5 sm:px-5 py-3 border-b border-slate-200/80 flex items-center justify-between gap-3 sm:gap-4 bg-white shrink-0">
                            <div class="flex items-center gap-2 sm:gap-3 min-w-0">
                                <!-- Mobile Back to Threads List Button -->
                                <button 
                                    @click="activeConversationId = null" 
                                    class="md:hidden p-2 -ml-1 text-slate-600 hover:bg-slate-100 hover:text-slate-900 rounded-xl cursor-pointer transition-colors shrink-0"
                                    title="Back to all conversations"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>

                                <a :href="activeConversation.other_user_id ? '{{ url('/profile') }}/' + activeConversation.other_user_id : 'javascript:void(0)'" class="flex items-center gap-2 sm:gap-3 min-w-0 hover:opacity-80 transition-opacity">
                                    <div class="relative shrink-0">
                                        <img :src="activeConversation.avatar" :alt="activeConversation.name" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border border-slate-200" />
                                        <span x-show="activeConversation.online" class="w-2.5 h-2.5 rounded-full bg-emerald-500 ring-2 ring-white absolute bottom-0 right-0"></span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                            <h3 class="text-xs sm:text-sm font-bold text-slate-900 truncate hover:underline" x-text="activeConversation.name"></h3>
                                            <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200" x-text="activeConversation.category"></span>
                                            <template x-if="activeConversation.is_incoming && activeConversation.status === 'pending'">
                                                <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                    [Incoming Request]
                                                </span>
                                            </template>
                                            <template x-if="!activeConversation.is_incoming && activeConversation.status === 'pending'">
                                                <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    [Pending Acceptance]
                                                </span>
                                            </template>
                                            <template x-if="!activeConversation.is_incoming && activeConversation.status === 'accepted'">
                                                <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                                    [Payment Required]
                                                </span>
                                            </template>
                                            <template x-if="activeConversation.is_incoming && activeConversation.status === 'accepted'">
                                                <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-300">
                                                    [Awaiting Payment]
                                                </span>
                                            </template>
                                            <template x-if="activeConversation.status === 'connected'">
                                                <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                                    [Connected]
                                                </span>
                                            </template>
                                            <template x-if="activeConversation.status === 'declined'">
                                                <span class="px-1.5 py-0.5 rounded-md text-[9px] sm:text-[10px] font-bold bg-slate-200 text-slate-700 border border-slate-300">
                                                    [Declined]
                                                </span>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-2 text-[10px] sm:text-[11px] text-slate-500 pt-0.5">
                                            <span x-text="activeConversation.title" class="truncate"></span>
                                            <span>•</span>
                                            <span x-text="activeConversation.location"></span>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="flex items-center gap-2">
                                <a 
                                    :href="activeConversation.other_user_id ? '{{ url('/profile') }}/' + activeConversation.other_user_id : 'javascript:void(0)'" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-[#0F172B] hover:bg-slate-800 text-white text-xs font-bold transition-colors cursor-pointer shrink-0 shadow-2xs"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Profile →</span>
                                </a>

                                <button 
                                    @click="deleteConversation(activeConversation)" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-3.5 sm:py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition-colors cursor-pointer shrink-0 shadow-2xs"
                                    title="Delete this chat conversation"
                                >
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span class="hidden sm:inline">Delete Chat</span>
                                </button>
                            </div>
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

                            <!-- Payment Pending Card (Initiator / Applicant View: Pays Fee) -->
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
                                                    Your connection request with <strong x-text="activeConversation.name"></strong> was approved! Complete the ₦1,000 platform connection fee to unlock direct chat messaging and private contact details.
                                                </p>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                                            Payment Required
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

                            <!-- Payment Pending Card (Recipient / Job Creator View: Waiting for Applicant Payment) -->
                            <template x-if="activeConversation.is_incoming && activeConversation.status === 'accepted'">
                                <div class="bg-indigo-50 border border-indigo-200/90 rounded-2xl p-4 text-indigo-950 space-y-2 mb-3 shadow-xs">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0">
                                                ⌛
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-900">Request Accepted — Waiting for Applicant Payment</h4>
                                                <p class="text-[11px] text-slate-600 font-normal">
                                                    You accepted <strong x-text="activeConversation.name"></strong>'s request. Waiting for <strong x-text="activeConversation.name"></strong> to complete the ₦1,000 connection payment. Direct chat will unlock automatically once paid.
                                                </p>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                                            Awaiting Payment
                                        </span>
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
                                            <span x-text="formatTime(msg.time)"></span>
                                            <template x-if="msg.sender === 'me'">
                                                <span class="inline-flex items-center ml-0.5" :title="msg.read_at ? 'Read' : 'Sent'">
                                                    <template x-if="msg.read_at">
                                                        <span class="text-emerald-400 font-bold text-[11px] leading-none tracking-tighter">✓✓</span>
                                                    </template>
                                                    <template x-if="!msg.read_at">
                                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                    </template>
                                                </span>
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

    <!-- Profile Slide-Over Drawer Modal -->
    <x-profile-drawer :user="$user" />
</x-dashboard-layout>

