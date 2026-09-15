<?php

namespace Tests\Feature;

use App\Actions\Connection\AcceptConnectionRequest;
use App\Actions\Connection\CreateConnectionRequest;
use App\Actions\Connection\DeclineConnectionRequest;
use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Events\ConnectionActivated;
use App\Models\ConnectionRequest;
use App\Models\Payment;
use App\Models\User;
use App\Notifications\ConnectionAcceptedNotification;
use App\Notifications\ConnectionActivatedNotification;
use App\Notifications\ConnectionDeclinedNotification;
use App\Notifications\ConnectionRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RealtimeConnectionNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $initiator;
    protected User $recipient;
    protected User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => '000feb6d22fce6026c29',
            'broadcasting.connections.pusher.secret' => 'dc8b69d991c084ca0db5',
            'broadcasting.connections.pusher.app_id' => '2194544',
            'broadcasting.connections.pusher.options' => [
                'cluster' => 'eu',
                'host' => 'api-eu.pusher.com',
                'port' => 443,
                'scheme' => 'https',
                'useTLS' => true,
            ],
        ]);

        $this->app->forgetInstance('Illuminate\Contracts\Broadcasting\Factory');
        $this->app->forgetInstance('Illuminate\Contracts\Broadcasting\Broadcaster');
        require base_path('routes/channels.php');

        $this->initiator = User::factory()->create(['name' => 'Initiator User']);
        $this->recipient = User::factory()->create(['name' => 'Recipient User']);
        $this->otherUser = User::factory()->create(['name' => 'Other User']);
    }

    /** @test */
    public function user_can_authorize_their_own_private_user_channel()
    {
        $response = $this->actingAs($this->initiator)->postJson('/broadcasting/auth', [
            'channel_name' => "private-user.{$this->initiator->id}",
            'socket_id' => '1234.5678',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['auth']);
    }

    /** @test */
    public function other_user_cannot_authorize_another_users_private_user_channel()
    {
        $response = $this->actingAs($this->otherUser)->postJson('/broadcasting/auth', [
            'channel_name' => "private-user.{$this->initiator->id}",
            'socket_id' => '1234.5678',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function creating_a_connection_request_broadcasts_notification_to_recipient_user_channel()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->initiator->id,
            'recipient_id' => $this->recipient->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
            'initial_message' => 'Please help me',
        ]);

        event(new \App\Events\ConnectionRequestCreated($connection->id));

        Notification::assertSentTo(
            $this->recipient,
            ConnectionRequestNotification::class,
            function ($notification, $channels) use ($connection) {
                return in_array('broadcast', $channels)
                    && $notification->broadcastOn()[0]->name === 'private-user.' . $this->recipient->id
                    && $notification->toArray($this->recipient)['type'] === 'connection_request';
            }
        );
    }

    /** @test */
    public function accepting_a_connection_request_broadcasts_notification_to_initiator_user_channel()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->initiator->id,
            'recipient_id' => $this->recipient->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);

        $connection->update([
            'status' => ConnectionStatus::Accepted,
            'accepted_at' => now(),
        ]);

        event(new \App\Events\ConnectionRequestAccepted($connection->id));

        Notification::assertSentTo(
            $this->initiator,
            ConnectionAcceptedNotification::class,
            function ($notification, $channels) use ($connection) {
                return in_array('broadcast', $channels)
                    && $notification->broadcastOn()[0]->name === 'private-user.' . $this->initiator->id
                    && $notification->toArray($this->initiator)['type'] === 'connection_accepted';
            }
        );
    }

    /** @test */
    public function declining_a_connection_request_broadcasts_notification_to_initiator_user_channel()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->initiator->id,
            'recipient_id' => $this->recipient->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);

        $connection->update([
            'status' => ConnectionStatus::Declined,
            'declined_at' => now(),
        ]);

        event(new \App\Events\ConnectionRequestDeclined($connection->id));

        Notification::assertSentTo(
            $this->initiator,
            ConnectionDeclinedNotification::class,
            function ($notification, $channels) use ($connection) {
                return in_array('broadcast', $channels)
                    && $notification->broadcastOn()[0]->name === 'private-user.' . $this->initiator->id
                    && $notification->toArray($this->initiator)['type'] === 'connection_declined';
            }
        );
    }

    /** @test */
    public function activating_a_connection_broadcasts_notifications_to_both_users_user_channels()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->initiator->id,
            'recipient_id' => $this->recipient->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
        ]);

        $payment = Payment::create([
            'user_id' => $this->initiator->id,
            'connection_request_id' => $connection->id,
            'amount' => 1000,
            'currency' => 'NGN',
            'status' => 'successful',
            'reference' => 'PAY-TEST-' . time(),
        ]);

        event(new ConnectionActivated($connection->id, $payment->id));

        Notification::assertSentTo(
            $this->initiator,
            ConnectionActivatedNotification::class,
            function ($notification, $channels) {
                return in_array('broadcast', $channels)
                    && $notification->broadcastOn()[0]->name === 'private-user.' . $this->initiator->id;
            }
        );

        Notification::assertSentTo(
            $this->recipient,
            ConnectionActivatedNotification::class,
            function ($notification, $channels) {
                return in_array('broadcast', $channels)
                    && $notification->broadcastOn()[0]->name === 'private-user.' . $this->recipient->id;
            }
        );
    }
}
