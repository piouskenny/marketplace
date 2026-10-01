<?php

namespace App\Notifications;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnreadMessageReminderNotification extends Notification
{
    use Queueable;

    public Conversation $conversation;
    public int $unreadCount;
    public User $sender;

    public function __construct(Conversation $conversation, int $unreadCount, User $sender)
    {
        $this->conversation = $conversation;
        $this->unreadCount = $unreadCount;
        $this->sender = $sender;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $senderName = $this->sender->name ?? 'A user';
        $connId = $this->conversation->connection_request_id;
        $targetUrl = url('/dashboard/messages' . ($connId ? '?conn_id=' . $connId : ''));

        $msgText = $this->unreadCount > 1
            ? "You have {$this->unreadCount} unread messages from {$senderName} on Skill Link NG waiting for your reply."
            : "You have an unread message from {$senderName} on Skill Link NG waiting for your reply.";

        return (new MailMessage)
            ->subject("Unread Message Reminder: {$senderName} on Skill Link NG")
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($msgText)
            ->action('View Messages on Skill Link NG', $targetUrl)
            ->line('Thank you for using Skill Link NG!');
    }

    public function toArray(object $notifiable): array
    {
        $senderName = $this->sender->name ?? 'A user';
        $connId = $this->conversation->connection_request_id;

        return [
            'type' => 'unread_message_reminder',
            'title' => "Unread Message Reminder",
            'message' => "You have {$this->unreadCount} unread message(s) from {$senderName}.",
            'url' => url('/dashboard/messages' . ($connId ? '?conn_id=' . $connId : '')),
            'icon' => 'message-square',
            'conversation_id' => $this->conversation->id,
            'connection_request_id' => $connId,
            'unread_count' => $this->unreadCount,
            'created_at' => now()->toIso8601String(),
        ];
    }
}
