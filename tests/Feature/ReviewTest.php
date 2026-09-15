<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Models\ConnectionRequest;
use App\Models\ProfessionalProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_submit_review_for_connected_request(): void
    {
        $initiator = User::factory()->create();
        $recipient = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $initiator->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $response = $this->actingAs($initiator)->postJson("/connections/{$connection->id}/review", [
            'rating' => 5,
            'comment' => 'Exemplary tutor! Highly recommended.',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('reviews', [
            'connection_request_id' => $connection->id,
            'reviewer_id' => $initiator->id,
            'reviewee_id' => $recipient->id,
            'rating' => 5,
            'comment' => 'Exemplary tutor! Highly recommended.',
        ]);
    }

    public function test_unconnected_request_cannot_be_reviewed(): void
    {
        $initiator = User::factory()->create();
        $recipient = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $initiator->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Pending,
        ]);

        $response = $this->actingAs($initiator)->postJson("/connections/{$connection->id}/review", [
            'rating' => 5,
            'comment' => 'Premature review',
        ]);

        $response->assertStatus(422);
        $this->assertDatabaseMissing('reviews', [
            'connection_request_id' => $connection->id,
        ]);
    }

    public function test_duplicate_review_is_prevented(): void
    {
        $initiator = User::factory()->create();
        $recipient = User::factory()->create();

        $connection = ConnectionRequest::create([
            'initiator_id' => $initiator->id,
            'recipient_id' => $recipient->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        Review::create([
            'connection_request_id' => $connection->id,
            'reviewer_id' => $initiator->id,
            'reviewee_id' => $recipient->id,
            'rating' => 4,
            'comment' => 'First review',
        ]);

        $response = $this->actingAs($initiator)->postJson("/connections/{$connection->id}/review", [
            'rating' => 5,
            'comment' => 'Second attempt',
        ]);

        $response->assertStatus(422);
        $this->assertEquals(1, Review::where('connection_request_id', $connection->id)->count());
    }

    public function test_professional_profile_average_rating_updates(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $professional = User::factory()->create();

        $category = \App\Models\Category::create([
            'name' => 'Home & Technical Services',
            'slug' => 'home-technical-services',
        ]);

        $profile = ProfessionalProfile::create([
            'user_id' => $professional->id,
            'category_id' => $category->id,
            'display_name' => 'Dr. Solar',
            'bio' => 'Expert electrician',
            'location' => 'Lagos',
            'average_rating' => 0,
            'reviews_count' => 0,
        ]);

        $conn1 = ConnectionRequest::create([
            'initiator_id' => $user1->id,
            'recipient_id' => $professional->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Connected,
        ]);

        $conn2 = ConnectionRequest::create([
            'initiator_id' => $user2->id,
            'recipient_id' => $professional->id,
            'type' => ConnectionType::ProfessionalRequest,
            'status' => ConnectionStatus::Connected,
        ]);

        $this->actingAs($user1)->postJson("/connections/{$conn1->id}/review", [
            'rating' => 4,
            'comment' => 'Good work',
        ]);

        $this->actingAs($user2)->postJson("/connections/{$conn2->id}/review", [
            'rating' => 5,
            'comment' => 'Outstanding!',
        ]);

        $profile->refresh();
        $this->assertEquals(4.50, (float) $profile->average_rating);
        $this->assertEquals(2, $profile->reviews_count);
    }
}
