<?php

namespace Tests\Feature;

use App\Actions\Conversations\CreateConversationAction;
use App\Actions\Messages\MarkMessagesAsReadAction;
use App\Actions\Messages\SendMessageAction;
use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Events\MessagesRead;
use App\Models\ConnectionRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ReadReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected ConnectionRequest $connection1;
    protected Conversation $conversation1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userA = User::factory()->create(['name' => 'User A']);
        $this->userB = User::factory()->create(['name' => 'User B']);

        $this->connection1 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $this->conversation1 = (new CreateConversationAction())->execute($this->connection1);
    }

    /** @test */
    public function recipient_opening_conversation_marks_incoming_messages_read_and_broadcasts_event()
    {
        Event::fake([MessagesRead::class]);

        $sendAction = new SendMessageAction();
        $msg1 = $sendAction->execute($this->conversation1, $this->userA, 'Hello from A (msg 1)');
        $msg2 = $sendAction->execute($this->conversation1, $this->userA, 'Hello from A (msg 2)');

        $this->assertNull($msg1->fresh()->read_at);
        $this->assertNull($msg2->fresh()->read_at);

        // User B reads conversation 1
        $readCount = (new MarkMessagesAsReadAction())->execute($this->conversation1, $this->userB);

        $this->assertEquals(2, $readCount);
        $this->assertNotNull($msg1->fresh()->read_at);
        $this->assertNotNull($msg2->fresh()->read_at);

        Event::assertDispatched(MessagesRead::class, function ($event) {
            return $event->conversationId === $this->conversation1->id
                && $event->readerId === $this->userB->id
                && count($event->messageIds) === 2;
        });
    }

    /** @test */
    public function sender_cannot_mark_their_own_outgoing_messages_as_read()
    {
        Event::fake([MessagesRead::class]);

        $sendAction = new SendMessageAction();
        $msgFromA = $sendAction->execute($this->conversation1, $this->userA, 'Outgoing from A');

        // User A attempts to run MarkMessagesAsReadAction on conversation 1
        $readCount = (new MarkMessagesAsReadAction())->execute($this->conversation1, $this->userA);

        $this->assertEquals(0, $readCount);
        $this->assertNull($msgFromA->fresh()->read_at);

        Event::assertNotDispatched(MessagesRead::class);
    }

    /** @test */
    public function reading_conversation_201_does_not_mark_messages_in_conversation_202_as_read()
    {
        // Second connection between User A and User B (e.g. Opportunity #2)
        $connection2 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $conversation2 = (new CreateConversationAction())->execute($connection2);

        $sendAction = new SendMessageAction();
        $msgConv1 = $sendAction->execute($this->conversation1, $this->userA, 'Message in Conv 1');
        $msgConv2 = $sendAction->execute($conversation2, $this->userA, 'Message in Conv 2');

        // User B reads Conv 1
        $this->actingAs($this->userB)->postJson("/conversations/{$this->conversation1->id}/read");

        // Conv 1 message should be read
        $this->assertNotNull($msgConv1->fresh()->read_at);

        // Conv 2 message MUST STILL BE UNREAD
        $this->assertNull($msgConv2->fresh()->read_at);
    }

    /** @test */
    public function unread_message_count_and_unread_conversation_count_distinction()
    {
        $connection2 = ConnectionRequest::create([
            'initiator_id' => $this->userA->id,
            'recipient_id' => $this->userB->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Connected,
        ]);
        $conversation2 = (new CreateConversationAction())->execute($connection2);

        $sendAction = new SendMessageAction();

        // User A sends 3 messages in Conv 1
        $sendAction->execute($this->conversation1, $this->userA, 'Conv 1 msg 1');
        $sendAction->execute($this->conversation1, $this->userA, 'Conv 1 msg 2');
        $sendAction->execute($this->conversation1, $this->userA, 'Conv 1 msg 3');

        // User A sends 2 messages in Conv 2
        $sendAction->execute($conversation2, $this->userA, 'Conv 2 msg 1');
        $sendAction->execute($conversation2, $this->userA, 'Conv 2 msg 2');

        // Total unread messages for User B across all conversations = 5
        $totalUnreadMessagesForB = Message::where('sender_id', '!=', $this->userB->id)
            ->whereNull('read_at')
            ->count();
        $this->assertEquals(5, $totalUnreadMessagesForB);

        // Total unread conversations for User B = 2
        $userBId = $this->userB->id;
        $unreadConversationsForB = Conversation::whereHas('messages', function ($q) use ($userBId) {
            $q->where('sender_id', '!=', $userBId)->whereNull('read_at');
        })->count();
        $this->assertEquals(2, $unreadConversationsForB);

        // User B opens Conv 1 (marks Conv 1 messages read)
        (new MarkMessagesAsReadAction())->execute($this->conversation1, $this->userB);

        // Remaining unread messages = 2 (all in Conv 2)
        $remainingUnreadMessages = Message::where('sender_id', '!=', $this->userB->id)
            ->whereNull('read_at')
            ->count();
        $this->assertEquals(2, $remainingUnreadMessages);

        // Remaining unread conversations = 1 (Conv 2)
        $remainingUnreadConvs = Conversation::whereHas('messages', function ($q) use ($userBId) {
            $q->where('sender_id', '!=', $userBId)->whereNull('read_at');
        })->count();
        $this->assertEquals(1, $remainingUnreadConvs);
    }

    /** @test */
    public function messages_read_event_broadcasts_on_private_conversation_channel()
    {
        $event = new MessagesRead($this->conversation1->id, $this->userB->id, now()->toIso8601String(), [10, 11]);

        $channels = $event->broadcastOn();

        $this->assertCount(1, $channels);
        $this->assertEquals("private-conversation.{$this->conversation1->id}", $channels[0]->name);
        $this->assertEquals('messages.read', $event->broadcastAs());
    }
}
