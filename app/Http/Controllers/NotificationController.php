<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for current user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $notifications = $user->notifications()->take(20)->get()->map(function ($n) {
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
        });

        return response()->json([
            'success' => true,
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead(Request $request, $id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => $user->unreadNotifications()->count(),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead(Request $request)
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back();
    }
}
