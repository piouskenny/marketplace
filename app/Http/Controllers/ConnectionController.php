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
        $user = Auth::user();

        // Check if DB opportunity exists
        $opportunity = Opportunity::find($opportunityId);

        // Block applying to your own opportunity
        if ($opportunity && $opportunity->user_id === $user->id) {
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
     * Accept an incoming connection request
     */
    public function accept(Request $request, $id)
    {
        $user = Auth::user();

        $cleanId = str_replace('conn_', '', $id);
        $conn = ConnectionRequest::find($cleanId);

        if ($conn) {
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
     * Process connection fee payment (₦1,000) and unlock direct chat (Status: Connected)
     */
    public function pay(Request $request, $id)
    {
        $user = Auth::user();

        $cleanId = str_replace('conn_', '', $id);
        $conn = ConnectionRequest::find($cleanId);
        $conversation = null;

        if ($conn) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($conn, $user, &$conversation) {
                $conn->update([
                    'status' => ConnectionStatus::Connected,
                    'connected_at' => now(),
                ]);

                $payment = \App\Models\Payment::create([
                    'user_id' => $user->id,
                    'connection_request_id' => $conn->id,
                    'reference' => 'CONN-FEE-' . strtoupper(uniqid()),
                    'provider' => 'paystack_demo',
                    'amount' => 1000,
                    'currency' => 'NGN',
                    'status' => \App\Enums\PaymentStatus::Successful,
                    'paid_at' => now(),
                ]);

                $createConversationAction = new \App\Actions\Conversations\CreateConversationAction();
                $conversation = $createConversationAction->execute($conn);

                \App\Events\ConnectionActivated::dispatch($conn->id, $payment->id);
            });
        }

        $paidConns = session()->get('paid_connections', []);
        $paidConns[] = (int) $cleanId;
        session()->put('paid_connections', array_unique($paidConns));

        $pendingApps = session()->get('pending_applications', []);
        foreach ($pendingApps as &$app) {
            if (isset($app['id']) && (string) $app['id'] === (string) $cleanId) {
                $app['status'] = 'connected';
            }
        }
        session()->put('pending_applications', $pendingApps);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Connection fee of ₦1,000 paid successfully! Direct messaging is now permanently unlocked.',
                'status' => 'connected',
                'conversation_id' => $conversation ? $conversation->id : null,
            ]);
        }

        return redirect()->to(url('/dashboard/messages?conn_id=' . $cleanId))
            ->with('status', 'Connection fee of ₦1,000 paid! Direct messaging is now unlocked.');
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
            $st = $app['status'] ?? 'pending';
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
            $st = $conn->status->value;
            if (in_array($conn->id, $paidConns)) {
                $st = 'connected';
            } elseif (in_array($conn->id, $sessionAccepted)) {
                $st = 'accepted';
            } elseif (in_array($conn->id, $sessionDeclined)) {
                $st = 'declined';
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
}
