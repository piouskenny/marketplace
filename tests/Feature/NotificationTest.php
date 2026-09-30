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

    public function test_applying_to_opportunity_sends_mail_notification_to_job_poster(): void
    {
        \Illuminate\Support\Facades\Mail::fake();

        $poster = User::factory()->create(['name' => 'Job Poster', 'email' => 'poster@example.com']);
        $applicant = User::factory()->create(['name' => 'Applicant User', 'phone' => '+2348011112222', 'email' => 'applicant@example.com']);

        $category = \App\Models\Category::create(['name' => 'Education & Tutoring', 'slug' => 'edu-tutor']);
        $opportunity = \App\Models\Opportunity::create([
            'user_id' => $poster->id,
            'category_id' => $category->id,
            'title' => 'Senior Math Tutor Required',
            'description' => 'Need math tutor for SS3 student.',
            'location' => 'Lagos',
            'opportunity_type' => 'Physical In-Person',
            'status' => 'open',
        ]);

        $connection = ConnectionRequest::create([
            'initiator_id' => $applicant->id,
            'recipient_id' => $poster->id,
            'opportunity_id' => $opportunity->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
            'initial_message' => 'I would love to tutor your child.',
        ]);

        event(new ConnectionRequestCreated($connection->id));

        $this->assertEquals(1, $poster->notifications()->count());
        $notification = $poster->notifications()->first();

        // Verify broadcast/database payload does NOT expose private phone or email
        $payload = $notification->data;
        $this->assertArrayNotHasKey('phone', $payload['applicant_profile'] ?? []);
        $this->assertArrayNotHasKey('email', $payload['applicant_profile'] ?? []);
        $this->assertEquals('Applicant User', $payload['initiator_name']);
        $this->assertEquals('Senior Math Tutor Required', $payload['opportunity_title']);
    }

    public function test_connection_request_email_contains_safe_link_and_opportunity_context(): void
    {
        $poster = User::factory()->create(['name' => 'Job Poster Owner', 'email' => 'owner@example.com']);
        $applicant = User::factory()->create(['name' => 'Talent Applicant', 'phone' => '+2348099998888', 'email' => 'talent@example.com']);

        $connection = ConnectionRequest::create([
            'initiator_id' => $applicant->id,
            'recipient_id' => $poster->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
            'initial_message' => 'I have 5 years experience in physics tutoring.',
        ]);

        $notification = new \App\Notifications\ConnectionRequestNotification($connection);
        $mail = $notification->toMail($poster);

        $this->assertStringContainsString('Talent Applicant', $mail->introLines[0]);
        $this->assertStringContainsString('I have 5 years experience in physics tutoring.', $mail->introLines[1] ?? $mail->introLines[0]);
        $this->assertStringContainsString('/dashboard/messages?conn_id=' . $connection->id, $mail->actionUrl);

        // Verify email content does NOT expose raw phone or email
        $mailContent = json_encode($mail);
        $this->assertStringNotContainsString('+2348099998888', $mailContent);
        $this->assertStringNotContainsString('talent@example.com', $mailContent);
    }
}
