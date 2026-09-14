<?php

namespace Tests\Feature;

use App\Actions\Conversations\CreateConversationAction;
use App\Actions\Messages\MarkMessagesAsReadAction;
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

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected User $userC;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create(['name' => 'User A']);
        $this->userB = User::factory()->create(['name' => 'User B']);
        $this->userC = User::factory()->create(['name' => 'User C']);
    }

    /** @test */
    public function non_participants_cannot_view_or_send_messages()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = (new CreateConversationAction())->execute($connection);

        // User C is a third party
        $this->actingAs($this->userC);

        $responseView = $this->getJson("/conversations/{$conversation->id}/messages");
        $responseView->assertStatus(403);

        $responseSend = $this->postJson("/conversations/{$conversation->id}/messages", [
            'body' => 'Unauthorized message attempt',
        ]);
        $responseSend->assertStatus(403);
    }

    /** @test */
    public function participants_cannot_send_messages_unless_connection_is_connected()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending, // NOT Connected
        ]);

        $conversation = Conversation::create([
            'connection_request_id' => $connection->id,
        ]);

        $this->actingAs($this->userA);

        $response = $this->postJson("/conversations/{$conversation->id}/messages", [
            'body' => 'Trying to message on pending connection',
        ]);

        $response->assertStatus(403);

        // Direct Action execution invariant check
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        (new SendMessageAction())->execute($conversation, $this->userA, 'Direct action attempt');
    }

    /** @test */
    public function empty_or_whitespace_messages_are_rejected()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = (new CreateConversationAction())->execute($connection);

        $this->actingAs($this->userA);

        $responseEmpty = $this->postJson("/conversations/{$conversation->id}/messages", [
            'body' => '',
        ]);
        $responseEmpty->assertStatus(422);

        $responseWhitespace = $this->postJson("/conversations/{$conversation->id}/messages", [
            'body' => '   ',
        ]);
        $responseWhitespace->assertStatus(422);
    }

    /** @test */
    public function connected_participants_can_send_messages_and_event_is_broadcast()
    {
        Event::fake([MessageSent::class]);

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation = (new CreateConversationAction())->execute($connection);

        $this->actingAs($this->userA);

        $response = $this->postJson("/conversations/{$conversation->id}/messages", [
            'body' => 'Hello User B!',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message.body', 'Hello User B!');

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Hello User B!',
        ]);

        Event::assertDispatched(MessageSent::class, function ($event) use ($conversation) {
            return $event->message->conversation_id === $conversation->id
                && $event->message->body === 'Hello User B!';
        });
    }

    /** @test */
    public function duplicate_payment_callbacks_and_action_do_not_create_duplicate_conversations()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
        ]);

        $action = new CreateConversationAction();

        $conv1 = $action->execute($connection);
        $conv2 = $action->execute($connection);

        $this->assertEquals($conv1->id, $conv2->id);
        $this->assertEquals(1, Conversation::where('connection_request_id', $connection->id)->count());
    }

    /** @test */
    public function database_unique_constraint_prevents_multiple_conversations_per_connection()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
        ]);

        Conversation::create(['connection_request_id' => $connection->id]);

        $this->expectException(\Illuminate\Database\QueryException::class);
        Conversation::create(['connection_request_id' => $connection->id]);
    }

    /** @test */
    public function read_at_only_updates_incoming_messages_for_recipient()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
        ]);

        $conversation = (new CreateConversationAction())->execute($connection);

        // User A sends a message
        $msgFromA = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userA->id,
            'body' => 'Message from A to B',
        ]);

        // User B sends a message
        $msgFromB = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $this->userB->id,
            'body' => 'Message from B to A',
        ]);

        // User B reads the conversation
        (new MarkMessagesAsReadAction())->execute($conversation, $this->userB);

        // Message from A to B should be marked read
        $msgFromA->refresh();
        $this->assertNotNull($msgFromA->read_at);

        // Message from B to A should STILL be unread (because User B sent it)
        $msgFromB->refresh();
        $this->assertNull($msgFromB->read_at);
    }
}
