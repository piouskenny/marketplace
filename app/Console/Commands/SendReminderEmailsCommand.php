<?php

namespace App\Console\Commands;

use App\Enums\ConnectionStatus;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ReminderLog;
use App\Notifications\PaymentPendingReminderNotification;
use App\Notifications\PendingRequestReminderNotification;
use App\Notifications\UnreadMessageReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SendReminderEmailsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send 24-hour reminder emails for unread messages, pending requests, and pending payments.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting reminder processing...');

        $unreadCount = $this->processUnreadMessageReminders();
        $pendingCount = $this->processPendingRequestReminders();
        $paymentCount = $this->processPaymentPendingReminders();

        $this->info("Reminder processing completed. Unread: {$unreadCount}, Pending: {$pendingCount}, Payment: {$paymentCount}");

        return Command::SUCCESS;
    }

    /**
     * Process 24-hour unread message reminders grouped by conversation and recipient.
     */
    protected function processUnreadMessageReminders(): int
    {
        $sentCount = 0;
        $cutoff = now()->subHours(24);

        // Find conversations that have unread messages >= 24h old and connected connection status
        $conversations = Conversation::whereHas('connectionRequest', function ($q) {
            $q->where('status', ConnectionStatus::Connected);
        })->whereHas('messages', function ($q) use ($cutoff) {
            $q->whereNull('read_at')
              ->where('created_at', '<=', $cutoff);
        })->with(['connectionRequest', 'messages' => function ($q) {
            $q->whereNull('read_at')->orderBy('created_at', 'asc');
        }])->get();

        foreach ($conversations as $conversation) {
            $conn = $conversation->connectionRequest;
            if (!$conn || (string) ($conn->status->value ?? $conn->status) !== ConnectionStatus::Connected->value) {
                continue;
            }

            // Group unread messages by recipient
            $unreadMessages = $conversation->messages->whereNull('read_at');
            if ($unreadMessages->isEmpty()) {
                continue;
            }

            // Determine qualifying unread messages (>= 24h old)
            $qualifyingMessages = $unreadMessages->filter(function ($msg) use ($cutoff) {
                return $msg->created_at <= $cutoff;
            });

            if ($qualifyingMessages->isEmpty()) {
                continue;
            }

            // Group qualifying messages by recipient ID
            $recipientGroups = [];
            foreach ($qualifyingMessages as $msg) {
                $recipientId = ((int) $msg->sender_id === (int) $conn->initiator_id)
                    ? (int) $conn->recipient_id
                    : (int) $conn->initiator_id;

                if (!isset($recipientGroups[$recipientId])) {
                    $recipientGroups[$recipientId] = [
                        'messages' => [],
                        'sender' => $msg->sender,
                    ];
                }
                $recipientGroups[$recipientId]['messages'][] = $msg;
            }

            foreach ($recipientGroups as $recipientId => $groupData) {
                // Re-verify that unread messages still exist for this recipient
                $recipientUser = \App\Models\User::find($recipientId);
                if (!$recipientUser) {
                    continue;
                }

                // Check if unread messages are still unread in database
                $currentUnreadCount = Message::where('conversation_id', $conversation->id)
                    ->where('sender_id', '!=', $recipientId)
                    ->whereNull('read_at')
                    ->count();

                if ($currentUnreadCount === 0) {
                    continue;
                }

                // Attempt atomic reminder log creation
                try {
                    $log = ReminderLog::create([
                        'user_id' => $recipientUser->id,
                        'remindable_type' => 'conversation',
                        'remindable_id' => $conversation->id,
                        'reminder_type' => 'unread_message_24h',
                        'sent_at' => now(),
                    ]);
                } catch (QueryException $e) {
                    // Unique constraint violation means reminder was already sent
                    continue;
                }

                // Synchronously send notification
                try {
                    $sender = $groupData['sender'] ?? $conversation->messages->first()?->sender;
                    $recipientUser->notify(new UnreadMessageReminderNotification($conversation, $currentUnreadCount, $sender));
                    $sentCount++;
                } catch (\Throwable $e) {
                    $log->delete();
                    Log::error("Failed to send unread_message_24h notification to user {$recipientUser->id}: " . $e->getMessage());
                }
            }
        }

        return $sentCount;
    }

    /**
     * Process 24-hour pending connection request reminders.
     */
    protected function processPendingRequestReminders(): int
    {
        $sentCount = 0;
        $cutoff = now()->subHours(24);

        ConnectionRequest::where('status', ConnectionStatus::Pending)
            ->where('created_at', '<=', $cutoff)
            ->whereNull('accepted_at')
            ->whereNull('declined_at')
            ->whereNull('cancelled_at')
            ->with(['recipient', 'initiator', 'opportunity'])
            ->chunk(100, function ($requests) use (&$sentCount) {
                foreach ($requests as $conn) {
                    // Re-check fresh status from database
                    $freshConn = ConnectionRequest::find($conn->id);
                    if (!$freshConn || (string) ($freshConn->status->value ?? $freshConn->status) !== ConnectionStatus::Pending->value) {
                        continue;
                    }
                    if ($freshConn->accepted_at || $freshConn->declined_at || $freshConn->cancelled_at) {
                        continue;
                    }

                    $recipient = $freshConn->recipient;
                    if (!$recipient) {
                        continue;
                    }

                    // Attempt atomic reminder log creation
                    try {
                        $log = ReminderLog::create([
                            'user_id' => $recipient->id,
                            'remindable_type' => 'connection_request',
                            'remindable_id' => $freshConn->id,
                            'reminder_type' => 'pending_request_24h',
                            'sent_at' => now(),
                        ]);
                    } catch (QueryException $e) {
                        // Unique constraint violation: already sent
                        continue;
                    }

                    // Synchronously send notification
                    try {
                        $recipient->notify(new PendingRequestReminderNotification($freshConn));
                        $sentCount++;
                    } catch (\Throwable $e) {
                        $log->delete();
                        Log::error("Failed to send pending_request_24h notification to user {$recipient->id}: " . $e->getMessage());
                    }
                }
            });

        return $sentCount;
    }

    /**
     * Process 24-hour payment pending reminders for connection initiators.
     */
    protected function processPaymentPendingReminders(): int
    {
        $sentCount = 0;
        $cutoff = now()->subHours(24);

        ConnectionRequest::whereIn('status', [ConnectionStatus::Accepted, ConnectionStatus::PaymentPending])
            ->where('accepted_at', '<=', $cutoff)
            ->whereNull('connected_at')
            ->whereNull('cancelled_at')
            ->with(['initiator', 'recipient', 'opportunity'])
            ->chunk(100, function ($requests) use (&$sentCount) {
                foreach ($requests as $conn) {
                    // Re-check fresh status from database
                    $freshConn = ConnectionRequest::find($conn->id);
                    if (!$freshConn) {
                        continue;
                    }

                    $statusVal = (string) ($freshConn->status->value ?? $freshConn->status);
                    if (!in_array($statusVal, [ConnectionStatus::Accepted->value, ConnectionStatus::PaymentPending->value], true)) {
                        continue;
                    }
                    if ($freshConn->connected_at || $freshConn->cancelled_at) {
                        continue;
                    }

                    $initiator = $freshConn->initiator;
                    if (!$initiator) {
                        continue;
                    }

                    // Attempt atomic reminder log creation
                    try {
                        $log = ReminderLog::create([
                            'user_id' => $initiator->id,
                            'remindable_type' => 'connection_request',
                            'remindable_id' => $freshConn->id,
                            'reminder_type' => 'payment_pending_24h',
                            'sent_at' => now(),
                        ]);
                    } catch (QueryException $e) {
                        // Unique constraint violation: already sent
                        continue;
                    }

                    // Synchronously send notification
                    try {
                        $initiator->notify(new PaymentPendingReminderNotification($freshConn));
                        $sentCount++;
                    } catch (\Throwable $e) {
                        $log->delete();
                        Log::error("Failed to send payment_pending_24h notification to user {$initiator->id}: " . $e->getMessage());
                    }
                }
            });

        return $sentCount;
    }
}
