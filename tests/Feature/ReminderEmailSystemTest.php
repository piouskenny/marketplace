<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ReminderLog;
use App\Models\User;
use App\Notifications\PaymentPendingReminderNotification;
use App\Notifications\PendingRequestReminderNotification;
use App\Notifications\UnreadMessageReminderNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ReminderEmailSystemTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create(['name' => 'User A']);
        $this->userB = User::factory()->create(['name' => 'User B']);
    }

    /** @test */
    public function unread_message_under_24h_does_not_trigger_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = Conversation::create([
            'connection_request_id' => $connection->id,
        ]);

        // Message created 10 hours ago (< 24h)
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Recent unread message',
        ]);
        $msg->created_at = now()->subHours(10);
        $msg->save();

        Artisan::call('reminders:send');

        Notification::assertNothingSent();
        $this->assertDatabaseMissing('reminder_logs', [
            'remindable_id' => $conversation->id,
            'reminder_type' => 'unread_message_24h',
        ]);
    }

    /** @test */
    public function unread_message_over_24h_triggers_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = Conversation::create([
            'connection_request_id' => $connection->id,
        ]);

        // Message created 25 hours ago
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Old unread message',
        ]);
        $msg->created_at = now()->subHours(25);
        $msg->save();

        Artisan::call('reminders:send');

        Notification::assertSentTo($this->userB, UnreadMessageReminderNotification::class);
        $this->assertDatabaseHas('reminder_logs', [
            'user_id' => $this->userB->id,
            'remindable_type' => 'conversation',
            'remindable_id' => $conversation->id,
            'reminder_type' => 'unread_message_24h',
        ]);
    }

    /** @test */
    public function read_message_does_not_trigger_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = Conversation::create([
            'connection_request_id' => $connection->id,
        ]);

        // Message created 30h ago but already read
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Read message',
            'read_at' => now()->subHours(5),
        ]);
        $msg->created_at = now()->subHours(30);
        $msg->save();

        Artisan::call('reminders:send');

        Notification::assertNothingSent();
    }

    /** @test */
    public function multiple_unread_messages_in_one_conversation_produce_one_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = Conversation::create([
            'connection_request_id' => $connection->id,
        ]);

        $msg1 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Unread msg 1',
        ]);
        $msg1->created_at = now()->subHours(30);
        $msg1->save();

        $msg2 = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Unread msg 2',
        ]);
        $msg2->created_at = now()->subHours(26);
        $msg2->save();

        Artisan::call('reminders:send');

        Notification::assertSentToTimes($this->userB, UnreadMessageReminderNotification::class, 1);
        $this->assertEquals(1, ReminderLog::where('remindable_id', $conversation->id)->count());
    }

    /** @test */
    public function different_conversations_independently_produce_reminders()
    {
        Notification::fake();

        $userC = User::factory()->create(['name' => 'User C']);

        $conn1 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
        ]);
        $conv1 = Conversation::create(['connection_request_id' => $conn1->id]);
        $msg1 = Message::create([
            'conversation_id' => $conv1->id,
            'sender_id' => $this->userA->id,
            'body' => 'Msg for B',
        ]);
        $msg1->created_at = now()->subHours(25);
        $msg1->save();

        $conn2 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $userC->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
        ]);
        $conv2 = Conversation::create(['connection_request_id' => $conn2->id]);
        $msg2 = Message::create([
            'conversation_id' => $conv2->id,
            'sender_id' => $this->userA->id,
            'body' => 'Msg for C',
        ]);
        $msg2->created_at = now()->subHours(25);
        $msg2->save();

        Artisan::call('reminders:send');

        Notification::assertSentTo($this->userB, UnreadMessageReminderNotification::class);
        Notification::assertSentTo($userC, UnreadMessageReminderNotification::class);
    }

    /** @test */
    public function existing_messages_remain_unread_after_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
        ]);
        $conversation = Conversation::create(['connection_request_id' => $connection->id]);
        $msg = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Message text',
        ]);
        $msg->created_at = now()->subHours(25);
        $msg->save();

        Artisan::call('reminders:send');

        $msg->refresh();
        $this->assertNull($msg->read_at);
    }

    /** @test */
    public function pending_connection_request_under_24h_does_not_trigger_reminder()
    {
        Notification::fake();

        $conn = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);
        $conn->created_at = now()->subHours(10);
        $conn->save();

        Artisan::call('reminders:send');

        Notification::assertNothingSent();
    }

    /** @test */
    public function pending_connection_request_over_24h_triggers_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);
        $connection->created_at = now()->subHours(25);
        $connection->save();

        Artisan::call('reminders:send');

        Notification::assertSentTo($this->userB, PendingRequestReminderNotification::class);
        $this->assertDatabaseHas('reminder_logs', [
            'user_id' => $this->userB->id,
            'remindable_type' => 'connection_request',
            'remindable_id' => $connection->id,
            'reminder_type' => 'pending_request_24h',
        ]);
    }

    /** @test */
    public function resolved_connection_requests_do_not_trigger_pending_reminder()
    {
        Notification::fake();

        // Accepted
        $c1 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
            'accepted_at' => now()->subHours(2),
        ]);
        $c1->created_at = now()->subHours(30);
        $c1->save();

        // Declined
        $c2 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Declined,
            'declined_at' => now()->subHours(2),
        ]);
        $c2->created_at = now()->subHours(30);
        $c2->save();

        // Cancelled
        $c3 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Cancelled,
            'cancelled_at' => now()->subHours(2),
        ]);
        $c3->created_at = now()->subHours(30);
        $c3->save();

        Artisan::call('reminders:send');

        Notification::assertNotSentTo($this->userB, PendingRequestReminderNotification::class);
    }

    /** @test */
    public function payment_pending_under_24h_does_not_trigger_reminder()
    {
        Notification::fake();

        $conn = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
            'accepted_at' => now()->subHours(10),
        ]);
        $conn->created_at = now()->subHours(25);
        $conn->save();

        Artisan::call('reminders:send');

        Notification::assertNothingSent();
    }

    /** @test */
    public function payment_pending_over_24h_triggers_initiator_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
            'accepted_at' => now()->subHours(25),
        ]);
        $connection->created_at = now()->subHours(30);
        $connection->save();

        Artisan::call('reminders:send');

        // Notification must go ONLY to initiator (userA)
        Notification::assertSentTo($this->userA, PaymentPendingReminderNotification::class);
        Notification::assertNotSentTo($this->userB, PaymentPendingReminderNotification::class);

        $this->assertDatabaseHas('reminder_logs', [
            'user_id' => $this->userA->id,
            'remindable_type' => 'connection_request',
            'remindable_id' => $connection->id,
            'reminder_type' => 'payment_pending_24h',
        ]);
    }

    /** @test */
    public function completed_payment_or_cancelled_does_not_trigger_payment_reminder()
    {
        Notification::fake();

        // Connected
        $c1 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'accepted_at' => now()->subHours(30),
            'connected_at' => now()->subHours(5),
        ]);
        $c1->created_at = now()->subHours(35);
        $c1->save();

        // Cancelled
        $c2 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Cancelled,
            'accepted_at' => now()->subHours(30),
            'cancelled_at' => now()->subHours(5),
        ]);
        $c2->created_at = now()->subHours(35);
        $c2->save();

        Artisan::call('reminders:send');

        Notification::assertNotSentTo($this->userA, PaymentPendingReminderNotification::class);
    }

    /** @test */
    public function two_scheduler_executions_do_not_duplicate_reminders()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);
        $connection->created_at = now()->subHours(25);
        $connection->save();

        // Execution 1
        Artisan::call('reminders:send');

        // Execution 2
        Artisan::call('reminders:send');

        Notification::assertSentToTimes($this->userB, PendingRequestReminderNotification::class, 1);
        $this->assertEquals(1, ReminderLog::where('remindable_id', $connection->id)->count());
    }

    /** @test */
    public function state_changed_immediately_before_processing_prevents_stale_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);
        $connection->created_at = now()->subHours(25);
        $connection->save();

        // User accepts request right before processing
        $connection->update([
            'status' => ConnectionStatus::Accepted,
            'accepted_at' => now(),
        ]);

        Artisan::call('reminders:send');

        Notification::assertNotSentTo($this->userB, PendingRequestReminderNotification::class);
    }

    /** @test */
    public function existing_connection_and_payment_state_remains_unchanged_after_reminder()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
            'accepted_at' => now()->subHours(25),
        ]);
        $connection->created_at = now()->subHours(30);
        $connection->save();

        Artisan::call('reminders:send');

        $connection->refresh();
        $this->assertEquals(ConnectionStatus::Accepted, $connection->status);
        $this->assertNull($connection->connected_at);
        $this->assertNull($connection->cancelled_at);
    }
}
