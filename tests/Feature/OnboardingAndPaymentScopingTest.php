<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Enums\OpportunityStatus;
use App\Models\Category;
use App\Models\ConnectionRequest;
use App\Models\Opportunity;
use App\Models\User;
use App\Notifications\ConnectionRequestNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class OnboardingAndPaymentScopingTest extends TestCase
{
    use RefreshDatabase;

    protected User $jobPoster;
    protected User $applicantIncomplete;
    protected User $applicantComplete;
    protected Category $category;
    protected Opportunity $opportunity;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jobPoster = User::factory()->create([
            'name' => 'Job Poster User',
            'onboarding_completed' => true,
        ]);

        $this->applicantIncomplete = User::factory()->create([
            'name' => 'Incomplete Applicant',
            'onboarding_completed' => false,
        ]);

        $this->applicantComplete = User::factory()->create([
            'name' => 'Complete Applicant',
            'onboarding_completed' => true,
        ]);

        $this->category = Category::create([
            'name' => 'Academic Tutoring',
            'slug' => 'academic-tutoring',
        ]);

        $this->opportunity = Opportunity::create([
            'user_id' => $this->jobPoster->id,
            'category_id' => $this->category->id,
            'title' => 'SS2 Math Tutor Needed',
            'description' => 'Need an experienced WAEC math tutor.',
            'status' => OpportunityStatus::Open,
            'location' => 'Ikeja, Lagos',
        ]);
    }

    /** @test */
    public function incomplete_user_cannot_apply_and_is_redirected_to_onboarding()
    {
        $response = $this->actingAs($this->applicantIncomplete)
            ->post("/opportunities/{$this->opportunity->id}/apply", [
                'note' => 'I would like to apply.',
            ]);

        $response->assertRedirect(route('onboarding'))
            ->assertSessionHas('error', 'Complete your profile before applying for opportunities.');

        $this->assertDatabaseMissing('connection_requests', [
            'initiator_id' => $this->applicantIncomplete->id,
            'opportunity_id' => $this->opportunity->id,
        ]);
    }

    /** @test */
    public function incomplete_user_json_apply_request_returns_403()
    {
        $response = $this->actingAs($this->applicantIncomplete)
            ->postJson("/opportunities/{$this->opportunity->id}/apply", [
                'note' => 'I would like to apply.',
            ]);

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'error' => 'Complete your profile before applying for opportunities.',
            ]);
    }

    /** @test */
    public function complete_user_can_apply_to_opportunity()
    {
        $response = $this->actingAs($this->applicantComplete)
            ->post("/opportunities/{$this->opportunity->id}/apply", [
                'note' => 'I am a qualified math tutor.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('connection_requests', [
            'initiator_id' => $this->applicantComplete->id,
            'recipient_id' => $this->jobPoster->id,
            'opportunity_id' => $this->opportunity->id,
            'status' => ConnectionStatus::Pending->value,
        ]);
    }

    /** @test */
    public function recipient_job_poster_cannot_pay_for_connection()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->applicantComplete->id,
            'recipient_id' => $this->jobPoster->id,
            'opportunity_id' => $this->opportunity->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
        ]);

        $response = $this->actingAs($this->jobPoster)
            ->post("/connections/{$connection->id}/pay");

        $response->assertStatus(403);
    }

    /** @test */
    public function initiator_applicant_can_pay_and_activate_connection()
    {
        $connection = ConnectionRequest::create([
            'initiator_id' => $this->applicantComplete->id,
            'recipient_id' => $this->jobPoster->id,
            'opportunity_id' => $this->opportunity->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Accepted,
        ]);

        $response = $this->actingAs($this->applicantComplete)
            ->post("/connections/{$connection->id}/pay");

        $response->assertRedirect();
        $this->assertDatabaseHas('connection_requests', [
            'id' => $connection->id,
            'status' => ConnectionStatus::Connected->value,
        ]);
    }

    /** @test */
    public function connection_request_notification_contains_rich_payload_for_realtime_ui()
    {
        Notification::fake();

        $connection = ConnectionRequest::create([
            'initiator_id' => $this->applicantComplete->id,
            'recipient_id' => $this->jobPoster->id,
            'opportunity_id' => $this->opportunity->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Pending,
            'initial_message' => 'Hello from complete applicant',
        ]);

        event(new \App\Events\ConnectionRequestCreated($connection->id));

        Notification::assertSentTo(
            $this->jobPoster,
            ConnectionRequestNotification::class,
            function ($notification, $channels) {
                $payload = $notification->toArray($this->jobPoster);

                return in_array('broadcast', $channels)
                    && $payload['type'] === 'connection_request'
                    && $payload['initiator_name'] === 'Complete Applicant'
                    && $payload['opportunity_title'] === 'SS2 Math Tutor Needed'
                    && $payload['initial_message'] === 'Hello from complete applicant';
            }
        );
    }
}
