<?php

namespace App\Http\Controllers;

use App\Enums\ConnectionStatus;
use App\Enums\PaymentStatus;
use App\Models\ConnectionRequest;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    /**
     * Display Payment History and Connection History Hub.
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->load([
            'professionalProfile.category',
            'professionalProfile.skills',
        ]);

        $activeTab = $request->query('tab', 'connections');
        if (!in_array($activeTab, ['connections', 'payments'])) {
            $activeTab = 'connections';
        }

        // Calculate dynamic profile completion percentage
        $completionPercentage = 35;
        if (!empty($user->phone)) {
            $completionPercentage += 15;
        }
        if (!empty($user->location)) {
            $completionPercentage += 15;
        }
        if ($user->professionalProfile) {
            $completionPercentage += 20;
        }
        if ($user->onboarding_completed) {
            $completionPercentage = 100;
        }

        // 1. Fetch Connection Requests History
        $connections = ConnectionRequest::with([
            'initiator.professionalProfile.category',
            'recipient.professionalProfile.category',
            'opportunity.category',
            'payments',
            'conversation',
        ])
        ->where(function ($q) use ($user) {
            $q->where('initiator_id', $user->id)
              ->orWhere('recipient_id', $user->id);
        })
        ->latest()
        ->get();

        // 2. Fetch Payment Transactions History
        $payments = Payment::with([
            'connectionRequest.initiator',
            'connectionRequest.recipient',
            'connectionRequest.opportunity',
        ])
        ->where('user_id', $user->id)
        ->latest()
        ->get();

        // Summary Statistics
        $totalConnectionsCount = $connections->count();
        $connectedCount = $connections->filter(function ($c) {
            $st = $c->status instanceof ConnectionStatus ? $c->status->value : (string) $c->status;
            return $st === ConnectionStatus::Connected->value || $c->conversation;
        })->count();

        $pendingCount = $connections->filter(function ($c) {
            $st = $c->status instanceof ConnectionStatus ? $c->status->value : (string) $c->status;
            return $st === ConnectionStatus::Pending->value;
        })->count();

        $successfulPaymentsCount = $payments->filter(fn($p) => $p->status === PaymentStatus::Successful)->count();
        $totalAmountSpentKobo = $payments->filter(fn($p) => $p->status === PaymentStatus::Successful)->sum('amount');
        $totalAmountSpentNaira = number_format($totalAmountSpentKobo / 100, 2);

        $pendingOrFailedPaymentsCount = $payments->filter(fn($p) => $p->status !== PaymentStatus::Successful)->count();

        $userNotifications = $user->notifications()->take(15)->get();
        $unreadCount = $user->unreadNotifications()->count();

        return view('dashboard.history', compact(
            'user',
            'completionPercentage',
            'activeTab',
            'connections',
            'payments',
            'totalConnectionsCount',
            'connectedCount',
            'pendingCount',
            'successfulPaymentsCount',
            'totalAmountSpentNaira',
            'pendingOrFailedPaymentsCount',
            'userNotifications',
            'unreadCount'
        ));
    }
}
