<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Events\ConnectionActivated;
use App\Events\ConnectionRequestAccepted;
use App\Events\ConnectionRequestCreated;
use App\Models\ConnectionRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_connection_request_created_dispatches_notification(): void
    {
        $initiator = User::factory()->create();
        $recipient = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $initiator->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Pending,
        ]);

        event(new ConnectionRequestCreated($connection->id));

        $this->assertEquals(1, $recipient->notifications()->count());
        $notification = $recipient->notifications()->first();
        $this->assertEquals('connection_request', $notification->data['type']);
    }

    public function test_connection_request_accepted_notifies_initiator(): void
    {
        $initiator = User::factory()->create();
        $recipient = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $initiator->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Accepted,
        ]);

        event(new ConnectionRequestAccepted($connection->id));

        $this->assertEquals(1, $initiator->notifications()->count());
        $notification = $initiator->notifications()->first();
        $this->assertEquals('connection_accepted', $notification->data['type']);
    }

    public function test_connection_activation_notifies_both_parties(): void
    {
        $initiator = User::factory()->create();
        $recipient = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $initiator->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Connected,
        ]);

        $payment = Payment::create([
            'user_id' => $initiator->id,
            'connection_request_id' => $connection->id,
            'reference' => 'TEST-REF-123',
            'provider' => 'paystack',
            'amount' => 1000,
            'currency' => 'NGN',
            'status' => \App\Enums\PaymentStatus::Successful,
        ]);

        event(new ConnectionActivated($connection->id, $payment->id));

        $this->assertEquals(1, $initiator->notifications()->count());
        $this->assertEquals(1, $recipient->notifications()->count());
    }

    public function test_user_can_list_and_mark_notifications_as_read(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $other->id,
            'recipient_id' => $user->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Pending,
        ]);

        event(new ConnectionRequestCreated($connection->id));

        $response = $this->actingAs($user)->getJson('/notifications');
        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'unread_count' => 1]);

        $notificationId = $user->unreadNotifications()->first()->id;

        $readResponse = $this->actingAs($user)->postJson("/notifications/{$notificationId}/read");
        $readResponse->assertStatus(200);
        $readResponse->assertJson(['unread_count' => 0]);
        $this->assertEquals(0, $user->unreadNotifications()->count());
    }
}
