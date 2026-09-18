<?php

namespace Tests\Feature;

use App\Enums\ConnectionStatus;
use App\Enums\ConnectionType;
use App\Models\Category;
use App\Models\ConnectionRequest;
use App\Models\Opportunity;
use App\Models\ProfessionalProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_another_users_profile_with_masked_contact_details_when_unconnected()
    {
        $viewer = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $category = Category::create([
            'name' => 'Education & Tutoring',
            'slug' => 'education-tutoring',
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Jane Professional',
            'phone' => '+2348012345678',
            'email' => 'jane.pro@example.com',
            'location' => 'Ikeja, Lagos',
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        ProfessionalProfile::create([
            'user_id' => $targetUser->id,
            'category_id' => $category->id,
            'display_name' => 'Senior Math Tutor',
            'bio' => 'Experienced tutor in Lagos.',
        ]);

        $response = $this->actingAs($viewer)->get(route('profile.show', $targetUser));

        $response->assertStatus(200);
        $response->assertSee('Jane Professional');
        $response->assertSee('Senior Math Tutor');
        $response->assertSee('Contact Details (Masked');
        $response->assertDontSee('+2348012345678');
        $response->assertDontSee('jane.pro@example.com');
    }

    public function test_connected_users_can_view_unmasked_contact_details_and_go_to_chat_button()
    {
        $viewer = User::factory()->create([
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        $category = Category::create([
            'name' => 'Education & Tutoring',
            'slug' => 'education-tutoring-2',
        ]);

        $targetUser = User::factory()->create([
            'name' => 'John Expert',
            'phone' => '+2348098765432',
            'email' => 'john.expert@example.com',
            'email_verified_at' => now(),
            'onboarding_completed' => true,
        ]);

        ProfessionalProfile::create([
            'user_id' => $targetUser->id,
            'category_id' => $category->id,
            'display_name' => 'Expert Physics Tutor',
        ]);

        $opportunity = Opportunity::create([
            'user_id' => $targetUser->id,
            'category_id' => $category->id,
            'title' => 'SS2 Physics Tutor',
            'description' => 'Need tutor 3 days a week.',
            'location' => 'Lagos',
            'opportunity_type' => 'Physical In-Person',
            'status' => 'open',
        ]);

        $conn = ConnectionRequest::create([
            'initiator_id' => $viewer->id,
            'recipient_id' => $targetUser->id,
            'opportunity_id' => $opportunity->id,
            'type' => ConnectionType::OpportunityApplication,
            'status' => ConnectionStatus::Connected,
            'connected_at' => now(),
        ]);

        $response = $this->actingAs($viewer)->get(route('profile.show', $targetUser));

        $response->assertStatus(200);
        $response->assertSee('John Expert');
        $response->assertSee('+2348098765432');
        $response->assertSee('john.expert@example.com');
        $response->assertSee('Direct Contact Details (Unlocked)');
        $response->assertSee('Go to Chat / Message Now →');
        $response->assertSee('conn_id=' . $conn->id);
    }
}
