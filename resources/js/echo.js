import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || "mt1",
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME || "https") === "https",
    ...(import.meta.env.VITE_PUSHER_HOST ? { wsHost: import.meta.env.VITE_PUSHER_HOST } : {}),
    ...(import.meta.env.VITE_PUSHER_PORT ? { wsPort: import.meta.env.VITE_PUSHER_PORT, wssPort: import.meta.env.VITE_PUSHER_PORT } : {}),
    enabledTransports: ["ws", "wss"],
});
