<?php

namespace App\Http\Controllers;

use App\Actions\Messages\MarkMessagesAsReadAction;
use App\Actions\Messages\SendMessageAction;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ChatController extends Controller
{
    /**
     * Get list of conversations for current user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $conversations = Conversation::with(['connectionRequest.initiator', 'connectionRequest.recipient', 'connectionRequest.opportunity', 'latestMessage'])
            ->whereHas('connectionRequest', function ($q) use ($user) {
                $q->where('status', \App\Enums\ConnectionStatus::Connected)
                  ->where(function ($sub) use ($user) {
                      $sub->where('initiator_id', $user->id)
                          ->orWhere('recipient_id', $user->id);
                  });
            })
            ->orderBy('last_message_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'conversations' => $conversations,
        ]);
    }

    /**
     * Get messages for a specific conversation.
     */
    public function show(Request $request, Conversation $conversation, MarkMessagesAsReadAction $markReadAction)
    {
        Gate::authorize('view', $conversation);

        $user = Auth::user();

        // Mark incoming messages as read
        $markReadAction->execute($conversation, $user);

        $messages = $conversation->messages()
            ->with('sender:id,name,avatar_url')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'conversation_id' => $conversation->id,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a message within a conversation.
     */
    public function store(Request $request, Conversation $conversation, SendMessageAction $sendMessageAction)
    {
        Gate::authorize('sendMessage', $conversation);

        $request->validate([
            'body' => 'required|string|min:1|max:5000',
            'client_msg_id' => 'nullable|string|max:100',
        ]);

        $sender = Auth::user();

        $message = $sendMessageAction->execute(
            $conversation,
            $sender,
            $request->input('body'),
            $request->input('client_msg_id')
        );

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'client_msg_id' => $request->input('client_msg_id'),
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'body' => $message->body,
                'read_at' => $message->read_at ? $message->read_at->toIso8601String() : null,
                'created_at' => $message->created_at->toIso8601String(),
                'time_formatted' => $message->created_at->toIso8601String(),
            ],
        ]);
    }

    /**
     * Mark unread messages in conversation as read.
     */
    public function markRead(Request $request, Conversation $conversation, MarkMessagesAsReadAction $markReadAction)
    {
        Gate::authorize('view', $conversation);

        $user = Auth::user();

        $updatedCount = $markReadAction->execute($conversation, $user);

        return response()->json([
            'success' => true,
            'read_count' => $updatedCount,
        ]);
    }

    /**
     * Send a message associated with a connection request ID (auto-creates or resolves Conversation).
     */
    public function storeByConnection(Request $request, \App\Models\ConnectionRequest $connectionRequest, SendMessageAction $sendMessageAction, \App\Actions\Conversations\CreateConversationAction $createConversationAction)
    {
        $user = Auth::user();
        if ((int) $connectionRequest->initiator_id !== (int) $user->id && (int) $connectionRequest->recipient_id !== (int) $user->id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $statusValue = $connectionRequest->status instanceof \App\Enums\ConnectionStatus 
            ? $connectionRequest->status->value 
            : (string) $connectionRequest->status;

        if ($statusValue !== \App\Enums\ConnectionStatus::Connected->value) {
            return response()->json(['success' => false, 'message' => 'Messaging is disabled because connection is not in Connected status.'], 403);
        }

        $conversation = $createConversationAction->execute($connectionRequest);
        return $this->store($request, $conversation, $sendMessageAction);
    }

    /**
     * Delete a conversation and its messages.
     */
    public function destroy(Request $request, Conversation $conversation)
    {
        Gate::authorize('view', $conversation);

        $conn = $conversation->connectionRequest;

        $conversation->messages()->delete();
        $conversation->delete();

        if ($conn) {
            $conn->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Chat deleted successfully.',
        ]);
    }
}
