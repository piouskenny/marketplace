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
}
