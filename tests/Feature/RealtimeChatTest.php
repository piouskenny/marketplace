<?php

namespace Tests\Feature;

use App\Actions\Conversations\CreateConversationAction;
use App\Actions\Messages\SendMessageAction;
use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Events\MessageSent;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RealtimeChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $participantA;
    protected User $participantB;
    protected User $unrelatedUser;
    protected ConnectionRequest $connectedConnection;
    protected Conversation $connectedConversation;

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

        $this->participantA = User::factory()->create(['name' => 'Participant A']);
        $this->participantB = User::factory()->create(['name' => 'Participant B']);
        $this->unrelatedUser = User::factory()->create(['name' => 'Unrelated User']);

        $this->connectedConnection = ConnectionRequest::create([
            'initiator_id' => $this->participantA->id,
            'recipient_id' => $this->participantB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $this->connectedConversation = (new CreateConversationAction())->execute($this->connectedConnection);
    }

    /** @test */
    public function connection_participant_can_authorize_private_conversation_channel_when_connected()
    {
        $responseA = $this->actingAs($this->participantA)->postJson('/broadcasting/auth', [
            'channel_name' => "private-conversation.{$this->connectedConversation->id}",
            'socket_id' => '1234.5678',
        ]);
        $responseA->assertStatus(200)
            ->assertJsonStructure(['auth']);

        $responseB = $this->actingAs($this->participantB)->postJson('/broadcasting/auth', [
            'channel_name' => "private-conversation.{$this->connectedConversation->id}",
            'socket_id' => '1234.5678',
        ]);
        $responseB->assertStatus(200)
            ->assertJsonStructure(['auth']);
    }

    /** @test */
    public function unrelated_user_is_denied_private_conversation_channel_authorization()
    {
        $response = $this->actingAs($this->unrelatedUser)->postJson('/broadcasting/auth', [
            'channel_name' => "private-conversation.{$this->connectedConversation->id}",
            'socket_id' => '1234.5678',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function participant_is_denied_channel_authorization_when_connection_is_not_connected()
    {
        $pendingConnection = ConnectionRequest::create([
            'initiator_id' => $this->participantA->id,
            'recipient_id' => $this->participantB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
        ]);

        $pendingConversation = Conversation::create([
            'connection_request_id' => $pendingConnection->id,
        ]);

        $response = $this->actingAs($this->participantA)->postJson('/broadcasting/auth', [
            'channel_name' => "private-conversation.{$pendingConversation->id}",
            'socket_id' => '1234.5678',
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function sending_a_message_persists_it_before_broadcasting()
    {
        Event::fake([MessageSent::class]);

        $action = new SendMessageAction();
        $message = $action->execute($this->connectedConversation, $this->participantA, 'Persistent message test');

        // Assert message exists in MySQL
        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'conversation_id' => $this->connectedConversation->id,
            'sender_id' => $this->participantA->id,
            'body' => 'Persistent message test',
        ]);

        // Assert broadcast event dispatched after persistence
        Event::assertDispatched(MessageSent::class, function ($event) use ($message) {
            return $event->message->id === $message->id;
        });
    }

    /** @test */
    public function message_sent_broadcasts_on_correct_private_conversation_channel()
    {
        $message = Message::create([
            'conversation_id' => $this->connectedConversation->id,
            'sender_id' => $this->participantA->id,
            'body' => 'Broadcasting channel check',
        ]);

        $event = new MessageSent($message);
        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals('private-conversation.' . $this->connectedConversation->id, $channels[0]->name);
        $this->assertEquals('message.sent', $event->broadcastAs());
    }

    /** @test */
    public function user_a_can_send_first_message_immediately_after_activation_and_triggers_broadcast_and_user_notification()
    {
        \Illuminate\Support\Facades\Event::fake([MessageSent::class]);
        \Illuminate\Support\Facades\Notification::fake();

        // 1. Connection accepted and paid
        $conn = ConnectionRequest::create([
            'initiator_id' => $this->participantB->id,
            'recipient_id' => $this->participantA->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
        ]);

        $response = $this->actingAs($this->participantB)->postJson("/connections/{$conn->id}/pay");
        $response->assertStatus(200)->assertJson(['success' => true]);

        $convId = $response->json('conversation_id');
        $this->assertNotNull($convId);

        // 2. User A (Job Owner) sends first message immediately
        $msgResponse = $this->actingAs($this->participantA)->postJson("/conversations/{$convId}/messages", [
            'body' => 'Hello User B, thanks for applying!',
            'client_msg_id' => 'cmsg_test_a1',
        ]);

        $msgResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $messageId = $msgResponse->json('message.id');
        $this->assertNotNull($messageId);

        // Assert MessageSent event was broadcasted to conversation channel
        \Illuminate\Support\Facades\Event::assertDispatched(MessageSent::class, function ($event) use ($convId, $messageId) {
            return (int)$event->message->conversation_id === (int)$convId 
                && (int)$event->message->id === (int)$messageId
                && $event->clientMsgId === 'cmsg_test_a1';
        });

        // Assert NewMessageNotification was sent to User B's user channel
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $this->participantB,
            \App\Notifications\NewMessageNotification::class,
            function ($notif) use ($convId, $messageId) {
                $payload = $notif->toArray($this->participantB);
                return (int)$payload['conversation_id'] === (int)$convId
                    && (int)$payload['message_id'] === (int)$messageId
                    && $notif->broadcastOn()[0]->name === 'private-user.' . $this->participantB->id;
            }
        );
    }

    /** @test */
    public function user_b_can_send_first_message_immediately_after_activation()
    {
        \Illuminate\Support\Facades\Event::fake([MessageSent::class]);
        \Illuminate\Support\Facades\Notification::fake();

        $conn = ConnectionRequest::create([
            'initiator_id' => $this->participantB->id,
            'recipient_id' => $this->participantA->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
        ]);

        $response = $this->actingAs($this->participantB)->postJson("/connections/{$conn->id}/pay");
        $response->assertStatus(200);

        $convId = $response->json('conversation_id');

        // User B sends first message
        $msgResponse = $this->actingAs($this->participantB)->postJson("/connections/{$conn->id}/messages", [
            'body' => 'Hi User A, I just completed payment!',
            'client_msg_id' => 'cmsg_test_b1',
        ]);

        $msgResponse->assertStatus(200)->assertJson(['success' => true]);

        \Illuminate\Support\Facades\Event::assertDispatched(MessageSent::class);
        \Illuminate\Support\Facades\Notification::assertSentTo(
            $this->participantA,
            \App\Notifications\NewMessageNotification::class
        );
    }

    /** @test */
    public function initial_placeholder_message_has_non_numeric_synthetic_id()
    {
        $conn = ConnectionRequest::create([
            'initiator_id' => $this->participantB->id,
            'recipient_id' => $this->participantA->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
            'initial_message' => 'Application initial note',
        ]);

        $response = $this->actingAs($this->participantB)->get('/dashboard/messages');
        $response->assertStatus(200);

        // Verify that initial placeholder message ID is synthetic 'init_conn_...'
        $response->assertSee('init_conn_' . $conn->id);
    }
}

