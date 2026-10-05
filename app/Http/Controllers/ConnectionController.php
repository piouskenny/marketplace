<?php

namespace App\Http\Controllers;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Models\ConnectionRequest;
use App\Models\Opportunity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConnectionController extends Controller
{
    /**
     * Submit an application & connection request to an opportunity poster
     */
    public function apply(Request $request, $opportunityId)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Enforce onboarding/profile completion before applying
        if (!$user->isProfileComplete()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Complete your profile before applying for opportunities.',
                    'redirect' => route('onboarding'),
                ], 403);
            }

            return redirect()->to(route('onboarding'))
                ->with('error', 'Complete your profile before applying for opportunities.');
        }

        // Check if DB opportunity exists
        $opportunity = Opportunity::find($opportunityId);

        // Block applying to your own opportunity
        if ($opportunity && $opportunity->user_id === $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You cannot apply to an opportunity that you created yourself.',
                ], 422);
            }
            return redirect()->back()->with('error', 'You cannot apply to an opportunity that you created yourself.');
        }

        $recipientId = $opportunity ? $opportunity->user_id : 1;

        $note = $request->input('note', 'I am interested in this opportunity and would like to connect.');

        $connectionRequest = ConnectionRequest::create([
            'initiator_id' => $user->id,
            'recipient_id' => $recipientId,
            'opportunity_id' => $opportunity ? $opportunity->id : null,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
            'initial_message' => $note,
        ]);

        \App\Events\ConnectionRequestCreated::dispatch($connectionRequest->id);

        // Push pending application to session array for demo preview
        $pendingApps = session()->get('pending_applications', []);
        $jobTitle = $opportunity ? $opportunity->title : $request->input('job_title', 'Opportunity Application');
        $jobCategory = $opportunity && $opportunity->category ? $opportunity->category->name : $request->input('job_category', 'General Service');

        $pendingApps[] = [
            'id' => $connectionRequest->id,
            'name' => $opportunity && $opportunity->user ? $opportunity->user->name : 'Job Owner / Household',
            'title' => $jobTitle,
            'category' => $jobCategory,
            'avatar' => asset('images/avatars/babajide.png'),
            'location' => $opportunity ? $opportunity->location : 'Lagos, Nigeria',
            'status' => 'pending',
            'last_time' => 'Just now',
            'note' => $note,
            'unread' => 0,
        ];

        session()->put('pending_applications', $pendingApps);

        return redirect()->to(url('/dashboard/messages?applied=1&conn_id=' . $connectionRequest->id))
            ->with('status', 'Your application and connection request for "' . $jobTitle . '" has been sent to the owner! It is now sitting as Pending on your Messages page.');
    }

    /**
     * Submit a direct Connect & Hire request to a professional / tutor
     */
    public function hireDirect(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Enforce onboarding/profile completion before connecting
        if (!$user->isProfileComplete()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Complete your profile before connecting with professionals.',
                    'redirect' => route('onboarding'),
                ], 403);
            }

            return redirect()->to(route('onboarding'))
                ->with('error', 'Complete your profile before connecting with professionals.');
        }

        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'brief' => 'nullable|string|max:1000',
        ]);

        $recipientId = (int) $request->input('recipient_id');

        // Block connecting with yourself
        if ($recipientId === $user->id) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'You cannot send a connection request to yourself.',
                ], 422);
            }
            return redirect()->back()->with('error', 'You cannot send a connection request to yourself.');
        }

        $recipient = \App\Models\User::findOrFail($recipientId);
        $note = trim($request->input('brief', '')) ?: $request->input('note', 'I am interested in hiring your services and would like to connect.');

        // Check if an existing connection request already exists between these 2 users (without opportunity or pending)
        $existing = ConnectionRequest::where('initiator_id', $user->id)
            ->where('recipient_id', $recipientId)
            ->whereNull('opportunity_id')
            ->whereIn('status', [ConnectionStatus::Pending, ConnectionStatus::Accepted, ConnectionStatus::Connected])
            ->first();

        if ($existing) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'You already have an active or pending connection request with ' . $recipient->name . '.',
                    'redirect' => url('/dashboard/messages?conn_id=' . $existing->id),
                    'connection_id' => $existing->id,
                ]);
            }
            return redirect()->to(url('/dashboard/messages?conn_id=' . $existing->id))
                ->with('status', 'You already have an active or pending connection request with ' . $recipient->name . '.');
        }

        $connectionRequest = ConnectionRequest::create([
            'initiator_id' => $user->id,
            'recipient_id' => $recipientId,
            'opportunity_id' => null,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Pending,
            'initial_message' => $note,
        ]);

        \App\Events\ConnectionRequestCreated::dispatch($connectionRequest->id);

        // Push pending application to session array for fallback preview
        $pendingApps = session()->get('pending_applications', []);
        $pendingApps[] = [
            'id' => $connectionRequest->id,
            'name' => $recipient->name,
            'title' => 'Connect & Hire Request',
            'category' => $recipient->professionalProfile?->category?->name ?? 'Direct Hire',
            'avatar' => $recipient->avatar_url,
            'location' => $recipient->location ?? 'Nigeria',
            'status' => 'pending',
            'last_time' => 'Just now',
            'note' => $note,
            'unread' => 0,
        ];
        session()->put('pending_applications', $pendingApps);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Connection request sent to ' . $recipient->name . '! Your request is currently pending their approval.',
                'redirect' => url('/dashboard/messages?applied=1&conn_id=' . $connectionRequest->id),
                'connection_id' => $connectionRequest->id,
            ]);
        }

        return redirect()->to(url('/dashboard/messages?applied=1&conn_id=' . $connectionRequest->id))
            ->with('status', 'Your connection request has been sent to ' . $recipient->name . '! It is currently pending their approval. Once accepted, you can make the ₦1,000 payment to unlock direct chat & contact details.');
    }

    /**
     * Accept an incoming connection request
     */
    public function accept(Request $request, $id)
    {
        $user = Auth::user();

        $cleanId = str_replace('conn_', '', $id);
        $conn = ConnectionRequest::find($cleanId);

        if ($conn) {
            $this->authorize('accept', $conn);

            $conn->update([
                'status' => ConnectionStatus::Accepted,
                'accepted_at' => now(),
            ]);

            \App\Events\ConnectionRequestAccepted::dispatch($conn->id);
        }

        $sessionAccepted = session()->get('accepted_connections', []);
        $sessionAccepted[] = (int) $cleanId;
        session()->put('accepted_connections', array_unique($sessionAccepted));

        // Sync pending applications status in session
        $pendingApps = session()->get('pending_applications', []);
        foreach ($pendingApps as &$app) {
            if (isset($app['id']) && (string) $app['id'] === (string) $cleanId) {
                $app['status'] = 'accepted';
            }
        }
        session()->put('pending_applications', $pendingApps);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Connection request accepted successfully! Direct messaging is now unlocked.',
                'status' => 'accepted',
            ]);
        }

        return redirect()->to(url('/dashboard/messages?conn_id=' . $cleanId))
            ->with('status', 'Connection request accepted! Direct messaging is now unlocked.');
    }

    /**
     * Decline / Reject an incoming connection request
     */
    public function reject(Request $request, $id)
    {
        $user = Auth::user();

        $cleanId = str_replace('conn_', '', $id);
        $conn = ConnectionRequest::find($cleanId);

        if ($conn) {
            $this->authorize('decline', $conn);

            $conn->update([
                'status' => ConnectionStatus::Declined,
                'declined_at' => now(),
            ]);

            \App\Events\ConnectionRequestDeclined::dispatch($conn->id);
        }

        $sessionDeclined = session()->get('declined_connections', []);
        $sessionDeclined[] = (int) $cleanId;
        session()->put('declined_connections', array_unique($sessionDeclined));

        // Sync pending applications status in session
        $pendingApps = session()->get('pending_applications', []);
        foreach ($pendingApps as &$app) {
            if (isset($app['id']) && (string) $app['id'] === (string) $cleanId) {
                $app['status'] = 'declined';
            }
        }
        session()->put('pending_applications', $pendingApps);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Connection request declined.',
                'status' => 'declined',
            ]);
        }

        return redirect()->to(url('/dashboard/messages'))
            ->with('status', 'Connection request declined.');
    }

    /**
     * Initialize connection fee payment (₦1,000) via Paystack
     */
    public function pay(Request $request, $id, \App\Actions\Payment\InitializeConnectionPayment $initializeAction)
    {
        $user = Auth::user();
        $cleanId = (int) str_replace('conn_', '', $id);
        $conn = ConnectionRequest::findOrFail($cleanId);

        $this->authorize('pay', $conn);

        try {
            $initData = $initializeAction->execute($conn->id, $user->id);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment initialized successfully.',
                    'authorization_url' => $initData['authorization_url'],
                    'reference' => $initData['reference'],
                    'access_code' => $initData['access_code'] ?? null,
                    'status' => 'payment_pending',
                ]);
            }

            return redirect()->away($initData['authorization_url']);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Payment initialization error: ' . $e->getMessage(), ['connection_id' => $conn->id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage() ?: 'Could not initialize payment. Please try again.',
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage() ?: 'Could not initialize payment.');
        }
    }

    /**
     * Verify payment status server-side after Paystack payment (AJAX / API)
     */
    public function verifyPayment(Request $request, \App\Actions\Payment\VerifyAndActivatePayment $verifyAction)
    {
        $request->validate([
            'reference' => 'required|string',
        ]);

        $reference = $request->input('reference');

        try {
            $conn = $verifyAction->execute($reference);

            return response()->json([
                'success' => true,
                'message' => 'Payment verified! Direct messaging is now permanently unlocked.',
                'status' => 'connected',
                'conversation_id' => $conn->conversation?->id,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Payment verification error: ' . $e->getMessage(), ['reference' => $reference]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'Payment verification failed.',
            ], 422);
        }
    }

    /**
     * Handle Paystack browser callback redirect
     */
    public function handleCallback(Request $request, \App\Actions\Payment\VerifyAndActivatePayment $verifyAction)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');

        if (!$reference) {
            return redirect()->to(url('/dashboard/history?tab=payments'))
                ->with('error', 'Payment attempt was cancelled or no transaction reference was returned. You can retry paying anytime from your Payment History.');
        }

        try {
            $conn = $verifyAction->execute($reference);

            // Sync session paid connections for immediate preview
            $paidConns = session()->get('paid_connections', []);
            $paidConns[] = (int) $conn->id;
            session()->put('paid_connections', array_unique($paidConns));

            return redirect()->to(url('/dashboard/messages?conn_id=' . $conn->id))
                ->with('status', 'Payment of ₦1,000 verified successfully! Direct chat & contact details are now unlocked.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Callback verification failed: ' . $e->getMessage(), ['reference' => $reference]);

            // Update existing payment status to Failed if present
            $payment = \App\Models\Payment::where('reference', $reference)->first();
            if ($payment && $payment->status !== \App\Enums\PaymentStatus::Successful) {
                $payment->update([
                    'status' => \App\Enums\PaymentStatus::Failed,
                    'metadata' => array_merge($payment->metadata ?? [], ['error_message' => $e->getMessage()]),
                ]);
            }

            return redirect()->to(url('/dashboard/history?tab=payments'))
                ->with('error', 'Payment attempt failed: ' . $e->getMessage() . '. You can click "Retry Payment" anytime below to try paying again.');
        }
    }

    /**
     * Handle Paystack asynchronous Webhook event
     */
    public function handleWebhook(Request $request, \App\Actions\Payment\VerifyAndActivatePayment $verifyAction)
    {
        $signature = $request->header('x-paystack-signature');
        $secretKey = config('services.paystack.secret_key');
        $payload = $request->getContent();

        if (!$signature || !$secretKey || !hash_equals(hash_hmac('sha512', $payload, $secretKey), $signature)) {
            \Illuminate\Support\Facades\Log::warning('Paystack webhook signature verification failed.');
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);

        if (isset($event['event']) && $event['event'] === 'charge.success') {
            $reference = $event['data']['reference'] ?? null;
            if ($reference) {
                try {
                    $verifyAction->execute($reference);
                    return response()->json(['status' => 'success', 'message' => 'Payment processed successfully.']);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Paystack Webhook execution error: ' . $e->getMessage(), ['reference' => $reference]);
                    return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
                }
            }
        }

        return response()->json(['status' => 'ignored'], 200);
    }


    /**
     * Get real-time connection status list for polling
     */
    public function status(Request $request)
    {
        $user = Auth::user();

        $sessionAccepted = session()->get('accepted_connections', []);
        $sessionDeclined = session()->get('declined_connections', []);
        $paidConns = session()->get('paid_connections', []);
        $pendingApps = session()->get('pending_applications', []);

        $dbConnections = ConnectionRequest::where('initiator_id', $user->id)
            ->orWhere('recipient_id', $user->id)
            ->get();

        $statuses = [];

        foreach ($pendingApps as $app) {
            $connId = (int) $app['id'];
            $dbConn = $dbConnections->firstWhere('id', $connId);
            $st = $dbConn ? ($dbConn->status instanceof ConnectionStatus ? $dbConn->status->value : (string)$dbConn->status) : ($app['status'] ?? 'pending');
            if (in_array($connId, $paidConns)) {
                $st = 'connected';
            } elseif (in_array($connId, $sessionAccepted)) {
                $st = 'accepted';
            } elseif (in_array($connId, $sessionDeclined)) {
                $st = 'declined';
            }
            $statuses[] = [
                'id' => $connId,
                'status' => $st,
            ];
        }

        foreach ($dbConnections as $conn) {
            $dbSt = $conn->status instanceof ConnectionStatus ? $conn->status->value : (string) $conn->status;
            if ($dbSt === ConnectionStatus::Connected->value || $conn->conversation) {
                $st = 'connected';
            } elseif ($dbSt === ConnectionStatus::Declined->value) {
                $st = 'declined';
            } elseif ($dbSt === ConnectionStatus::Accepted->value) {
                $st = in_array($conn->id, $paidConns) ? 'connected' : 'accepted';
            } else {
                $st = 'pending';
                if (in_array($conn->id, $paidConns)) {
                    $st = 'connected';
                } elseif (in_array($conn->id, $sessionAccepted)) {
                    $st = 'accepted';
                } elseif (in_array($conn->id, $sessionDeclined)) {
                    $st = 'declined';
                }
            }
            $statuses[] = [
                'id' => $conn->id,
                'status' => $st,
            ];
        }

        $demoStatus = 'pending';
        if (in_array(901, $paidConns)) {
            $demoStatus = 'connected';
        } elseif (in_array(901, $sessionAccepted)) {
            $demoStatus = 'accepted';
        } elseif (in_array(901, $sessionDeclined)) {
            $demoStatus = 'declined';
        }
        $statuses[] = [
            'id' => 901,
            'status' => $demoStatus,
        ];

        return response()->json([
            'success' => true,
            'connections' => $statuses,
        ]);
    }

    /**
     * Mark a connection request and its conversation messages as read.
     */
    public function markRead(Request $request, $id)
    {
        $user = Auth::user();
        $cleanId = str_replace('conn_', '', $id);
        $conn = ConnectionRequest::with('conversation')->find($cleanId);

        if (!$conn) {
            return response()->json(['success' => false, 'message' => 'Connection not found.'], 404);
        }

        if ((int) $conn->initiator_id !== (int) $user->id && (int) $conn->recipient_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $conn->update(['read_at' => now()]);

        if ($conn->conversation) {
            app(\App\Actions\Messages\MarkMessagesAsReadAction::class)->execute($conn->conversation, $user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Marked as read.',
        ]);
    }

    /**
     * Delete a chat conversation and soft delete its connection request.
     */
    public function deleteChat(Request $request, $id)
    {
        $user = Auth::user();
        $cleanId = str_replace('conn_', '', $id);
        $conn = ConnectionRequest::with('conversation')->find($cleanId);

        if (!$conn) {
            return response()->json(['success' => false, 'message' => 'Connection not found.'], 404);
        }

        if ((int) $conn->initiator_id !== (int) $user->id && (int) $conn->recipient_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($conn->conversation) {
            $conn->conversation->messages()->delete();
            $conn->conversation->delete();
        }

        $conn->delete();

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully.',
        ]);
    }
}
